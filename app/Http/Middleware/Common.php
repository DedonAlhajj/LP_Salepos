<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;

class Common
{

    public function handle(Request $request, Closure $next)
    {
        $general_setting =  Cache::remember('general_setting', 60*60*24*365, function () {
            return DB::table('general_settings')->latest()->first();
        });

        if(isset($_COOKIE['language'])) {
            App::setLocale($_COOKIE['language']);
        }
        else {
            App::setLocale('en');
        }
        //setting theme
        if(isset($_COOKIE['theme'])) {
            View::share('theme', $_COOKIE['theme']);
        }
        else {
            View::share('theme', 'light');
        }

        $currency = Cache::remember('currency', 60*60*24*365, function () {
            $settingData = DB::table('general_settings')->select('currency')->latest()->first();
            return \App\Models\Currency::find($settingData->currency);
        });



        View::share('general_setting', $general_setting);
        View::share('currency', $currency);
        config(['staff_access' => $general_setting->staff_access, 'is_packing_slip' => $general_setting->is_packing_slip, 'date_format' => $general_setting->date_format, 'currency' => $currency->code, 'currency_position' => $general_setting->currency_position, 'decimal' => $general_setting->decimal, 'is_zatca' => $general_setting->is_zatca, 'company_name' => $general_setting->company_name, 'vat_registration_number' => $general_setting->vat_registration_number, 'without_stock' => $general_setting->without_stock, 'addons' => $general_setting->modules]);



        return $next($request);
    }


}
