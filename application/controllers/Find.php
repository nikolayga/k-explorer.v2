<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Find extends CI_Controller {
    
    public function index($offset = 0){
        $this->load->model('find_model');
        /*
        $config['base_url'] = base_url().'find/index';
        
        $config['total_rows'] = count($this->find_model->get_list(true));
        $config['per_page'] = 10;
        $config['uri_segment'] = 3;
        $config['attributes'] = array('class' => 'page-link');
        
		$config['first_tag_open'] = '<li class="page-item">';
		$config['first_tag_close'] = '</li>';
		 
		$config['last_tag_open'] = '<li class="page-item">';
		$config['last_tag_close'] = '</li>';
		 
		$config['next_tag_open'] = '<li class="page-item">';
		$config['next_tag_close'] = '</li>';

		$config['prev_tag_open'] = '<li class="page-item">';
		$config['prev_tag_close'] = '</li>';

		$config['cur_tag_open'] = '<li class="page-item active"><span class="page-link">';
		$config['cur_tag_close'] = '</span></li>';

		$config['num_tag_open'] = '<li class="page-item">';
		$config['num_tag_close'] = '</li>';
            
        $this->pagination->initialize($config);
        */

		$data = array();
        $data['items'] = $this->find_model->get_list($_REQUEST['address']);
        $this->load->view('templates/header');
        $this->load->view('find/index', $data);
        $this->load->view('templates/footer');
    }
}
