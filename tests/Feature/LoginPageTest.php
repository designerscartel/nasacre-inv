<?php

namespace Tests\Feature;

use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class LoginPageTest extends TestCase
{
    public function test_login_page_uses_the_inertia_authentication_component(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Auth/Login')
                ->has('canResetPassword')
                ->has('status'));
    }
}
