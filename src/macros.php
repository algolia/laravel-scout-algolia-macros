<?php

use Laravel\Scout\Builder;


if (! Builder::hasMacro('count')) {
    /**
     * Return the total amount of results for the current query.
     *
     * @return int Number of results
     */
    Builder::macro('count', function () {
        $raw = $this->engine()->search($this);

        return (int) $raw['nbHits'];
    });
}

if (! Builder::hasMacro('aroundLatLng')) {
    /**
     * Search for entries around a given location.
     *
     * @see https://www.algolia.com/doc/guides/geo-search/geo-search-overview/
     *
     * @param float $lat Latitude of the center
     * @param float $lng Longitude of the center
     *
     * @return Laravel\Scout\Builder
     */
    Builder::macro('aroundLatLng', function ($lat, $lng) {
        $callback = $this->callback;

        $this->callback = function ($algolia, $query, $options) use ($lat, $lng, $callback) {
            $options['aroundLatLng'] = (float) $lat . ',' . (float) $lng;

            if ($callback) {
                return call_user_func(
                    $callback,
                    $algolia,
                    $query,
                    $options
                );
            }

            return $algolia->search($query, $options);
        };

        return $this;
    });
}

if (! Builder::hasMacro('with')) {
    /**
     * Override the algolia search options to give you full control over the request,
     * similar to official algolia-laravel package.
     *
     * Adds the final missing piece to scout to make the library useful.
     *
     * @param array $opts Latitude of the center
     *
     * @return Laravel\Scout\Builder
     */
    Builder::macro('with', function ($opts) {
        $callback = $this->callback;

        $this->callback = function ($algolia, $query, $options) use ($opts, $callback) {
            $options = array_merge($options, $opts);

            if ($callback) {
                return call_user_func(
                    $callback,
                    $algolia,
                    $query,
                    $options
                );
            }

            return $algolia->search($query, $options);
        };

        return $this;
    });
}
