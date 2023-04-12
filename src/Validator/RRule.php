<?php

namespace TantHammar\Recurring\Validator;

use Illuminate\Contracts\Validation\Rule;
use Recurr\Exception\InvalidRRule;

/**
 * Validates Simshaun\Recurr\Rule string<br>
 * Example use: 'str_rule' => ['required', new RRule]
 */
class RRule implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
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
}
