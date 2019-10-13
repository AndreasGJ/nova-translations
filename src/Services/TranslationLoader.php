<?php

namespace Agj\NovaTranslations\Services;

use Illuminate\Translation\FileLoader;
use Agj\NovaTranslations\Models\Translation;
use Cache;

class TranslationLoader extends FileLoader
{
    /**
     * Load the messages for the given locale.
     *
     * @param string $locale
     * @param string $group
     * @param string $namespace
     *
     * @return array
     */
    public function load($locale, $group, $namespace = null)
    {
        if ($namespace !== null && $namespace !== '*') {
            return $this->loadNamespaced($locale, $group, $namespace);
        }
        $old_texts = parent::load($locale, $group, $namespace);
        $texts = Cache::remember("locale.translations.{$locale}.{$group}", 60,
            function () use ($group, $locale) {
                return Translation::getGroup($group, $locale);
            });

        foreach($old_texts as $k => $v){
            if(empty($texts[$k])){
                $texts[$k] = $v;
            }
        }

        return $texts;
    }

    /**
     * Load the messages for the given locale with no cache
     * and with revision <- new and old
     *
     * @param string $locale
     * @param string $group
     *
     * @return array
     */
    public function loadNoCache($locale, $group)
    {
        $old_texts = parent::load($locale, $group, null);
        $texts = Translation::getGroup($group, $locale);
        $tree = [];

        foreach($old_texts as $k => $v){
            // if(!is_string($v)){
            //     continue;
            // }

            $tree = $this->extractTexts($v, $k, $texts, $tree);
        }

        return $tree;
    }


    private function extractTexts($value, $key, $texts, $tree)
    {
        if(is_object($value) || is_array($value)){
            foreach($value as $k => $v){
                $tree = $this->extractTexts($v, $key.'.'.$k, $texts, $tree);
            }
        } elseif(is_string($value) || is_numeric($value)){
            $tree[$key] = [
                'old' => $value,
                'new' => !empty($texts[$key]) ? $texts[$key] : null
            ];
        }

        return $tree;
    }

    /**
     * Load translation from files
     */
    public function loadFromTransFile($locale, $group)
    {
        $old_texts = parent::load($locale, $group);

        return $old_texts;
    }
}
