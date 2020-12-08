<?php 
class Dashboard_model extends CI_Model {
    public function __construct(){
        $this->load->database();
    }

	public function get_totalMinted(){
        $query = $this->db->query("SELECT MAX(value) AS value FROM  TokenContract WHERE `from`='0x0000000000000000000000000000000000000000' GROUP BY txhash ORDER BY txhash;");
        $totalMinted = 0;
        foreach ($query->result_array() as $row)
		{
			 $totalMinted+=$row['value'];
		}
        return $totalMinted;  
    }

	public function get_currentSuply(){
        $totalRedempted = 0;
        $totalMinted_ = 0;
        
        $query = $this->db->query("SELECT SUM(value) AS value FROM  TokenContract WHERE `from`='0x0000000000000000000000000000000000000000'");
        if ($row = $query->result_array())
		{
			 $totalMinted_=$row[0]['value'];
		}
		
        $query = $this->db->query("SELECT SUM(value) AS value FROM  TokenContract WHERE `to`='0x0000000000000000000000000000000000000000'");
        if ($row = $query->result_array())
		{
			 $totalRedempted=$row[0]['value'];
		}

        return ($totalMinted_ - $totalRedempted);  
    }
    
    public function get_transfers24h(){     
        $query = $this->db->query("SELECT count(*) as total FROM  TokenContract WHERE `date`>'".date("Y-m-d 00:00:00")."' AND `from`!='0x0000000000000000000000000000000000000000' and `to`!='0x0000000000000000000000000000000000000000' ORDER BY `date` DESC");
        if ($row = $query->result_array())
		{
			return $row[0]['total'];
		}
		
        return 0;
    }
    
    public function get_deposit_stat(){     
        $query = $this->db->query("SELECT COUNT(*) as counter, DATE_FORMAT(t1.format_date, '%Y-%m-%d') as `date` FROM systemContract AS t1
              WHERE t1.event='Created' AND t1._depositContractAddress IN 
              (SELECT t2._depositContractAddress FROM systemContract t2 WHERE t2.event='Funded' ) 
              GROUP by t1.format_date order by t1.format_date asc;");
              
        if ($result = $query->result_array())
		{
			file_put_contents($_SERVER['DOCUMENT_ROOT']."/assets/json-data/deposites.json",json_encode($result));
		}	
    }
	
	public function get_transfers_stat(){     
        $query = $this->db->query("SELECT ROUND(SUM(value),2) as counter, DATE_FORMAT(format_date, '%Y-%m-%d') as `date` FROM TokenContract
              WHERE `from`!='0x0000000000000000000000000000000000000000' and `to`!='0x0000000000000000000000000000000000000000'
              GROUP by format_date order by format_date asc");
              
        if ($result = $query->result_array())
		{
			file_put_contents($_SERVER['DOCUMENT_ROOT']."/assets/json-data/transfers.json",json_encode($result));
		}	
    }
    
	public function get_keeptransfers_stat(){     
        $query = $this->db->query("SELECT ROUND(SUM(value)) as counter, DATE_FORMAT(format_date, '%Y-%m-%d') as `date` FROM KeepToken
              WHERE `from`!='0x0000000000000000000000000000000000000000' and `to`!='0x0000000000000000000000000000000000000000'
              GROUP by format_date order by format_date asc");
              
        if ($result = $query->result_array())
		{
			file_put_contents($_SERVER['DOCUMENT_ROOT']."/assets/json-data/keeptransfers.json",json_encode($result));
		}	
    }
	
    public function get_redeemed_stat(){     
        $query = $this->db->query("SELECT COUNT(*) as counter, DATE_FORMAT(t1.format_date, '%Y-%m-%d') as `date` FROM systemContract AS t1
              WHERE t1.event='Created' AND t1._depositContractAddress IN 
              (SELECT t2._depositContractAddress FROM systemContract t2 WHERE t2.event='Redeemed' ) 
              GROUP by t1.format_date order by t1.format_date asc;");
              
        if ($result = $query->result_array())
		{
			file_put_contents($_SERVER['DOCUMENT_ROOT']."/assets/json-data/redeemed.json",json_encode($result));
		}	
    }
}
