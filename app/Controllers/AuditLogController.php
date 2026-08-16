<?php

namespace App\Controllers;

use Core\Controller;
use Core\Request;
use Core\Response;
use Core\Auth;
use App\Models\AuditLog;
use App\Models\User;

/**
 * AuditLog Controller
 * Visual enterprise audit trail and security compliance viewer
 */
class AuditLogController extends Controller
{
    private AuditLog $auditModel;
    private User $userModel;
    private Auth $auth;

    public function __construct(Request $request, \Core\Container $container)
    {
        parent::__construct($request, $container);
        $this->auditModel = new AuditLog();
        $this->userModel = new User();
        $this->auth = $container->get(Auth::class);
    }

    private function checkAccess(): ?Response
    {
        if (!$this->auth->check()) {
            return $this->redirect('/login');
        }

        $user = $this->auth->user();
        $role = strtolower($user['role_name'] ?? $user['role'] ?? 'cashier');

        // Only Superadmin and Pharmacy Owner can view audit trails
        if (!in_array($role, ['superadmin', 'owner'])) {
            return $this->redirect('/dashboard');
        }

        return null;
    }

    /**
     * Audit Trail Viewer Dashboard
     */
    public function index(Request $request): Response
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $filters = [
            'search' => $request->get('search'),
            'action_type' => $request->get('action_type', 'all'),
            'user_id' => $request->get('user_id'),
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date')
        ];

        $page = (int)$request->get('page', 1);
        $perPage = (int)$request->get('per_page', 25);

        $paginated = $this->auditModel->getPaginatedLogs($filters, $page, $perPage);
        $stats = $this->auditModel->getStats();
        $users = $this->userModel->all();

        if ($request->header('Accept') === 'application/json' || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return $this->json([
                'success' => true,
                'data' => $paginated['items'],
                'pagination' => $paginated,
                'stats' => $stats
            ]);
        }

        return $this->render('audit_logs/index', [
            'title' => 'Security Audit Trail & Compliance Logs | MediCore ERP',
            'user' => $this->auth->user(),
            'logs' => $paginated['items'],
            'pagination' => $paginated,
            'stats' => $stats,
            'users' => $users,
            'filters' => $filters
        ]);
    }

    /**
     * Export Audit Trail to CSV
     */
    public function export(Request $request): Response
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $filters = [
            'search' => $request->get('search'),
            'action_type' => $request->get('action_type', 'all'),
            'user_id' => $request->get('user_id'),
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date')
        ];

        // Fetch up to 2000 matching logs for export
        $paginated = $this->auditModel->getPaginatedLogs($filters, 1, 2000);
        $logs = $paginated['items'];

        $csvHeader = ['Log ID', 'Timestamp', 'User Email', 'User Name', 'Role', 'Action', 'Entity Type', 'Entity ID', 'Description', 'IP Address', 'Metadata'];
        $output = fopen('php://temp', 'r+');
        fputcsv($output, $csvHeader);

        foreach ($logs as $l) {
            fputcsv($output, [
                $l['id'],
                $l['created_at'],
                $l['user_email'] ?? 'System/Guest',
                $l['user_name'] ?? 'System',
                $l['role_name'] ?? 'system',
                $l['action'],
                $l['entity_type'],
                $l['entity_id'] ?? '-',
                $l['description'],
                $l['ip_address'],
                $l['metadata'] ?? ''
            ]);
        }

        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        $filename = 'medicore_audit_logs_' . date('Ymd_His') . '.csv';

        return new Response($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\""
        ]);
    }
}
