<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    protected $public_controllers = ['auth']; // controllers that do not require auth

    public function __construct()
    {
        parent::__construct();

        // Don't enforce auth on CLI
        if (is_cli()) {
            return;
        }

        $controller = $this->router->class;
        $method = $this->router->method;

        // Allow public controllers (like auth)
        if (in_array(strtolower($controller), $this->public_controllers)) {
            return;
        }

        // If user not logged in, redirect to login
        if (!$this->session->userdata('logged_in')) {
            // Save intended URL to redirect after login if desired
            $this->session->set_userdata('redirect_after_login', current_url());
            redirect('auth/login');
        }
    }
}
