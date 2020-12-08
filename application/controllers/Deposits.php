<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Deposits extends CI_Controller {
    
    public function index($offset = 0){
        $this->load->model('deposits_model');
        $config['base_url'] = base_url().'deposits/index';
        
        $config['total_rows'] = count($this->deposits_model->get_list(true));
        $config['per_page'] = 50;
        $config['uri_segment'] = 3;
        $config['attributes'] = array('class' => 'page-link');
        $config['reuse_query_string']  = TRUE;
		
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
        $data['items'] = $this->deposits_model->get_list(false, $config['per_page'], $offset);
		$data['total'] = $this->deposits_model->get_total();
        $data['found'] = $config['total_rows'];

		$data['lotsizes'] = $this->deposits_model->get_lot_list();
        
        $this->load->view('templates/header');
        $this->load->view('deposits/index', $data);
        $this->load->view('templates/footer');
    }
    
     public function view($address){
		 $data = array();
		 $this->load->model('deposits_model');
		 $data = $this->deposits_model->get($address);
		 
		 if(!empty($_POST['addMembers']) && !empty($_POST['members'])) $this->deposits_model->saveKeepMembers($address, $_POST['members']);
		 if(!empty($_POST['addBond']) && !empty($_POST['bond'])) $this->deposits_model->saveKeepBond($address, $_POST['bond']);
		 if(!empty($_POST['updateState']) && !empty($_POST['state'])) $this->deposits_model->saveState($address, intval($_POST['state']));
		 
		 $this->load->view('templates/header');
         $this->load->view('deposits/view', $data);
         $this->load->view('templates/footer');
	 }
}
?>
