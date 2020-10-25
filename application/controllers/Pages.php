<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pages extends CI_Controller {
    
    public function view($page = 'home'){
        if(!file_exists(APPPATH.'views/pages/'.$page.'.php')){
            show_404();
        }
        
        $data = array();
        
        if($page=="home"){
			$this->load->model('dashboard_model');
			$data['totalMinted']=round($this->dashboard_model->get_totalMinted(),2);
			$data['currentSuply'] = round($this->dashboard_model->get_currentSuply(),2);
			$data['transfers24h'] = round($this->dashboard_model->get_transfers24h(),2);
			$this->dashboard_model->get_deposit_stat();
			$this->dashboard_model->get_transfers_stat();
			$this->dashboard_model->get_redeemed_stat();
		}
		
        $this->load->view('templates/header');
        $this->load->view('pages/'.$page, $data);
        $this->load->view('templates/footer');
    }
}
