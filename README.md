[![Build Status](https://travis-ci.org/takeawaytown/laravel-uuid.svg?branch=master)](https://travis-ci.org/takeawaytown/laravel-uuid)

# Laravel UUID
A package for working with UUID values in Laravel.

You can use the package to generate and validate version 1, 3, 4 and 5 UUID identifiers.

## Installation
To install, simply type the following at your bash prompt:
```
composer require takeawaytown/laravel-uuid
```

The package automatically registers the Service Provider and Alias in Laravel 5.5 and above. If using Laravel <= 5.4, then you must manually add them.

For the Service Provider, add the following to your app config file:
```
    TakeawayTown\LaravelUuid\UuidServiceProvider::class,
```

For the Alias, add the following to your app config:
```
    'UUID' => TakeawayTown\LaravelUuid\Uuid::class,
```

## Basic Usage
The most basic generator usage is:
```
Uuid::generate();
```

This will generate a UUID object, which will be 'Version 1' and will use a random MAC address.

You can also generate a UUID string using type-hinting or using a method. Either of the following is exactly the same:
```
(string) Uuid::generate();
Uuid::generate()->string;
```
