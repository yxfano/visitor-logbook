<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Visitor_model extends MY_Model {
    
    public function get_all_visitors() {
        $this->db->select('visitors.*, entrance_locations.location_name');
        $this->db->from('visitors');
        $this->db->join('entrance_locations', 'entrance_locations.id = visitors.location_id');
        $this->db->order_by('visitors.visit_date DESC, visitors.visit_time DESC');
        return $this->db->get()->result();
    }

    // Count total records (no filter)
    public function count_all()
    {
        return $this->db->count_all('visitors');
    }

    // Count filtered records based on search
    public function count_filtered($search)
    {
        $this->db->from('visitors');
        $this->db->join('entrance_locations', 'entrance_locations.id = visitors.location_id');
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('visitors.name', $search);
            $this->db->or_like('visitors.reason', $search);
            $this->db->or_like('entrance_locations.location_name', $search);
            $this->db->group_end();
        }
        return $this->db->count_all_results();
    }

    // Get paginated, sorted, filtered results for DataTables server-side
    public function get_datatables($start, $length, $search = '', $order_column = 'visit_date', $order_dir = 'DESC')
    {
        $this->db->select('visitors.*, entrance_locations.location_name');
        $this->db->from('visitors');
        $this->db->join('entrance_locations', 'entrance_locations.id = visitors.location_id');

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('visitors.name', $search);
            $this->db->or_like('visitors.reason', $search);
            $this->db->or_like('entrance_locations.location_name', $search);
            $this->db->group_end();
        }

        // Map order_column friendly names to actual columns
        $valid_columns = [
            'name' => 'visitors.name',
            'visit_date' => 'visitors.visit_date',
            'visit_time' => 'visitors.visit_time',
            'location_name' => 'entrance_locations.location_name'
        ];

        if (isset($valid_columns[$order_column])) {
            $this->db->order_by($valid_columns[$order_column] . ' ' . ($order_dir === 'asc' ? 'ASC' : 'DESC'));
        } else {
            $this->db->order_by('visitors.visit_date DESC, visitors.visit_time DESC');
        }

        if ($length != -1) {
            $this->db->limit($length, $start);
        }

        return $this->db->get()->result();
    }
    
    public function get_visitor($id) {
        return $this->db->get_where('visitors', array('id' => $id))->row();
    }
    
    public function create_visitor($data) {
        $this->db->insert('visitors', $data);
        return $this->db->insert_id();
    }
    
    public function update_visitor($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('visitors', $data);
    }
    
    public function delete_visitor($id) {
        $visitor = $this->get_visitor($id);
        if ($visitor) {
            // Delete associated images
            if (!empty($visitor->face_photo)) {
                unlink(FCPATH . 'uploads/faces/' . $visitor->face_photo);
            }
            if (!empty($visitor->id_photo)) {
                unlink(FCPATH . 'uploads/ids/' . $visitor->id_photo);
            }
        }
        return $this->db->delete('visitors', array('id' => $id));
    }
}