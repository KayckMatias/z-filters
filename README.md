# zFilters

##### a simple package to filter your Laravel models without repetitive loops or various if-else

## Installation

You can install the package via composer:

```bash
composer require kayckmatias/z-filters
```

## Usage
To use is very simple, you need only to things: make a new filter and create a scope in your model to point filter.

To make a new Filter:

```bash
php artisan make:zfilter NameFilter
```

Example:

![Example new Filter Image](https://i.imgur.com/A6dmq1Q.png)

and configure your filter options according to your preference
([read here](#configure))

Now, is need make a scope in your model, example:
```php
public function scopeFilterBy(Builder $query, array $filters)
{
    $filter = new UsersFilter($query, $filters);

    return $filter->apply();
}
```

## Configure
zFilters supports two filters type, simple and complex.

#### Simple Filters:
Simple Filter is a whereIn condition, when you make a simple filter you is saying.
```
['custom_name' => 'column to verify set whereIn']
```
Let's assume that your user table has the column "department_id", to make a simple filter you just need to reference how the name of the filter option will be, let's call it "departments" and the column, which would be
"department_id"

```
['departaments' => 'departament_id']
```

Now the array of values or single value you send in $filters['departments'] will be filtered in whereIn condition on the simple filter

#### Complex Filters
The complex filter can deal with more specific conditions, it accepts a callback function and you can filter however you want, let's go to another example.
Assuming your same user table has a name column and a summary column and you expect to do a complex search for both conditions by value, one way to do it would be:
```
'search' => function ($q, $filterValue) {
    $q->where(function ($q) use ($filterValue) {
        $q->where('name', 'LIKE', "%" . $filterValue . "%");
        $q->orWhere('summary', 'LIKE', "%" . $filterValue . "%");
    });
}
```

The complex filter option named 'search' will execute the function, the value of $q will be the query builder being formed and the second parameter, $filterValue will be automatically implemented to what is sent in $filters['search'], as it is a callback function you can work on it too, manipulate the values sent, as if it were inside the query builder itself.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information

<br />
<p align="center">Made with &hearts; by Kayck Matias</p>