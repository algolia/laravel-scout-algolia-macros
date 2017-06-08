<?php

namespace Algolia\ScoutMacros;

use Illuminate\Support\ServiceProvider;


class ScoutBuilderMacrosServiceProvider extends ServiceProvider
{
    public function register()
    {
        require_once __DIR__.'/macros.php';
    }
}
