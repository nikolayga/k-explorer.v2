<?php 
class Find_model extends CI_Model {
    public function __construct(){
        $this->load->database();
    }

	public function get_list($address){
        $result = array();
        if(!empty($address)){
			$this->db->order_by('date', 'DESC');
			$query = $this->db->get_where('TokenContract', array('from'=>'0x0000000000000000000000000000000000000000','to'=>htmlspecialchars($address)));
			if($arr = $query->result_array()){
				foreach($arr  as $sub_result){
					$query = $this->db->query("SELECT * FROM  TokenContract WHERE txhash='".$sub_result['txhash']."' ORDER BY value DESC");
					if($sub_results= $query->result_array()){
						$tmp = $sub_results[0];
						$tmp['_depositContractAddress']=$sub_results[1]['to'];
						$tmp['fee']=$sub_results[1]['value'];
						
						$sql_state = "select t.*,(select s.event from `systemContract` as s where s._depositContractAddress = t._depositContractAddress and s.event='Funded') as isFunded,(select p.event from `systemContract` as p where p._depositContractAddress = t._depositContractAddress and p.event='Redeemed') as isRedeemed from `systemContract` as t where t.event='Created' and t._depositContractAddress='".$tmp['_depositContractAddress']."'  order by t.`date` DESC";
						$query = $this->db->query($sql_state);
						
						if($state_results= $query->result_array()){
							$tmp['_keepAddress']=$state_results[0]['_keepAddress'];
							$tmp['isFunded']=$state_results[0]['isFunded'];
							$tmp['isRedeemed']=$state_results[0]['isRedeemed'];
						}
						$result[]=$tmp;
					}
				}
			}
		}

        return $result;
    }
}
