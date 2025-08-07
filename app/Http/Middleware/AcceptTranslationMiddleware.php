<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AcceptTranslationMiddleware
{
    public function handle(Request $request, Closure $next) {
        $locale = $request->header('Accept-Language', 'en');
        if (!in_array($locale, ['en', 'ar'])) {
            $locale = 'en';
        }
        app()->setLocale($locale);
        $response = $next($request);
        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $data = $response->getData(true);
            if (isset($data['data'])) {
                $data['data'] = array_map(function ($item) use ($locale) {
                    if (is_array($item)) {
                        foreach ($item as $key => $value) {
                            if (is_array($value)) {
                                $item[$key] = $value[$locale] ?? reset($value);
                            }
                        }
                    }
                    return $item;
                }, $data['data']);
            }
            $response->setData($data);
        }
        return $response;
    }
}
