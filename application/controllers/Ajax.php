<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends CI_Controller {
    public function auth($offset = 0){
		if(isset($_POST['confirmed']) && $_POST['confirmed']==1 && !empty($_POST['address'])){
			$_SESSION['AUTH'] = array('IS_AUTH'=>true,'ADDRESS'=>$_POST['address']);
			$_SESSION['MM_AUTH_ACCOUNT'] = $_POST['address'];
		}elseif(isset($_POST['confirmed']) && $_POST['confirmed']==0){
			unset($_SESSION['AUTH']);
		}
		
		$_SESSION['MM_AUTH_CONFIRMED'] = intval($_POST['confirmed']);
    }
	
	public function send_subscribe_confirmation(){
		$this->load->helper('string');
		$result = array('success'=>false);
		if(isset($_POST['email']) && !empty($_POST['email']) && !isset($_POST['code'])){
			$this->load->library('email'); 
			$this->email->set_mailtype("html");
			$this->email->from('noreply@keep-explorer.info', 'noreply@keep-explorer.info');
			$this->email->to($_POST['email']);
			$this->email->subject("Email confirmation");
			$data = array(
				'CODE'=> strtoupper(random_string('alnum', 5)),
			);
			$_SESSION['EMAIL_CONFIRMATION_CODE'] = $data['CODE'];
			$_SESSION['EMAIL'] = trim($_POST['email']);
			
			$message = $this->load->view('emails/confirm_email',$data,TRUE);
			$this->email->message($message);
			$this->email->send();
			$result['success']=true;
			$result['action']='get_code';
		}
		
		if(isset($_POST['code']) && !empty($_POST['code']) && !empty($_SESSION['AUTH']['ADDRESS'])){
			if(trim($_POST['code'])==$_SESSION['EMAIL_CONFIRMATION_CODE']){
				if(!empty($_POST['contractAddress'])){
					$this->load->model('deposits_model');
					$data = array('email'=>$_SESSION['EMAIL'],'address'=>$_SESSION['AUTH']['ADDRESS'],'contractAddress'=>$_POST['contractAddress']);
					$this->deposits_model->subscribeDeposit($data);
					$result['success']=true;
					$result['action']='subscribe';
					$_SESSION['EMAIL_CONFIRMATION_CODE'] = null;
					$_SESSION['EMAIL'] = null;
				}elseif(!empty($_POST['operatorAddress'])){
					$this->load->model('operators_model');
					$data = array('email'=>$_SESSION['EMAIL'],'address'=>$_SESSION['AUTH']['ADDRESS'],'operator'=>$_POST['operatorAddress']);
					$this->operators_model->subscribeOperator($data);
					$result['success']=true;
					$result['action']='subscribe';
					$_SESSION['EMAIL_CONFIRMATION_CODE'] = null;
					$_SESSION['EMAIL'] = null;
				}
			}else{
				$result['success']=false;
				$result['action']='wrong_code';
			}
		}
		
		if(isset($_POST['unsubscribe']) && $_POST['unsubscribe']=="Y" && !empty($_POST['contractAddress'])){
			$this->load->model('deposits_model');
			$data = array('address'=>$_SESSION['AUTH']['ADDRESS'],'contractAddress'=>$_POST['contractAddress']);
			$this->deposits_model->unsubscribeDeposit($data);
			$result['success']=true;
			$result['action']='unsubscribe';
			$_SESSION['EMAIL_CONFIRMATION_CODE'] = null;
			$_SESSION['EMAIL'] = null;
		}
		
		if(isset($_POST['unsubscribe']) && $_POST['unsubscribe']=="Y" && !empty($_POST['operatorAddress'])){
			$this->load->model('operators_model');
			$data = array('address'=>$_SESSION['AUTH']['ADDRESS'],'operator'=>$_POST['operatorAddress']);
			$this->operators_model->unsubscribeOperator($data);
			$result['success']=true;
			$result['action']='unsubscribe';
			$_SESSION['EMAIL_CONFIRMATION_CODE'] = null;
			$_SESSION['EMAIL'] = null;
		}
		
		if(isset($_POST['save']) && $_POST['save']=='configuration' ){
			$events = array();
			if(!empty($_POST['event'])) $events = $_POST['event'];
			
			if(!empty($_POST['contractAddress'])){
				$this->load->model('deposits_model');
				$data = array('address'=>$_SESSION['AUTH']['ADDRESS'],'contractAddress'=>$_POST['contractAddress'],'events'=>json_encode($events));
				$this->deposits_model->subscribeDeposit($data);
			}elseif(!empty($_POST['operatorAddress'])){
				$this->load->model('operators_model');
				$data = array('address'=>$_SESSION['AUTH']['ADDRESS'],'operator'=>$_POST['operatorAddress'],'events'=>json_encode($events));
				if(!empty($_POST['collateralization'])) $data['collateralization'] = intval($_POST['collateralization']);
				$this->operators_model->subscribeOperator($data);
			}
		}
		
		print json_encode($result);
    }
}
?>
