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
- $ php artisan make:resource TourResource

Create tests
- $ php artisan make:test TravelListTest

Create Factory: Feeds test database
- $ php artisan make:factory TravelFactory --model=Travel
- $ php artisan make:factory TourFactory --model=Tour

FormRequests can validate GET requests as well. Not only POST requests
- $ php artisan make:request ToursListRequest

Focus testing a single test feature;
- $ php artisan test --filter ToursListTest::test_tours_list_filters_by_price_correctly

Create a custom command in Laravel
- $ php artisan make:command CreateUserCommand

Create a seeder
- $ php artisan make:seeder RoleSeeder

Run a specific seeder
- $ php artisan db:seed --class=RoleSeeder

NOTE: 
Admin controllers can go into their own folder

- $ php artisan make:controller Api/V1/Admin/TravelController

When using auth:sanctum middleware below steps occur inside LoginController;
- Login Endpoint with email and password i.e. inside api.php
- If successful, return api token i.e. inside login controller's method
- Then each subsequent request will be performed with that token passed as "Bearer Token"

Create the LoginController.
NOTE: the --invokable flag, generates a single __invoke() method in the controller
- $ php artisan make:controller Api/V1/Auth/LoginController --invokable

Create a laravel middleware
- $ php artisan make:middleware RoleMiddleWare
