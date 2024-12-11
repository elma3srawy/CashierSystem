<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderPricing extends Model
{
    use HasFactory , SoftDeletes;

    protected $table = 'order_pricing';

    protected $fillable = ['order_id' , 'price'];

    public $timestamps = false;


    protected static function boot()
    {
        parent::boot();

        // Set changed_at only during record creation
        static::creating(function ($model) {
            $model->changed_at = now();
        });
    }


    public function scopeGetWeeklyStats($query)
    {
        return $query->select(
                DB::raw('GROUP_CONCAT(order_pricing.id) as ids'),
                DB::raw('GROUP_CONCAT(DISTINCT order_pricing.order_id) as order_ids'),
                DB::raw('invoices.status as status'),
                DB::raw('order_pricing.deleted_at'),
                DB::raw('SUM(order_pricing.price) as total_price'),
                DB::raw("DATE_FORMAT(DATE_SUB(changed_at, INTERVAL ((WEEKDAY(changed_at) + 2) % 7) DAY), '%Y-%m-%d') as week_start_date"),
                DB::raw("DATE_FORMAT(DATE_ADD(DATE_SUB(changed_at, INTERVAL ((WEEKDAY(changed_at) + 2) % 7) DAY), INTERVAL 6 DAY), '%Y-%m-%d') as week_end_date")
            )
            ->leftJoin('orders' , 'order_pricing.order_id' , '=' , 'orders.id')
            ->leftJoin('invoices' , 'orders.invoice_id' , '=' , 'invoices.id')
            ->groupBy(
                DB::raw('invoices.status'),
                DB::raw("DATE_FORMAT(DATE_SUB(changed_at, INTERVAL ((WEEKDAY(changed_at) + 2) % 7) DAY), '%Y-%m-%d')"),
                DB::raw("DATE_FORMAT(DATE_ADD(DATE_SUB(changed_at, INTERVAL ((WEEKDAY(changed_at) + 2) % 7) DAY), INTERVAL 6 DAY), '%Y-%m-%d')")
            )
            ->having('total_price' , '>' , 0)
            ->orderBy(DB::raw("DATE_FORMAT(DATE_SUB(changed_at, INTERVAL ((WEEKDAY(changed_at) + 2) % 7) DAY), '%Y-%m-%d')"), 'desc');
    }
    public static function GetWeeklyWhere($start_date , $end_date , $status)
    {
        return self::
         leftJoin('orders', 'order_pricing.order_id', '=', 'orders.id')
        ->leftJoin('products', 'orders.product_id', '=', 'products.id')
        ->leftJoin('additions', 'orders.addition_id', '=', 'additions.id')
        ->leftJoin('invoices', 'orders.invoice_id', '=', 'invoices.id')
        ->where(DB::raw("DATE_FORMAT(order_pricing.changed_at, '%Y-%m-%d')"), '>=', $start_date)
        ->where(DB::raw("DATE_FORMAT(order_pricing.changed_at, '%Y-%m-%d')"), '<=', $end_date)
        ->where('invoices.status', '=', $status)
        ->where('order_pricing.price' , '>' , 0)
        ->select('order_pricing.price', 'order_pricing.changed_at',  'orders.id' , 'products.id AS product_id' , 'invoices.client_id', 'additions.id AS addition_id');
    }
}
