<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Mocking;
use App\Service;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    public function creatMocking()
    {
        $this->instance(Service::class, Mockery::mock(Service::class, function ($mock) {
        $mock->shouldReceive('getContact')->once();
        }));
    }
}
