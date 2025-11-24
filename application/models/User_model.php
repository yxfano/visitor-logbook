<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    public function get_by_username($username)
    {
        return $this->db->get_where('users', ['username' => $username])->row();
    }

    public function create_user($username, $password_plain)
    {
        $data = [
            'username' => $username,
            // Store as MD5 per user request (note: MD5 is insecure for production)
            'password' => md5($password_plain),
            'created_at' => date('Y-m-d H:i:s')
        ];
        return $this->db->insert('users', $data);
    }
}
