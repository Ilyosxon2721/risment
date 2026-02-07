<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Force testing env (.env overrides phpunit.xml APP_ENV on this system)
        $this->app['env'] = 'testing';
    }
}
