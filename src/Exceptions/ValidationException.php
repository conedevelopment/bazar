<?php

namespace Bazar\Exceptions;

use Illuminate\Validation\ValidationException as Exception;

class ValidationException extends Exception
{
    /**
     * Get all of the validation error messages.
     *
     * @return array
     */
    public function errors(): array
    {
        return array_map(function (array $messages) {
            return $messages[0];
        }, $this->validator->errors()->messages());
    }
}
