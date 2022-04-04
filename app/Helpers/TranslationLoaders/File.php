<?php

namespace App\Helpers\TranslationLoaders;

use Spatie\TranslationLoader\Exceptions\InvalidConfiguration;
use Spatie\TranslationLoader\LanguageLine;

class File implements \Spatie\TranslationLoader\TranslationLoaders\TranslationLoader
{
    public function loadTranslations(string $locale, string $group): array
    {
        $model = $this->getConfiguredModelClass();

        $trans = $model::getTranslationsForGroup($locale, $group);
        dump($trans);
        return $trans;
    }

    protected function getConfiguredModelClass(): string
    {
        $modelClass = config('translation-loader.model');

        if (! is_a(new $modelClass, LanguageLine::class)) {
            throw InvalidConfiguration::invalidModel($modelClass);
        }

        return $modelClass;
    }
}
