<?php

namespace App\Http\Controllers\Invoice;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Section;
use App\Models\Addition;
use Illuminate\Http\Request;
use App\Events\InvoiceProcessed;
use App\Traits\InvoiceProcessing;
use Illuminate\Support\Facades\DB;
use App\Events\RegisterOrderPricing;
use App\Http\Controllers\Controller;
use App\Http\Requests\InvoiceRequest;
use App\Http\Resources\invoiceUpdate;

class InvoiceController extends Controller
{
    use InvoiceProcessing;

    private $invoice;


    public function __construct()
    {
        $this->middleware(['super.admin'])->only(['pay' , 'destroy' , 'restore']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invoices = Invoice::withTrashed()
        ->with(['client', 'orders' => function ($query) {
            $query->orderByRaw('IF(orders.payment < orders.price, 1, 0) DESC')
                ->orderBy('orders.created_at', 'desc');
        }])
        ->selectRaw('invoices.*,
            (SELECT SUM(orders.price) FROM orders WHERE orders.invoice_id = invoices.id) as total_price,
            (SELECT SUM(orders.payment) FROM orders WHERE orders.invoice_id = invoices.id) as total_payment'
        )
        ->orderByRaw('ISNULL(restored_at) DESC')
        ->orderByRaw('IF(total_payment < total_price, 1, 0) DESC') // Prioritize invoices with unpaid amounts
        ->orderBy('invoices.created_at', 'desc')
        ->paginate(PAGINATE);

        return view('invoices.index' , ['invoices' => $invoices]);
    }

    /**
     * Show the form for creating a new resource.
     */
    // public function create()
    // {
    //     // $parent_sections = $this->getSections();
    //     $parent_sections = Section::whereNull('section_id')->get(['id' , 'name']);

    //     return view('invoices.create' , ['parent_sections' => $parent_sections]);
    // }
    public function createPending()
    {
        $parent_sections = $this->getSections(1);

        return view('invoices.create-pending' , ['parent_sections' => $parent_sections]);
    }
    public function createInactive()
    {
        $parent_sections = $this->getSections(0);

        return view('invoices.create-inactive' , ['parent_sections' => $parent_sections]);
    }

    public function getSections($status)
    {
        return Section::whereNull('section_id')
        ->where('status' , $status)
        ->get(['id' , 'name']);
    }

    public function getSection(Request $request)
    {
        try
        {
            $request->validate([
                'parent_id' => 'required|integer',
            ]);
            $section = Section::where('section_id',$request->parent_id)->get();
            return response()->json(['data' =>$section], 200);
        }
        catch (\Exception $e)
        {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getProductData(Request $request)
    {
        try {
            $request->validate([
                'section_id' => 'required|integer',
                'status' => 'required|string|in:inactive,pending',
                'date_of_receipt' => 'nullable|date',
                'return_date' => 'nullable|date',
            ]);


            $start = Carbon::parse($request->date_of_receipt ?? Carbon::now())->format('Y-m-d');
            $end = Carbon::parse($request->return_date ?? Carbon::now())->format('Y-m-d');
            $ids = [];
            $product = Product::where('section_id', $request->section_id)
            ->leftJoin('orders', 'orders.product_id', '=', 'products.id')
            ->leftJoin('invoices', 'invoices.id', '=', 'orders.invoice_id')
            ->whereNull('invoices.restored_at')
            ->distinct();

            if($request->status === 'pending'){
                if($product->count() > 0) {
                    $ids = $product->where(function ($query) use ($start, $end) {
                        $query->whereBetween('date_of_receipt', [$start, $end])
                            ->orWhereBetween('return_date', [$start, $end])
                            ->orWhere(function ($query) use ($start, $end) {
                                $query->where('date_of_receipt', '<', $start)
                                        ->where('return_date', '>', $end);
                            });
                    })
                    ->pluck('products.id');
                    foreach($ids as $index => $id){
                        $count = Invoice::leftJoin('orders' , 'orders.invoice_id' , 'invoices.id')->where('orders.product_id', $id)->count();
                        if(Product::whereId($id)->value("quantity") > $count){
                            unset($ids[$index]);
                        }
                    }
                }
            }else{

                if($product->count() > 0) {
                    $ids = $product->where(function ($query) use ($start, $end) {
                        $query->whereBetween('date_of_receipt', [$start, $end])
                            ->orWhereBetween('return_date', [$start, $end])
                            ->orWhere(function ($query) use ($start, $end) {
                                $query->where('date_of_receipt', '>', $start)
                                        ->orWhere('return_date', '>', $end);
                            });
                    })
                    ->pluck('products.id');
                    foreach($ids as $index => $id){
                        $count = Invoice::leftJoin('orders' , 'orders.invoice_id' , 'invoices.id')->where('orders.product_id', $id)->count();
                        if(Product::whereId($id)->value("quantity") > $count){
                            unset($ids[$index]);
                        }
                    }
                }

            }
            $products = Product::whereNotIn('id' , $ids)->where('products.quantity' ,">=" , 1)->where('section_id' , $request->section_id)->get();
            return response()->json(['data' =>$products], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function pay(string $id)
    {
        DB::beginTransaction();
        try {
            $invoice = Invoice::withTrashed()
            ->select('id', 'status' ,'return_date', 'created_at')
            ->with(['orders:id,invoice_id,price,payment'])
            ->whereId($id)
            ->first();

            foreach($invoice->orders as $order)
            {
                event(new RegisterOrderPricing($order->id , $order->price - $order->payment));
                $order->update(['payment'=> $order->price]);
                $order->save();
            }
            DB::commit();

            // event(new InvoiceProcessed($invoice));
            return back()->with('success' , 'تم تحديث الفاتورة بنجاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error',"خطا");
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InvoiceRequest $request)
    {
        DB::beginTransaction();
        try
        {
            $request['address'] = trim(($request->city ?? '') . ' ' . ($request->address ?? ''));
            $client = Client::create($request->only(['name' , 'phone' , 'address']));

            $invoice = Invoice::create($request->only(['status', 'date_of_receipt' ,'return_date']) + ['client_id' => $client->id]);

            if(is_array($request->get('list-product')) && count($request->get('list-product')) > 0){
                foreach ($request->get('list-product') as $products) {
                    unset($products['section_id']);
                    $products['invoice_id'] = $invoice->id;
                    if($request->status == 'inactive'){
                        Product::whereId($products['product_id'])->decrement('quantity');
                    }
                    $order =  Order::create($products);
                    event(new RegisterOrderPricing($order->id , $order->payment));
                }
            }
            if(is_array($request->get('list-product-1')) && count($request->get('list-product-1')) > 0){
                foreach ($request->get('list-product-1') as $additions) {
                    $addition = Addition::create(['title' => $additions['title'] , 'data' => $additions['data']]);
                    unset($additions['title'] ,$additions['data']);
                    $additions['invoice_id'] = $invoice->id;
                    $additions['addition_id'] = $addition->id;
                    $order = Order::create($additions);
                    event(new RegisterOrderPricing($order->id , $order->payment));
                }
            }
            // event(new InvoiceProcessed($invoice));
            DB::commit();
            return to_route('invoice.print' , $invoice->id)->with("success" ,'تم اضافة فاتورة بنجاح' );
        }catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error','خطا');
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $this->invoice = Invoice::withTrashed()->with(['client','orders.product', 'orders.addition'])->where('id', $id)->first();
        if(!$this->invoice)
        {
            abort(404);
        }
        $orders = $this->invoice->orders;
        return view('invoices.show' , ['orders' => $orders]);
    }

    public function print(string $id)
    {
        $this->show($id);
        return view('invoices.print', ['invoice' => $this->invoice]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function edit(string $id , string $status)
    {
        $parent_sections = $this->getSections($status === 'pending' ? 1 : 0);
        $invoice = self::getInvoice($id);
  

        $invoice->products = json_decode($invoice->products, true);
        $invoice->additions = json_decode($invoice->additions, true);

 
        $invoice->products = is_array($invoice->products) ? array_values(array_filter($invoice->products)) : [];
        $invoice->additions = is_array($invoice->additions) ? array_values(array_filter($invoice->additions)) : [];

        $invoice->products = collect($invoice->products)->map(function($product) {
            if (is_null($product['parent_id'])) {
                $product['parent_id']= $product['section_id'];
                unset($product['section_id']);
            }
            return $product;
        })->toArray();


        // return $invoice;
        return view('invoices.edit', [
            'parent_sections' => $parent_sections,
            'invoice' => $invoice,
        ]);
    }
    public function update(InvoiceRequest $request, string $id)
    {
        // return $request;
        DB::beginTransaction();
        try
        {
            $this->destroy($id);
            $this->store($request);
            DB::commit();
            return to_route('invoice.index')->with(['success' => 'تم تعديل الفاتوره بنجاح']);
        }catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error','خطا');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $invoice = Invoice::withTrashed()->findOrFail($id);
        $client = $invoice->client;

        $invoice->delete();

        if ($client->invoice()->count() === 0) {
            $client->delete();
        }


        // event(new InvoiceProcessed($invoice));
        return back()->with('success' , 'تم حذف الفاتورة بنجاح');
    }
    public function restore(string $id)
    {
        $product_ids = DB::table('invoices')
        ->where('invoices.id' , '=' , $id)
        ->where('status' , 'inactive')
        ->leftJoin('orders' , 'orders.invoice_id' , 'invoices.id')
        ->leftJoin('products' , 'products.id' , 'orders.product_id')
        ->select("product_id")
        ->get();

        DB::table('invoices')
        ->where('invoices.id' , '=' , $id)
        ->update(['restored_at' => now()]);

        $ids = array_filter($product_ids->all(), function($item) {
            return $item->product_id !== null;
        });

        foreach($ids as $id){
            Product::whereId($id->product_id)->increment('quantity');
        }
        return back()->with('success' , 'تم استرجاع المنتج في المخزن بنجاح');
    }

    public function search(Request $request)
    {
        $query = $request->input('search');
        $status = $request->input('status');
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');


        $queryBuilder = DB::table('invoices')
            ->rightJoin('clients', 'clients.id', 'invoices.client_id')
            ->rightJoin('orders', 'orders.invoice_id', 'invoices.id')
            ->select(
                'invoices.id',
                'invoices.status',
                'invoices.date_of_receipt',
                'invoices.return_date',
                'invoices.created_at',
                'invoices.restored_at',
                'clients.name AS name',
                'clients.address AS address',
                'clients.phone AS phone',
                DB::raw('SUM(orders.price) AS price'),
                DB::raw('SUM(orders.payment) AS payment'),
                DB::raw('COUNT(orders.invoice_id) AS count')
            )
            ->groupBy(
                'invoices.id',
                'invoices.status',
                'clients.name',
                'clients.address',
                'clients.phone',
                'invoices.date_of_receipt',
                'invoices.return_date',
                'invoices.restored_at',
                'invoices.created_at'
            );

        if ($query) {
            $queryBuilder->where(function ($subQuery) use ($query) {
                $subQuery->where('clients.name', 'like', "%{$query}%");
            });
        }


        if ($status) {
            $queryBuilder->where('invoices.status', $status);
        }
        if ($start_date) {
            $queryBuilder->where(function ($subQuery) use ($start_date) {
                $subQuery->WhereDate('invoices.date_of_receipt', '>=', $start_date);
            });
        }

        if ($end_date) {
            $queryBuilder->where(function ($subQuery) use ($end_date) {
                $subQuery->whereDate('invoices.return_date', '<=', $end_date);
            });
        }

        $invoices = $queryBuilder->orderByRaw('ISNULL(restored_at) DESC')->paginate(PAGINATE);

        return response()->json([
            'tableRows' => view('partials.invoices_table', ['invoices' => $invoices])->render(),
            'pagination' => $invoices->appends(['search' => $query, 'status' => $status])->links()->render()
        ]);
    }

    public function pending()
    {
        // $invoices = Invoice::withTrashed()
        // ->where('status' ,'pending')
        // ->with(['client','orders'])
        // ->orderByRaw('ISNULL(restored_at) DESC')
        // ->paginate(PAGINATE);
        $invoices = $this->getInvoiceByStatus('pending');

        return view('invoices.pending' , ['invoices' => $invoices]);
    }
    public function inactive()
    {
        $invoices = $this->getInvoiceByStatus('inactive');
        // $invoices = Invoice::withTrashed()->where('status' ,'inactive')->with(['client','orders'])->orderByRaw('ISNULL(restored_at) DESC')->paginate(PAGINATE);
        return view('invoices.inactive' , ['invoices' => $invoices]);
    }

    private function getInvoiceByStatus($status)
    {
        return Invoice::withTrashed()
        ->with(['client', 'orders' => function ($query) {
            $query->orderByRaw('IF(orders.payment < orders.price, 1, 0) DESC')
                ->orderBy('orders.created_at', 'desc');
        }])
        ->where('status' , $status)
        ->selectRaw('invoices.*,
            (SELECT SUM(orders.price) FROM orders WHERE orders.invoice_id = invoices.id) as total_price,
            (SELECT SUM(orders.payment) FROM orders WHERE orders.invoice_id = invoices.id) as total_payment'
        )
        ->orderByRaw('ISNULL(restored_at) DESC')
        ->orderByRaw('IF(total_payment < total_price, 1, 0) DESC') // Prioritize invoices with unpaid amounts
        ->orderBy('invoices.created_at', 'desc')
        ->paginate(PAGINATE);
    }

    // public function createOrder()
    // {
    //     return view('invoices.createOrder');
    // }

    public function updateOrder(Request $request)
    {
        DB::beginTransaction();
        try{
            $order = Order::where('id' , $request->order_id)->first();
            $request->validate([
                'order_id' => ['required', 'integer'],
                'invoice_id' => ['required', 'integer'],
                'price' => ['required', 'integer' , 'min:1' , 'max:1000000'],
                'payment' => ['required', 'integer' , 'min:1' , 'max:1000000','lte:price', 'gt:' . $order->payment],
            ]);


            $price = $request->payment -$order->payment;

            event(new RegisterOrderPricing($request->order_id , $price));
            $order->update(['payment' => $request->payment]);

            $invoice = Invoice::where('id', $request->invoice_id)->withTrashed()->first();
            // event(new InvoiceProcessed($invoice));
            DB::commit();
            return back()->with('success' , 'تم تحديث الفاتوره بنجاح');
        }catch(\Exception){
            DB::rollback();
            return back();
        }
    }

    // public function destroyOrder(Request $request)
    // {
    //     $invoice = Invoice::where('id', $request->invoice_id)->withTrashed()->first();
    //     DB::table('orders')->where('id' , $request->order_id)->delete();
    //     event(new InvoiceCreated($invoice));
    //     return to_route('invoice.index')->with('success' , 'Order deleted successfully');
    // }
}
