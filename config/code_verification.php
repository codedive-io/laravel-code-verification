<?php declare(strict_types=1);

return [
    'code_length' => env('CODEDIVE_CODE_VERIFICATION_CODE_LENGTH', 6),              // default code length, max 20
    'expires_in' => env('CODEDIVE_CODE_VERIFICATION_EXPIRES_IN', 300),              // expires in by second
    'max_attempts' => env('CODEDIVE_CODE_VERIFICATION_MAX_ATTEMPTS', 3),            // max attempts
];
