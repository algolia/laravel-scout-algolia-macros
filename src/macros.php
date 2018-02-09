<?php

use Laravel\Scout\Builder;
use Illuminate\Database\Eloquent\Collection;


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

if (! Builder::hasMacro('withCount')) {
    /**
     * Return the total amount of results for the current query.
     *
     * @return int Number of results
     */
    Builder::macro('withCount', function () {
        $this->model->withCount(['people']);

        return $this;
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

if (! Builder::hasMacro('hydrate')) {
    /**
     * get() hydrates records by looking up the Ids in the corresponding database
     * This macro uses the data returned from the search results to hydrate
     *  the models and return a collection
     *
     * @return Collection
     */
    Builder::macro('hydrate', function () {
        $results = $this->engine()->search($this);

        if (count($results['hits']) === 0) {
            return Collection::make();
        }

        $hits = collect($results['hits']);
        $className = get_class($this->model);
        $models = new Collection();

        /* If the model is fully guarded, we unguard it.
        Fully garded is the default configuration and it will
        only result in error.
        If the `$guarded` attribute is set to a list of attribute
        we take it into account. */
        if (in_array('*', $this->model->getGuarded())) {
            Eloquent::unguard();
        }

        $hits->each(function($item, $key) use ($className, $models) {
            $models->push(new $className($item));
        });

        Eloquent::reguard();

        return $models;
    });
}
