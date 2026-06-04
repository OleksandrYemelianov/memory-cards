<?php

namespace Tests\Unit\Services;

use App\Services\DeeplTranslateService;
use App\Services\EmptyTranslateService;
use App\Services\TranslatorFactory;
use Mockery;
use Tests\TestCase;

/**
 * TranslatorFactory::make() uses config() and the container (app()),
 * so this test boots the framework — it extends Tests\TestCase rather than
 * the plain PHPUnit one. The DeepL branch binds a mock so we test the
 * factory's routing logic without constructing a real DeepL\Translator.
 */
class TranslatorFactoryTest extends TestCase
{
    public function test_make_returns_empty_translator_for_empty_service(): void
    {
        $factory = new TranslatorFactory();

        $this->assertInstanceOf(
            EmptyTranslateService::class,
            $factory->make('')
        );
    }

    public function test_make_returns_deepl_translator_for_deepl_service(): void
    {
        $this->app->bind(
            DeeplTranslateService::class,
            fn () => Mockery::mock(DeeplTranslateService::class)
        );

        $factory = new TranslatorFactory();

        $this->assertInstanceOf(
            DeeplTranslateService::class,
            $factory->make('deepl')
        );
    }

    public function test_make_falls_back_to_config_when_no_service_given(): void
    {
        config(['translate.service' => '']);

        $factory = new TranslatorFactory();

        $this->assertInstanceOf(
            EmptyTranslateService::class,
            $factory->make()
        );
    }

    public function test_make_throws_for_unsupported_service(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new TranslatorFactory())->make('google');
    }
}
