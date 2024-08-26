<?php

namespace Tests\Browser;

use Tests\DuskTestCase;

class WelcomeTest extends DuskTestCase
{
    /** @test */
    public function basic_test()
    {
        $this->browse(function ($browser) {
            $browser->visit('/')
                ->waitFor('@title', 1)
                ->assertSee('Forms for Notion');
        });
    }
}
