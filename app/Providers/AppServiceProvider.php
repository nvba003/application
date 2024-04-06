<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Menu;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('components.sidebar', function ($view) {
            $menus = Menu::whereNull('parent_id') // Lấy menu cấp cao nhất
                        ->with('children') // Tải luôn menu con
                        ->get();
            $view->with('menus', $menus);
        });
        //---------------------------------
        
    }
}
