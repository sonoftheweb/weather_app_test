<?php


namespace Tests;

use Illuminate\Foundation\Testing\WithFaker as BaseWithFaker;

trait WithFaker
{
    use BaseWithFaker;

    protected function setUpFaker()
    {
        $this->faker = $this->makeFaker(config('app.faker_locale'));
    }
}
