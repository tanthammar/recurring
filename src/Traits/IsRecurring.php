<?php

namespace TantHammar\Recurring\Traits;

use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

trait IsRecurring
{
    #[Pure]
    public function recurr(): Builder
    {
        return new Builder($this);
    }

    #[ArrayShape(['start_at' => "string|Carbon", 'end_at' => "null|string|Carbon", 'timezone' => "null|string", 'str_rule' => "null|string", 'except_on' => "null|array"])]
    public function getRecurringConfig(): object
    {
        return (object) [
            'start_at' => $this->start_at,
            'end_at'   => $this->end_at,
            'timezone'   => $this->timezone,
            'str_rule'  => $this->str_rule,
            'except_on' => $this->except_on,
        ];
    }
}
