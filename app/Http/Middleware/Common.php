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

        View::share('general_setting', $general_setting);

        return $next($request);
    }


}
