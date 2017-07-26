# Laravel Scout Algolia Macros

A collection of useful macros to extend Laravel Scout capabilities when using the [Algolia engine](https://laravel.com/docs/5.4/scout#driver-prerequisites).

This package aims to provide a set of macros to take advantage of the
Algolia-specific feature.


## Installation

Pull the package using composer

```
composer install holidaywatchdog/laravel-scout-algolia-macros
```

Next, you should add the `HolidayWatchdog\ScoutMacros\ServiceProvider` to the `providers`
array of your `config/app.php` configuration file:

```php
HolidayWatchdog\ScoutMacros\ServiceProvider::class
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


## `with`

The`with` method gives you complete access to the Algolia options parameter. This allows you
to customise the search parameters exactly the same as you would using the algolia php library directly.

One thing to note

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
            'aroundPrecision' => 200), //200 Meters
            'getRankingInfo' => true, //return ranking information in results
            'filters' => $filterString // add filters
        ];

$result = Model::search('')->with($params)->get();

```


## `hydrate`

The `hydrate` method is similar to the standard get() method, except it hydrates the models from your Algolia index
instead of of using the objects keys to pull the related models from your database, meaning much quicker response times.

This also gives you the ability to overide the fill() method on any model to use the additional data that you store 
in your indexes.

Note: By using this method you must be sure that you are correctly keeping your algolia index in sync with your database
to avoid populating stale data.

Example model with additional data from Algolia Index being populated:

```php

class ExampleModel extends Model
{
    use Searchable;

    protected $appends = [
        'rankingInfo' //Add rankingInfo when converted to array
    ];

    protected $rankingInfo = [];
    protected $highlightResult = [];

    /**
     * Adds the ranking & highlight results from the search request to get search score/geo distance etc
     *
     * @param array $attributes
     * @return mixed
     */
    public function fill(array $attributes)
    {

        if (isset($attributes['_rankingInfo']))
        {
            $this->setRankingInfo($attributes['_rankingInfo']);
        }
        
        //Add any additional data stored in algolia as required

        return parent::fill($attributes);
    }

    public function getRankingInfoAttribute(): array
    {
        return $this->rankingInfo;
    }

    public function setRankingInfo(array $rankingInfo)
    {
        $this->rankingInfo = $rankingInfo;
    }

$result = ExampleModel::search('')->with($params)->get();

```

## Contributing

Feel free to open an issue to request a macro.

Open any pull request you want, so we can talk about it and improve the package. :tada:

[1]: https://www.algolia.com/doc/guides/geo-search/geo-search-overview/
