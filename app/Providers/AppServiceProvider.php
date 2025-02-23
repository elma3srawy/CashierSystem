<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\Section;
use App\Observers\OrderObserver;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\JsonResource;

define('PAGINATE' , 15);
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();

        Paginator::useBootstrap();
        View::share('sections',
            Section::whereNull('section_id')
            // ->whereDoesntHave("products")
            ->get()
        );

        View::composer('layouts.content', function ($view) {
            $sections = Section::whereNull('section_id')
                ->whereDoesntHave("products")
                ->get();
            $view->with('parent_sections', $sections);
        });
        // Order::observe(OrderObserver::class);
    }
}

