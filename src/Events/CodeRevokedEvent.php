<?php declare(strict_types=1);
namespace Codedive\LaravelCodeVerification\Events;

use Codedive\LaravelCodeVerification\Models\VerificationCode;

/**
 * Event triggered when a verification code is revoked
 *
 * This event contains the verification code instance that was revoked
 * and can be used to notify listeners about the revocation
 *
 * @package Codedive\LaravelCodeverification\Events
 * @since 1.0.0
 */
class CodeRevokedEvent
{
    /**
     * The verification code instance
     *
     * @var VerificationCode
     */
    public VerificationCode $verificationCode;

    /**
     * Create a new event instance
     *
     * @param VerificationCode $verificationCode Then verification code instance
     */
    public function __construct(VerificationCode $verificationCode)
    {
        $this->verificationCode = $verificationCode;
    }
}
