# Laravel Scout Algolia Macros

A collection of useful macros to extend Laravel Scout capabilities when using the [Algolia engine](https://laravel.com/docs/5.4/scout#driver-prerequisites).

This package aims to provide a set of macros to take advantage of the
Algolia-specific feature.


## Installation

Pull the package using composer

```
composer install algolia/laravel-scout-algolia-macros
```

Next, you should add the `ScoutBuilderMacrosServiceProvider` to the `providers`
array of your `config/app.php` configuration file:

```php
Algolia\ScoutMacros\ScoutBuilderMacrosServiceProvider::class
```


## Usage

### `count`

The count method will return the number of results after the request to Algolia.

The point is to avoid pull data from the database and building the collection.

```php
$nbHits = Model::search('query')->count();
```

## `aroundLatLng`

The`aroundLatLng` method will add [geolocation parameter](1) to the search request. You
can define a point with its coordinate.

```php
// Models around Paris latitude and longitude
Model::search('query')->aroundLatLng(48.8588536, 2.3125377)->get();
```

Where clauses can also be added

```php
Model::search('query')
    ->aroundLatLng(48.8588536, 2.3125377)
    ->where('something_id', 1)
    ->get();
```


## Contributing

Feel free to open an issue to request a macro.

Open any pull request you want, so we can talk about it and improve the package. :tada:

[1]: https://www.algolia.com/doc/guides/geo-search/geo-search-overview/
