<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper(['url', 'form']);
        $this->load->library(['form_validation', 'session']);
        $this->load->model('user_model');
    }

    public function login() {
        // If already logged in, redirect home
        if ($this->session->userdata('logged_in')) {
            redirect('');
        }

        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() === FALSE) {
            $data['title'] = 'Login';
            $this->load->view('templates/header', $data);
            $this->load->view('auth/login', $data);
            $this->load->view('templates/footer');
            return;
        }

        $username = $this->input->post('username');
        $password = $this->input->post('password');

        // Authenticate against the users table (passwords stored as MD5 hashes)
        $user = $this->user_model->get_by_username($username);
        if ($user && $user->password === md5($password)) {
            $this->session->set_userdata(['logged_in' => TRUE, 'username' => $user->username]);
            $redirect = $this->session->userdata('redirect_after_login') ?: '';
            $this->session->unset_userdata('redirect_after_login');
            redirect($redirect);
        }

        $this->session->set_flashdata('error', 'Invalid username or password');
        redirect('auth/login');
    }

    public function logout() {
        $this->session->unset_userdata(['logged_in', 'username']);
        $this->session->sess_destroy();
        redirect('auth/login');
    }
}
