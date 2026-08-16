<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Core\Application;
use Core\Auth;
use Core\Session;
use App\Controllers\ProfileController;
use Core\Request;
use Core\Response;

/**
 * Change Password Controller Tests
 */

class ChangePasswordTest extends TestCase
{
    private Application $app;
    private Auth $auth;
    private Session $session;
    private array $testUser;

    protected function setUp(): void
    {
        $this->app = Application::create();
        $this->auth = $this->app->getContainer()->get(Auth::class);
        $this->session = $this->app->getContainer()->get(Session::class);

        // Create test user data (without database)
        $this->testUser = [
            'id' => 1,
            'email' => 'passwordtest@example.com',
            'full_name' => 'Password Test User',
            'is_active' => true,
            'email_verified' => true,
            'role_name' => 'pharmacist'
        ];
    }

    protected function tearDown(): void
    {
        // Clean up session
        $this->session->clear();
    }

    /**
     * Test change password form without authentication
     */
    public function test_change_password_form_without_auth_redirects_to_login(): void
    {
        // Ensure no user is logged in
        $this->session->clear();

        $request = new Request();
        $request->setMethod('GET');
        $request->setUri('/change-password');
        $request->setContainer($this->app->getContainer());

        $response = new Response();
        $controller = new ProfileController($request, $this->app->getContainer());

        $result = $controller->showChangePassword();

        // Should redirect to login
        $headers = $result->getHeaders();
        $this->assertArrayHasKey('Location', $headers);
        $this->assertEquals('/login', $headers['Location']);
    }

    /**
     * Test change password form with authentication
     */
    public function test_change_password_form_with_auth_displays(): void
    {
        // Login test user
        $this->auth->login($this->testUser);

        $request = new Request();
        $request->setMethod('GET');
        $request->setUri('/change-password');
        $request->setContainer($this->app->getContainer());

        $response = new Response();
        $controller = new ProfileController($request, $this->app->getContainer());

        $result = $controller->showChangePassword();

        // Should return some content (view rendering)
        $this->assertIsString($result->getContent());
    }

    /**
     * Test change password without authentication
     */
    public function test_change_password_without_auth_returns_401(): void
    {
        // Ensure no user is logged in
        $this->session->clear();

        $request = new Request();
        $request->setMethod('POST');
        $request->setUri('/change-password');
        $request->setContainer($this->app->getContainer());
        $request->setData([
            'current_password' => 'oldpass123',
            'new_password' => 'newpass123',
            'password_confirmation' => 'newpass123'
        ]);

        $response = new Response();
        $controller = new ProfileController($request, $this->app->getContainer());

        $result = $controller->changePassword();

        // Should return 401 error
        $this->assertEquals(401, $result->getStatusCode());
    }

    /**
     * Test change password with password mismatch
     */
    public function test_change_password_with_password_mismatch_fails_validation(): void
    {
        // Login test user
        $this->auth->login($this->testUser);

        $request = new Request();
        $request->setMethod('POST');
        $request->setUri('/change-password');
        $request->setContainer($this->app->getContainer());
        $request->setData([
            'current_password' => 'oldpass123',
            'new_password' => 'newpass123',
            'password_confirmation' => 'differentpass'
        ]);

        $response = new Response();
        $controller = new ProfileController($request, $this->app->getContainer());

        $result = $controller->changePassword();

        // Should return validation error
        $this->assertEquals(422, $result->getStatusCode());
        
        $content = json_decode($result->getContent(), true);
        $this->assertFalse($content['success']);
        $this->assertArrayHasKey('errors', $content);
    }

    /**
     * Test change password with short password
     */
    public function test_change_password_with_short_password_fails_validation(): void
    {
        // Login test user
        $this->auth->login($this->testUser);

        $request = new Request();
        $request->setMethod('POST');
        $request->setUri('/change-password');
        $request->setContainer($this->app->getContainer());
        $request->setData([
            'current_password' => 'oldpass123',
            'new_password' => 'short',
            'password_confirmation' => 'short'
        ]);

        $response = new Response();
        $controller = new ProfileController($request, $this->app->getContainer());

        $result = $controller->changePassword();

        // Should return validation error
        $this->assertEquals(422, $result->getStatusCode());
        
        $content = json_decode($result->getContent(), true);
        $this->assertFalse($content['success']);
        $this->assertArrayHasKey('errors', $content);
    }
}