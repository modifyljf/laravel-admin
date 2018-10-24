<?php

namespace Guesl\Admin\Rules;

use Illuminate\Contracts\Validation\Rule;

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
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Give only number from the value.
        $phoneNumber = preg_replace('/(\\D)/', '', $value);

        $pattern = '/^\+?[1-9]\d{1,14}$/';
        $isMatch = preg_match($pattern, $value, $phoneNumber);
        return $isMatch != false;
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
