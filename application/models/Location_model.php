<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Location_model extends MY_Model {
    
    public function get_all_locations() {
        return $this->db->get('entrance_locations')->result();
    }
    
    public function get_location($id) {
        return $this->db->get_where('entrance_locations', array('id' => $id))->row();
    }
    
    public function create_location($data) {
        return $this->db->insert('entrance_locations', $data);
    }
    
    public function update_location($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('entrance_locations', $data);
    }
    
    public function delete_location($id) {
        // Check if location is being used by any visitor
        $this->db->where('location_id', $id);
        $count = $this->db->count_all_results('visitors');
        
        if ($count > 0) {
            return false; // Location is in use
        }
        
        return $this->db->delete('entrance_locations', array('id' => $id));
    }
}