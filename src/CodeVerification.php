<?php declare(strict_types=1);

namespace Codedive\LaravelCodeVerification;

use Codedive\LaravelCodeVerification\Models\VerificationCode;
use Illuminate\Support\Facades\Facade;

/**
 * Verification Code Facade
 *
 * @method static VerificationCode issue(string $receiver, string $purpose, int $userId)
 * @method static bool verify(string $receiver, string $purpose, int $userId, string $code)
 */
class CodeVerification extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return CodeVerificationService::class;
    }
}
