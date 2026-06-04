<?php

namespace Tests\Unit\Services;

use App\Services\EmptyTranslateService;
use PHPUnit\Framework\TestCase;

/**
 * Pure unit test: EmptyTranslateService has no dependencies, so it can be
 * instantiated directly without booting the framework.
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

    public function test_access_is_always_granted(): void
    {
        $this->assertTrue($this->service->checkAccessTranslate());
    }

    public function test_access_langs_have_expected_shape(): void
    {
        $langs = $this->service->getAccessLangs();

        $this->assertNotEmpty($langs);
        $this->assertArrayHasKey('loc', $langs[0]);
        $this->assertArrayHasKey('name', $langs[0]);
    }
}
