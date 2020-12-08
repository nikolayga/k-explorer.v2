<?php 
class KeepTransfers_model extends CI_Model {
    public function __construct(){
        $this->load->database();
    }

	public function get_list($all = false, $limit = false, $offset = false){
        $this->load->helper('helper');
		if($all==false) $this->db->limit($limit, $offset);
        $this->db->order_by('date', 'DESC');
		$this->db->from('KeepToken');
		$this->db->where(array('from !='=>'0x0000000000000000000000000000000000000000','to !='=>'0x0000000000000000000000000000000000000000'));
		
		if(!empty($_REQUEST['date_from']) && date("Y-m-d",strtotime($_REQUEST['date_from'])) ==$_REQUEST['date_from']) {
			$this->db->where("`date`>='".$_REQUEST['date_from']." 00:00:00'");
		}
		
		if(!empty($_REQUEST['date_to']) && date("Y-m-d",strtotime($_REQUEST['date_to'])) ==$_REQUEST['date_to']) {
			$this->db->where("`date`<='".$_REQUEST['date_to']." 23:25:59'");
		}
		if(!empty($_REQUEST['fromAddr']) && isEtheriumAddress(trim($_REQUEST['fromAddr']))) {
			$this->db->where('from', trim($_REQUEST['fromAddr']));
		}
		if(!empty($_REQUEST['toAddr']) && isEtheriumAddress(trim($_REQUEST['toAddr']))) {
			$this->db->where('to', trim($_REQUEST['toAddr']));
		}
		
		if(!empty($_REQUEST['value_from']) && is_numeric(trim($_REQUEST['value_from']))) {
			$this->db->where("`value`>='".intval($_REQUEST['value_from'])."'");
		}
		
		if(!empty($_REQUEST['value_to']) && isEtheriumAddress(trim($_REQUEST['value_to']))) {
			$this->db->where("`value`<='".intval($_REQUEST['value_from'])."'");
		}
		$query =  $this->db->get();
        return $query->result_array();
    }
}
