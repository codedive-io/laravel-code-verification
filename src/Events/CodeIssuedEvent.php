<?php declare(strict_types=1);
namespace Codedive\LaravelCodeVerification\Events;

use Codedive\LaravelCodeVerification\Models\VerificationCode;

/**
 * Event triggered when a verification code is issued
 *
 * This event contains the verification code instance that was generated
 * and can be used to notify listeners about the issuance
 *
 * @package Codedive\LaravelCodeverification\Events
 * @since 1.0.0
 */
class CodeIssuedEvent
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
