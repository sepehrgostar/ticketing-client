# Sepehrgostar Ticketing System for laravel package


## Installation

Via Composer

``` bash
$ composer require sepehrgostar/ticketing-client
```

publish view and assets
```
php artisan vendor:publish --tag=sepehrgostar.ticketingClient --force
```

migrate 
```
php artisan migrate 
```
this command add column varchar 64 and nullabe "sepehrgostar_api_token" to users table

## usage<br>
add main route in your user dashboard
```
route('Sepehrgostar.TicketingClient.index')
```
<br>
2=dsf

## Security

If you discover any security related issues, please email author email instead of using the issue tracker.

 
