<?php

namespace App\Controllers;

use Core\Controller;
use Core\Request;
use Core\Response;
use Core\Auth;
use App\Models\Report;

class ReportController extends Controller
{
    private Report $reportModel;
    private Auth $auth;

    public function __construct(Request $request, $container)
    {
        parent::__construct($request, $container);
        $this->reportModel = new Report();
        $this->auth = $container->get(Auth::class);
    }

    /**
     * Verify user access for financial & executive reports
     */
    private function checkAccess(): ?Response
    {
        if (!$this->auth->check()) {
            return $this->redirect('/login');
        }

        $user = $this->auth->user();
        $role = $user['role_name'] ?? 'cashier';

        // Superadmin, Owner, Pharmacist have report access
        if (!in_array($role, ['superadmin', 'owner', 'pharmacist'])) {
            return $this->redirect('/dashboard');
        }

        return null;
    }

    /**
     * Reports Hub Index
     */
    public function index(Request $request): Response
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $startDate = $request->get('start_date', date('Y-m-01'));
        $endDate = $request->get('end_date', date('Y-m-d'));

        $pl = $this->reportModel->getProfitLoss($startDate, $endDate);
        $valuation = $this->reportModel->getInventoryValuation();

        return $this->render('reports/index', [
            'title' => 'Financial & Operational Reports | MediCore ERP',
            'user' => $this->auth->user(),
            'pl' => $pl,
            'valuation' => $valuation,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }

    /**
     * Profit & Loss Statement (Laporan Laba Rugi Farmasi)
     */
    public function profitLoss(Request $request): Response
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $startDate = $request->get('start_date', date('Y-m-01'));
        $endDate = $request->get('end_date', date('Y-m-d'));

        $pl = $this->reportModel->getProfitLoss($startDate, $endDate);

        return $this->render('reports/profit_loss', [
            'title' => 'Profit & Loss Statement | MediCore ERP',
            'user' => $this->auth->user(),
            'pl' => $pl,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }

    /**
     * Sales Report per Payment Method & Cashier
     */
    public function salesSummary(Request $request): Response
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $startDate = $request->get('start_date', date('Y-m-01'));
        $endDate = $request->get('end_date', date('Y-m-d'));

        $paymentMethods = $this->reportModel->getSalesByPaymentMethod($startDate, $endDate);
        $cashiers = $this->reportModel->getSalesByCashier($startDate, $endDate);

        return $this->render('reports/sales', [
            'title' => 'Sales & Cashier Audit Report | MediCore ERP',
            'user' => $this->auth->user(),
            'paymentMethods' => $paymentMethods,
            'cashiers' => $cashiers,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }

    /**
     * Inventory Valuation Report (Valuasi Nilai Aset Stok FEFO)
     */
    public function inventoryValuation(Request $request): Response
    {
        if ($redirect = $this->checkAccess()) return $redirect;

        $valuation = $this->reportModel->getInventoryValuation();

        return $this->render('reports/inventory', [
            'title' => 'Inventory Asset Valuation Report | MediCore ERP',
            'user' => $this->auth->user(),
            'summary' => $valuation['summary'],
            'categoryBreakdown' => $valuation['category_breakdown'],
            'products' => $valuation['products']
        ]);
    }
}
