<?php

namespace TakeawayTown\LaravelUuid\Providers;

use Illuminate\Support\ServiceProvider;

class UuidServiceProvider extends ServiceProvider
{
  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  public function boot()
  {
    if (function_exists('config_path')) {
      $this->publishes([
        realpath(__DIR__.'/../../config/uuid.php') => config_path('uuid.php'),
      ]);
    }
  }
}
