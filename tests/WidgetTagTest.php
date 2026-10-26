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
}
