<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Core\EmailService;

/**
 * Email Service Tests
 */

class EmailServiceTest extends TestCase
{
    private EmailService $emailService;
    private array $testConfig;

    protected function setUp(): void
    {
        $this->testConfig = [
            'host' => 'smtp.gmail.com',
            'port' => 587,
            'username' => 'test@example.com',
            'password' => 'testpassword',
            'encryption' => 'tls',
            'from_email' => 'noreply@medicore.com',
            'from_name' => 'MediCore Pharmacy',
            'reply_to' => 'noreply@medicore.com',
            'reply_to_name' => 'MediCore Support',
            'debug' => false
        ];

        $this->emailService = new EmailService($this->testConfig);
    }

    /**
     * Test email service initialization
     */
    public function test_email_service_initializes_correctly(): void
    {
        $this->assertInstanceOf(EmailService::class, $this->emailService);
    }

    /**
     * Test welcome email template rendering
     */
    public function test_welcome_email_template_renders_correctly(): void
    {
        $method = new \ReflectionMethod($this->emailService, 'renderTemplate');
        $method->setAccessible(true);

        $result = $method->invoke($this->emailService, 'welcome', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'verification_url' => 'http://example.com/verify'
        ]);

        $this->assertStringContainsString('Test User', $result);
        $this->assertStringContainsString('test@example.com', $result);
        $this->assertStringContainsString('MediCore', $result);
    }

    /**
     * Test password reset email template rendering
     */
    public function test_password_reset_template_renders_correctly(): void
    {
        $method = new \ReflectionMethod($this->emailService, 'renderTemplate');
        $method->setAccessible(true);

        $result = $method->invoke($this->emailService, 'password_reset', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'reset_url' => 'http://example.com/reset'
        ]);

        $this->assertStringContainsString('Test User', $result);
        $this->assertStringContainsString('Password Reset', $result);
        $this->assertStringContainsString('http://example.com/reset', $result);
    }

    /**
     * Test email verification template rendering
     */
    public function test_email_verification_template_renders_correctly(): void
    {
        $method = new \ReflectionMethod($this->emailService, 'renderTemplate');
        $method->setAccessible(true);

        $result = $method->invoke($this->emailService, 'email_verification', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'verification_url' => 'http://example.com/verify'
        ]);

        $this->assertStringContainsString('Test User', $result);
        $this->assertStringContainsString('Email Verification', $result);
        $this->assertStringContainsString('http://example.com/verify', $result);
    }

    /**
     * Test welcome email method exists
     */
    public function test_send_welcome_email_method_exists(): void
    {
        $this->assertTrue(method_exists($this->emailService, 'sendWelcomeEmail'));
    }

    /**
     * Test password reset email method exists
     */
    public function test_send_password_reset_email_method_exists(): void
    {
        $this->assertTrue(method_exists($this->emailService, 'sendPasswordResetEmail'));
    }

    /**
     * Test email verification method exists
     */
    public function test_send_email_verification_method_exists(): void
    {
        $this->assertTrue(method_exists($this->emailService, 'sendEmailVerification'));
    }

    /**
     * Test test connection method exists
     */
    public function test_test_connection_method_exists(): void
    {
        $this->assertTrue(method_exists($this->emailService, 'testConnection'));
    }
}