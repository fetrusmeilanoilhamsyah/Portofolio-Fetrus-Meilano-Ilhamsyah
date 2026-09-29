<?php

namespace App\Filament\Concerns;

use Spatie\Translatable\HasTranslations;

trait FillsTranslatableAttributes
{
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();

        if ($record && in_array(HasTranslations::class, class_uses_recursive($record))) {
            foreach ($record->getTranslatableAttributes() as $attribute) {
                $translations = $record->getTranslations($attribute);
                foreach ($translations as $locale => $value) {
                    $data[$attribute][$locale] = $value;
                }
            }
        }

        return $data;
    }
}
