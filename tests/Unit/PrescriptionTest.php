<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Core\Application;
use Core\Database;
use Core\Request;
use App\Models\Prescription;
use App\Models\Product;
use App\Controllers\PrescriptionController;

class PrescriptionTest extends TestCase
{
    private static $container;
    private Prescription $prescriptionModel;
    private Product $productModel;

    public static function setUpBeforeClass(): void
    {
        $app = Application::create();
        self::$container = $app->getContainer();
    }

    protected function setUp(): void
    {
        $db = self::$container->get(Database::class);
        $this->prescriptionModel = new Prescription($db);
        $this->productModel = new Product($db);
    }

    public function testGeneratePrescriptionNumber(): void
    {
        $rxNumber = $this->prescriptionModel->generateNumber();
        $this->assertNotEmpty($rxNumber);
        $this->assertStringStartsWith('RX-' . date('Ymd'), $rxNumber);
    }

    public function testCreatePrescriptionWithFinishedDrugsAndCompounding(): void
    {
        $products = $this->productModel->all();
        $this->assertNotEmpty($products, "Should have products available for prescribing");

        $firstProduct = $products[0];
        $secondProduct = $products[1] ?? $products[0];

        $data = [
            'prescription_number' => $this->prescriptionModel->generateNumber(),
            'patient_name' => 'Unit Test Patient',
            'patient_age' => 28,
            'patient_gender' => 'female',
            'patient_weight' => 55.0,
            'doctor_name' => 'dr. Testing Sp.PD',
            'doctor_sip' => 'SIP: 999/TEST/2026',
            'doctor_clinic' => 'Klinik Medika Test',
            'diagnosis' => 'Acute Pharyngitis',
            'clinical_notes' => 'Test prescription clinical instruction',
            'status' => 'pending'
        ];

        $items = [
            [
                'product_id' => $firstProduct['id'],
                'quantity' => 2,
                'dosage_instructions' => '3x1 tablet sesudah makan',
                'usage_time' => 'Sesudah Makan'
            ]
        ];

        $compounds = [
            [
                'compound_name' => 'Puyer Demam Racik No. X',
                'packaging_type' => 'puyer',
                'quantity_pack' => 10,
                'dosage_instructions' => '3x1 bungkus bila demam',
                'compounding_fee' => 5000,
                'packaging_fee' => 2000,
                'ingredients' => [
                    [
                        'product_id' => $secondProduct['id'],
                        'dose_per_pack' => '250 mg',
                        'quantity_used' => 5
                    ]
                ]
            ]
        ];

        $rxId = $this->prescriptionModel->createPrescription($data, $items, $compounds);
        $this->assertGreaterThan(0, $rxId);

        $details = $this->prescriptionModel->getDetails($rxId);
        $this->assertNotNull($details);
        $this->assertEquals('Unit Test Patient', $details['patient_name']);
        $this->assertEquals('dr. Testing Sp.PD', $details['doctor_name']);
        $this->assertCount(1, $details['items']);
        $this->assertCount(1, $details['compounds']);
        $this->assertCount(1, $details['compounds'][0]['ingredients']);
        $this->assertGreaterThan(0, (float)$details['total_amount']);
    }

    public function testPharmacistReviewAndSignOff(): void
    {
        $queue = $this->prescriptionModel->getQueue(['limit' => 1]);
        $this->assertNotEmpty($queue);
        $rxId = $queue[0]['id'];

        $reviewed = $this->prescriptionModel->reviewAndSignOff(
            $rxId,
            1, // Superadmin ID
            'Dosage verified and SIP authenticated.',
            'reviewed'
        );

        $this->assertTrue($reviewed);

        $details = $this->prescriptionModel->getDetails($rxId);
        $this->assertEquals('reviewed', $details['status']);
        $this->assertEquals('Dosage verified and SIP authenticated.', $details['pharmacist_notes']);
        $this->assertNotNull($details['reviewed_at']);
    }

    public function testPrescriptionStatusWorkflow(): void
    {
        $queue = $this->prescriptionModel->getQueue(['limit' => 1]);
        $rxId = $queue[0]['id'];

        // Move to compounding
        $this->prescriptionModel->updateStatus($rxId, 'compounding');
        $details1 = $this->prescriptionModel->getDetails($rxId);
        $this->assertEquals('compounding', $details1['status']);

        // Move to ready
        $this->prescriptionModel->updateStatus($rxId, 'ready');
        $details2 = $this->prescriptionModel->getDetails($rxId);
        $this->assertEquals('ready', $details2['status']);

        // Move to dispensed
        $this->prescriptionModel->updateStatus($rxId, 'dispensed');
        $details3 = $this->prescriptionModel->getDetails($rxId);
        $this->assertEquals('dispensed', $details3['status']);
        $this->assertNotNull($details3['dispensed_at']);
    }

    public function testPrescriptionPosLookupApi(): void
    {
        $queue = $this->prescriptionModel->getQueue(['limit' => 1]);
        $targetRxNumber = $queue[0]['prescription_number'];

        $request = new Request();
        $request->setMethod('GET');
        $request->setUri('/api/prescriptions/lookup');
        $request->setData(['code' => $targetRxNumber]);

        $controller = new PrescriptionController($request, self::$container);
        $response = $controller->posLookup($request);

        $this->assertEquals(200, $response->getStatusCode());
        $body = json_decode($response->getContent(), true);

        $this->assertTrue($body['success']);
        $this->assertEquals($targetRxNumber, $body['prescription']['prescription_number']);
        $this->assertNotEmpty($body['prescription']['patient_name']);
    }

    public function testQueueFilteringAndStatusCounts(): void
    {
        $counts = $this->prescriptionModel->getCountsByStatus();
        $this->assertArrayHasKey('all', $counts);
        $this->assertArrayHasKey('pending', $counts);
        $this->assertArrayHasKey('reviewed', $counts);
        $this->assertArrayHasKey('compounding', $counts);
        $this->assertArrayHasKey('ready', $counts);
        $this->assertArrayHasKey('dispensed', $counts);
        $this->assertGreaterThanOrEqual(1, $counts['all']);

        $allQueue = $this->prescriptionModel->getQueue();
        $this->assertNotEmpty($allQueue);
    }
}
