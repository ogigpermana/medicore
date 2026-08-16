<?php

namespace App\Controllers;

use Core\Controller;
use Core\Request;
use Core\Response;
use Core\Auth;
use Core\RateLimiter;
use Core\Application;
use Core\AuditLogger;
use App\Models\User;

/**
 * Auth Controller
 * Handles authentication operations
 */

class AuthController extends Controller
{
    private Auth $auth;
    private User $userModel;
    private RateLimiter $rateLimiter;
    private AuditLogger $auditLogger;
    private \Core\RememberMe $rememberMe;
    private \Core\EmailService $emailService;

    public function __construct(Request $request, \Core\Container $container)
    {
        parent::__construct($request, $container);
        // Initialize dependencies directly to avoid container issues
        $this->auth = new \Core\Auth($this->session);
        $this->userModel = new \App\Models\User();
        $this->rateLimiter = new \Core\RateLimiter(5, 15);
        
        // Get database config directly
        $dbConfig = require __DIR__ . '/../../config/database.php';
        $database = new \Core\Database($dbConfig);
        
        $this->auditLogger = new \Core\AuditLogger($database);
        $this->rememberMe = new \Core\RememberMe($database, $this->session);
        
        $emailConfig = require __DIR__ . '/../../config/email.php';
        $this->emailService = new \Core\EmailService($emailConfig);
    }

    /**
     * Show login form
     */
    public function showLogin(Request $request): Response
    {
        $content = $this->view->render('auth.login', [
            'csrf_token' => $this->csrfToken()
        ]);
        
        return $this->response->setContent($content);
    }

    /**
     * Handle login request
     */
    public function login(Request $request): Response
    {
        try {
            $credentials = $this->request->all();
            $ip = $this->request->header('X-Forwarded-For') ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $rateLimitKey = 'login:' . $ip . ':' . ($credentials['email'] ?? 'unknown');

            // Check rate limit
            if ($this->rateLimiter->attempt($rateLimitKey)) {
                $secondsRemaining = $this->rateLimiter->availableIn($rateLimitKey);
                return $this->json([
                    'success' => false,
                    'message' => 'Too many login attempts. Please try again in ' . ceil($secondsRemaining / 60) . ' minutes.',
                    'retry_after' => $secondsRemaining
                ], 429);
            }

            // Validate input
            $validation = $this->validate($this->request, [
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if (!$validation['valid']) {
                return $this->json([
                    'success' => false,
                    'errors' => $validation['errors']
                ], 422);
            }

            // Attempt login
            $user = $this->userModel->findByEmailWithRole($credentials['email']);

            if (!$user) {
                // Log failed login attempt
                $this->auditLogger->logAuthEvent('failed_login', [
                    'email' => $credentials['email'],
                    'ip' => $ip
                ]);
                
                $remaining = $this->rateLimiter->remaining($rateLimitKey);
                return $this->json([
                    'success' => false,
                    'message' => 'Invalid credentials',
                    'remaining_attempts' => $remaining
                ], 401);
            }

            // Check if account is locked
            if ($this->userModel->isLocked($user['id'])) {
                $lockTimeRemaining = $this->userModel->getLockTimeRemaining($user['id']);
                return $this->json([
                    'success' => false,
                    'message' => 'Account is temporarily locked. Please try again in ' . ceil($lockTimeRemaining / 60) . ' minutes.',
                    'locked_until' => $lockTimeRemaining
                ], 423);
            }

            if (!$this->userModel->verifyPassword($credentials['email'], $credentials['password'])) {
                // Increment failed attempts
                $this->userModel->incrementFailedAttempts($user['id']);
                
                $remaining = $this->rateLimiter->remaining($rateLimitKey);
                return $this->json([
                    'success' => false,
                    'message' => 'Invalid credentials',
                    'remaining_attempts' => $remaining
                ], 401);
            }

            if (!$user['is_active']) {
                return $this->json([
                    'success' => false,
                    'message' => 'Account is deactivated'
                ], 403);
            }

            // Clear rate limit on successful login
            $this->rateLimiter->clear($rateLimitKey);

            // Login user
            $this->auth->login($user);
            
            // Set user ID for audit logging
            $this->auditLogger->setUserId($user['id']);
            
            // Log successful login
            $this->auditLogger->logAuthEvent('login', [
                'email' => $user['email'],
                'ip' => $ip
            ]);
            
            // Update last login
            $this->userModel->updateLastLogin($user['id']);

            // Handle remember me
            if (isset($credentials['remember']) && $credentials['remember'] === 'true') {
                $this->rememberMe->createToken($user['id']);
            }

            return $this->json([
                'success' => true,
                'message' => 'Login successful',
                'redirect' => '/dashboard'
            ]);
        } catch (\Exception $e) {
            // Log error
            error_log('Login error: ' . $e->getMessage());
            
            return $this->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show registration form
     */
    public function showRegister(Request $request): Response
    {
        // Initialize auth
        $this->auth = $this->container->get(\Core\Auth::class);

        if ($this->auth->check()) {
            return $this->redirect('/dashboard');
        }

        $content = $this->view->render('auth.register', [
            'csrf_token' => $this->csrfToken()
        ]);
        
        return $this->response->setContent($content);
    }

    /**
     * Handle registration request
     */
    public function register(Request $request): Response
    {
        // Initialize dependencies
        $this->userModel = $this->container->get(\App\Models\User::class);

        $data = $this->request->all();

        // Validate input
        $validation = $this->validate($this->request, [
            'email' => 'required|email',
            'password' => 'required|min:8',
            'full_name' => 'required|min:2',
            'password_confirmation' => 'required|confirmed:password'
        ]);

        if (!$validation['valid']) {
            return $this->json([
                'success' => false,
                'errors' => $validation['errors']
            ], 422);
        }

        // Check if email already exists
        if ($this->userModel->findByEmail($data['email'])) {
            return $this->json([
                'success' => false,
                'message' => 'Email already registered'
            ], 409);
        }

        // Remove password confirmation and role from user fields
        unset($data['password_confirmation']);
        $roleName = $data['role'] ?? 'pharmacist';
        unset($data['role']);

        try {
            // Create user
            $userId = $this->userModel->register($data);

            // Auto-verify and activate user for immediate access
            $db = $this->container->get(\Core\Database::class);
            $db->query("UPDATE users SET email_verified = 1, email_verified_at = NOW(), is_active = 1 WHERE id = ?", [$userId]);

            // Assign role
            $role = $db->fetch("SELECT id FROM roles WHERE name = ? LIMIT 1", [$roleName]);
            if ($role) {
                $db->query("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)", [$userId, $role['id']]);
            } else {
                $defaultRole = $db->fetch("SELECT id FROM roles WHERE name = 'pharmacist' LIMIT 1");
                if ($defaultRole) {
                    $db->query("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)", [$userId, $defaultRole['id']]);
                }
            }

            // Automatically log in the user
            $newUser = $this->userModel->findWithRole($userId);
            if ($newUser) {
                $this->auth->login($newUser);
            }

            return $this->json([
                'success' => true,
                'message' => 'Registration successful! Welcome to MediCore.',
                'redirect' => '/dashboard'
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Registration failed'
            ], 500);
        }
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request): Response
    {
        $currentUser = $this->auth->user();
        
        // Log logout event
        if ($currentUser) {
            $this->auditLogger->setUserId($currentUser['id']);
            $this->auditLogger->logAuthEvent('logout', [
                'email' => $currentUser['email']
            ]);
        }

        $this->auth->logout();

        // Check if request is AJAX / expecting JSON
        $accept = $request->header('Accept') ?? '';
        $xReq = $request->header('X-Requested-With') ?? '';
        if (str_contains($accept, 'application/json') || $xReq === 'XMLHttpRequest') {
            return $this->json([
                'success' => true,
                'message' => 'Logout successful',
                'redirect' => '/login'
            ]);
        }

        return $this->redirect('/login');
    }

    /**
     * Show forgot password form
     */
    public function showForgotPassword(Request $request): Response
    {
        $content = $this->view->render('auth.forgot-password', [
            'csrf_token' => $this->csrfToken()
        ]);
        
        return $this->response->setContent($content);
    }

    /**
     * Send password reset email
     */
    public function sendPasswordReset(Request $request): Response
    {
        try {
            $data = $this->request->all();

            // Validate input
            $validation = $this->validate($this->request, [
                'email' => 'required|email'
            ]);

            if (!$validation['valid']) {
                return $this->json([
                    'success' => false,
                    'errors' => $validation['errors']
                ], 422);
            }

            // Check if user exists
            $user = $this->userModel->findByEmail($data['email']);
            
            if (!$user) {
                // Don't reveal if user exists for security
                return $this->json([
                    'success' => true,
                    'message' => 'If an account exists with this email, a password reset link has been sent.'
                ]);
            }

            // Generate reset token
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Store token in database
            $sql = "INSERT INTO password_resets (email, token, expires_at) 
                    VALUES (:email, :token, :expires_at)";
            
            $this->container->get(\Core\Database::class)->query($sql, [
                'email' => $data['email'],
                'token' => $token,
                'expires_at' => $expires
            ]);

            // Generate reset URL
            $resetUrl = getenv('APP_URL') . '/reset-password/' . $token;

            // Initialize email service
            if ($this->emailService === null) {
                $emailConfig = require __DIR__ . '/../../config/email.php';
                $this->emailService = new \Core\EmailService($emailConfig);
            }

            // Send email
            $emailSent = $this->emailService->sendPasswordResetEmail(
                $data['email'],
                $user['full_name'],
                $resetUrl
            );

            if ($emailSent) {
                // Log password reset request
                $this->auditLogger->setUserId($user['id']);
                $this->auditLogger->logAuthEvent('password_reset_requested', [
                    'email' => $data['email']
                ]);

                return $this->json([
                    'success' => true,
                    'message' => 'Password reset link has been sent to your email.'
                ]);
            }

            return $this->json([
                'success' => false,
                'message' => 'Failed to send password reset email. Please try again.'
            ], 500);

        } catch (\Exception $e) {
            error_log('Password reset error: ' . $e->getMessage());
            
            return $this->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show reset password form
     */
    public function showResetPassword(Request $request): Response
    {
        $token = $request->param('token');
        
        // Validate token
        $sql = "SELECT * FROM password_resets 
                WHERE token = :token 
                AND expires_at > NOW() 
                LIMIT 1";
        
        $db = $this->container->get(\Core\Database::class);
        $result = $db->query($sql, ['token' => $token]);

        if (empty($result)) {
            return $this->redirect('/forgot-password');
        }

        $content = $this->view->render('auth.reset-password', [
            'token' => $token,
            'csrf_token' => $this->csrfToken()
        ]);
        
        return $this->response->setContent($content);
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request): Response
    {
        $token = $request->param('token');
        
        try {
            $data = $this->request->all();

            // Validate input
            $validation = $this->validate($this->request, [
                'password' => 'required|min:8',
                'password_confirmation' => 'required|same:password'
            ]);

            if (!$validation['valid']) {
                return $this->json([
                    'success' => false,
                    'errors' => $validation['errors']
                ], 422);
            }

            // Validate token
            $sql = "SELECT * FROM password_resets 
                    WHERE token = :token 
                    AND expires_at > NOW() 
                    LIMIT 1";
            
            $db = $this->container->get(\Core\Database::class);
            $result = $db->query($sql, ['token' => $token]);

            if (empty($result)) {
                return $this->json([
                    'success' => false,
                    'message' => 'Invalid or expired reset token.'
                ], 400);
            }

            $resetRequest = $result[0];

            // Get user
            $user = $this->userModel->findByEmail($resetRequest['email']);
            
            if (!$user) {
                return $this->json([
                    'success' => false,
                    'message' => 'User not found.'
                ], 404);
            }

            // Update password
            $updateData = [
                'password' => password_hash($data['password'], PASSWORD_BCRYPT)
            ];

            $updated = $this->userModel->update($user['id'], $updateData);

            if ($updated) {
                // Delete used token
                $db->query("DELETE FROM password_resets WHERE token = :token", ['token' => $token]);

                // Log password reset
                $this->auditLogger->setUserId($user['id']);
                $this->auditLogger->logAuthEvent('password_reset_completed', [
                    'email' => $user['email']
                ]);

                return $this->json([
                    'success' => true,
                    'message' => 'Password has been reset successfully. Please login with your new password.',
                    'redirect' => '/login'
                ]);
            }

            return $this->json([
                'success' => false,
                'message' => 'Failed to reset password.'
            ], 500);

        } catch (\Exception $e) {
            error_log('Password reset error: ' . $e->getMessage());
            
            return $this->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current authenticated user
     */
    public function me(Request $request): Response
    {
        if (!$this->auth->check()) {
            return $this->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        return $this->json([
            'success' => true,
            'data' => $this->auth->user()
        ]);
    }

    /**
     * Verify email
     */
    public function verifyEmail(Request $request): Response
    {
        $token = $request->param('token');
        
        try {
            // Validate token
            $sql = "SELECT * FROM users 
                    WHERE email_verification_token = :token 
                    AND email_verification_expires > NOW() 
                    LIMIT 1";
            
            $db = $this->container->get(\Core\Database::class);
            $result = $db->query($sql, ['token' => $token]);

            if (empty($result)) {
                return $this->redirect('/login');
            }

            $user = $result[0];

            // Mark email as verified
            $updateData = [
                'email_verified' => true,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'email_verification_token' => null,
                'email_verification_expires' => null
            ];

            $updated = $this->userModel->update($user['id'], $updateData);

            if ($updated) {
                // Log email verification
                $this->auditLogger->setUserId($user['id']);
                $this->auditLogger->logAuthEvent('email_verified', [
                    'email' => $user['email']
                ]);

                return $this->json([
                    'success' => true,
                    'message' => 'Email verified successfully. You can now login.',
                    'redirect' => '/login'
                ]);
            }

            return $this->json([
                'success' => false,
                'message' => 'Failed to verify email.'
            ], 500);

        } catch (\Exception $e) {
            error_log('Email verification error: ' . $e->getMessage());
            
            return $this->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Resend verification email
     */
    public function resendVerification(Request $request): Response
    {
        if (!$this->auth->check()) {
            return $this->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        try {
            $currentUser = $this->auth->user();
            
            // Get user from database
            $user = $this->userModel->find($currentUser['id']);
            
            if (!$user) {
                return $this->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Check if already verified
            if ($user['email_verified']) {
                return $this->json([
                    'success' => false,
                    'message' => 'Email is already verified'
                ], 400);
            }

            // Generate new verification token
            $verificationToken = bin2hex(random_bytes(32));
            $verificationExpires = date('Y-m-d H:i:s', strtotime('+24 hours'));

            // Update verification token
            $sql = "UPDATE users 
                    SET email_verification_token = :token, 
                        email_verification_expires = :expires 
                    WHERE id = :id";
            
            $db = $this->container->get(\Core\Database::class);
            $db->query($sql, [
                'token' => $verificationToken,
                'expires' => $verificationExpires,
                'id' => $user['id']
            ]);

            // Generate verification URL
            $verificationUrl = getenv('APP_URL') . '/verify-email/' . $verificationToken;

            // Initialize email service
            if ($this->emailService === null) {
                $emailConfig = require __DIR__ . '/../../config/email.php';
                $this->emailService = new \Core\EmailService($emailConfig);
            }

            // Send verification email
            $emailSent = $this->emailService->sendEmailVerification(
                $user['email'],
                $user['full_name'],
                $verificationUrl
            );

            if ($emailSent) {
                return $this->json([
                    'success' => true,
                    'message' => 'Verification email has been sent.'
                ]);
            }

            return $this->json([
                'success' => false,
                'message' => 'Failed to send verification email.'
            ], 500);

        } catch (\Exception $e) {
            error_log('Resend verification error: ' . $e->getMessage());
            
            return $this->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
}