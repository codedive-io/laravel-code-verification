<?php declare(strict_types=1);
namespace Codedive\LaravelCodeVerification\Events;

use Codedive\LaravelCodeVerification\Models\VerificationCode;

/**
 * Event triggered when a verification code is successfully verified
 *
 * This event contains the verification code instance that was verified
 * and can be used to notify listeners about the verification process
 *
 * @package Codedive\LaravelCodeverification\Events
 * @since 1.0.0
 */
class CodeVerifiedEvent
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
