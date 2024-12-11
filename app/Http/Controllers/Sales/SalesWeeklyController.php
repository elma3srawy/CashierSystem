<?php

namespace App\Http\Controllers\Sales;

use App\Models\OrderPricing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class SalesWeeklyController extends Controller
{
    public function index()
    {
        $sales = OrderPricing::GetWeeklyStats()->paginate(PAGINATE);
        return view('salesWeekly.index' , ['sales' => $sales]);
    }
    public function pending()
    {
        $sales = OrderPricing::GetWeeklyStats()->having('invoices.status' , 'pending')->paginate(PAGINATE);
        return view('salesWeekly.pending' , ['sales' => $sales]);
    }
    public function inactive()
    {
        $sales = OrderPricing::GetWeeklyStats()->having('invoices.status' , 'inactive')->paginate(PAGINATE);
        return view('salesWeekly.inactive' , ['sales' => $sales]);
    }

    public function archive(string $ids)
    {
        DB::table('order_pricing')
        ->whereIn('id', explode(',', $ids))
        ->update(['deleted_at' => now()]);
        return back();
    }
    public function archiveAll()
    {
        $sales = OrderPricing::onlyTrashed()->GetWeeklyStats()->paginate(PAGINATE);
        return view('salesWeekly.index' , ['sales' => $sales]);
    }
    public function archivePending()
    {
        $sales = OrderPricing::onlyTrashed()->GetWeeklyStats()->having('invoices.status' , 'pending')->paginate(PAGINATE);
        return view('salesWeekly.pending' , ['sales' => $sales]);
    }
    public function archiveInactive()
    {
        $sales = OrderPricing::onlyTrashed()->GetWeeklyStats()->having('invoices.status' , 'inactive')->paginate(PAGINATE);
        return view('salesWeekly.inactive' , ['sales' => $sales]);
    }


    public function showOrders($start_week , $end_week , $status)
    {

        $orders = OrderPricing::GetWeeklyWhere($start_week , $end_week , $status);

        if(request('archive') == "true"){
            $orders->onlyTrashed();
        }
        $orders = $orders->paginate(PAGINATE);

        return view('salesWeekly.show' , ['orders' => $orders]);
    }
}
