<?php

namespace Core;

/**
 * View Class
 * Handles view rendering with data
 */

class View
{
    private string $viewPath;
    private array $globalData = [];

    public function __construct()
    {
        $this->viewPath = __DIR__ . '/../app/Views/';
    }

    /**
     * Set global data available to all views
     */
    public function share(string $key, mixed $value): void
    {
        $this->globalData[$key] = $value;
    }

    /**
     * Render a view template
     */
    public function render(string $template, array $data = []): string
    {
        $viewFile = $this->viewPath . str_replace('.', '/', $template) . '.php';

        if (!file_exists($viewFile)) {
            throw new \Exception("View file not found: {$viewFile}");
        }

        // Merge global data with view-specific data
        $data = array_merge($this->globalData, $data);

        // Extract data as variables
        extract($data);

        // Start output buffering
        ob_start();
        include $viewFile;
        $content = ob_get_clean();

        return $content;
    }

    /**
     * Render view with layout
     */
    public function renderWithLayout(string $layout, string $template, array $data = []): string
    {
        $content = $this->render($template, $data);
        $layoutData = array_merge($data, ['content' => $content]);
        return $this->render($layout, $layoutData);
    }

    /**
     * Check if view exists
     */
    public function exists(string $template): bool
    {
        $viewFile = $this->viewPath . str_replace('.', '/', $template) . '.php';
        return file_exists($viewFile);
    }
}