<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Core\Application;
use App\Models\Category;
use App\Models\Product;

/**
 * Category CRUD & Validation Unit Tests
 */
class CategoryCrudTest extends TestCase
{
    private Application $app;
    private Category $categoryModel;
    private Product $productModel;

    protected function setUp(): void
    {
        $this->app = Application::create();
        $this->categoryModel = new Category();
        $this->productModel = new Product();
    }

    /**
     * Test creating a category and generating unique slug
     */
    public function test_create_category_with_unique_slug(): void
    {
        $uniqueName = 'Ophthalmic Eye Drops ' . uniqid();
        $slug = $this->categoryModel->generateSlug($uniqueName);

        $id = $this->categoryModel->create([
            'name' => $uniqueName,
            'slug' => $slug,
            'description' => 'Eye care solutions',
            'requires_prescription' => 1,
            'is_active' => 1
        ]);

        $this->assertGreaterThan(0, $id);

        $saved = $this->categoryModel->find($id);
        $this->assertEquals($uniqueName, $saved['name']);
        $this->assertEquals($slug, $saved['slug']);
        $this->assertEquals(1, $saved['requires_prescription']);
    }

    /**
     * Test category update
     */
    public function test_update_category(): void
    {
        $uniqueName = 'Dermatology ' . uniqid();
        $slug = $this->categoryModel->generateSlug($uniqueName);

        $id = $this->categoryModel->create([
            'name' => $uniqueName,
            'slug' => $slug,
            'description' => 'Skin ointments',
            'requires_prescription' => 0,
            'is_active' => 1
        ]);

        $updatedName = 'Dermatology & Skin Care ' . uniqid();
        $updatedSlug = $this->categoryModel->generateSlug($updatedName, $id);

        $updated = $this->categoryModel->update($id, [
            'name' => $updatedName,
            'slug' => $updatedSlug,
            'description' => 'Updated description'
        ]);

        $this->assertTrue($updated);

        $rechecked = $this->categoryModel->find($id);
        $this->assertEquals($updatedName, $rechecked['name']);
        $this->assertEquals($updatedSlug, $rechecked['slug']);
    }

    /**
     * Test delete safety guard when products exist
     */
    public function test_category_cannot_be_deleted_if_has_products(): void
    {
        // Category 'antibiotics' has products attached
        $antibiotics = $this->categoryModel->findBySlug('antibiotics');
        $this->assertNotNull($antibiotics);

        $canDelete = $this->categoryModel->canDelete($antibiotics['id']);
        $this->assertFalse($canDelete, 'Categories with active products should not be deletable');
    }

    /**
     * Test deleting an empty category
     */
    public function test_delete_empty_category(): void
    {
        $uniqueName = 'Temporary Category ' . uniqid();
        $slug = $this->categoryModel->generateSlug($uniqueName);

        $id = $this->categoryModel->create([
            'name' => $uniqueName,
            'slug' => $slug,
            'description' => 'To be deleted',
            'requires_prescription' => 0,
            'is_active' => 1
        ]);

        $canDelete = $this->categoryModel->canDelete($id);
        $this->assertTrue($canDelete);

        $deleted = $this->categoryModel->delete($id);
        $this->assertTrue($deleted);

        $rechecked = $this->categoryModel->find($id);
        $this->assertNull($rechecked);
    }
}
