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
 *
 * Note on make(''): an empty argument is not a direct alias for the empty
 * translator — it falls back to config('translate.service'). The empty-string
 * map entry is only reached when the configured service itself is empty.
 */
class TranslatorFactoryTest extends TestCase
{
    public function test_make_returns_deepl_translator_for_deepl_service(): void
    {
        $this->bindDeeplMock();

        $factory = new TranslatorFactory();

        $this->assertInstanceOf(
            DeeplTranslateService::class,
            $factory->make('deepl')
        );
    }

    public function test_make_returns_empty_translator_when_configured_service_is_empty(): void
    {
        config(['translate.service' => '']);

        $factory = new TranslatorFactory();

        $this->assertInstanceOf(
            EmptyTranslateService::class,
            $factory->make()
        );
    }

    public function test_make_uses_configured_service_when_no_argument_given(): void
    {
        config(['translate.service' => 'deepl']);
        $this->bindDeeplMock();

        $factory = new TranslatorFactory();

        $this->assertInstanceOf(
            DeeplTranslateService::class,
            $factory->make()
        );
    }

    public function test_make_throws_for_unsupported_service(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new TranslatorFactory())->make('google');
    }

    private function bindDeeplMock(): void
    {
        $this->app->bind(
            DeeplTranslateService::class,
            fn () => Mockery::mock(DeeplTranslateService::class)
        );
    }
}
