# Laravel Eloquent RRULE helpers
This package adds Eloquent helpers for [Simshauns php Recurr package](https://github.com/simshaun/recurr)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/tanthammar/recurring.svg?style=flat-square)](https://packagist.org/packages/tanthammar/recurring)
[![Total Downloads](https://img.shields.io/packagist/dt/tanthammar/recurring.svg?style=flat-square)](https://packagist.org/packages/tanthammar/recurring)


## Installation

You can install the package via composer:

```bash
composer require tanthammar/recurring
```

## Important
- `start_at` and `end_at` are expected to be stored in its timezone, not converted to utc in db. The `Builder` will convert them using the timezone attribute.
- `end_at` represents **DURATION** not last recurrence, src: https://github.com/simshaun/recurr/issues/44

## Usage
Let's say you have a DatePattern model in your codebase.

The Builder parses them in combination with the timezone attribute.


Your model must have the following attributes:
```php
protected $casts = [
    'start_at'  => 'immutable_date', //required
    'end_at'    => 'immutable_date', //nullable, represents duration, not last recurrence
    'timezone' => 'string', //required
    'str_rule'  => 'string', //required
    'except_on' => 'array', //array with excluded dates
];
```


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

All `RecurrenceCollection` methods returns max 732 recurrences. (Same as parent simshaun/recurr package).
Use the `$count` parameter to set how many recurrences you want returned.

```php
$dp = App\Models\DatePattern::first();

$dp->recurr()->first(): bool|Recurrence
$dp->recurr()->firstStart(): bool|Carbon
$dp->recurr()->firstEnd(): bool|Carbon

$dp->recurr()->last(): bool|Recurrence
$dp->recurr()->lastStart(): bool|Carbon
$dp->recurr()->lastEnd(): bool|Carbon

$dp->recurr()->next(): bool|Recurrence
$dp->recurr()->nextStart(): bool|Carbon
$dp->recurr()->nextEnd(): bool|Carbon

$dp->recurr()->current(): bool|Recurrence
$dp->recurr()->currentStart(): bool|Carbon
$dp->recurr()->currentEnd(): bool|Carbon

$dp->recurr()->rule(): Rule

$dp->recurr()->all(): RecurrenceCollection, //limited to 732 recurrences

$dp->recurr()->schedule(?int $count): RecurrenceCollection

$dp->recurr()->scheduleBetween(string|DateTime $startDate, string|DateTime $endDate, ?int $count): RecurrenceCollection

$dp->recurr()->scheduleBefore(string|DateTime $beforeDate, ?int $count): RecurrenceCollection

$dp->recurr()->scheduleAfter(string|DateTime $afterDate, ?int $count): RecurrenceCollection

```

## Contributing

Happy for every contribution, make a PR :)


## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
