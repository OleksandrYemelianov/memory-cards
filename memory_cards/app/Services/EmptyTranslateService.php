<?php

namespace App\Services;

use App\Services\Contracts\TranslatorInterface;

class EmptyTranslateService implements TranslatorInterface
{
    public function translate(string $text, string $from, string $to): string
    {
        return $text;
    }

    /**
     * @inheritDoc
     */
    public function getAccessLangs(): array
    {
        return [
            [
                'loc' => 'BG',
                'name' => 'Bulgarian',
            ],
            [
                'loc' => 'CS',
                'name' => 'Czech',
            ],
            [
                'loc' => 'DA',
                'name' => 'Danish',
            ],
            [
                'loc' => 'DE',
                'name' => 'German',
            ],
            [
                'loc' => 'EL',
                'name' => 'Greek',
            ],
            [
                'loc' => 'EN',
                'name' => 'English'
            ],
            [
                'loc' => 'ES',
                'name' => 'Spanish'
            ],
            [
                'loc' => 'ET',
                'name' => 'Estonian'
            ],
            [
                'loc' => 'FI',
                'name' => 'Finnish'
            ],
            [
                'loc' => 'FR',
                'name' => 'French'
            ],
            [
                'loc' => 'HU',
                'name' => 'Hungarian'
            ],
            [
                'loc' => 'ID',
                'name' => 'Indonesian'
            ],
            [
                'loc' => 'IT',
                'name' => 'Italian'
            ],
            [
                'loc' => 'JA',
                'name' => 'Japanese'
            ],
            [
                'loc' => 'KO',
                'name' => 'Korean'
            ],
            [
                'loc' => 'LT',
                'name' => 'Lithuanian'
            ],
            [
                'loc' => 'LV',
                'name' => 'Latvian'
            ],
            [
                'loc' => 'NB',
                'name' => 'Norwegian'
            ],
            [
                'loc' => 'NL',
                'name' => 'Dutch'
            ],
            [
                'loc' => 'PL',
                'name' => 'Polish'
            ],
            [
                'loc' => 'PT',
                'name' => 'Portuguese'
            ],
            [
                'loc' => 'RO',
                'name' => 'Romanian'
            ],
            [
                'loc' => 'RU',
                'name' => 'Russian'
            ],
            [
                'loc' => 'SK',
                'name' => 'Slovak'
            ],
            [
                'loc' => 'SL',
                'name' => 'Slovenian'
            ],
            [
                'loc' => 'SV',
                'name' => 'Swedish'
            ],
            [
                'loc' => 'TR',
                'name' => 'Turkish'
            ],
            [
                'loc' => 'UK',
                'name' => 'Ukrainian'
            ],
            [
                'loc' => 'ZH',
                'name' => 'Chinese'
            ]
        ];
    }

    public function checkAccessTranslate(): bool
    {
        return false;
    }
}
