**DEPRECATED: Use of this repository is deprecated. Please use Scout Extended - https://github.com/algolia/scout-extended instead.**

# Laravel Scout Algolia Macros

A collection of useful macros to extend Laravel Scout capabilities when using the [Algolia engine](https://laravel.com/docs/5.4/scout#driver-prerequisites).

This package aims to provide a set of macros to take advantage of the
Algolia-specific feature.


## Installation

Pull the package using composer

```
composer require algolia/laravel-scout-algolia-macros
```

Next, you should add the `Algolia\ScoutMacros\ServiceProvider` to the `providers`
array of your `config/app.php` configuration file:

```php
Algolia\ScoutMacros\ServiceProvider::class
```


## Usage

### `count`

The count method will return the number of results after the request to Algolia.

The point is to avoid pull data from the database and building the collection.

```php
$nbHits = Model::search('query')->count();
```

### `hydrate`

The `hydrate` method is similar to the standard get() method, except it hydrates the models from your Algolia index.

By default, Scout will use the IDs of the results from Algolia and pull the data from the local database to create the collection.

Hence, `hydrate` will be much faster than `get`.


**Note**: By using this method you must be sure that you are correctly keeping your algolia index in sync with your database
to avoid populating stale data.

#### Restrict attributes

By default, this method will add all attributes from Algolia's record to your model. If you want to remove sensitive or irrelevant data from your model, you have two options.

You can set a list of retrievable attributes in your Algolia dashboard. In this case, Algolia will only return these attributes while still searching every `searchableAttributes`.

You may as well use the laravel `$guarded` attributes of your model class. For instance, if you don't want to see the `_h` attribute in your collection, you will have the following.

```php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class People extends Model
{
    use Searchable;

    protected $guarded = ['_highlightResult'];
}
```

### `with`

The `with` method gives you complete access to the Algolia options parameter. This allows you
to customise the search parameters exactly the same as you would using the algolia php library directly.

#### Simple example

```php
$result = Model::search('')
					->with(['hitsPerPage' => 30])
					->get();
```

#### Advanced example

```php

$filters = [
    'averge_ratings >= 3',
    'total_reviews >= 1'
];

$filterString = implode(' AND ', $filters);

$params = [
            'aroundLatLng' => $lat.','.$lon,
            'hitsPerPage' => 30,
            'page' => 0,
            'aroundRadius' => 30000, //30km
            'aroundPrecision' => 200, //200 Meters
            'getRankingInfo' => true, //return ranking information in results
            'filters' => $filterString // add filters
        ];

$result = Model::search('')->with($params)->get();

```


### `aroundLatLng`

The`aroundLatLng` method will add [geolocation parameter](1) to the search request. You
can define a point with its coordinate.

Note that this method is pure syntactic sugar, you can use `with` to specify more location details (like radius for instance)

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
