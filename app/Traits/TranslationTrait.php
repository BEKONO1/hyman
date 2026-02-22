<?php

namespace App\Traits;

trait TranslationTrait
{

    public function languagesArray()
    {
        $language_option = sitesetupSession('get')->language_option ?? ["ar","nl","en","fr","de","hi","it"];
        $language_array = languagesArray($language_option);
        return $language_array;
    }
    /**
     * Generate an array of languages with name and flag path.
     *
     * @return array
     */
    public function getLanguageArray()
    {
        // Get language options from the session or set default languages
        $language_option = sitesetupSession('get')->language_option ?? ["ar","nl","en","fr","de","hi","it"];
        // Generate language array with title and flag path
        $language_array = [];
        foreach ($language_option as $lang_id) {
            $language_array[] = [
                'id' => $lang_id,
                'title' => strtoupper($lang_id),
                'flag_path' => file_exists(public_path('/images/flags/' . $lang_id . '.png'))
                    ? asset('/images/flags/' . $lang_id . '.png')
                    : asset('/images/language.png')
            ];
        }

        return $language_array;
    }
    public function saveTranslations(array $data, array $attributes, array $language_option, $primary_locale)
    {
        // Ensure translations key exists in the data
        if (!isset($data['translations']) || !is_array($data['translations'])) {
            \Log::info('No translations data provided.');
            return;
        }
        $this->translations()->delete();
        
        foreach ($language_option as $locale) {
            // Skip the primary locale since it's stored in the main table
            if ($locale === "en") {
                continue;
            }

            // Check if translations exist for this locale
            if (!isset($data['translations'][$locale])) {
                \Log::info("No translations for locale", ['locale' => $locale]);
                continue;
            }

            // Loop through attributes and save each translation
            foreach ($attributes as $attribute) {
                $value = $data['translations'][$locale][$attribute] ?? null;

                // If the value exists, save or update the translation
                if ($value !== null && $value !== '') {
                    $this->translations()->updateOrCreate(
                        [
                            'locale' => $locale,
                            'attribute' => $attribute
                        ],
                        [
                            'value' => $value
                        ]
                    );
                }
            }
        }
    }


    function getTranslation($translations, $locale, $attribute = 'name', $fallbackValue = null)
    {
            // Ensure $translations is a collection
        if (!$translations || !is_iterable($translations)) {
            return $fallbackValue;
        }

        // Find the translation for the requested locale and attribute
        $translation = collect($translations)->where('locale', $locale)->where('attribute', $attribute)->first();

        if ($translation) {
            return $translation->value;
        } 

        // Fall back to English if the translation for the desired locale is not available
        $fallbackTranslation = $translations->firstWhere('locale', 'en');
        if ($fallbackTranslation && isset($fallbackTranslation->$attribute)) {
            return $fallbackTranslation->$attribute;
        }

        return $fallbackValue;
    }
}
