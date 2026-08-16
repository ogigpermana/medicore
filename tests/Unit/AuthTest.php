<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Core\Auth;
use Core\Session;

/**
 * Auth Test
 * Tests authentication functionality
 */

class AuthTest extends TestCase
{
    private Auth $auth;
    private Session $session;

    protected function setUp(): void
    {
        parent::setUp();
        $this->session = new Session(false); // Don't start session in tests
        $this->auth = new Auth($this->session);
    }

    public function testAuthInitiallyNotLoggedIn()
    {
        $this->assertFalse($this->auth->check());
    }

    public function testCanLoginUser()
    {
        $user = [
            'id' => 1,
            'email' => 'test@example.com',
            'full_name' => 'Test User',
            'role' => 'user'
        ];

        $this->auth->login($user);

        $this->assertTrue($this->auth->check());
        $this->assertEquals(1, $this->auth->id());
    }

    public function testCanGetLoggedInUser()
    {
        $user = [
            'id' => 1,
            'email' => 'test@example.com',
            'full_name' => 'Test User',
            'role' => 'user'
        ];

        $this->auth->login($user);
        $currentUser = $this->auth->user();

        $this->assertIsArray($currentUser);
        $this->assertEquals('test@example.com', $currentUser['email']);
        $this->assertEquals('Test User', $currentUser['name']);
    }

    public function testCanLogoutUser()
    {
        $user = [
            'id' => 1,
            'email' => 'test@example.com',
            'full_name' => 'Test User',
            'role' => 'user'
        ];

        $this->auth->login($user);
        $this->assertTrue($this->auth->check());

        $this->auth->logout();
        $this->assertFalse($this->auth->check());
    }

    public function testCanCheckUserRole()
    {
        $user = [
            'id' => 1,
            'email' => 'admin@example.com',
            'full_name' => 'Admin User',
            'role' => 'admin'
        ];

        $this->auth->login($user);
        $this->assertTrue($this->auth->hasRole('admin'));
        $this->assertFalse($this->auth->hasRole('user'));
    }

    public function testCanCheckPermissions()
    {
        $user = [
            'id' => 1,
            'email' => 'superadmin@example.com',
            'full_name' => 'Super Admin',
            'role' => 'superadmin'
        ];

        $this->auth->login($user);
        $this->assertTrue($this->auth->can('products.*'));
        $this->assertTrue($this->auth->can('sales.*'));
    }

    public function testRegularUserCannotAccessAdminFeatures()
    {
        $user = [
            'id' => 1,
            'email' => 'user@example.com',
            'full_name' => 'Regular User',
            'role' => 'user'
        ];

        $this->auth->login($user);
        $this->assertFalse($this->auth->can('products.delete'));
    }

    public function testLogoutRedirectsToLoginPageForStandardFormPost(): void
    {
        $app = \Core\Application::create();
        $container = $app->getContainer();
        $auth = $container->get(Auth::class);
        $auth->login(['id' => 1, 'email' => 'test@example.com', 'full_name' => 'Test User', 'role' => 'pharmacist']);

        $request = new \Core\Request();
        $request->setMethod('POST');
        $request->setUri('/logout');
        $request->setContainer($container);

        $controller = new \App\Controllers\AuthController($request, $container);
        $response = $controller->logout($request);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('/login', $response->getHeader('Location'));
        $this->assertFalse($auth->check());
    }

    public function testLogoutReturnsJsonForAjaxRequest(): void
    {
        $app = \Core\Application::create();
        $container = $app->getContainer();
        $auth = $container->get(Auth::class);
        $auth->login(['id' => 1, 'email' => 'test@example.com', 'full_name' => 'Test User', 'role' => 'pharmacist']);

        $request = new \Core\Request();
        $request->setMethod('POST');
        $request->setUri('/logout');
        $request->setHeaders([
            'Accept' => 'application/json',
            'X-Requested-With' => 'XMLHttpRequest'
        ]);
        $request->setContainer($container);

        $controller = new \App\Controllers\AuthController($request, $container);
        $response = $controller->logout($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('application/json', $response->getHeader('Content-Type'));

        $data = json_decode($response->getContent(), true);
        $this->assertTrue($data['success']);
        $this->assertEquals('/login', $data['redirect']);
    }
}