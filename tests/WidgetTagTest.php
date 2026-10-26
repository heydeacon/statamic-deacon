<?php

namespace HeyDeacon\StatamicDeacon\Tests;

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
}
