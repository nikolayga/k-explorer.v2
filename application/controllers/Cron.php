<?php
class Cron extends CI_Controller {
	public function depositSubscribeExecute()
	{
		 $this->load->database();
		 $query = $this->db->get_where('depositSubscribeQueue', array('	sended' => 0));
		 if($result= $query->result_array()){
			 $event_names = array('Funded'=>'Funded','RedemptionRequested'=>'Redemption Requested','Redeemed'=>'Redeemed','CourtesyCalled'=>'Courtesy Called','ExitedCourtesyCall'=>'Exited Courtesy Call','StartedLiquidation'=>'Started Liquidation','Liquidated'=>'Liquidated');
			
			 
			 foreach($result as $item){
				$this->load->library('email'); 
				$this->email->set_mailtype("html");
				$this->email->from('noreply@keep-explorer.info', 'noreply@keep-explorer.info');
				
				$this->email->to($item['email']);
				$this->email->subject("New ".$event_names[$item['event']]." event!");
				
				$data = array(
					'event_name'=> $event_names[$item['event']],
					'depositAddress'=>$item['contractAddress'],
					'depositAddressLink'=>'https://keep-explorer.com/deposit/'.$item['contractAddress']
				);

				$message = $this->load->view('emails/subscribe_deposits',$data,TRUE);
				$this->email->message($message);
				$this->email->send();
				
				$this->db->set('sended', 1, true);
				$this->db->where('id',$item['id'] );
				$this->db->update('depositSubscribeQueue');
			
			 }
		 }
	}
}
