<?php

namespace TantHammar\Recurring;

use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Model;
use JetBrains\PhpStorm\Pure;
use Recurr\DateExclusion;
use Recurr\Exception\InvalidRRule;
use Recurr\Exception\InvalidWeekday;
use Recurr\RecurrenceCollection;
use Recurr\Rule;
use Recurr\Transformer\ArrayTransformer;
use Recurr\Transformer\ArrayTransformerConfig;
use Recurr\Transformer\Constraint\AfterConstraint;
use Recurr\Transformer\Constraint\BeforeConstraint;
use Recurr\Transformer\Constraint\BetweenConstraint;
use Recurr\Transformer\TextTransformer;

class Builder
{
    private Model $model;

    private Config $config;

    #[Pure]
    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->config = $this->buildConfig();
    }

    /**
     * @throws InvalidWeekday|InvalidRRule
     */
    public function firstStart(): bool|Carbon
    {
        if (! $schedule = $this->schedule()) {
            return false;
        }

        return Carbon::instance($schedule->first()->getStart());
    }

    /**
     * @throws InvalidRRule
     * @throws InvalidWeekday
     */
    public function firstEnd(): bool|Carbon
    {
        if (! $schedule = $this->schedule()) {
            return false;
        }

        return Carbon::instance($schedule->first()->getEnd());
    }

    /**
     * @throws InvalidWeekday|InvalidRRule
     */
    public function lastStart(): bool|Carbon
    {
        if (! $schedule = $this->schedule()) {
            return false;
        }

        return Carbon::instance($schedule->last()->getStart());
    }

    /**
     * @throws InvalidWeekday|InvalidRRule
     */
    public function lastEnd(): bool|Carbon
    {
        if (! $schedule = $this->schedule()) {
            return false;
        }

        return Carbon::instance($schedule->last()->getEnd());
    }

    /**
     * @throws InvalidWeekday|InvalidRRule
     */
    public function nextStart(): bool|Carbon
    {
        if (! $schedule = $this->schedule()) {
            return false;
        }

        if (! $next = $schedule->next()) {
            return false;
        }

        return Carbon::instance($next->getStart());
    }

    /**
     * @throws InvalidWeekday|InvalidRRule
     */
    public function nextEnd(): bool|Carbon
    {
        if (! $schedule = $this->schedule()) {
            return false;
        }

        if (! $next = $schedule->next()) {
            return false;
        }

        return Carbon::instance($next->getEnd());
    }

    /**
     * @throws InvalidWeekday|InvalidRRule
     */
    public function currentStart(): bool|Carbon
    {
        if (! $schedule = $this->schedule()) {
            return false;
        }

        return Carbon::instance($schedule->current()->getStart());
    }

    /**
     * @throws InvalidWeekday|InvalidRRule
     */
    public function currentEnd(): bool|Carbon
    {
        if (! $schedule = $this->schedule()) {
            return false;
        }

        return Carbon::instance($schedule->current()->getEnd());
    }

    /**
     * Alias for schedule()
     *
     * @throws InvalidRRule
     * @throws InvalidWeekday
     */
    public function all(): RecurrenceCollection
    {
        return $this->schedule();
    }

    /** 732 is default limit of recurrences returned from the transformer if infinit rrule */
    public function transformer(int $limit): ArrayTransformer
    {
        if($limit > 732) $limit = 732;

        $transformerConfig = new ArrayTransformerConfig();
        $transformerConfig->enableLastDayOfMonthFix();
        $transformerConfig->setVirtualLimit($limit);

        $transformer = new ArrayTransformer();
        $transformer->setConfig($transformerConfig);

        return $transformer;
    }

    protected function limit(?int $limit = null): int
    {
        return is_null($limit) ? 732 : $limit;
    }

    /**
     * @throws InvalidWeekday|InvalidRRule
     */
    public function schedule(?int $count = null): RecurrenceCollection
    {
        return $this->transformer($this->limit($count))->transform($this->rule());
    }

    /**
     * @throws InvalidRRule
     * @throws InvalidWeekday
     * @throws Exception
     */
    public function scheduleBetween(string|DateTime $startDate, string|DateTime $endDate, ?int $count = null): RecurrenceCollection
    {

        // The $countConstraintFailures in the ArrayTransformer::transform() method
        // decides whether the transformer will stop looping or just count failures
        // toward the limit of recurrences.
        // true = count toward limit
        // false = stop looping after endDate
        // We want it to stop looping since we're searching between two dates
        // so that once the dates go beyond the range it will return.
        return $this->transformer($this->limit($count))->transform(
            $this->rule(),
            constraint: new BetweenConstraint($this->convertDate($startDate), $this->convertDate($endDate), true),
            countConstraintFailures: false
        );
    }

    /**
     * @throws InvalidRRule
     * @throws InvalidWeekday
     * @throws Exception
     */
    public function scheduleBefore(string|DateTime $beforeDate, ?int $count = null): RecurrenceCollection
    {
        return $this->transformer($this->limit($count))->transform(
            $this->rule(),
            constraint: new BeforeConstraint($this->convertDate($beforeDate), true),
            countConstraintFailures: false
        );
    }

    /**
     * @throws InvalidRRule
     * @throws InvalidWeekday
     * @throws Exception
     */
    public function scheduleAfter(string|DateTime $afterDate, ?int $count = null): RecurrenceCollection
    {
        return $this->transformer($this->limit($count))->transform(
            $this->rule(),
            constraint: new AfterConstraint($this->convertDate($afterDate), true),
            countConstraintFailures: false
        );
    }

    /**
     * @throws InvalidRRule
     * @throws Exception
     */
    public function rule(): Rule
    {
        $timezone = $this->config->timezone;
        $rule = new Rule(
            rrule: $this->config->str_rule, //UNTIL and COUNT in str_rule affects last() occurrence
            startDate: Carbon::create($this->config->startDate, $timezone)->toDateTime(),
            //endDate is DURATION not last occurrence, https://github.com/simshaun/recurr/issues/44
            endDate: $this->config->endDate ? Carbon::create($this->config->endDate, $timezone)->toDateTime() : '',
            timezone: $this->config->timezone,
        );
        if (count($this->config->except_on) > 0) {
            $rule->setExDates(
                collect($this->config->except_on)
                    ->map(fn ($date) => new DateExclusion(Carbon::create($date, $timezone)->toDateTime(), false))
                    ->toArray()
            );
        }

        return $rule;
    }

    /**
     * @throws InvalidRRule
     */
    public function textRule(): mixed
    {
        return (new TextTransformer)->transform($this->rule());
    }

    #[Pure]
    private function buildConfig(): Config
    {
        $config = $this->model->getRecurringConfig();

        return new Config(
            startDate: $config->start_at,
            timezone: $config->timezone,
            str_rule: $config->str_rule,
            endDate: $config->end_at ?? '',
            except_on: $config->except_on ?? []
        );
    }

    /**
     * @throws Exception
     */
    private function convertDate(null|string|DateTime $date): DateTime
    {
        return $date instanceof DateTime ? $date : new DateTime($date ?? 'now');
    }
}
