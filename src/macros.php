<?php

use Laravel\Scout\Builder;


if (! Builder::hasMacro('count')) {
    /**
     * Return the number of results without building the final Collection
     * No database calls are made
     *
     * @return int Number of results
     */
    Builder::macro('count', function () {
        $raw = $this->engine()->search($this);

        return (int) $raw['nbHits'];
    });
}

if (! Builder::hasMacro('around')) {
    /**
     * Add geolocation paramters to the search query
     *
     * @see https://www.algolia.com/doc/guides/geo-search/geo-search-overview/
     *
     * @param float $lat    Latitude of the center
     * @param float $lng    Longitude of the center
     * @param int   $radius Radius of the search (in meters)
     *
     * @return Laravel\Scout\Builder
     */
    Builder::macro('around', function ($lat, $lng, $radius) {
        $location = [
            'aroundLatLng' => $lat.','.$lng,
            'aroundRadius' => $radius
        ];
        $callback = $this->callback;

        $this->callback = function ($algolia, $query, $options) use ($location, $callback) {
            $options = array_merge($options, $location);

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
