### Some commands to project setup

- Create Roles table and a migration
- $ php artisan make:model Role -m

Roles to Users migration is a many-to-many relationship. A pivot table is 
required;

- $ php artisan make:migration "create role user table"

A times if the plural name is ambiguos or easily confused or maybe client's 
requirements. Change it in below places;

- inside migration i.e. table name e.g. travels
- actual migration name **2023_08_15_082122_create_travels_table**
- inside model file using `protected $table='travels'`

Some columns might be Virtuals i.e. computed from other columns' values. Use Accessors

- e.g. "number_of_nights" = number_of_days - 1;

Create an Observer

- php artisan make:observer TravelObserver --model=Travel

Add prefixes to API e.g. "v1".
- Inside RouteServiceProvider. Omits redundancy adding it repeatedly in each route
e.g. in Route::group() or Individual route urls e.g. Route::get("v1/travels")

Create a controller for a versioned API
- $ php artisan make:controller Api/V1/TravelController

Create a Resource. To return formatted or a subset of results
- $ php artisan make:resource TravelResource

Create tests
- $ php artisan make:test TravelListTest

Create Factory: Feeds test database
- $ php artisan make:factory TravelFactory --model=Travel
