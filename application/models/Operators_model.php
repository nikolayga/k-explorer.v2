<?php 
class Operators_model extends CI_Model {
    public function __construct(){
        $this->load->database();
    }
	
	public function get_lot_list(){
        $sql = "SELECT distinct(lotsize) from depositHistory order by lotsize asc";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
    public function get_list($filter = true){
		$this->load->helper('helper');
		
		
		if($filter==true && (!empty($_REQUEST['state']) || !empty($_REQUEST['depositAddr']) || !empty($_REQUEST['ctn']))){
			$deposits = $this->get_depositis_list();
			$operators = array();
			foreach($deposits as $d){
				$tmp = explode(",",$d['keepMembers']);
				$operators = array_merge($operators,$tmp);
			}
			if(empty($operators)) $operators[]=-1;
			$operators = array_unique($operators);
		}
		
		$this->db->from('operators');
		
		if($filter==true){
			if(!empty($_REQUEST['operatorAddr']) && isEtheriumAddress(trim($_REQUEST['operatorAddr']))){
				$this->db->where('operator', trim($_REQUEST['operatorAddr']));
			}
			
			if(isset($operators) && !empty($operators)){
				$where = "operator IN ('".implode("','",$operators)."')";
				$this->db->where($where);
			}
		}
		
		$total_staked = 0;
		$total_bonded = 0;
		$bond_avail = 0;
		
		$query =  $this->db->get();
		$result = $query->result_array();
		
		foreach($result as $key=>$row){
			 $sql = "SELECT SUM(keepBond) / 3 as bonded, count(*) as total FROM depositHistory WHERE  (currentState!=0 && currentState!=3 && currentState!=7 && currentState!=11) and keepMembers like '%".$row['operator']."%' ";
			 $query_bonded = $this->db->query($sql);
			 $b =  $query_bonded->result_array();
			 
			 $result[$key]['deposits']= round($b[0]['total']);
			 $result[$key]['bonded']= round($b[0]['bonded']); 
			 $total_staked+=$row['staked'];
			 $total_bonded+=$result[$key]['bonded'];
			 $bond_avail+=intval($row['availBond']);
		}
		
        return array('items'=>$result,'total_staked'=>$total_staked,'total_bonded'=>$total_bonded,'bond_avail'=>$bond_avail);
	}
	
	public function get_depositis_list(){
        $this->load->helper('helper');
        
        $this->db->from('depositHistory');
		
		if(!empty($_REQUEST['state']) && is_numeric($_REQUEST['state']) && intval($_REQUEST['state'])>=1 && intval($_REQUEST['state'])<=11) {
			$this->db->where('currentState', intval($_REQUEST['state']));
			if(intval($_REQUEST['state'])==3) $this->db->where('_signingGroupPubkeyX is null');
		}elseif(!empty($_REQUEST['state']) && $_REQUEST['state']=='f') {
			$this->db->where('_signingGroupPubkeyX is not null');
			$this->db->where('currentState', 3);
		}
		
		if(!empty($_REQUEST['lotsize']) && floatval($_REQUEST['lotsize'])>0 && floatval($_REQUEST['lotsize'])<=10) $this->db->where('lotsize', floatval($_REQUEST['lotsize']));
		if(!empty($_REQUEST['isminted']) && intval($_REQUEST['isminted'])==1) $this->db->where('isMinted', 1);
		if(!empty($_REQUEST['isminted']) && intval($_REQUEST['isminted'])==-1) $this->db->where('(isMinted is null or isMinted=0)');
		if(!empty($_REQUEST['depositAddr']) && isEtheriumAddress(trim($_REQUEST['depositAddr']))) $this->db->where('depositContractAddress', trim($_REQUEST['depositAddr']));
		if(!empty($_REQUEST['operatorAddr']) && isEtheriumAddress(trim($_REQUEST['operatorAddr']))){
			$where = "keepMembers LIKE '%".htmlentities($_REQUEST['operatorAddr'])."%'";
			$this->db->where($where);
		}
		if(!empty($_REQUEST['ctn']) && intval($_REQUEST['ctn'])>0) {
			$client = new Codenixsv\CoinGeckoApi\CoinGeckoClient();
			$data_cur = $client->simple()->getPrice('ethereum,bitcoin', 'usd,btc');
			//$pr = ($deposit['keepBond'] * $data['ethereum']['btc'] / $deposit['lotsize']) * 100;	
			//$where = "((keepBond * 393) / (lotsize * 12752)) * 100 < ".intval($_REQUEST['securing']);
			$where = "((keepBond * ".$data_cur['ethereum']['btc'].") / lotsize ) * 100 < ".intval($_REQUEST['ctn']);
			$this->db->where($where);
		}
		
		$query =  $this->db->get();
        return $query->result_array();
    }
    
    public function get($address){
        $this->load->helper('helper');
        $result = array();
        if(isEtheriumAddress($address)) {
			$query = $this->db->get_where('operators', array('operator' => $address));
			$result ['operator'] = $query->result_array()[0];
			
			$sql = "SELECT SUM(keepBond) / 3 as bonded, count(*) as total FROM depositHistory WHERE  (currentState!=0 && currentState!=3 && currentState!=7 && currentState!=11) and keepMembers like '%".$result ['operator']['operator']."%' ";
			$query_bonded = $this->db->query($sql);
			$b =  $query_bonded->result_array();
			 
			$result ['operator']['deposits']= round($b[0]['total']);
			$result ['operator']['bonded']= round($b[0]['bonded']); 
			  
			//$sql = "SELECT count(*) as total_f FROM depositHistory WHERE  currentState=3 and pubKey is not null  and pubKey = '0' and keepMembers like '%".$result ['operator']['operator']."%' ";
			//$query_failed= $this->db->query($sql);
			//$bf =  $query_failed->result_array();
			//$result ['operator']['faults']= round($bf[0]['total_f']); 
			
			#groups
			$sql = "SELECT count(*) as maxGroups FROM `KeepRandomBeaconOperator` WHERE `event` = 'DkgResultSubmittedEvent'";
			$query_groups= $this->db->query($sql);
			$bg =  $query_groups->result_array();
			$result ['operator']['maxGroups']=$bg[0]['maxGroups']; 
			
			#tbtc rewards
			$tbtc_rewards = 0;
			$sql = "SELECT * FROM depositHistory WHERE currentState!=3 AND keepMembers like '%".$result ['operator']['operator']."%' ";
			$query_rewards = $this->db->query($sql);
			$deposits =  $query_rewards->result_array();
			foreach($deposits as $deposit){
				$sql = "SELECT sum(value) as rewards FROM TokenContract WHERE `to` = '".$deposit ['depositContractAddress']."' AND `from` = '0x0000000000000000000000000000000000000000'";
				$query_rewards = $this->db->query($sql);
				$rewards =  $query_rewards->result_array();
				$tbtc_rewards+=floatval($rewards[0]['rewards']);
			}
			
			#securing deposits
			$sql = "SELECT * FROM depositHistory WHERE (currentState!=0 && currentState!=3 && currentState!=7 && currentState!=11)  AND keepMembers like '%".$result ['operator']['operator']."%' ";
			$query_rewards = $this->db->query($sql);
			$deposits =  $query_rewards->result_array();
			
			
			$result ['operator']['tbtc_rewards'] = ($tbtc_rewards / 3);
			$result ['operator']['deposits_list'] = $deposits;
			
			if(!empty($_SESSION['AUTH']['ADDRESS'])){
				$query = $this->db->get_where('operatorEventsSubscribe', array('address' => $_SESSION['AUTH']['ADDRESS'],'operator'=>$address));
				if($result ['subscribe'] = $query->result_array()) $result ['subscribe'] = $result ['subscribe'][0];
			}else{
				$result ['subscribe'] = null;
			}

			return $result;
		}
		
		return array();
    }
    
    public function saveETHRewards($address,$rewards_eth){
        $this->load->helper('helper');
        $result = array();
        
        $this->db->set('eth_rewards', $rewards_eth, true);
		$this->db->where('operator', $address);
		$this->db->update('operators');
    }
    
    public function saveBondAvail($address,$bondAvail){
        $this->db->set('availBond', $bondAvail, true);
		$this->db->where('operator', $address);
		$this->db->update('operators');
    }
    
	public function saveStaked($address,$staked){
        $this->db->set('staked', $staked, true);
		$this->db->where('operator', $address);
		$this->db->update('operators');
    }
    
	public function subscribeOperator($data){
        $query = $this->db->get_where('operatorEventsSubscribe', array('address' => $data['address'],'operator'=>$data['operator']));
        if($exist = $query->result_array()){
			foreach($data as $key=>$v) $this->db->set($key, $v, true);
			$this->db->where('address', $data['address']);
			$this->db->where('operator', $data['operator']);
			$this->db->update('operatorEventsSubscribe');
		}else{
			$this->db->insert('operatorEventsSubscribe', $data);
		}
    }
	
	public function unsubscribeOperator($data){
        $this->db->delete('operatorEventsSubscribe', $data);
    }
}
