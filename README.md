# GeoHash

A PHP implementation of [GeoHash](https://en.wikipedia.org/wiki/Geohash) encoding and decoding.

## What is a GeoHash?

A GeoHash is a compact string representation of a geographic location. It works by dividing the world into a grid and encoding a latitude/longitude pair as a short alphanumeric string. Longer strings give greater precision; shorter strings represent a larger area.

For example, the Eiffel Tower is at approximately 48.858, 2.294. As a GeoHash:

| Precision | GeoHash    | Area            |
|-----------|------------|-----------------|
| 3 chars   | `u09`      | ~156 km         |
| 5 chars   | `u09tу`    | ~4.9 km         |
| 8 chars   | `u09tunqu` | ~38 m           |

GeoHashes are useful for proximity searches, spatial indexing, and anywhere you need to group or compare locations efficiently.

## Requirements

PHP ^8.2

## Installation

```bash
composer require fyfey/geohash
```

## Usage

```php
use Fyfey\GeoHash\GeoHash;

$geoHash = new GeoHash();

// Encode a lat/lon to a GeoHash (default precision: 5 chars)
$hash = $geoHash->encode(48.858, 2.294);
// => "u09ty"

// Encode with higher precision
$hash = $geoHash->encode(48.858, 2.294, 8);
// => "u09tunqu"

// Decode a GeoHash back to lat/lon
[$lat, $lon] = $geoHash->decode('u09ty');
// => [48.856..., 2.296...]

// Decode with rounding
[$lat, $lon] = $geoHash->decode('u09ty', 3);
// => [48.857, 2.296]
```

## Running tests

```bash
composer install
vendor/bin/phpunit tests/
```
