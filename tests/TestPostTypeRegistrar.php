<?php
use PHPUnit\Framework\TestCase;
use ArtPulse\Core\PostTypeRegistrar;

class TestPostTypeRegistrar extends TestCase
{
    public function testRegisterMethodExists()
    {
        \$this->assertTrue(
            method_exists(PostTypeRegistrar::class, 'register'),
            'PostTypeRegistrar::register method should exist'
        );
    }
}
