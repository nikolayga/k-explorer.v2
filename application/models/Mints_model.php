<?php 
class Mints_model extends CI_Model {
    public function __construct(){
        $this->load->database();
    }

	public function get_list($all = false, $limit = false, $offset = false){
		if($all==false) $this->db->limit($limit, $offset);
        $this->db->order_by('date', 'DESC');
        $query = $this->db->get_where('TokenContract', array('from'=>'0x0000000000000000000000000000000000000000'));
        return $query->result_array();
    }
}
