<?php

namespace App\Controllers;

use Core\Controller;
use Core\Request;
use Core\Response;
use Core\Auth;

/**
 * Landing Controller
 * Serves the modern public landing page for MediCore Pharmacy System
 */
class LandingController extends Controller
{
    /**
     * Display the main landing page
     */
    public function index(Request $request): Response
    {
        $auth = new Auth($this->session);
        $isLoggedIn = $auth->check();
        $currentUser = $isLoggedIn ? $auth->user() : null;

        return $this->view('landing.index', [
            'title' => 'MediCore — Intelligent Pharmacy Management System',
            'isLoggedIn' => $isLoggedIn,
            'currentUser' => $currentUser,
            'stats' => [
                'dispensing_accuracy' => '99.98%',
                'transaction_latency' => '< 120ms',
                'zero_expired_loss' => '100% FEFO',
                'uptime_sla' => '99.95%'
            ]
        ]);
    }
}
