<?php

namespace App\Http\Controllers\Sales;

use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class SalesController extends Controller
{
    public function index()
    {

        $sales = $this->getSales();
        return view('sales.index', ['sales' => $sales]);
    }
    public function pending()
    {
        $sales = $this->getSales('pending');
        return view('sales.pending', ['sales' => $sales]);
    }

    public function inactive()
    {
        $sales = $this->getSales('inactive');
        return view('sales.inactive', ['sales' => $sales]);
    }


    public function showOrders(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|string',
        ]);

        $ids = explode(',', $request->order_ids);

        $orders = Order::whereIn('id' , $ids)
        ->with(['invoice' => function($query) {
            $query->withTrashed()->select('id', 'client_id');
        }, 'product', 'addition'])
        ->select('id' , 'invoice_id','product_id' , 'addition_id','price' , 'payment')
        ->get();

        return view('sales.show' , ['orders' => $orders]);

    }

    public function archive(string $id)
    {
        DB::table('sales')->where('id' , "=" , $id)->update(['deleted_at' => now()]);
        return back()->with('success' , 'تم ارشفة الحسابات بنجاح');
    }

    private function getSales($status = null , $archive = false)
    {
        $sales = DB::table('sales');

        if($status){
            $sales->where('status' , $status);
        }
        
        return $sales->paginate(PAGINATE);
    }
    public function archiveAll()
    {
        $sales = $this->getSales(archive: true);
        return view('sales.index', ['sales' => $sales]);
    }
    public function archivePending()
    {
        $sales = $this->getSales('pending', true);
        return view('sales.pending', ['sales' => $sales]);
    }
    public function archiveInactive()
    {
        $sales = $this->getSales('inactive' , true);
        return view('sales.inactive', ['sales' => $sales]);
    }
}
