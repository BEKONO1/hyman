<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
class LanguageTranslator
{
   public function handle(Request $request, Closure $next)
    {
        if ($request->hasHeader('language-code')) {
            $locale = $request->header('language-code');
            if (in_array($locale, config('app.available_locales', ['en', 'ar','de','es','fr','hi','it','nl']))) {
                \App::setLocale($locale);
                session()->put('locale', $locale); 
            }
        } elseif (session()->has('locale')) {
            \App::setLocale(session()->get('locale'));
        }

        return $next($request);
    }
}
