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
