<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Core\Application;
use App\Models\Category;

/**
 * Category Unit Tests
 */
class CategoryTest extends TestCase
{
    private Application $app;
    private Category $categoryModel;

    protected function setUp(): void
    {
        $this->app = Application::create();
        $this->categoryModel = new Category();
    }

    /**
     * Test find category by slug
     */
    public function test_find_by_slug(): void
    {
        $category = $this->categoryModel->findBySlug('antibiotics');

        $this->assertNotNull($category, 'Antibiotics category should exist');
        $this->assertEquals('Antibiotics', $category['name']);
    }

    /**
     * Test active categories with counts
     */
    public function test_get_active_with_counts(): void
    {
        $categories = $this->categoryModel->getActiveWithCounts();

        $this->assertIsArray($categories);
        $this->assertNotEmpty($categories);
        $this->assertArrayHasKey('product_count', $categories[0]);
    }
}
