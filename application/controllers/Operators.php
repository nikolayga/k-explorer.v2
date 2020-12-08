<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Operators extends CI_Controller {
    
    public function index($offset = 0){
        $this->load->model('operators_model');
       
		$data = array();
		$result = $this->operators_model->get_list(false);
        $data['total'] = count($result['items']);
        $data['total_staked'] = $result['total_staked'];
        $data['total_bonded'] = $result['total_bonded'];
        $data['bond_avail'] = $result['bond_avail'];
        
        $result = $this->operators_model->get_list(true);
        $data['items'] = $result['items'];
        
        
        $this->load->view('templates/header');
        $this->load->view('operators/index', $data);
        $this->load->view('templates/footer');
    }
    
     public function view($address){
		 $data = array();
		 $this->load->model('operators_model');
		 $data = $this->operators_model->get($address);
		 
		 if(!empty($_POST['update_rewards_eth']) && !empty($_POST['rewards_eth'])) $this->operators_model->saveETHRewards($address, $_POST['rewards_eth']);
		 if(!empty($_POST['updateBA']) && !empty($_POST['bonding_avail'])) $this->operators_model->saveBondAvail($address, $_POST['bonding_avail']);
		 if(!empty($_POST['updateStaked']) && !empty($_POST['staked'])) $this->operators_model->saveStaked($address, intval($_POST['staked']));
		 
		 $this->load->view('templates/header');
         $this->load->view('operators/view', $data);
         $this->load->view('templates/footer');
	 }
}
?>
