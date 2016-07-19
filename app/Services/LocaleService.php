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
}