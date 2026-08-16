<?php

namespace Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Email Service
 * Handles email sending using PHPMailer
 */

class EmailService
{
    private PHPMailer $mailer;
    private array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->mailer = new PHPMailer(true);
        
        $this->configureMailer();
    }

    /**
     * Configure PHPMailer settings
     */
    private function configureMailer(): void
    {
        // Server settings
        $this->mailer->isSMTP();
        $this->mailer->Host = $this->config['host'] ?? 'smtp.gmail.com';
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $this->config['username'] ?? '';
        $this->mailer->Password = $this->config['password'] ?? '';
        $this->mailer->SMTPSecure = $this->config['encryption'] ?? PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = $this->config['port'] ?? 587;
        
        // Debug mode (disable in production)
        $this->mailer->SMTPDebug = $this->config['debug'] ?? 0;
        
        // Sender settings
        $this->mailer->setFrom(
            $this->config['from_email'] ?? 'noreply@medicore.com',
            $this->config['from_name'] ?? 'MediCore Pharmacy'
        );
        
        // Default reply-to
        $this->mailer->addReplyTo(
            $this->config['reply_to'] ?? $this->config['from_email'] ?? 'noreply@medicore.com',
            $this->config['reply_to_name'] ?? 'MediCore Support'
        );
        
        // Default charset
        $this->mailer->CharSet = 'UTF-8';
        
        // HTML emails
        $this->mailer->isHTML(true);
    }

    /**
     * Send email
     */
    public function send(array $data): bool
    {
        try {
            // Set recipient
            $this->mailer->addAddress($data['to'], $data['to_name'] ?? '');
            
            // Set subject
            $this->mailer->Subject = $data['subject'];
            
            // Set body
            $this->mailer->Body = $data['body'];
            
            // Set plain text version
            $this->mailer->AltBody = $data['alt_body'] ?? strip_tags($data['body']);
            
            // Send email
            $this->mailer->send();
            
            // Clear all addresses for next email
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();
            
            return true;
            
        } catch (Exception $e) {
            error_log('Email sending failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send welcome email
     */
    public function sendWelcomeEmail(string $email, string $name, string $verificationUrl = ''): bool
    {
        $subject = 'Welcome to MediCore Pharmacy';
        
        $body = $this->renderTemplate('welcome', [
            'name' => $name,
            'email' => $email,
            'verification_url' => $verificationUrl
        ]);
        
        return $this->send([
            'to' => $email,
            'to_name' => $name,
            'subject' => $subject,
            'body' => $body
        ]);
    }

    /**
     * Send password reset email
     */
    public function sendPasswordResetEmail(string $email, string $name, string $resetUrl): bool
    {
        $subject = 'Password Reset Request - MediCore Pharmacy';
        
        $body = $this->renderTemplate('password_reset', [
            'name' => $name,
            'email' => $email,
            'reset_url' => $resetUrl
        ]);
        
        return $this->send([
            'to' => $email,
            'to_name' => $name,
            'subject' => $subject,
            'body' => $body
        ]);
    }

    /**
     * Send email verification email
     */
    public function sendEmailVerification(string $email, string $name, string $verificationUrl): bool
    {
        $subject = 'Verify Your Email - MediCore Pharmacy';
        
        $body = $this->renderTemplate('email_verification', [
            'name' => $name,
            'email' => $email,
            'verification_url' => $verificationUrl
        ]);
        
        return $this->send([
            'to' => $email,
            'to_name' => $name,
            'subject' => $subject,
            'body' => $body
        ]);
    }

    /**
     * Render email template
     */
    private function renderTemplate(string $template, array $data): string
    {
        $templates = [
            'welcome' => $this->getWelcomeTemplate(),
            'password_reset' => $this->getPasswordResetTemplate(),
            'email_verification' => $this->getEmailVerificationTemplate()
        ];
        
        $templateContent = $templates[$template] ?? '';
        
        // Replace placeholders
        foreach ($data as $key => $value) {
            $templateContent = str_replace('{' . strtoupper($key) . '}', $value, $templateContent);
        }
        
        return $templateContent;
    }

    /**
     * Get welcome email template
     */
    private function getWelcomeTemplate(): string
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to MediCore</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
            <h1 style="color: white; margin: 0;"><i class="fas fa-hospital-alt"></i> MediCore</h1>
            <p style="color: white; margin: 10px 0 0 0;">Pharmacy Management System</p>
        </div>
        <div style="background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px;">
            <h2 style="color: #667eea;">Welcome to MediCore, {NAME}!</h2>
            <p>Thank you for registering with MediCore Pharmacy Management System.</p>
            <p>Your account has been created successfully with the email: <strong>{EMAIL}</strong></p>
            
            {VERIFICATION_SECTION}
            
            <p>If you have any questions, feel free to contact our support team.</p>
            
            <div style="text-align: center; margin-top: 30px;">
                <p style="color: #666; font-size: 12px;">This is an automated email. Please do not reply.</p>
            </div>
        </div>
    </div>
</body>
</html>';
    }

    /**
     * Get password reset template
     */
    private function getPasswordResetTemplate(): string
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
            <h1 style="color: white; margin: 0;"><i class="fas fa-key"></i> Password Reset</h1>
            <p style="color: white; margin: 10px 0 0 0;">MediCore Pharmacy</p>
        </div>
        <div style="background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px;">
            <h2 style="color: #667eea;">Password Reset Request</h2>
            <p>Hello {NAME},</p>
            <p>We received a request to reset your password for your MediCore account.</p>
            <p>Click the button below to reset your password:</p>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{RESET_URL}" style="background: #667eea; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block;">Reset Password</a>
            </div>
            
            <p style="color: #666; font-size: 14px;">This link will expire in 1 hour.</p>
            <p>If you did not request this password reset, please ignore this email.</p>
            
            <div style="text-align: center; margin-top: 30px;">
                <p style="color: #666; font-size: 12px;">This is an automated email. Please do not reply.</p>
            </div>
        </div>
    </div>
</body>
</html>';
    }

    /**
     * Get email verification template
     */
    private function getEmailVerificationTemplate(): string
    {
        return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
            <h1 style="color: white; margin: 0;"><i class="fas fa-envelope-check"></i> Email Verification</h1>
            <p style="color: white; margin: 10px 0 0 0;">MediCore Pharmacy</p>
        </div>
        <div style="background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px;">
            <h2 style="color: #667eea;">Verify Your Email</h2>
            <p>Hello {NAME},</p>
            <p>Thank you for registering with MediCore Pharmacy Management System.</p>
            <p>Please verify your email address by clicking the button below:</p>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{VERIFICATION_URL}" style="background: #667eea; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block;">Verify Email</a>
            </div>
            
            <p style="color: #666; font-size: 14px;">This link will expire in 24 hours.</p>
            <p>If you did not create this account, please ignore this email.</p>
            
            <div style="text-align: center; margin-top: 30px;">
                <p style="color: #666; font-size: 12px;">This is an automated email. Please do not reply.</p>
            </div>
        </div>
    </div>
</body>
</html>';
    }

    /**
     * Test email configuration
     */
    public function testConnection(): bool
    {
        try {
            return $this->mailer->getSMTPInstance()->connect();
        } catch (Exception $e) {
            error_log('Email connection test failed: ' . $e->getMessage());
            return false;
        }
    }
}