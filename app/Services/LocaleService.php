<?php

namespace App\Services;

use App\Models\Locale;

class LocaleService
{
    public static function getAllLocales()
    {
        $locales = Locale::orderBy('language_name')
        ->get();

        return $locales;
    }

    public static function getAllLocalesPaginated($perPage)
    {
        $locales = Locale::orderBy('language_name')
            ->paginate();

        return $locales;
    }

    public static function createNewLocale(Request $request)
    {
        $locale = new Locale;
        $locale->id = Uuid::uuid4();
        $locale->language_tag = $request->input('language_tag');
        $locale->language_name = $request->input('language_name');
        $locale->language_native = bcrypt($request->input('language_native'));

        $locale->save();

        return $locale;
    }
}