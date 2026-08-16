<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Core\Application;
use Core\Database;
use App\Models\Supplier;
use App\Models\Customer;

class CrmAndSupplierCrudTest extends TestCase
{
    private static $container;
    private $db;
    private Supplier $supplierModel;
    private Customer $customerModel;

    public static function setUpBeforeClass(): void
    {
        $app = Application::create();
        self::$container = $app->getContainer();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = self::$container->get(Database::class);
        $this->supplierModel = new Supplier($this->db);
        $this->customerModel = new Customer($this->db);
    }

    public function testSupplierLifecycleCrud(): void
    {
        // 1. Generate code
        $code = $this->supplierModel->generateCode();
        $this->assertStringStartsWith('PBF-', $code);

        // 2. Create supplier
        $supplierId = $this->supplierModel->createSupplier([
            'code' => $code,
            'name' => 'PT Kimia Farma Trading & Distribution',
            'contact_person' => 'Ahmad Fauzi',
            'phone' => '021-3847754',
            'email' => 'order@kftd.co.id',
            'address' => 'Jl. Budi Utomo No. 1, Jakarta Pusat',
            'is_active' => 1
        ]);

        $this->assertGreaterThan(0, $supplierId);

        // 3. Verify in list
        $found = $this->supplierModel->find($supplierId);
        $this->assertNotNull($found);
        $this->assertEquals('PT Kimia Farma Trading & Distribution', $found['name']);
        $this->assertEquals(1, $found['is_active']);

        // 4. Update supplier
        $updated = $this->supplierModel->updateSupplier($supplierId, [
            'name' => 'PT Kimia Farma Trading & Distribution (Pusat)',
            'phone' => '021-3847799',
            'is_active' => 1
        ]);
        $this->assertTrue($updated);

        $afterUpdate = $this->supplierModel->find($supplierId);
        $this->assertEquals('PT Kimia Farma Trading & Distribution (Pusat)', $afterUpdate['name']);
        $this->assertEquals('021-3847799', $afterUpdate['phone']);

        // 5. Search supplier
        $searchResults = $this->supplierModel->getAllSuppliers('Kimia Farma', 'active');
        $this->assertNotEmpty($searchResults);

        // 6. Delete / Deactivate supplier
        $deleted = $this->supplierModel->deleteSupplier($supplierId);
        $this->assertTrue($deleted);
    }

    public function testCustomerPatientCrmLifecycleCrud(): void
    {
        // 1. Generate patient code
        $code = $this->customerModel->generateCode();
        $this->assertStringStartsWith('CUST-', $code);

        // 2. Create customer / patient with drug allergy
        $custId = $this->customerModel->createCustomer([
            'code' => $code,
            'name' => 'Bpk. Ahmad Rian Syahputra',
            'phone' => '081299881122',
            'email' => 'ahmad.rian@test.com',
            'gender' => 'male',
            'birth_date' => '1990-08-15',
            'address' => 'Jl. Gatot Subroto No. 99, Jakarta',
            'allergy_notes' => 'Alergi Golongan Cephalosporin (Cefixime, Ceftriaxone)',
            'chronic_disease_notes' => 'Gastritis Kronis',
            'is_active' => 1
        ]);

        $this->assertGreaterThan(0, $custId);

        // 3. Verify customer in database
        $patient = $this->customerModel->find($custId);
        $this->assertNotNull($patient);
        $this->assertEquals('Bpk. Ahmad Rian Syahputra', $patient['name']);
        $this->assertStringContainsString('Cephalosporin', $patient['allergy_notes']);

        // 4. Update patient details
        $updated = $this->customerModel->updateCustomer($custId, [
            'name' => 'Bpk. Ahmad Rian Syahputra, S.T.',
            'phone' => '081299881199',
            'chronic_disease_notes' => 'Gastritis Kronis & GERD'
        ]);
        $this->assertTrue($updated);

        $afterUpdate = $this->customerModel->find($custId);
        $this->assertEquals('Bpk. Ahmad Rian Syahputra, S.T.', $afterUpdate['name']);
        $this->assertEquals('081299881199', $afterUpdate['phone']);
        $this->assertEquals('Gastritis Kronis & GERD', $afterUpdate['chronic_disease_notes']);

        // 5. CRM Aggregated Stats & Details
        $stats = $this->customerModel->getCrmStats();
        $this->assertGreaterThan(0, $stats['total_customers']);
        $this->assertGreaterThan(0, $stats['allergy_flagged']);

        $details = $this->customerModel->getDetails($custId);
        $this->assertNotNull($details);
        $this->assertArrayHasKey('sales_history', $details);
        $this->assertArrayHasKey('prescription_history', $details);

        // 6. Autocomplete search lookup
        $lookupResults = $this->customerModel->search('Ahmad Rian', 5);
        $this->assertNotEmpty($lookupResults);
        $this->assertContains($custId, array_column($lookupResults, 'id'));

        // 7. Delete patient
        $deleted = $this->customerModel->deleteCustomer($custId);
        $this->assertTrue($deleted);
    }
}
