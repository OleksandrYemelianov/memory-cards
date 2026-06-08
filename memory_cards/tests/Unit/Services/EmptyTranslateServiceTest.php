<?php

namespace Tests\Unit\Services;

use App\Services\EmptyTranslateService;
use PHPUnit\Framework\TestCase;

/**
 * Pure unit test: EmptyTranslateService has no dependencies, so it can be
 * instantiated directly without booting the framework.
 *
 * It is the fallback used when no translation API key is configured, so it
 * reports translation access as unavailable and echoes text back unchanged.
 */
class EmptyTranslateServiceTest extends TestCase
{
    private EmptyTranslateService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new EmptyTranslateService();
    }

    public function test_translate_returns_text_unchanged(): void
    {
        $this->assertSame('hello', $this->service->translate('hello', 'EN', 'DE'));
    }

    public function test_access_is_denied_without_api_key(): void
    {
        $this->assertFalse($this->service->checkAccessTranslate());
    }

    public function test_access_langs_have_expected_shape(): void
    {
        $langs = $this->service->getAccessLangs();

        $this->assertNotEmpty($langs);
        $this->assertArrayHasKey('loc', $langs[0]);
        $this->assertArrayHasKey('name', $langs[0]);
    }
}
