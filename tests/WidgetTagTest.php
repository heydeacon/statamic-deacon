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
    public function it_prints_the_same_tag_from_a_blade_layout(): void
    {
        config(['deacon.key' => 'pk_test123']);

        $this->assertSame(
            '<script src="https://heydeacon.com/widget/v1/loader.js" data-key="pk_test123" async></script>',
            $this->renderedTag('/blade'),
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
        .'Set DEACON_KEY in .env, or the key in config/deacon.php. In Deacon, open Install (the button at '
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

    #[Test]
    public function it_passes_the_context_to_deacon_as_the_data_context_attribute(): void
    {
        config([
            'deacon.key' => 'pk_test123',
            'deacon.context' => ['site' => 'docs', 'version' => '3.2', 'beta' => true],
        ]);

        $this->assertSame(
            '<script src="https://heydeacon.com/widget/v1/loader.js" data-key="pk_test123" '
            .'data-context="{&quot;site&quot;:&quot;docs&quot;,&quot;version&quot;:&quot;3.2&quot;,'
            .'&quot;beta&quot;:true}" async></script>',
            $this->renderedTag(),
        );
    }

    #[Test]
    public function it_keeps_a_context_value_that_looks_like_html_as_it_was_written(): void
    {
        config([
            'deacon.key' => 'pk_test123',
            'deacon.context' => ['note' => '</script><b>"hi" &amp;</b>'],
        ]);

        $document = new \DOMDocument;
        $document->loadHTML($this->renderedTag(), LIBXML_NOERROR);
        $script = $document->getElementsByTagName('script')->item(0);

        $this->assertSame('{"note":"</script><b>\"hi\" &amp;</b>"}', $script?->getAttribute('data-context'));
    }

    #[Test]
    public function it_holds_back_page_views_with_the_data_no_page_views_attribute(): void
    {
        config(['deacon.key' => 'pk_test123', 'deacon.page_views' => false]);

        $this->assertSame(
            '<script src="https://heydeacon.com/widget/v1/loader.js" data-key="pk_test123" '
            .'data-no-page-views async></script>',
            $this->renderedTag(),
        );
    }

    #[Test]
    public function it_carries_the_nonce_vite_use_csp_nonce_gives_the_request(): void
    {
        config(['deacon.key' => 'pk_test123']);

        $this->assertSame(
            '<script src="https://heydeacon.com/widget/v1/loader.js" data-key="pk_test123" async '
            .'nonce="n0nce"></script>',
            $this->renderedTag('/antlers-with-vite-nonce'),
        );
    }

    #[Test]
    public function it_carries_the_nonce_spatie_laravel_csp_gives_the_request(): void
    {
        config(['deacon.key' => 'pk_test123']);

        $this->assertSame(
            '<script src="https://heydeacon.com/widget/v1/loader.js" data-key="pk_test123" async '
            .'nonce="sp4tie"></script>',
            $this->renderedTag('/antlers-with-spatie-nonce'),
        );
    }

    #[Test]
    public function it_refuses_a_page_views_that_isnt_true_or_false(): void
    {
        config(['deacon.key' => 'pk_test123', 'deacon.page_views' => 'false']);

        $this->assertSame(
            [\InvalidArgumentException::class, 'statamic-deacon: page_views in config/deacon.php must be true or false.'],
            $this->renderingError(),
        );
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function wrongContexts(): array
    {
        return [
            'a string' => ['plan=pro'],
            'a list' => [['docs']],
            'an array with a nested value' => [['plan' => ['tier' => 'pro']]],
            'an array with a null value' => [['plan' => null]],
        ];
    }

    #[Test]
    #[DataProvider('wrongContexts')]
    public function it_refuses_a_context_whose_values_arent_strings_numbers_or_booleans(mixed $context): void
    {
        config(['deacon.key' => 'pk_test123', 'deacon.context' => $context]);

        $this->assertSame(
            [
                \InvalidArgumentException::class,
                'statamic-deacon: context in config/deacon.php must be an array whose values are strings, '
                .'numbers or booleans.',
            ],
            $this->renderingError(),
        );
    }

    #[Test]
    public function it_trims_spaces_pasted_around_the_key(): void
    {
        config(['deacon.key' => "  pk_test123\n"]);

        $this->assertSame(
            '<script src="https://heydeacon.com/widget/v1/loader.js" data-key="pk_test123" async></script>',
            $this->renderedTag(),
        );
    }
}
