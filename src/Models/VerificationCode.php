<?php declare(strict_types=1);
namespace Codedive\LaravelCodeVerification\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Verification Code Model
 *
 * This model represents a verification code entity in the system
 * It provides methods to interact with the verification_codes table
 * and includes business logic related to verification codes
 *
 * @property int $id Auto-incrementing unique identifier
 * @property string $receiver Recipient information (e.g., Email, Phone)
 * @property string $purpose Purpose of the verification
 * @property int|null $user_id User's unique identifier; zero for guests
 * @property string $code The verification code
 * @property Carbon $expires_at Expiration datetime of the code
 * @property int $attempts Number of verification attempts
 * @property bool $is_verified Verification status (false: not verified, true: verified)
 * @property Carbon $verified_at Datetime when verified
 * @property bool $is_revoked Revocation status (false: not revoked, true: revoked)
 * @property Carbon $revoked_at Datetime when revoked
 * @property Carbon $created_at Creation datetime
 * @property Carbon $updated_at Updated datetime
 *
 * @package Codedive\LaravelCodeverification\Models
 * @since 1.0.0
 */
class VerificationCode extends Model
{
    protected $table = 'verification_codes';

    protected $fillable = [
        'user_id', 'receiver', 'purpose', 'code', 'expires_at', 'attempts', 'is_verified', 'verified_at', 'is_revoked', 'revoked_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'is_revoked' => 'boolean',
        'revoked_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Check if the verification code has expired
     *
     * @return bool True if expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * User Relation
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'));
    }
}
