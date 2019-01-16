<?php

abstract class TestCase extends Laravel\Lumen\Testing\TestCase
{
    // use CreatesApplication;
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    public function setUp()
    {
        parent::setUp();
        $this->test_api_url = env('APP_URL');
    }
}
