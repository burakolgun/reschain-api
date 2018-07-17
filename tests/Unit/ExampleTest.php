<?php

namespace Tests\Unit;

use App\User;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $this->assertTrue(true);
    }

    public function testRegister()
    {
        $user = User::create(['name' => 'testUser', 'email' => 'test' . str_random(5) . '@email.com', 'password' => \Hash::make(123123), 'is_verified' => true]);
        self::assertInstanceOf(User::class, $user );
    }
}
