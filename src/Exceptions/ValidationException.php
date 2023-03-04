<?php

namespace Cone\Bazar\Exceptions;

use Illuminate\Validation\ValidationException as Exception;

class ValidationException extends Exception
{
    /**
     * Get all of the validation error messages.
     */
    public function errors(): array
    {
        return array_map(static function (array $messages): string {
            return $messages[0];
        }, $this->validator->errors()->messages());
    }
}
