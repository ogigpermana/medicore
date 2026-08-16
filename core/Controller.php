<?php

namespace Core;

/**
 * Controller Class
 * Base controller for all application controllers
 */

abstract class Controller
{
    protected Request $request;
    protected Response $response;
    protected View $view;
    protected Session $session;
    protected \Core\Container $container;

    public function __construct(Request $request, \Core\Container $container)
    {
        $this->request = $request;
        $this->container = $container;
        $this->response = new Response();
        $this->view = new View();
        $this->session = $container->get(\Core\Session::class);
    }

    /**
     * Return JSON response
     */
    protected function json(array $data, int $status = 200): Response
    {
        return $this->response->json($data, $status);
    }

    /**
     * Return view response
     */
    protected function view(string $template, array $data = []): Response
    {
        $content = $this->view->render($template, $data);
        return $this->response->setContent($content);
    }

    /**
     * Return rendered view response (alias of view)
     */
    protected function render(string $template, array $data = []): Response
    {
        return $this->view($template, $data);
    }

    /**
     * Return view with layout
     */
    protected function layout(string $layout, string $template, array $data = []): Response
    {
        $content = $this->view->renderWithLayout($layout, $template, $data);
        return $this->response->setContent($content);
    }

    /**
     * Return redirect response
     */
    protected function redirect(string $url): Response
    {
        return $this->response->redirect($url);
    }

    /**
     * Redirect back to previous page
     */
    protected function back(): Response
    {
        return $this->response->redirect($_SERVER['HTTP_REFERER'] ?? '/');
    }

    /**
     * Validate request data
     */
    protected function validate(Request $request, array $rules): array
    {
        $validator = app()->getContainer()->get(Validator::class);
        return $validator->validate($request->all(), $rules);
    }

    /**
     * Check authorization
     */
    protected function authorize(string $ability, mixed $argument = null): void
    {
        if (!$this->can($ability, $argument)) {
            http_response_code(403);
            echo 'Forbidden';
            exit;
        }
    }

    /**
     * Check user ability
     */
    protected function can(string $ability, mixed $argument = null): bool
    {
        // Implementation depends on auth system
        return true;
    }

    /**
     * Get CSRF token
     */
    protected function csrfToken(): string
    {
        return $this->session->getCsrfToken();
    }

    /**
     * Get CSRF token field
     */
    protected function csrfField(): string
    {
        $token = $this->session->getCsrfToken();
        return '<input type="hidden" name="_token" value="' . $token . '">';
    }
}