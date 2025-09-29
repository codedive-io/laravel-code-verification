<?php
namespace Codedive\LaravelCodeVerification\Tests;

use Codedive\LaravelCodeVerification\CodeVerificationService;
use Codedive\LaravelCodeVerification\Events\CodeIssuedEvent;
use Codedive\LaravelCodeVerification\Events\CodeRevokedEvent;
use Codedive\LaravelCodeVerification\Events\CodeVerifiedEvent;
use Codedive\LaravelCodeVerification\Models\VerificationCode;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Unit tests for the CodeVerificationService class
 */
class CodeVerificationServiceTest extends TestCase
{
    /**
     * @var CodeVerificationService
     */
    protected $service;

    /**
     * @var array
     */
    protected $config = [
        'code_length' => 6,
        'expires_in' => 600, // 10 minutes
        'max_attempts' => 3,
    ];

    /**
     * Test if a verification code is successfully issued and an event is fired
     *
     * @return void
     */
    public function test_a_verification_code_can_be_issued(): void
    {
        // Mock the Event facade to prevent the actual event from being dispatched
        Event::fake();

        // Prepare a mock for the createVerificationCode method
        $mockVerification = $this->createMock(VerificationCode::class);
        $this->service = $this->getMockBuilder(CodeVerificationService::class)
            ->setConstructorArgs([$this->config])
            ->onlyMethods(['createVerificationCode'])
            ->getMock();

        $this->service->method('createVerificationCode')
            ->willReturn($mockVerification);

        $receiver = 'test@example.com';
        $purpose = 'password_reset';
        $userId = 1;

        $verification = $this->service->issue($receiver, $purpose, $userId);

        $this->assertInstanceOf(VerificationCode::class, $verification);
        Event::assertDispatched(CodeIssuedEvent::class);
    }

    /**
     * Provides test data for various verification scenarios.
     *
     * @return array
     */
    public static function verificationScenariosProvider(): array
    {
        return [
            // Scenario 1 : correct code
            'correct_code_is_verified' => [
                'inputCode' => 'ABCDEF',
                'mockIsVerified' => false,
                'mockIsRevoked' => false,
                'mockAttempts' => 0,
                'mockIsExpired' => false,
                'mockFindVerificationCodeReturn' => 'code_object',
                'expectedResult' => true,
                'expectedIsVerified' => true,
                'expectedAttempts' => 1,
                'expectedEvent' => CodeVerifiedEvent::class,
                'expectSaveCall' => true,
            ],
            // Scenario 2 : wrong code
            'incorrect_code_fails_verification' => [
                'inputCode' => 'WRONG',
                'mockIsVerified' => false,
                'mockIsRevoked' => false,
                'mockAttempts' => 0,
                'mockIsExpired' => false,
                'mockFindVerificationCodeReturn' => 'code_object',
                'expectedResult' => false,
                'expectedIsVerified' => false,
                'expectedAttempts' => 1,
                'expectedEvent' => null,
                'expectSaveCall' => true,
            ],
            // Scenario 3 : over max attempts
            'exceeding_max_attempts_revokes_code' => [
                'inputCode' => 'WRONG',
                'mockIsVerified' => false,
                'mockIsRevoked' => false,
                'mockAttempts' => 3, // Max attempts exceeded
                'mockIsExpired' => false,
                'mockFindVerificationCodeReturn' => 'code_object',
                'expectedResult' => false,
                'expectedIsVerified' => false,
                'expectedAttempts' => 4,
                'expectedEvent' => CodeRevokedEvent::class,
                'expectSaveCall' => true,
            ],
            // Scenario 4 : expired
            'expired_code_fails_verification' => [
                'inputCode' => 'ABCDEF',
                'mockIsVerified' => false,
                'mockIsRevoked' => false,
                'mockAttempts' => 0,
                'mockIsExpired' => true,
                'mockFindVerificationCodeReturn' => 'code_object',
                'expectedResult' => false,
                'expectedIsVerified' => false,
                'expectedAttempts' => 0,
                'expectedEvent' => null,
                'expectSaveCall' => false,
            ],
            // Scenario 5 : revoked
            'revoked_code_fails_verification' => [
                'inputCode' => 'ABCDEF',
                'mockIsVerified' => false,
                'mockIsRevoked' => true,
                'mockAttempts' => 0,
                'mockIsExpired' => false,
                'mockFindVerificationCodeReturn' => 'code_object',
                'expectedResult' => false,
                'expectedIsVerified' => false,
                'expectedAttempts' => 0,
                'expectedEvent' => null,
                'expectSaveCall' => false,
            ],
            // Scenario 6 : already verified code
            'already_verified_code_fails_reverification' => [
                'inputCode' => 'ABCDEF',
                'mockIsVerified' => true,
                'mockIsRevoked' => false,
                'mockAttempts' => 0,
                'mockIsExpired' => false,
                'mockFindVerificationCodeReturn' => 'code_object',
                'expectedResult' => false,
                'expectedIsVerified' => true,
                'expectedAttempts' => 0,
                'expectedEvent' => null,
                'expectSaveCall' => false,
            ],
            // Scenario 6 : not found code
            'code_not_found_fails_verification' => [
                'inputCode' => 'UNKNOWN',
                'mockIsVerified' => false,
                'mockIsRevoked' => false,
                'mockAttempts' => 0,
                'mockIsExpired' => false,
                'mockFindVerificationCodeReturn' => null, // findVerificationCode return null
                'expectedResult' => false, // must return false
                'expectedIsVerified' => false,
                'expectedAttempts' => 0,
                'expectedEvent' => null,
                'expectSaveCall' => false,
            ],
        ];
    }

    /**
     * Test various verification scenarios using a data provider.
     *
     * @dataProvider verificationScenariosProvider
     * @param string $inputCode
     * @param bool $mockIsVerified
     * @param bool $mockIsRevoked
     * @param int $mockAttempts
     * @param bool $mockIsExpired
     * @param mixed $mockFindVerificationCodeReturn
     * @param bool $expectedResult
     * @param bool $expectedIsVerified
     * @param int $expectedAttempts
     * @param string|null $expectedEvent
     * @param bool $expectSaveCall
     * @return void
     */
    #[DataProvider('verificationScenariosProvider')]
    public function test_verification_scenarios(
        string $inputCode,
        bool $mockIsVerified,
        bool $mockIsRevoked,
        int $mockAttempts,
        bool $mockIsExpired,
        mixed $mockFindVerificationCodeReturn,
        bool $expectedResult,
        bool $expectedIsVerified,
        int $expectedAttempts,
        ?string $expectedEvent,
        bool $expectSaveCall
    ): void
    {
        Event::fake();

        // findVerificationCode mocking
        if ($mockFindVerificationCodeReturn) {
            $verification = $this->getMockBuilder(VerificationCode::class)
                ->onlyMethods(['isExpired', 'save']) // Mocking `save` method
                ->getMock();

            // is_verified, is_revoked, attempts
            $verification->is_verified = $mockIsVerified;
            $verification->is_revoked = $mockIsRevoked;
            $verification->code = 'ABCDEF';     // right code
            $verification->attempts = $mockAttempts;

            $verification->method('isExpired')->willReturn($mockIsExpired);

            if ($expectSaveCall) {
                $verification->expects($this->once())->method('save');
            } else {
                $verification->expects($this->never())->method('save');
            }
        } else {
            $verification = null;
        }

        $this->service = $this->getMockBuilder(CodeVerificationService::class)
            ->setConstructorArgs([$this->config])
            ->onlyMethods(['findVerificationCode'])
            ->getMock();

        $this->service->method('findVerificationCode')
            ->willReturn($verification);

        $isVerified = $this->service->verify('test@example.com', 'password_reset', 1, $inputCode);

        $this->assertEquals($expectedResult, $isVerified);

        if ($verification) {
            $this->assertEquals($expectedIsVerified, $verification->is_verified);
            $this->assertEquals($expectedAttempts, $verification->attempts);
        }

        if ($expectedEvent) {
            Event::assertDispatched($expectedEvent);
        }
    }
}
