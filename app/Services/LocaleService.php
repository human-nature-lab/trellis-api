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
        $locale->language_name = $request->input('language_name');
        $locale->language_native = $request->input('language_native');
        $locale->language_tag = $request->input('language_tag');

        $locale->save();

        return $locale;
    }

    public static function updateLocale(Request $request, $id)
    {
        $locale = Locale::find($id);

        $locale->language_name = $request->input('language_name');
        $locale->language_native = $request->input('language_native');
        $locale->language_tag = $request->input('language_tag');
        $locale->save();

        return $locale;
    }

    public static function deleteLocale($id)
    {
        $locale = Locale::destroy($id);

        return;
    }
}
