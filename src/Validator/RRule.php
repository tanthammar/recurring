<?php

namespace TantHammar\Recurring\Validator;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Recurr\Exception\InvalidRRule;

/**
 * Validates Simshaun\Recurr\Rule string<br>
 * Example use: 'str_rule' => ['required', new RRule]
 */
class RRule implements ValidationRule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     */
    public function passes($attribute, $value): bool
    {
        try {
            new \Recurr\Rule(rrule: $value);

            return true;
        } catch (InvalidRRule $e) {
            return false;
        }
    }

    public function message(): string
    {
        return 'The :attribute is not a valid recurring rule string';
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->passes($attribute, $value)) {
            $fail($this->message());
        }
    }
}
