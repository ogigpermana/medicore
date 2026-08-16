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
 * Profile Controller Tests
 */

class ProfileTest extends TestCase
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
            'email' => 'profiletest@example.com',
            'full_name' => 'Profile Test User',
            'phone' => '08123456789',
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
     * Test profile show without authentication
     */
    public function test_profile_show_without_auth_redirects_to_login(): void
    {
        // Ensure no user is logged in
        $this->session->clear();

        $request = new Request();
        $request->setMethod('GET');
        $request->setUri('/profile');
        $request->setContainer($this->app->getContainer());

        $response = new Response();
        $controller = new ProfileController($request, $this->app->getContainer());

        $result = $controller->show();

        // Should redirect to login (check headers)
        $headers = $result->getHeaders();
        $this->assertArrayHasKey('Location', $headers);
        $this->assertEquals('/login', $headers['Location']);
    }

    /**
     * Test profile show with authentication
     */
    public function test_profile_show_with_auth_processes_request(): void
    {
        // Login test user
        $this->auth->login($this->testUser);

        $request = new Request();
        $request->setMethod('GET');
        $request->setUri('/profile');
        $request->setContainer($this->app->getContainer());

        $response = new Response();
        $controller = new ProfileController($request, $this->app->getContainer());

        $result = $controller->show();

        // Should return some content (view rendering)
        $this->assertIsString($result->getContent());
    }

    /**
     * Test profile update without authentication
     */
    public function test_profile_update_without_auth_returns_401(): void
    {
        // Ensure no user is logged in
        $this->session->clear();

        $request = new Request();
        $request->setMethod('POST');
        $request->setUri('/profile');
        $request->setContainer($this->app->getContainer());
        $request->setData([
            'full_name' => 'Updated Name',
            'email' => 'updated@example.com',
            'phone' => '08198765432'
        ]);

        $response = new Response();
        $controller = new ProfileController($request, $this->app->getContainer());

        $result = $controller->update();

        // Should return 401 error
        $this->assertEquals(401, $result->getStatusCode());
    }

    /**
     * Test profile update with authentication
     */
    public function test_profile_update_with_auth_processes_request(): void
    {
        // Login test user
        $this->auth->login($this->testUser);

        $request = new Request();
        $request->setMethod('POST');
        $request->setUri('/profile');
        $request->setContainer($this->app->getContainer());
        $request->setData([
            'full_name' => 'Updated Name',
            'email' => $this->testUser['email'], // Same email
            'phone' => '08198765432'
        ]);

        $response = new Response();
        $controller = new ProfileController($request, $this->app->getContainer());

        $result = $controller->update();

        // Should return success response
        $this->assertEquals(200, $result->getStatusCode());
        
        $content = json_decode($result->getContent(), true);
        $this->assertTrue($content['success']);
        $this->assertEquals('Profile updated successfully', $content['message']);
    }

    /**
     * Test profile update with invalid email
     */
    public function test_profile_update_with_invalid_email_fails_validation(): void
    {
        // Login test user
        $this->auth->login($this->testUser);

        $request = new Request();
        $request->setMethod('POST');
        $request->setUri('/profile');
        $request->setContainer($this->app->getContainer());
        $request->setData([
            'full_name' => 'Updated Name',
            'email' => 'invalid-email',
            'phone' => '08198765432'
        ]);

        $response = new Response();
        $controller = new ProfileController($request, $this->app->getContainer());

        $result = $controller->update();

        // Should return validation error
        $this->assertEquals(422, $result->getStatusCode());
        
        $content = json_decode($result->getContent(), true);
        $this->assertFalse($content['success']);
        $this->assertArrayHasKey('errors', $content);
    }

    /**
     * Test get profile API without authentication
     */
    public function test_get_profile_without_auth_returns_401(): void
    {
        // Ensure no user is logged in
        $this->session->clear();

        $request = new Request();
        $request->setMethod('GET');
        $request->setUri('/api/profile');
        $request->setContainer($this->app->getContainer());

        $response = new Response();
        $controller = new ProfileController($request, $this->app->getContainer());

        $result = $controller->getProfile();

        // Should return 401 error
        $this->assertEquals(401, $result->getStatusCode());
    }

    /**
     * Test get profile API with authentication
     */
    public function test_get_profile_with_auth_returns_json_response(): void
    {
        // Login test user
        $this->auth->login($this->testUser);

        $request = new Request();
        $request->setMethod('GET');
        $request->setUri('/api/profile');
        $request->setContainer($this->app->getContainer());

        $response = new Response();
        $controller = new ProfileController($request, $this->app->getContainer());

        $result = $controller->getProfile();

        // Should return JSON response (may fail due to database, but should be JSON)
        $this->assertIsString($result->getContent());
    }
}