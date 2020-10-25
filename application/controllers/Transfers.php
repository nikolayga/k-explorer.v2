<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transfers extends CI_Controller {
    
    public function index($offset = 0){
        $this->load->model('transfers_model');
        $config['base_url'] = base_url().'transfers/index';
        
        $config['total_rows'] = count($this->transfers_model->get_list(true));
        $config['per_page'] = 10;
        $config['uri_segment'] = 3;
        $config['attributes'] = array('class' => 'page-link');
        
        //$config['first_link'] = 'First Page';
		$config['first_tag_open'] = '<li class="page-item">';
		$config['first_tag_close'] = '</li>';
		 
		//$config['last_link'] = 'Last Page';
		$config['last_tag_open'] = '<li class="page-item">';
		$config['last_tag_close'] = '</li>';
		 
		//$config['next_link'] = 'Next Page';
		$config['next_tag_open'] = '<li class="page-item">';
		$config['next_tag_close'] = '</li>';

		//$config['prev_link'] = 'Prev Page';
		$config['prev_tag_open'] = '<li class="page-item">';
		$config['prev_tag_close'] = '</li>';

		$config['cur_tag_open'] = '<li class="page-item active"><span class="page-link">';
		$config['cur_tag_close'] = '</span></li>';

		$config['num_tag_open'] = '<li class="page-item">';
		$config['num_tag_close'] = '</li>';
            
        $this->pagination->initialize($config);
 
		$data = array();
        $data['items'] = $this->transfers_model->get_list(false, $config['per_page'], $offset);
        $this->load->view('templates/header');
        $this->load->view('transfers/index', $data);
        $this->load->view('templates/footer');
    }
}
