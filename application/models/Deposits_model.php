<?php 
class Deposits_model extends CI_Model {
    public function __construct(){
        $this->load->database();
    }
	
	public function get_lot_list(){
        $sql = "SELECT distinct(lotsize) from depositHistory order by lotsize asc";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
    
	public function get_list($all = false, $limit = false, $offset = false){
        $this->load->helper('helper');
        
        if($all==false) $this->db->limit($limit, $offset);
        if(!empty($_REQUEST['sorting']) && $_REQUEST['sorting']=='updated') $this->db->order_by('updated', 'DESC');
			else $this->db->order_by('datetime', 'DESC');
			
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
		if(!empty($_REQUEST['s']) && isEtheriumAddress(trim($_REQUEST['s']))) {
			$this->db->or_where('keepAddress', trim($_REQUEST['s']));
			$this->db->or_where('depositContractAddress', trim($_REQUEST['s']));
		}elseif(!empty($_REQUEST['s'])){
			$this->db->or_where('bitcoinAddress', trim($_REQUEST['s']));
			$this->db->or_where('bitcoinTransaction', trim($_REQUEST['s']));
			$this->db->or_where('bitcoinTransaction', trim($_REQUEST['s']));
		}
		
		$query =  $this->db->get();
        return $query->result_array();
    }
    
	public function get_total(){
		$sql = "SELECT COUNT(*) as total from depositHistory ";
        $query = $this->db->query($sql);
        return $query->result_array()[0]['total'];
	}
	
    public function get($address){
        $this->load->helper('helper');
        $result = array();
        if(isEtheriumAddress($address)) {
			$query = $this->db->get_where('depositHistory', array('depositContractAddress' => $address));
			$result ['deposit'] = $query->result_array()[0];
			
			$query = $this->db->get_where('systemContract', array('_depositContractAddress' => $address,'event'=>'Created'));
			$result ['systemCreated'] = $query->result_array()[0];
			
			if(!empty($_SESSION['AUTH']['ADDRESS'])){
				$query = $this->db->get_where('depositEventsSubscribe', array('address' => $_SESSION['AUTH']['ADDRESS'],'contractAddress'=>$address));
				if($result ['subscribe'] = $query->result_array()) $result ['subscribe'] = $result ['subscribe'][0];
			}else{
				$result ['subscribe'] = null;
			}
			
			return $result;
		}
		
		return array();
    }
    
    public function saveKeepMembers($address,$members){
        $this->load->helper('helper');
        $result = array();
        
        $this->db->set('keepMembers', implode(",",$members), true);
		$this->db->where('depositContractAddress', $address);
		$this->db->update('depositHistory');
    }
    
    public function saveKeepBond($address,$bond){
        $result = array();
        
        $this->db->set('keepBond', $bond, true);
		$this->db->where('depositContractAddress', $address);
		$this->db->update('depositHistory');
    }
    
	public function saveState($address,$state){
        $result = array();
        
        $this->db->set('currentState', $state, true);
		$this->db->where('depositContractAddress', $address);
		$this->db->update('depositHistory');
    }
    
	public function subscribeDeposit($data){
        $query = $this->db->get_where('depositEventsSubscribe', array('address' => $data['address'],'contractAddress'=>$data['contractAddress']));
        if($exist = $query->result_array()){
			foreach($data as $key=>$v) $this->db->set($key, $v, true);
			$this->db->where('address', $data['address']);
			$this->db->where('contractAddress', $data['contractAddress']);
			$this->db->update('depositEventsSubscribe');
		}else{
			$this->db->insert('depositEventsSubscribe', $data);
		}
    }
	
	public function unsubscribeDeposit($data){
        $this->db->delete('depositEventsSubscribe', $data);
    }
}
