<?php

namespace App\Models;

use Core\Model;
use Core\Database;
use PDO;

class Report extends Model
{
    public function __construct(?Database $db = null)
    {
        parent::__construct($db);
    }

    /**
     * Profit & Loss Statement (Laporan Laba Rugi Farmasi)
     */
    public function getProfitLoss(string $startDate, string $endDate): array
    {
        // 1. Total Sales Revenue & Discounts
        $salesSql = "SELECT 
                        COUNT(id) as transaction_count,
                        COALESCE(SUM(subtotal), 0) as gross_sales,
                        COALESCE(SUM(discount_amount), 0) as total_discounts,
                        COALESCE(SUM(tax_amount), 0) as total_tax,
                        COALESCE(SUM(total_amount), 0) as net_sales
                     FROM sales
                     WHERE status = 'completed' AND DATE(created_at) BETWEEN ? AND ?";
        $salesSummary = $this->db->fetch($salesSql, [$startDate, $endDate]) ?: [
            'transaction_count' => 0,
            'gross_sales' => 0,
            'total_discounts' => 0,
            'total_tax' => 0,
            'net_sales' => 0
        ];

        // 2. Cost of Goods Sold (COGS / HPP) from Sale Items
        $cogsSql = "SELECT 
                        COALESCE(SUM(si.quantity * p.buy_price), 0) as total_cogs
                    FROM sale_items si
                    JOIN sales s ON si.sale_id = s.id
                    JOIN products p ON si.product_id = p.id
                    WHERE s.status = 'completed' AND DATE(s.created_at) BETWEEN ? AND ?";
        $cogsData = $this->db->fetch($cogsSql, [$startDate, $endDate]);
        $totalCogs = (float)($cogsData['total_cogs'] ?? 0);

        // 3. Compounding Services (Tuslah & Embalase fees earned)
        $rxSql = "SELECT 
                    COALESCE(SUM(tuslah_fee), 0) as total_tuslah,
                    COALESCE(SUM(embalase_fee), 0) as total_embalase,
                    COUNT(id) as prescription_count
                  FROM prescriptions
                  WHERE status = 'dispensed' AND DATE(created_at) BETWEEN ? AND ?";
        $rxData = $this->db->fetch($rxSql, [$startDate, $endDate]) ?: [
            'total_tuslah' => 0,
            'total_embalase' => 0,
            'prescription_count' => 0
        ];

        $netSales = (float)$salesSummary['net_sales'];
        $grossProfit = $netSales - $totalCogs;
        $profitMargin = $netSales > 0 ? ($grossProfit / $netSales) * 100 : 0;

        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'transaction_count' => (int)$salesSummary['transaction_count'],
            'gross_sales' => (float)$salesSummary['gross_sales'],
            'total_discounts' => (float)$salesSummary['total_discounts'],
            'total_tax' => (float)$salesSummary['total_tax'],
            'net_sales' => $netSales,
            'total_cogs' => $totalCogs,
            'gross_profit' => $grossProfit,
            'profit_margin' => $profitMargin,
            'total_tuslah' => (float)$rxData['total_tuslah'],
            'total_embalase' => (float)$rxData['total_embalase'],
            'prescription_count' => (int)$rxData['prescription_count']
        ];
    }

    /**
     * Sales breakdown by Payment Method
     */
    public function getSalesByPaymentMethod(string $startDate, string $endDate): array
    {
        $sql = "SELECT 
                    payment_method,
                    COUNT(id) as total_transactions,
                    COALESCE(SUM(total_amount), 0) as total_amount
                FROM sales
                WHERE status = 'completed' AND DATE(created_at) BETWEEN ? AND ?
                GROUP BY payment_method
                ORDER BY total_amount DESC";
        return $this->db->query($sql, [$startDate, $endDate]);
    }

    /**
     * Sales breakdown by Cashier Staff
     */
    public function getSalesByCashier(string $startDate, string $endDate): array
    {
        $sql = "SELECT 
                    u.id as user_id,
                    u.full_name as cashier_name,
                    COUNT(s.id) as total_transactions,
                    COALESCE(SUM(s.total_amount), 0) as total_sales
                FROM sales s
                JOIN users u ON s.user_id = u.id
                WHERE s.status = 'completed' AND DATE(s.created_at) BETWEEN ? AND ?
                GROUP BY u.id, u.full_name
                ORDER BY total_sales DESC";
        return $this->db->query($sql, [$startDate, $endDate]);
    }

    /**
     * Inventory Valuation Report (Valuasi Nilai Aset Stok FEFO)
     */
    public function getInventoryValuation(): array
    {
        // 1. Overall Valuation Summary
        $sumSql = "SELECT 
                        COUNT(p.id) as total_products,
                        COALESCE(SUM(p.stock_quantity), 0) as total_units_in_stock,
                        COALESCE(SUM(p.stock_quantity * p.buy_price), 0) as total_asset_buy_value,
                        COALESCE(SUM(p.stock_quantity * p.sell_price), 0) as total_potential_retail_value
                   FROM products p
                   WHERE p.is_active = 1";
        $summary = $this->db->fetch($sumSql) ?: [
            'total_products' => 0,
            'total_units_in_stock' => 0,
            'total_asset_buy_value' => 0,
            'total_potential_retail_value' => 0
        ];

        // 2. Valuation breakdown per category
        $catSql = "SELECT 
                        c.name as category_name,
                        COUNT(p.id) as product_count,
                        COALESCE(SUM(p.stock_quantity), 0) as stock_qty,
                        COALESCE(SUM(p.stock_quantity * p.buy_price), 0) as category_buy_value,
                        COALESCE(SUM(p.stock_quantity * p.sell_price), 0) as category_sell_value
                   FROM categories c
                   LEFT JOIN products p ON p.category_id = c.id AND p.is_active = 1
                   GROUP BY c.id, c.name
                   ORDER BY category_buy_value DESC";
        $categoryBreakdown = $this->db->query($catSql);

        // 3. Product-level high value assets
        $prodSql = "SELECT 
                        p.id, p.name, p.sku, p.stock_quantity, p.buy_price, p.sell_price,
                        u.symbol as unit_symbol, c.name as category_name,
                        (p.stock_quantity * p.buy_price) as total_buy_value,
                        (p.stock_quantity * p.sell_price) as total_sell_value,
                        (SELECT COUNT(*) FROM batches WHERE product_id = p.id AND is_active = 1 AND current_quantity > 0) as active_batches_count
                    FROM products p
                    LEFT JOIN units u ON p.unit_id = u.id
                    LEFT JOIN categories c ON p.category_id = c.id
                    WHERE p.is_active = 1
                    ORDER BY total_buy_value DESC";
        $products = $this->db->query($prodSql);

        return [
            'summary' => $summary,
            'category_breakdown' => $categoryBreakdown,
            'products' => $products
        ];
    }

    /**
     * BPOM Regulatory Compliance Report (SIPNAP - Precursor & OOT Dispensing Log)
     */
    public function getRegulatoryComplianceReport(string $startDate, string $endDate): array
    {
        $sql = "SELECT 
                    rx.prescription_number,
                    rx.created_at as dispensed_date,
                    rx.patient_name,
                    rx.patient_age,
                    rx.doctor_name,
                    rx.doctor_sip,
                    rx.doctor_clinic,
                    rx.diagnosis,
                    p.name as medication_name,
                    p.sku,
                    rxi.quantity as quantity_dispensed,
                    u.symbol as unit_symbol,
                    c.name as category_name,
                    phar.full_name as pharmacist_name,
                    rx.pharmacist_notes
                FROM prescriptions rx
                JOIN prescription_items rxi ON rxi.prescription_id = rx.id
                JOIN products p ON rxi.product_id = p.id
                LEFT JOIN units u ON p.unit_id = u.id
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN users phar ON rx.pharmacist_id = phar.id
                WHERE rx.status = 'dispensed'
                  AND DATE(rx.created_at) BETWEEN ? AND ?
                ORDER BY rx.created_at DESC";
        return $this->db->query($sql, [$startDate, $endDate]);
    }
}
