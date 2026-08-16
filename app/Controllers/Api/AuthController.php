<?php

namespace App\Controllers\Api;

use Core\Controller;
use Core\Request;
use Core\Response;
use Core\Jwt;
use Core\Database;
use App\Models\User;
use App\Models\AuditLog;

/**
 * Api\AuthController
 * Handles stateless REST API JWT login, refresh tokens, and user profile
 */
class AuthController extends Controller
{
    private User $userModel;
    private AuditLog $auditModel;
    private Jwt $jwt;
    private Database $db;

    public function __construct(Request $request, \Core\Container $container)
    {
        parent::__construct($request, $container);
        $this->db = $container->get(Database::class);
        $this->userModel = new User($this->db);
        $this->auditModel = new AuditLog($this->db);
        $this->jwt = new Jwt();
    }

    /**
     * API Login endpoint (Issue JWT Access & Refresh tokens)
     * POST /api/auth/login
     */
    public function login(Request $request): Response
    {
        $email = trim((string)$request->input('email'));
        $password = (string)$request->input('password');

        if (empty($email) || empty($password)) {
            return Response::json([
                'success' => false,
                'message' => 'Email and password are required'
            ], 422);
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            $this->auditModel->record(
                null,
                'api_login_failed',
                'User',
                null,
                "Failed API JWT login attempt for email: {$email}",
                ['email' => $email]
            );

            return Response::json([
                'success' => false,
                'message' => 'Invalid email or password'
            ], 401);
        }

        if (empty($user['is_active'])) {
            return Response::json([
                'success' => false,
                'message' => 'Account is inactive. Please contact administrator.'
            ], 403);
        }

        // Get user role
        $roleRow = $this->db->fetch(
            "SELECT r.name as role_name FROM user_roles ur JOIN roles r ON ur.role_id = r.id WHERE ur.user_id = ? LIMIT 1",
            [$user['id']]
        );
        $user['role_name'] = $roleRow['role_name'] ?? 'cashier';

        // Generate JWT Access & Refresh Tokens
        $accessToken = $this->jwt->generateToken($user, 3600);
        $refreshToken = $this->jwt->generateRefreshToken($user, 604800);

        // Record audit log
        $this->auditModel->record(
            (int)$user['id'],
            'api_login_success',
            'User',
            (int)$user['id'],
            "Successful API JWT authentication for {$user['full_name']}",
            ['role' => $user['role_name']]
        );

        return Response::json([
            'success' => true,
            'message' => 'Authentication successful',
            'data' => [
                'access_token' => $accessToken,
                'token_type' => 'Bearer',
                'expires_in' => 3600,
                'refresh_token' => $refreshToken,
                'user' => [
                    'id' => (int)$user['id'],
                    'email' => $user['email'],
                    'full_name' => $user['full_name'],
                    'role' => $user['role_name']
                ]
            ]
        ]);
    }

    /**
     * Refresh JWT token
     * POST /api/auth/refresh
     */
    public function refresh(Request $request): Response
    {
        $refreshToken = $request->input('refresh_token');

        if (empty($refreshToken)) {
            return Response::json([
                'success' => false,
                'message' => 'Refresh token is required'
            ], 422);
        }

        $payload = $this->jwt->validateToken($refreshToken, true);

        if (!$payload || empty($payload['sub'])) {
            return Response::json([
                'success' => false,
                'message' => 'Invalid or expired refresh token'
            ], 401);
        }

        $userId = (int)$payload['sub'];
        $user = $this->userModel->find($userId);

        if (!$user || empty($user['is_active'])) {
            return Response::json([
                'success' => false,
                'message' => 'User account no longer active'
            ], 403);
        }

        $roleRow = $this->db->fetch(
            "SELECT r.name as role_name FROM user_roles ur JOIN roles r ON ur.role_id = r.id WHERE ur.user_id = ? LIMIT 1",
            [$userId]
        );
        $user['role_name'] = $roleRow['role_name'] ?? 'cashier';

        $newAccessToken = $this->jwt->generateToken($user, 3600);

        return Response::json([
            'success' => true,
            'message' => 'Token refreshed successfully',
            'data' => [
                'access_token' => $newAccessToken,
                'token_type' => 'Bearer',
                'expires_in' => 3600
            ]
        ]);
    }

    /**
     * Get current authenticated user profile
     * GET /api/auth/me
     */
    public function me(Request $request): Response
    {
        $currentUser = $request->user();

        if (!$currentUser) {
            return Response::json([
                'success' => false,
                'message' => 'Unauthenticated'
            ], 401);
        }

        return Response::json([
            'success' => true,
            'data' => $currentUser
        ]);
    }
}
