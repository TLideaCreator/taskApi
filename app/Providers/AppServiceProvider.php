<?php

namespace App\Providers;

use App\Format\ProjectFormat;
use App\Format\UserFormat;
use App\Modals\Project;
use App\Models\User;
use Dingo\Api\Transformer\Factory;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        app(Factory::class)->register(User::class, UserFormat::class);
        app(Factory::class)->register(Project::class, ProjectFormat::class);
    }
}
