<?php

namespace TantHammar\Recurring\Traits;

use TantHammar\Recurring\Builder;

trait IsRecurring
{
    public function recurr(): Builder
    {
        return new Builder($this);
    }

    public function getRecurringConfig(): object
    {
        return (object) [
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'timezone' => $this->timezone,
            'str_rule' => $this->str_rule,
            'except_on' => $this->except_on,
        ];
    }
}
