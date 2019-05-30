<?php

namespace Agj\NovaTranslations\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Translation\FileLoader;
use Agj\NovaTranslations\Services\TranslationLoader;
use Illuminate\Support\Facades\File;
use Agj\NovaTranslations\Models\Translation;

class TranslatorController extends Controller
{
    public function index()
    {
        $default_locale = config('app.locale', 'en');
        $locales = config('app.locales', []);
        $path = resource_path('lang/');

        $files = File::allFiles($path);
        $tree = [];
        $groups = [];

        if ($files && count($files) > 0) {
            foreach ($files as $file) {
                $path = $file->getRelativePathname();
                $arr = explode('/', $path);

                if (count($arr) == 2) {
                    $locale = $arr[0];

                    if(!in_array($locale, $locales)){
                        $locales[] = $locale;
                    }
                    $group = str_replace('.php', '', $arr[1]);

                    if (!in_array($group, $groups)) {
                        $groups[] = $group;
                    }
                }
            }
        }
        $loader = app('translation.loader');
        if (count($locales) > 0) {
            $i = array_search($default_locale, $locales);
            if($i > 0){
                array_splice($locales, $i, 1);
                array_unshift($locales, $default_locale);
            }

            // Loop through all the locales
            foreach ($locales as $locale) {
                // Loop through all the groups
                $arr = [];
                foreach ($groups as $group) {
                    $arr[$group] = $loader->loadNoCache($locale, $group);
                }
                $tree[$locale] = $arr;
            }
        }

        return response()->json([
            'data' => [
                'locales' => $locales,
                'groups' => $groups,
                'tree' => $tree,
            ]
        ]);
    }

    public function update_translation(Request $request)
    {
        $data = $request->validate([
            'locale' => 'required|string',
            'key' => 'required|string',
            'text' => 'nullable|string'
        ]);

        $trans = Translation::where('key', $data['key'])->first();

        if (!$trans) {
            $trans = new Translation;
            $trans->key = $data['key'];
        }
        if(!empty($data['text'])){
            $trans->setTranslation('text', $data['locale'], $data['text']);
        } else {
            $trans->forgetTranslation('text', $data['locale']);
        }
        if($trans->save()){
            return response()->json([
                'status' => true
            ]);
        }
        return response()->json([
            'error' => 'Wtf'
        ]);
    }
}
