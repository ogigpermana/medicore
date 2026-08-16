<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Core\Application;
use Core\Database;
use Core\Response;
use Core\Request;
use App\Models\AuditLog;

class AuditLogAndSecurityTest extends TestCase
{
    private static $container;
    private $db;
    private AuditLog $auditModel;

    public static function setUpBeforeClass(): void
    {
        $app = Application::create();
        self::$container = $app->getContainer();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = self::$container->get(Database::class);
        $this->auditModel = new AuditLog($this->db);
    }

    public function testAuditLogRecordingAndPagination(): void
    {
        // 1. Record an audit event
        $logId = $this->auditModel->record(
            1,
            'pos_checkout',
            'Sale',
            999,
            'Completed POS cash sale of Rp 150.000',
            ['invoice' => 'INV-TEST-001', 'total' => 150000]
        );

        $this->assertGreaterThan(0, $logId);

        // 2. Fetch paginated logs
        $res = $this->auditModel->getPaginatedLogs(['action_type' => 'pos_checkout'], 1, 10);
        $this->assertArrayHasKey('items', $res);
        $this->assertArrayHasKey('total', $res);
        $this->assertGreaterThan(0, $res['total']);

        // 3. Check stats
        $stats = $this->auditModel->getStats();
        $this->assertArrayHasKey('total_events', $stats);
        $this->assertArrayHasKey('today_events', $stats);
        $this->assertGreaterThan(0, $stats['total_events']);
    }

    public function testSecurityHeaders(): void
    {
        $res = new Response('OK', 200);
        
        ob_start();
        $res->send();
        $output = ob_get_clean();

        $this->assertEquals('OK', $output);
        $headers = $res->getHeaders();

        $this->assertEquals('nosniff', $headers['X-Content-Type-Options']);
        $this->assertEquals('SAMEORIGIN', $headers['X-Frame-Options']);
        $this->assertEquals('1; mode=block', $headers['X-XSS-Protection']);
        $this->assertEquals('strict-origin-when-cross-origin', $headers['Referrer-Policy']);
    }

    public function testRouteNotFoundReturnsCustom404(): void
    {
        $app = Application::create();
        $router = $app->getRouter();

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/non-existent-page-test-404';

        $req = Request::capture();
        $req->setContainer(self::$container);
        $res = $router->dispatch($req);

        $this->assertEquals(404, $res->getStatusCode());
        $this->assertStringContainsString('Page Not Found', $res->getContent());
    }
}
