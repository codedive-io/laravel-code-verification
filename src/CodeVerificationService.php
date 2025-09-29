<?php declare(strict_types=1);

namespace Codedive\LaravelCodeVerification;

use Carbon\Carbon;
use Codedive\LaravelCodeVerification\Events\CodeIssuedEvent;
use Codedive\LaravelCodeVerification\Events\CodeRevokedEvent;
use Codedive\LaravelCodeVerification\Events\CodeVerifiedEvent;
use Codedive\LaravelCodeVerification\Models\VerificationCode;
use Illuminate\Support\Str;

/**
 * Handles the issuance, verification, and revocation of verification codes
 *
 * This service encapsulates the business logic related to verification codes,
 * including generating codes, checking expiration, and managing verification attempts
 *
 * @package Codedive\LaravelCodeVerification
 * @since 1.0.0
 */
class CodeVerificationService
{
    /**
     * Configuration settings fo the verification process
     *
     * @var array $config
     */
    protected array $config;

    /**
     * Initializes the service with the provided configuration
     *
     * @param array $config Configuration settings for verification codes
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Fires an event with the given event class and parameters
     *
     * @param string $event The event class name
     * @param array $eventValue Parameters to pass to the event constructor
     * @return void
     */
    public function fireEvent(string $event, array $eventValue): void
    {
        event(new $event(...$eventValue));
    }

    /**
     * Generate a new verification code
     *
     * @return string
     */
    public function generateVerificationCode(): string
    {
        return Str::upper(Str::random($this->config['code_length']));
    }

    /**
     * Calculates the expiration datetime for the verification code
     *
     * @return string The expiration datetime in 'Y-m-d H:i:s' format.
     */
    public function generateExpiresAt(): String
    {
        return Carbon::now()->addSeconds($this->config['expires_in'])->toDateTimeString();
    }

    /**
     * Retrieves the latest verification code for a given receiver, purpose, and user.
     *
     * @param string $receiver Recipient information (e.g., Email, Phone)
     * @param string $purpose Purpose of the verification
     * @param int $userId User's unique identifier; zero for guests
     *
     * @return VerificationCode|null The latest verification code, or null if not found.
     */
    public function findVerificationCode(string $receiver, string $purpose, int $userId): ?VerificationCode
    {
        return VerificationCode::where('receiver', $receiver)
            ->where('purpose', $purpose)
            ->where('user_id', $userId)
            ->latest()
            ->first();
    }

    /**
     * Creates a new verification code record in the database
     *
     * @param string $receiver Recipient information (e.g., Email, Phone)
     * @param string $purpose Purpose of the verification
     * @param int|null $userId User's unique identifier; zero for guests
     * @param string $code The verification Code
     * @param string $expiresAt Expiration datetime
     * @return VerificationCode The created verification code instance
     */
    public function createVerificationCode(string $receiver, string $purpose, ?int $userId, string $code, string $expiresAt): VerificationCode
    {
        return VerificationCode::create([
            'receiver' => $receiver,
            'purpose' => $purpose,
            'user_id' => $userId,
            'code' => $code,
            'expires_at' => $expiresAt,
        ]);
    }

    /**
     * Issue a new verification code for a given receiver, purpose, and user
     *
     * @param string $receiver Recipient information (e.g., Email, Phone)
     * @param string $purpose Purpose of the verification
     * @param int $userId User's unique identifier; zero for guests
     * @return VerificationCode The issued verification code
     */
    public function issue(string $receiver, string $purpose, int $userId): VerificationCode
    {
        $verificationCode = $this->createVerificationCode(
            $receiver,
            $purpose,
            $userId,
            $this->generateVerificationCode(),
            $this->generateExpiresAt(),
        );

        $this->fireEvent(CodeIssuedEvent::class, [$verificationCode]);

        return $verificationCode;
    }

    /**
     * Verifies the provided code for a given receiver, purpose, and user
     *
     * @param string $receiver Recipient information (e.g., Email, Phone)
     * @param string $purpose Purpose of the verification
     * @param int $userId User's unique identifier; zero for guests
     * @param string $code The verification code
     * @return bool True if the code is valid and verification is successful
     */
    public function verify(string $receiver, string $purpose, int $userId, string $code): bool
    {
        $fireEventName = null;

        /** @var VerificationCode $verificationCode */
        $verificationCode = $this->findVerificationCode($receiver, $purpose, $userId);

        // check if the verification code exists, is not verified, not revoked, and not expired
        if ($verificationCode && !$verificationCode->is_verified && !$verificationCode->is_revoked && !$verificationCode->isExpired())
        {
            // Increment the attempts counter
            $verificationCode->attempts = $verificationCode->attempts + 1;

            // Revoke the code if maximum attempts are exceeded
            if ($verificationCode->attempts > $this->config['max_attempts']) {
                $verificationCode->is_revoked = true;
                $verificationCode->revoked_at = Carbon::now();
                $fireEventName = CodeRevokedEvent::class;

            } else {

                // Verify the code if it matches
                if ($verificationCode->code == $code) {
                    $verificationCode->is_verified = true;
                    $verificationCode->verified_at = Carbon::now();
                    $fireEventName = CodeVerifiedEvent::class;
                }

            }

            // Save
            $verificationCode->save();

            if ($fireEventName) {
                $this->fireEvent($fireEventName, [$verificationCode]);
            }

            return $verificationCode->is_verified;
        }

        return false;
    }

}
