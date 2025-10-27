<?php

namespace Sportyneo\SDK\Exceptions;

use Exception;

/**
 * Base API Exception
 */
class ValidationException extends ApiException
{
    /** @var array */
    protected $errors = [];

    public function __construct(string $message = "", int $code = 0, array $errors = [])
    {
        parent::__construct($message, $code);
        $this->errors = $errors;
    }

    /**
     * Get validation errors
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}