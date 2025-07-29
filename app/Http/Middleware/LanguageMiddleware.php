<?php

namespace App\Http\Middleware;

use App\Constants\Status;
use App\Models\Language;
use Closure;
use Illuminate\Support\Facades\Log;

class LanguageMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //session()->put('lang', $this->getCode());
//        Log::info('Request Hit', [
//            'url' => $request->fullUrl(),
//            'method' => $request->method(),
//            'ip' => $request->ip(),
//            'data' => $request->all(),
//        ]);

        app()->setLocale(request()->cookie('lang', $this->getCode()));
        return $next($request);
    }

    public function getCode()
    {
        if (session()->has('lang')) {
            return session('lang');
        }
        $language = Language::where('is_default', Status::ENABLE)->first();
        return $language ? $language->code : 'en';
    }


}
