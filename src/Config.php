<?php

namespace TantHammar\Recurring;

use Illuminate\Contracts\Support\Arrayable;
use Recurr\Frequency;

class Config implements Arrayable
{
    // for future validation?
    private array $frequencies = [
        Frequency::YEARLY => 'YEARLY',
        Frequency::MONTHLY => 'MONTHLY',
        Frequency::WEEKLY => 'WEEKLY',
        Frequency::DAILY => 'DAILY',
        Frequency::HOURLY => 'HOURLY',
        Frequency::MINUTELY => 'MINUTELY',
        Frequency::SECONDLY => 'SECONDLY',
    ];

    public function __construct(
        public string $startDate,
        public string $timezone,
        public string $str_rule,
        public string $endDate = '',
        public array $except_on = [])
    {
    }

    // for future validation?
    public function getFrequencies(): array
    {
        return $this->frequencies;
    }

    public function getStartDate(): string
    {
        return $this->startDate;
    }

    public function setStartDate(string $value): self
    {
        $this->startDate = $value;

        return $this;
    }

    public function getEndDate(): string
    {
        return $this->endDate;
    }

    public function setEndDate(string $value): self
    {
        $this->endDate = $value;

        return $this;
    }

    public function getTimezone(): string
    {
        return $this->timezone;
    }

    public function setTimezone(string $value): self
    {
        $this->timezone = $value;

        return $this;
    }

    public function getExceptions(): array
    {
        return $this->except_on;
    }

    public function setExceptions(array $dates): self
    {
        $this->except_on = $dates;

        return $this;
    }

    public function toArray(): array
    {
        return (array) $this;
    }
}
