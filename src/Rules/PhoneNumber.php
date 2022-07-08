<?php

namespace Modifyljf\Admin\Rules;

use Illuminate\Contracts\Validation\Rule;

/**
 * Class PhoneNumber
 * @package Modifyljf\Admin\Rules
 */
class PhoneNumber implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Give only number from the value.
        $phoneNumber = preg_replace('/(\\D)/', '', $value);

        $pattern = '/^\+?[1-9]\d{9,14}$/';
        $matches = preg_match($pattern, $phoneNumber, $matches);
        return !empty($matches);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute formation is wrong.';
    }
}
