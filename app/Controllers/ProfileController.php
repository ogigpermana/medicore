<?php

namespace App\Controllers;

use Core\Controller;
use Core\Request;
use Core\Response;
use Core\Auth;
use App\Models\User;

/**
 * Profile Controller
 * Handles user profile management
 */

class ProfileController extends Controller
{
    private Auth $auth;
    private User $userModel;

    public function __construct(Request $request, \Core\Container $container)
    {
        parent::__construct($request, $container);
        // Dependencies will be resolved in methods to avoid constructor injection issues
    }

    /**
     * Show profile form
     */
    public function show(): Response
    {
        // Resolve dependencies
        $auth = new \Core\Auth($this->session);
        $userModel = new User();

        if (!$auth->check()) {
            return $this->redirect('/login');
        }

        $currentUser = $auth->user();
        $user = $userModel->findWithRole($currentUser['id']) ?? $currentUser;
        $role = strtolower($user['role_name'] ?? $user['role'] ?? 'pharmacist');

        $content = $this->view->render('profile.edit', [
            'user' => $user,
            'role' => $role,
            'activeMenu' => 'profile',
            'csrf_token' => $this->session->getCsrfToken()
        ]);
        
        return $this->response->setContent($content);
    }

    /**
     * Handle profile update
     */
    public function update(): Response
    {
        // Resolve dependencies
        $auth = new \Core\Auth($this->session);
        $userModel = new User();

        if (!$auth->check()) {
            return $this->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        try {
            $currentUser = $auth->user();
            $data = $this->request->all();

            // Validate input
            $validation = $this->validate($this->request, [
                'full_name' => 'required|min:3',
                'email' => 'required|email',
                'phone' => 'nullable|min:10|max:20'
            ]);

            if (!$validation['valid']) {
                return $this->json([
                    'success' => false,
                    'errors' => $validation['errors']
                ], 422);
            }

            // Check if email is being changed and if it's already taken
            if ($data['email'] !== $currentUser['email']) {
                $existingUser = $userModel->findByEmail($data['email']);
                if ($existingUser && $existingUser['id'] !== $currentUser['id']) {
                    return $this->json([
                        'success' => false,
                        'message' => 'Email already in use'
                    ], 400);
                }
            }

            // Update user profile
            $updateData = [
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null
            ];

            $updated = $userModel->update($currentUser['id'], $updateData);

            if ($updated) {
                // Update session data
                $this->session->set('user_email', $data['email']);
                $this->session->set('user_name', $data['full_name']);

                return $this->json([
                    'success' => true,
                    'message' => 'Profile updated successfully',
                    'redirect' => '/profile'
                ]);
            }

            return $this->json([
                'success' => false,
                'message' => 'Failed to update profile'
            ], 500);

        } catch (\Exception $e) {
            error_log('Profile update error: ' . $e->getMessage());
            
            return $this->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current user profile via API
     */
    public function getProfile(): Response
    {
        // Resolve dependencies
        $auth = new \Core\Auth($this->session);
        $userModel = new User();

        if (!$auth->check()) {
            return $this->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        try {
            $currentUser = $auth->user();
            $user = $userModel->findWithRole($currentUser['id']);

            if (!$user) {
                return $this->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Remove sensitive data
            unset($user['password']);
            unset($user['remember_token']);

            return $this->json([
                'success' => true,
                'data' => $user
            ]);

        } catch (\Exception $e) {
            error_log('Get profile error: ' . $e->getMessage());
            
            return $this->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show change password form
     */
    public function showChangePassword(): Response
    {
        // Resolve dependencies
        $auth = new \Core\Auth($this->session);

        if (!$auth->check()) {
            return $this->redirect('/login');
        }

        $userModel = new User();
        $currentUser = $auth->user();
        $user = $userModel->findWithRole($currentUser['id']) ?? $currentUser;
        $role = strtolower($user['role_name'] ?? $user['role'] ?? 'pharmacist');

        $content = $this->view->render('profile.change-password', [
            'user' => $user,
            'role' => $role,
            'activeMenu' => 'password',
            'csrf_token' => $this->session->getCsrfToken()
        ]);
        
        return $this->response->setContent($content);
    }

    /**
     * Handle password change
     */
    public function changePassword(): Response
    {
        // Resolve dependencies
        $auth = new \Core\Auth($this->session);
        $userModel = new User();

        if (!$auth->check()) {
            return $this->json([
                'success' => false,
                'message' => 'Authentication required'
            ], 401);
        }

        try {
            $currentUser = $auth->user();
            $data = $this->request->all();

            // Validate input
            $validation = $this->validate($this->request, [
                'current_password' => 'required',
                'new_password' => 'required|min:8',
                'password_confirmation' => 'required|same:new_password'
            ]);

            if (!$validation['valid']) {
                return $this->json([
                    'success' => false,
                    'errors' => $validation['errors']
                ], 422);
            }

            // Verify current password
            if (!$userModel->verifyPassword($currentUser['email'], $data['current_password'])) {
                return $this->json([
                    'success' => false,
                    'message' => 'Current password is incorrect'
                ], 400);
            }

            // Update password
            $updateData = [
                'password' => password_hash($data['new_password'], PASSWORD_BCRYPT)
            ];

            $updated = $userModel->update($currentUser['id'], $updateData);

            if ($updated) {
                // Log out user after password change for security
                $auth->logout();

                return $this->json([
                    'success' => true,
                    'message' => 'Password changed successfully. Please login with your new password.',
                    'redirect' => '/login'
                ]);
            }

            return $this->json([
                'success' => false,
                'message' => 'Failed to change password'
            ], 500);

        } catch (\Exception $e) {
            error_log('Change password error: ' . $e->getMessage());
            
            return $this->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
}