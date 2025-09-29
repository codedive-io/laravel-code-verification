<?php

return [
    'migration' => [
        'id' => 'Auto-incrementing unique identifier',
        'receiver' => 'Receiver information',
        'purpose' => 'Verification purpose information',
        'user_id' => 'User identifier, zero for guest',
        'code' => 'Verification code, max 20 chars',
        'expires_at' => 'Expiration datetime',
        'attempts' => 'Number of attempts',
        'is_verified' => 'Verification Status (0: not verified, 1: verified)',
        'verified_at' => 'Verification completion datetime',
        'is_revoked' => 'Revocation status (0: not revoked, 1: revoked)',
        'revoked_at' => 'Revocation date',
        'idx_receiver_purpose' => 'receiver and purpose',
    ],
];
