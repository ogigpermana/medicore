<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Core\Application;
use Core\Database;
use Core\Request;
use Core\Jwt;

class JwtAuthenticationTest extends TestCase
{
    private static $container;
    private $db;
    private Jwt $jwt;

    public static function setUpBeforeClass(): void
    {
        $app = Application::create();
        self::$container = $app->getContainer();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = self::$container->get(Database::class);
        $this->jwt = new Jwt();
    }

    public function testJwtTokenGenerationAndValidation(): void
    {
        $user = [
            'id' => 1,
            'email' => 'admin@medicore.id',
            'full_name' => 'Super Administrator',
            'role_name' => 'superadmin'
        ];

        // 1. Generate access token
        $token = $this->jwt->generateToken($user, 3600);
        $this->assertIsString($token);
        $this->assertNotEmpty($token);

        // 2. Validate access token
        $payload = $this->jwt->validateToken($token);
        $this->assertNotNull($payload);
        $this->assertEquals('1', $payload['sub']);
        $this->assertEquals('admin@medicore.id', $payload['user']['email']);
        $this->assertEquals('superadmin', $payload['user']['role']);

        // 3. Generate and validate refresh token
        $refreshToken = $this->jwt->generateRefreshToken($user, 604800);
        $this->assertIsString($refreshToken);

        $refreshPayload = $this->jwt->validateToken($refreshToken, true);
        $this->assertNotNull($refreshPayload);
        $this->assertEquals('1', $refreshPayload['sub']);

        // Access token should fail refresh token validation
        $this->assertNull($this->jwt->validateToken($token, true));
    }

    public function testApiLoginEndpoint(): void
    {
        $app = Application::create();
        $router = $app->getRouter();

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/api/auth/login';

        $req = new Request(
            ['email' => 'admin@medicore.com', 'password' => 'admin123'],
            ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
            'POST',
            '/api/auth/login'
        );
        $req->setContainer(self::$container);

        $res = $router->dispatch($req);
        $this->assertEquals(200, $res->getStatusCode());

        $data = json_decode($res->getContent(), true);
        $this->assertTrue($data['success']);
        $this->assertArrayHasKey('access_token', $data['data']);
        $this->assertArrayHasKey('refresh_token', $data['data']);
        $this->assertEquals('Bearer', $data['data']['token_type']);

        $token = $data['data']['access_token'];
        $refreshToken = $data['data']['refresh_token'];

        // Test GET /api/auth/me with Bearer token
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/api/auth/me';
        $_SERVER['HTTP_AUTHORIZATION'] = "Bearer {$token}";

        $meReq = new Request(
            [],
            ['Authorization' => "Bearer {$token}", 'Accept' => 'application/json'],
            'GET',
            '/api/auth/me'
        );
        $meReq->setContainer(self::$container);

        $meRes = $router->dispatch($meReq);
        $this->assertEquals(200, $meRes->getStatusCode());
        $meData = json_decode($meRes->getContent(), true);
        $this->assertTrue($meData['success']);
        $this->assertEquals('admin@medicore.com', $meData['data']['email']);

        // Test POST /api/auth/refresh
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/api/auth/refresh';

        $refReq = new Request(
            ['refresh_token' => $refreshToken],
            ['Accept' => 'application/json'],
            'POST',
            '/api/auth/refresh'
        );
        $refReq->setContainer(self::$container);

        $refRes = $router->dispatch($refReq);
        $this->assertEquals(200, $refRes->getStatusCode());
        $refData = json_decode($refRes->getContent(), true);
        $this->assertTrue($refData['success']);
        $this->assertArrayHasKey('access_token', $refData['data']);
    }

    public function testProtectedApiWithoutTokenReturns401(): void
    {
        $app = Application::create();
        $router = $app->getRouter();

        unset($_SERVER['HTTP_AUTHORIZATION']);
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/api/auth/me';

        $req = new Request(
            [],
            ['Accept' => 'application/json'],
            'GET',
            '/api/auth/me'
        );
        $req->setContainer(self::$container);

        $res = $router->dispatch($req);
        $this->assertEquals(401, $res->getStatusCode());
        $data = json_decode($res->getContent(), true);
        $this->assertFalse($data['success']);
    }
}
