<?php

namespace Bazar\Rules;

use Bazar\Contracts\Models\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class MatchingPassword implements Rule
{
    /**
     * The user instance.
     *
     * @var \Bazar\Contracts\Models\User
     */
    protected $user;

    /**
     * Create a new rule instance.
     *
     * @param  \Bazar\Contracts\Models\User  $user
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return Hash::check($value, $this->user->password);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __('The :attribute is invalid.');
    }
}
