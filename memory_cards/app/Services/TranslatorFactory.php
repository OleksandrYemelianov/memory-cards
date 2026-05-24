<?php

namespace App\Services;

use App\Services\Contracts\TranslatorInterface;

class TranslatorFactory
{
    protected array $translators;

    public function __construct()
    {
        $this->translators = [
            'deepl' => DeeplTranslateService::class,
            '' => EmptyTranslateService::class
        ];
    }

    public function make(string $service = ''): TranslatorInterface
    {
        if (empty($service)) {
            $service = config('translate.service');
        }

        if (!isset($this->translators[$service])) {
            throw new \InvalidArgumentException("The $service is not supported.");
        }

        return app($this->translators[$service]);
    }
}
