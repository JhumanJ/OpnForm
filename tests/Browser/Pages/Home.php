<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;

class Home extends Page
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/home';
    }

    /**
     * Assert that the browser is on the page.
     *
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->waitForLocation($this->url())->assertPathIs($this->url());
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [];
    }

    /**
     * Click on the log out link.
     *
     * @param  \Laravel\Dusk\Browser  $browser
     * @return void
     */
    public function clickLogout($browser)
    {
        $browser->click('@nav-dropdown-button')
            ->waitFor('@nav-dropdown')
            ->waitForText('Logout')
            ->clickLink('Logout')
            ->pause(100);
    }
}
