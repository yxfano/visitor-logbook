<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Visitors extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('visitor_model', '', TRUE);
        $this->load->model('location_model', '', TRUE);
        $this->load->helper(['url', 'form']);
        $this->load->library(['form_validation']);
    }

    public function index() {
        $data['title'] = 'Visitor Records';
        
        $this->load->view('templates/header', $data);
        $this->load->view('visitors/index', $data);
        $this->load->view('templates/footer');
    }

    // DataTables server-side processing endpoint
    public function ajax_list()
    {
        $this->output->set_content_type('application/json');

        $draw = intval($this->input->post('draw'));
        $start = intval($this->input->post('start'));
        $length = intval($this->input->post('length'));
        $search = $this->input->post('search')['value'] ?? '';

        // Determine order
        $order_column_index = $this->input->post('order')[0]['column'] ?? null;
        $order_dir = $this->input->post('order')[0]['dir'] ?? 'desc';

        $columns = $this->input->post('columns');
        $order_column = 'visit_date';
        if (is_array($columns) && isset($order_column_index) && isset($columns[$order_column_index]['data'])) {
            $order_column = $columns[$order_column_index]['data'];
        }

        $data = [];
        $recordsTotal = $this->visitor_model->count_all();
        $recordsFiltered = $this->visitor_model->count_filtered($search);

        $list = $this->visitor_model->get_datatables($start, $length, $search, $order_column, $order_dir);
        foreach ($list as $item) {
            $row = [];
            $row['name'] = htmlspecialchars($item->name);
            $row['visit_date'] = $item->visit_date;
            $row['visit_time'] = $item->visit_time;
            $row['location_name'] = htmlspecialchars($item->location_name);
            // Photos column
            $photos = '';
            if (!empty($item->face_photo)) {
                $photos .= '<a href="#" data-img="'.base_url('uploads/faces/'.$item->face_photo).'" class="btn btn-sm btn-info me-1 photo-btn"><i class="fas fa-user"></i></a>';
            }
            if (!empty($item->id_photo)) {
                $photos .= '<a href="#" data-img="'.base_url('uploads/ids/'.$item->id_photo).'" class="btn btn-sm btn-info photo-btn"><i class="fas fa-id-card"></i></a>';
            }
            $row['photos'] = $photos;

            $actions = '';
            $actions .= '<a href="'.site_url('visitors/view/'.$item->id).'" class="btn btn-sm btn-info me-1"><i class="fas fa-eye"></i></a>';
            $actions .= '<button type="button" class="btn btn-sm btn-danger delete-visitor" data-id="'.$item->id.'"><i class="fas fa-trash"></i></button>';
            $row['actions'] = $actions;
            $data[] = $row;
        }

        $output = [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ];

        echo json_encode($output);
    }

    public function create() {
        $data['title'] = 'New Visitor Entry';
        $data['locations'] = $this->location_model->get_all_locations();

        $this->form_validation->set_rules('name', 'Name', 'required');
        $this->form_validation->set_rules('reason', 'Reason', 'required');
        $this->form_validation->set_rules('location_id', 'Location', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->load->view('templates/header', $data);
            $this->load->view('visitors/create', $data);
            $this->load->view('templates/footer');
        } else {
            // Handle image uploads
            $face_photo = $this->_save_photo('face_photo', 'faces');
            $id_photo = $this->_save_photo('id_photo', 'ids');

            $visitor_data = array(
                'name' => $this->input->post('name'),
                'visit_date' => date('Y-m-d'),
                'visit_time' => date('H:i:s'),
                'face_photo' => $face_photo,
                'id_photo' => $id_photo,
                'reason' => $this->input->post('reason'),
                'location_id' => $this->input->post('location_id')
            );

            $this->visitor_model->create_visitor($visitor_data);
            redirect('visitors');
        }
    }

    private function _save_photo($field_name, $folder) {
        $photo_data = $this->input->post($field_name);
        if (empty($photo_data)) {
            return '';
        }

        // Remove the "data:image/jpeg;base64," part
        $photo_data = str_replace('data:image/jpeg;base64,', '', $photo_data);
        $photo_data = base64_decode($photo_data);

        $file_name = uniqid() . '.jpg';
        $file_path = FCPATH . 'uploads/' . $folder . '/' . $file_name;

        file_put_contents($file_path, $photo_data);
        return $file_name;
    }

    public function view($id) {
        $data['visitor'] = $this->visitor_model->get_visitor($id);
        
        if (empty($data['visitor'])) {
            show_404();
        }

        $data['title'] = 'Visitor Details';
        
        $this->load->view('templates/header', $data);
        $this->load->view('visitors/view', $data);
        $this->load->view('templates/footer');
    }

    public function delete($id) {
        $this->visitor_model->delete_visitor($id);
        redirect('visitors');
    }
}