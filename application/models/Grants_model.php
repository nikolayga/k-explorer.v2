<?php 
class Grants_model extends CI_Model {
    public function __construct(){
        $this->load->database();
    }

	public function get_list($all = false, $limit = false, $offset = false){
        $this->db->order_by('id', 'ASC');
        $query = $this->db->get_where('grants', array());
        return $query->result_array();
    }
    
	public function get_total(){
        $query = $this->db->query("SELECT sum(amount) as total FROM grants WHERE 1");
        if ($data = $query->result_array()) return $data[0]['total'];
    }
	
    public function updateData(){
		$this->db->from('grants');
		$this->db->where('revokedAt is null AND cliff<now() AND `end` > now()');
		$query =  $this->db->get();
        if($grants =  $query->result_array()){
			foreach($grants as $grant){
				$dif = strtotime("now")-strtotime($grant['start']);
				$duration = $grant['duration'] * 86400;
				$UnlockedAmount = $grant['amount'] * ($dif / $duration) ;
				$withdrawable = $UnlockedAmount - $grant['withdrawn'] -  $grant['staked'];
				if($withdrawable<0) $withdrawable = 0;

				$this->db->set('unlockedAmount', $UnlockedAmount, true);
				$this->db->set('withdrawable', $withdrawable, true);
				$this->db->where('id', $grant['id']);
				$this->db->update('grants');
			}
		}
	}
	
	public function get_grants_stat(){     
        $result = array();
        
        #Withdrawn
        $query = $this->db->query("SELECT sum(withdrawn) as withdrawn FROM grants WHERE withdrawn>0");
        if ($data = $query->result_array()) $result[]=array("type"=>"Withdrawn","keep"=>round($data[0]['withdrawn']),"color"=>"#289830");
		
		#revokedAmount
        $query = $this->db->query("SELECT sum(revokedAmount) as revokedAmount FROM grants WHERE revokedAmount>0");
        if ($data = $query->result_array()) $result[]=array("type"=>"Revoked","keep"=>round($data[0]['revokedAmount']),"color"=>"#9e0303");
		
		#withdrawable
		$query = $this->db->query("SELECT sum(withdrawable) as withdrawable FROM grants WHERE withdrawable>0");
        if ($data = $query->result_array()) $result[]=array("type"=>"Withdrawable","keep"=>round($data[0]['withdrawable']),"color"=>"#48DBB4");
        
        #staked
		$query = $this->db->query("SELECT sum(staked) as staked FROM grants WHERE staked>0");
        if ($data = $query->result_array()) $result[]=array("type"=>"Staked","keep"=>round($data[0]['staked']),"color"=>"#5BC0DE");
        
        #Locked
		$query = $this->db->query("SELECT (sum(amount) - sum(staked) - sum(withdrawable) - sum(revokedAmount) - sum(withdrawn)) as locked FROM grants WHERE 1");
        if ($data = $query->result_array()) $result[]=array("type"=>"Locked","keep"=>round($data[0]['locked']),"color"=>"#272C30");
        
		file_put_contents($_SERVER['DOCUMENT_ROOT']."/assets/json-data/grants_stat.json",json_encode($result));
    }
    
    public function get($GrantID){    
		$this->db->from('grants');
		$this->db->where('id', $GrantID);
		$query =  $this->db->get();
        return $query->result_array();
	}
}
