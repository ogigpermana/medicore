<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Core\Application;
use App\Models\Batch;

/**
 * Batch & FEFO Unit Tests
 */
class BatchTest extends TestCase
{
    private Application $app;
    private Batch $batchModel;

    protected function setUp(): void
    {
        $this->app = Application::create();
        $this->batchModel = new Batch();
    }

    /**
     * Test expiring batches query
     */
    public function test_get_expiring_batches(): void
    {
        $batches = $this->batchModel->getExpiringBatches(60);

        $this->assertIsArray($batches);
        foreach ($batches as $b) {
            $this->assertLessThanOrEqual(60, $b['days_until_expiry']);
        }
    }
}
