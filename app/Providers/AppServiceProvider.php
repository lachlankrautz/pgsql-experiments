<?php

namespace App\Providers;

use Faker\Generator as Faker;
use Illuminate\Support\ServiceProvider;
use PleaProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app
            ->resolving(Faker::class, function (Faker $faker) {
                $faker->addProvider(new PleaProvider($faker));
            });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
    }
}
