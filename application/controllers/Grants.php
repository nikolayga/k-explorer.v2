<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Grants extends CI_Controller {
    
    public function index($offset = 0){
        $this->load->model('grants_model');
        $config['base_url'] = base_url().'grants/index';
		
        $config['per_page'] = 10;
        
        
        $this->grants_model->updateData();
        $this->grants_model->get_grants_stat();
        
		$data = array();
        $data['items'] = $this->grants_model->get_list(/*false, $config['per_page'], $offset*/);
		$data['total'] = $this->grants_model->get_total();
        $this->load->view('templates/header');
        $this->load->view('grants/index', $data);
        $this->load->view('templates/footer');
    }
    
    public function view($GrantID){
		$this->load->model('grants_model');
		$data = array();
		$data['grant'] = $this->grants_model->get($GrantID);
		$data['grant'] = $data['grant'][0];
		$this->load->view('grants/view', $data);
	}
}
