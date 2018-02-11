<?php

namespace Algolia\ScoutMacros;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;


class ServiceProvider extends LaravelServiceProvider
{
    public function boot()
    {
        // AlgoliaUserAgent::addSuffixUserAgentSegment('; Laravel Scout macros package', '0.x');

        if (!class_exists('AlgoliaSearch\Client')) {
            throw new Exception("It seems like you are not using Algolia. This package requires the Algolia engine.", 1);
        }

        require_once __DIR__.'/macros.php';
    }
}
