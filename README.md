# Laravel Eloquent RRULE helpers
This package adds Eloquent helpers for [Simshauns php Recurr package](https://github.com/simshaun/recurr)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/tanthammar/recurring.svg?style=flat-square)](https://packagist.org/packages/tanthammar/recurring)
[![Total Downloads](https://img.shields.io/packagist/dt/tanthammar/recurring.svg?style=flat-square)](https://packagist.org/packages/tanthammar/recurring)


## Installation

You can install the package via composer:

```bash
composer require tanthammar/recurring
```

## Usage
Let's say you have a DatePattern model in your codebase.

Your model must have the following attributes:
```php
protected $casts = [
    'start_at'  => 'immutable_date', //not nullable
    'end_at'    => 'immutable_date', //represents duration, not last occurrence
    'timezone' => 'string',
    'str_rule'  => 'string',
    'except_on' => 'array', //array with excluded dates
];
```

**OBSERVE that end_at represents DURATION** not last occurrence, src: https://github.com/simshaun/recurr/issues/44

Apply the `IsRecurring` trait to the model
```php
class DatePattern extends Model
{
    use IsRecurring;
    //...
}
```

After saving a string `rrule` to the `str_rule` field, you'll have access to the following methods.
For further information about additional methods see https://github.com/simshaun/recurr

```php
$dp = App\Models\DatePattern::first();

$dp->recurr()->firstStart(): bool|Carbon
$dp->recurr()->firstEnd(): bool|Carbon

$dp->recurr()->lastStart(): bool|Carbon
$dp->recurr()->lastEnd(): bool|Carbon

$dp->recurr()->nextStart(): bool|Carbon
$dp->recurr()->nextEnd(): bool|Carbon

$dp->recurr()->currentStart(): bool|Carbon
$dp->recurr()->currentEnd(): bool|Carbon

$dp->recurr()->rule(): Recurr\Rule

$dp->recurr()->schedule(): Recurr\RecurrenceCollection

$dp->recurr()->scheduleBetween($startDate, $endDate): Recurr\RecurrenceCollection
```

## Contributing

Happy for every contribution, make a PR :)


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
