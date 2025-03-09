<?php
namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\RecentInvoices;


trait InvoiceProcessing
{

    public function getRecentInvoices()
    {
        $data =  DB::table('invoices')
        ->leftJoin('clients' , 'invoices.client_id' , 'clients.id')
        ->rightJoin('orders', 'invoices.id' , 'orders.invoice_id')
        ->leftJoin('products' , 'orders.product_id' , 'products.id')
        ->leftJoin('additions' , 'orders.addition_id' , 'additions.id')
        ->select(
            'invoices.status',
            'invoices.date_of_receipt',
            'invoices.return_date',
            'invoices.created_at',
            'clients.name' ,
            'orders.price' ,
            'orders.payment',
            'products.title as section_title',
            'products.size',
            'products.model',
            'products.color',
            'additions.title',
            'additions.data',
        )
        ->orderBy('created_at' , 'desc')
        ->paginate(PAGINATE);

        $invoices = RecentInvoices::collection($data);
        return view('invoices.recent-invoice' , ['invoices' => $invoices]);
    }

    public function searchRecentInvoices(Request $request)
    {
        // var_dump($request);

        $data =  DB::table('invoices')
        ->leftJoin('clients' , 'invoices.client_id' , 'clients.id')
        ->rightJoin('orders', 'invoices.id' , 'orders.invoice_id')
        ->leftJoin('products' , 'orders.product_id' , 'products.id')
        ->leftJoin('additions' , 'orders.addition_id' , 'additions.id')
        ->select(
            'invoices.status',
            'invoices.date_of_receipt',
            'invoices.return_date',
            'invoices.created_at',
            'clients.name' ,
            'orders.price' ,
            'orders.payment',
            'products.title as section_title',
            'products.size',
            'products.model',
            'products.color',
            'additions.title',
            'additions.data',
        )
        // ->where('clients.name' , 'LIKE' , '%{$request->search}%')
        ->where('clients.name', 'LIKE', '%' . $request->search . '%')
        ->orWhere('invoices.status', 'LIKE', '%' . $request->search . '%')
        ->orWhere('invoices.date_of_receipt', 'LIKE', '%' . $request->search . '%')
        ->orWhere('invoices.return_date', 'LIKE', '%' . $request->search . '%')
        ->orWhere('invoices.created_at', 'LIKE', '%' . $request->search . '%')
        ->orWhere('products.title', 'LIKE', '%' . $request->search . '%')
        ->orWhere('products.size', 'LIKE', '%' . $request->search . '%')
        ->orWhere('products.model', 'LIKE', '%' . $request->search . '%')
        ->orWhere('products.color', 'LIKE', '%' . $request->search . '%')
        ->orWhere('additions.title', 'LIKE', '%' . $request->search . '%')
        ->orWhere('additions.data', 'LIKE', '%' . $request->search . '%')
        ->orderBy('created_at' , 'desc')
        ->paginate(PAGINATE);

        $invoices = RecentInvoices::collection($data);
        // return ;
        return view('partials.recent-invoice' , ['invoices' => $invoices]);
    }


    public static function getInvoice($id)
    {
        return DB::table('invoices')
        ->where('invoices.id', $id)
        ->leftJoin('clients', 'invoices.client_id', '=', 'clients.id')
        ->rightJoin('orders', 'invoices.id', '=', 'orders.invoice_id')
        ->leftJoin('products', 'orders.product_id', '=', 'products.id')
        ->leftJoin('additions', 'orders.addition_id', '=', 'additions.id')
        ->leftJoin('sections', 'products.section_id', '=', 'sections.id')
        ->select(
            'invoices.id',
            'invoices.status',
            'invoices.date_of_receipt',
            'invoices.return_date',
            'invoices.created_at',
            'clients.name',
            'clients.phone',
            'clients.address',
    
            DB::raw('
                COALESCE(
                    JSON_ARRAYAGG(
                        CASE 
                            WHEN products.id IS NOT NULL THEN JSON_OBJECT(
                                "product_id", products.id,
                                "section_id", products.section_id,
                                "parent_id", sections.section_id,
                                "price", orders.price,
                                "payment", orders.payment
                            )
                        END
                    ), "[]"
                ) as products
            '),
    
            DB::raw('
                COALESCE(
                    JSON_ARRAYAGG(
                        CASE 
                            WHEN additions.id IS NOT NULL THEN JSON_OBJECT(
                                "title", additions.title,
                                "data", additions.data,
                                "price", orders.price,
                                "payment", orders.payment
                            )
                        END
                    ), "[]"
                ) as additions
            ')
        )
        ->groupBy('invoices.id')
        ->first();
    
    

    }
}
