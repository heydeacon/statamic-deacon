<?php

namespace HeyDeacon\StatamicDeacon\Tests;

use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class WidgetTagTest extends TestCase
{
    #[Test]
    public function it_prints_the_tag_from_install_with_the_key_from_config(): void
    {
        config(['deacon.key' => 'pk_test123']);

        $this->assertSame(
            '<script src="https://heydeacon.com/widget/v1/loader.js" data-key="pk_test123" async></script>',
            $this->renderedTag(),
        );
    }

    #[Test]
    public function it_takes_the_key_from_deacon_key_when_config_has_none(): void
    {
        $this->bootWithEnvironmentKey('pk_from_env');

        $this->assertSame(
            '<script src="https://heydeacon.com/widget/v1/loader.js" data-key="pk_from_env" async></script>',
            $this->renderedTag(),
        );
    }

    private const MISSING_KEY = 'statamic-deacon: the key is missing, so the page has no Deacon widget. '
        .'Set DEACON_KEY in .env, or key in config/deacon.php. In Deacon, open Install (the button at '
        .'the top of the dashboard) and copy the data-key value from the snippet. It starts with pk_.';

    /**
     * @return array<string, array{?string}>
     */
    public static function missingKeys(): array
    {
        return [
            'unset' => [null],
            'empty, as from an empty DEACON_KEY' => [''],
            'blank' => ['   '],
        ];
    }

    #[Test]
    #[DataProvider('missingKeys')]
    public function it_prints_nothing_and_logs_a_warning_when_the_key_is_missing(?string $key): void
    {
        config(['deacon.key' => $key]);
        Log::spy();

        $this->assertSame('', $this->renderedTag());
        Log::shouldHaveReceived('warning')->once()->with(self::MISSING_KEY);
    }
}
