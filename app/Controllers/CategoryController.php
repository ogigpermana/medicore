<?php

namespace App\Controllers;

use Core\Controller;
use Core\Request;
use Core\Response;
use Core\Auth;
use App\Models\Category;

/**
 * Category Controller
 * Handles full CRUD for medication classifications and therapeutic categories
 */
class CategoryController extends Controller
{
    private Category $categoryModel;
    private Auth $auth;

    public function __construct(Request $request, \Core\Container $container)
    {
        parent::__construct($request, $container);
        $this->categoryModel = new Category();
        $this->auth = new Auth($this->session);
    }

    /**
     * Display categories list
     */
    public function index(Request $request): Response
    {
        if (!$this->auth->check()) {
            return $this->redirect('/login');
        }

        $categories = $this->categoryModel->getActiveWithCounts();

        if ($request->expectsJson() || $request->isAjax()) {
            return $this->json(['success' => true, 'data' => $categories]);
        }

        $currentUser = $this->auth->user();
        $role = strtolower($currentUser['role_name'] ?? $currentUser['role'] ?? 'pharmacist');

        return $this->view('inventory.categories', [
            'title' => 'Medication Categories — MediCore',
            'user' => $currentUser,
            'role' => $role,
            'activeMenu' => 'categories',
            'categories' => $categories
        ]);
    }

    /**
     * Store a new category
     */
    public function store(Request $request): Response
    {
        if (!$this->auth->check()) {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $name = trim($request->input('name') ?? '');
        if (empty($name)) {
            return $this->json(['success' => false, 'message' => 'Category name is required.'], 422);
        }

        $slug = $this->categoryModel->generateSlug($name);
        $description = trim($request->input('description') ?? '');
        $requiresPrescription = !empty($request->input('requires_prescription')) ? 1 : 0;

        try {
            $id = $this->categoryModel->create([
                'name' => $name,
                'slug' => $slug,
                'description' => $description,
                'requires_prescription' => $requiresPrescription,
                'is_active' => 1
            ]);

            return $this->json([
                'success' => true,
                'message' => "Category '{$name}' created successfully.",
                'category_id' => $id
            ], 201);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to create category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an existing category
     */
    public function update(Request $request): Response
    {
        if (!$this->auth->check()) {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $id = (int)$request->input('id');
        $category = $this->categoryModel->find($id);

        if (!$category) {
            return $this->json(['success' => false, 'message' => 'Category not found.'], 404);
        }

        $name = trim($request->input('name') ?? '');
        if (empty($name)) {
            return $this->json(['success' => false, 'message' => 'Category name cannot be empty.'], 422);
        }

        $slug = $this->categoryModel->generateSlug($name, $id);
        $description = trim($request->input('description') ?? '');
        $requiresPrescription = !empty($request->input('requires_prescription')) ? 1 : 0;

        try {
            $this->categoryModel->update($id, [
                'name' => $name,
                'slug' => $slug,
                'description' => $description,
                'requires_prescription' => $requiresPrescription
            ]);

            return $this->json([
                'success' => true,
                'message' => "Category '{$name}' updated successfully."
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to update category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a category
     */
    public function delete(Request $request): Response
    {
        if (!$this->auth->check()) {
            return $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $id = (int)$request->input('id');
        $category = $this->categoryModel->find($id);

        if (!$category) {
            return $this->json(['success' => false, 'message' => 'Category not found.'], 404);
        }

        if (!$this->categoryModel->canDelete($id)) {
            return $this->json([
                'success' => false,
                'message' => "Cannot delete '{$category['name']}' because medications are linked to this category. Please reassign the medications first."
            ], 400);
        }

        try {
            $this->categoryModel->delete($id);

            return $this->json([
                'success' => true,
                'message' => "Category '{$category['name']}' deleted successfully."
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => 'Failed to delete category: ' . $e->getMessage()
            ], 500);
        }
    }
}
