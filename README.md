# Laravel Code Verification

A package for issuing and verifying temporary confirmation codes for users in a Laravel application. It can be used for various purposes such as password reset, email or phone number verification, and two-factor authentication.

## Key Features

* Code Issuance : Generate a unique confirmation code for a specific recipient (e.g., email, phone number)
* Code Verification : Validates whether the issued code is correct, not expired, and has not exceeded the maximum number of attempts.
* Event-Driven : Events are fired upon code issuance, verification, or revocation, allowing for easy integration with you business logic.

## Installation

Install the package using Composer.

```bash
composer require codedive-io/laravel-code-verification
```

Database Migration

After installing the package, you need to run the migrations to create the `verification_codes` table. The migrations are automatically loaded by the service provider.

```bash
php artisan migrate
```

## Usage

### 1. Code Issuance

Use the `issue` method of the `CodeVerificationService` to issue a confirmation code.

**Using the Service**

```php
use Codedive\LaravelCodeVerification\CodeVerificationService;

$service = app(CodeVerificationService::class);

$verificationCode = $service->issue(
    $receiver = 'user@example.com',
    $purpose = 'verify_email',
    $userId = 1
)
```

**Using the Facades**

```php
use Codedive\LaravelCodeVerification\CodeVerification;

CodeVerification::issue(
    $receiver = 'user@example.com',
    $purpose = 'verify_email',
    $userId = 1
);
```

### 2. Code Verification

Use the `verify` method to validate the code entered by the user.

**Using the Services**

```php
use Codedive\LaravelCodeVerification\CodeVerificationService;

$service = app(CodeVerificationService::class);

$isVerified = $service->verify(
    $receiver = 'user@example.com',
    $purpose = 'verify_email',
    $user_id = 1,
    $code = '123456'
);

if ($isVerified) {
    // The code was successfully verified
} else {
    // Code verification failed
}
```

**Using the Facades**

```php
use Codedive\LaravelCodeVerification\CodeVerification;

$isVerified = CodeVerification::verify(
    $receiver = 'user@example.com',
    $purpose = 'verify_email',
    $user_id = 1,
    $code = '123456'
);
```

### 3. Event

You can listen for the following events when they are triggered:

- When issued : `CodeIssuedEvent`
- When Verified : `CodeVerifiedEvent`
- When Revoked : `CodeRevokedEvent`

### 4. Configuration

If you need, you can publish configuration file.

```bash
php artisan vendor:publish --tag=code-verification-config
```

You can change the configuration values through the `config/code_verification.php` file.

```php
return [
    'code_length' => 6,     // Length of the generated code
    'expires_in' => 300,    // Code expiration time (in seconds)
    'max_attempts' => 3,    // Maximum number of attempts
];
```

## Uninstall Package

Rollback the specific migration

```bash
php artisan migrate:rollback --path=/vendor/codedive-io/laravel-code-verification/database/migration
```

Uninstall the package using Composer.

```bash
composer remove codedive-io/laravel-code-verification
```

## Contribution

Bug reports, feature suggestions, and pull requests are welcome. You can contribute via the [Github Repository](https://github.com/codedive-io/laravel-code-verification).

## LICENSE

This package is open-sourced software licensed under the [MIT license](LICENSE).
