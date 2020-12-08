<?php
error_reporting(E_ALL & ~E_NOTICE);
class Cron extends CI_Controller {
	public function depositSubscribeExecute()
	{
		 $this->load->database();
		 $query = $this->db->get_where('depositSubscribeQueue', array('sended' => 0));
		 if($result= $query->result_array()){
			 $event_names = array('Funded'=>'Funded','RedemptionRequested'=>'Redemption Requested','Redeemed'=>'Redeemed','CourtesyCalled'=>'Courtesy Called','ExitedCourtesyCall'=>'Exited Courtesy Call','StartedLiquidation'=>'Started Liquidation','Liquidated'=>'Liquidated');
			
			 
			 foreach($result as $item){
				$this->load->library('email'); 
				$this->email->set_mailtype("html");
				$this->email->from('noreply@keep-explorer.info', 'noreply@keep-explorer.info');
				
				$this->email->to($item['email']);
				$this->email->subject("New ".$event_names[$item['event']]." event for deposit!");
				
				$data = array(
					'event_name'=> $event_names[$item['event']],
					'depositAddress'=>$item['contractAddress'],
					'depositAddressLink'=>'https://keep-explorer.info/deposit/'.$item['contractAddress']
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
	
	public function operatorSubscribeExecute()
	{
		 $this->load->database();
		 $query = $this->db->get_where('operatorSubscribeQueue', array('sended' => 0));
		 if($result= $query->result_array()){
			 $event_names = array('Funded'=>'Funded','RedemptionRequested'=>'Redemption Requested','Redeemed'=>'Redeemed','CourtesyCalled'=>'Courtesy Called','ExitedCourtesyCall'=>'Exited Courtesy Call','StartedLiquidation'=>'Started Liquidation','Liquidated'=>'Liquidated');
			
			 foreach($result as $item){
					$this->load->library('email'); 
					$this->email->set_mailtype("html");
					$this->email->from('noreply@keep-explorer.info', 'noreply@keep-explorer.info');
					
					$this->email->to($item['email']);
					if(!empty($item['event'])) $this->email->subject("The Operator has deposit with ".$event_names[$item['event']]." event!");
					elseif(empty($item['event']) && !empty($item['collateralization']))  $this->email->subject("The Operator has deposits with collateralization less than ".$item['collateralization']."%");
					
					if(!empty($item['event'])){
						$data = array(
							'event_name'=> $event_names[$item['event']],
							'depositAddress'=>$item['contractAddress'],
							'operator'=>$item['operator']
						);

						$message = $this->load->view('emails/subscribe_operators_event',$data,TRUE);
					}elseif(empty($item['event']) && !empty($item['collateralization'])){
						$deposits = '';
						$deposits_array = json_decode($item['params'],true);
						
						foreach($deposits_array as $dep){
							$deposits.='<p><a href="https://keep-explorer.info/deposit/'.$dep['depositContractAddress'].'" target="_blank">'.$dep['depositContractAddress'].'</a>&nbsp;&nbsp;&nbsp;<b>'.$dep['lotsize'].' BTC</b>&nbsp;&nbsp;&nbsp;Collateralization: '.round($dep['collateralization'],2).'%</p>';
						}
						
						$data = array(
							'operator'=>$item['operator'],
							'collateralization'=>$item['collateralization'],
							'deposits'=>$deposits
						);
						$message = $this->load->view('emails/subscribe_operators_collateralization',$data,TRUE);
					}
					
					$this->email->message($message);
					$this->email->send();
					
					$this->db->set('sended', 1, true);
					$this->db->where('id',$item['id'] );
					$this->db->update('operatorSubscribeQueue');
			 }
		 }
	}
	
	public function operatorSubscribePrepare()
	{
		$this->load->database();
		$client = new Codenixsv\CoinGeckoApi\CoinGeckoClient();
		$data_cur = $client->simple()->getPrice('ethereum,bitcoin', 'usd,btc');
		if(!empty($data_cur['ethereum']['btc'])){	
			$query = $this->db->get_where('operatorEventsSubscribe', "collateralization IS NOT NULL");
			if($result= $query->result_array()){
				 if(!empty($result)) {
					  foreach($result as $rule){
							$this->db->from('depositHistory');
							$this->db->select('depositContractAddress, lotsize, ((keepBond * '.$data_cur['ethereum']['btc'].') / lotsize ) * 100 as collateralization');

							$this->db->where('currentState', 4);
							
							$where = "(nextCheckCollateralization IS NULL OR (nextCheckCollateralization IS NOT NULL AND nextCheckCollateralization<now()))";
							$this->db->where($where);
							
							$where = "keepMembers LIKE '%".htmlentities($rule['operator'])."%'";
							$this->db->where($where);
							
							$where = "((keepBond * ".$data_cur['ethereum']['btc'].") / lotsize ) * 100 < ".intval($rule['collateralization']);
							$this->db->where($where);
							$query =  $this->db->get();
							$deposits =  $query->result_array();
							if(empty($deposits)) continue;
							
							$add_item = array(
								'datetime'=>date("Y-m-d h:i:s"),
								'address'=>$rule['address'],
								'operator'=>$rule['operator'],
								'email'=>$rule['email'],
								'collateralization'=>$rule['collateralization'],
								'sended'=>0,
								'params'=>json_encode($deposits)
							);
							
							$date = new DateTime(date("Y-m-d H:i:s"));
							$date->modify('+1 day');
							$nextCheckCollateralization = $date->format('Y-m-d H:i:s');
							
							$this->db->insert('operatorSubscribeQueue', $add_item);
							#next check over 1 day
							foreach($deposits as $d){
								$this->db->query("UPDATE depositHistory SET nextCheckCollateralization='".$nextCheckCollateralization."' WHERE depositContractAddress='".$d['depositContractAddress']."'");
							}
					 }
				 }
			}
		}
	}
	
	public function updateGrants()
	{
		 $this->load->database();
		 
	}
}
