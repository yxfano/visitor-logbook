<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Locations extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('location_model');
        $this->load->helper(['url', 'form']);
        $this->load->library(['form_validation']);
    }

    public function index() {
        $data['locations'] = $this->location_model->get_all_locations();
        $data['title'] = 'Entrance Locations';
        
        $this->load->view('templates/header', $data);
        $this->load->view('locations/index', $data);
        $this->load->view('templates/footer');
    }

    public function create() {
        $data['title'] = 'Add New Location';

        $this->form_validation->set_rules('location_name', 'Location Name', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('locations/create', $data);
            $this->load->view('templates/footer');
        } else {
            $this->location_model->create_location([
                'location_name' => $this->input->post('location_name')
            ]);
            redirect('locations');
        }
    }

    public function edit($id) {
        $data['location'] = $this->location_model->get_location($id);
        
        if (empty($data['location'])) {
            show_404();
        }

        $data['title'] = 'Edit Location';

        $this->form_validation->set_rules('location_name', 'Location Name', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('locations/edit', $data);
            $this->load->view('templates/footer');
        } else {
            $this->location_model->update_location($id, [
                'location_name' => $this->input->post('location_name')
            ]);
            redirect('locations');
        }
    }

    public function delete($id) {
        if (!$this->location_model->delete_location($id)) {
            $this->session->set_flashdata('error', 'Cannot delete location as it is being used by visitors');
        }
        redirect('locations');
    }
}