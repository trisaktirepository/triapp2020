<?php 
class App_Model_Application_DbTable_ApplicantPlacementSchedule extends Zend_Db_Table_Abstract
{
    protected $_name = 'appl_placement_schedule';
	protected $_primary = "aps_id";
	
	
	public function getInfo($id=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$id = (int)$id;
		
		    $select = $db ->select()
						->from(array('aps'=>$this->_name))
						->where("aps_test_date > curdate()");

			//echo $select;
									
	        $stmt = $db->query($select);
	        $row = $stmt->fetchAll();
		    return $row;
	}
	
	
	public function getLocationByDate($date,$time='',$aps=null){
		
			$db = Zend_Db_Table::getDefaultAdapter();
		
		    $select = $db ->select()
						->from(array('aps'=>$this->_name))
						->joinLeft(array('l'=>'appl_location'),'l.al_id=aps.aps_location_id ',array('location_id'=>'l.al_id','location_name'=>'l.al_location_name'))
						->where("aps.aps_test_date = '".$date."'");
		    
			if ($time!=0 && $time!='')	$select->where("aps.aps_start_time = '".$time."'");
			if ($aps!=null) $select->where('aps_placement_code=?',$aps);
	       //echo $select;exit;
	        $row = $db->fetchAll($select);
			return $row;
	}
		
	public function addData($data){		
	   $id = $this->insert($data);
	   return $id;
	}
	
	public function updateData($data,$id){
		 $this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteData($id){		
	  $this->delete($this->_primary .' =' . (int)$id);
	}
	
	
public function getAvailableDate($appl_id=0, $txn_id=0,$aphtype=0,$placementcode=0){
			
			$db = Zend_Db_Table::getDefaultAdapter();
			
			/* $select_date = $db ->select()
			->from(array('at'=>'tbl_academic_period'))
			->where('ap_va_expired >= curdate()')
			->order('ap_va_expired ASC');
			$txn=$db->fetchRow($select_date); */
			 
			
		 	$select_date = $db ->select()
						->from(array('at'=>'applicant_transaction'),array())
						->join(array('apt'=>'applicant_ptest'),'apt.apt_at_trans_id=at.at_trans_id',array())
						->join(array('aps'=>'appl_placement_schedule'),'aps.aps_id=apt.apt_aps_id',array('aps_test_date'=>'distinct(aps.aps_test_date)'))
						->where("at_appl_id= '".$appl_id."'")
						->where('aps.aps_placement_code=?',$placementcode)
		 				->where('at.at_pes_id is not null');
						//->where("at_appl_type = 1");
						
			//if($txn_id!=0){
			//	$select_date->where("at_trans_id != '".$txn_id."'");
			//}			
		
		    $select = $db ->select()
						->from(array('aps'=>$this->_name))
						//->join(array('per'=>'tbl_academic_period'),'aps.aps_test_date=per.ap_usm_date')
						->join(array('aph'=>'appl_placement_head'),'aps.aps_placement_code=aph.aph_placement_code',array('aph_fees_program','aph_fees_location'))
						->where("aph.aph_testtype = '".$aphtype."'") 
						->where('aps.aps_placement_code=?',$placementcode)
						->where("aps_test_date NOT IN (?)",$select_date)
		    			//->where("aps_test_date >= DATE_ADD(NOW(), INTERVAL 1 DAYS)");
		    			->where("aps_test_date >= NOW()");
 		
	        $stmt = $db->query($select);
	        $row = $stmt->fetchAll();
	       // echo date('Y-m-d h:s:i', strtotime(date('now')));
	      // echo $select;
	       //echo var_dump($row);exit;
		    return $row;
	}
	
	
	public function getAvailableDateOld($appl_id=0, $txn_id=0,$aphtype=0,$placementcode=0){
			
		$db = Zend_Db_Table::getDefaultAdapter();
			
		$select_date = $db ->select()
		->from(array('at'=>'tbl_academic_period'))
		->where('ap_va_expired >= curdate()')
		->order('ap_va_expired ASC');
		$txn=$db->fetchRow($select_date);
	
			
		$select_date = $db ->select()
		->from(array('at'=>'applicant_transaction'),array())
		->join(array('apt'=>'applicant_ptest'),'apt.apt_at_trans_id=at.at_trans_id',array())
		->join(array('aps'=>'appl_placement_schedule'),'aps.aps_id=apt.apt_aps_id',array('aps_test_date'=>'distinct(aps.aps_test_date)'))
		->where("at_appl_id= '".$appl_id."'")
		->where('aps.aps_placement_code=?',$placementcode)
		->where("at_appl_type = 1");
	
		if($txn_id!=0){
			$select_date->where("at_trans_id != '".$txn_id."'");
		}
	
		$select = $db ->select()
		->from(array('aps'=>$this->_name))
		->join(array('per'=>'tbl_academic_period'),'aps.aps_test_date=per.ap_usm_date')
		->join(array('aph'=>'appl_placement_head'),'aps.aps_placement_code=aph.aph_placement_code',array('aph_fees_program','aph_fees_location'))
		->where("aph.aph_testtype = '".$aphtype."'")
		->where("ap_va_expired >=NOW()")
		->where('aps.aps_placement_code=?',$placementcode)
		->where("aps_test_date NOT IN (?)",$select_date);
			
		$stmt = $db->query($select);
		$row = $stmt->fetchAll();
		// echo date('Y-m-d h:s:i', strtotime(date('now')));
		// echo $select;
		//echo var_dump($row);exit;
		return $row;
	}
	
	public function getData($id=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$id = (int)$id;
		
		    $select = $db ->select()
						->from(array('aps'=>$this->_name))
						->where($this->_primary .' = '. (int)$id);
 			$row = $db->fetchRow($select);
			return $row;
	}
	
	
	public function getAvailableDateAgent($appl_id=0, $txn_id=0){
			
			$db = Zend_Db_Table::getDefaultAdapter();
			
		 	$select_date = $db ->select()
						->from(array('at'=>'applicant_transaction'),array())
						->join(array('apt'=>'applicant_ptest'),'apt.apt_at_trans_id=at.at_trans_id',array())
						->join(array('aps'=>'appl_placement_schedule'),'aps.aps_id=apt.apt_aps_id',array('aps_test_date'=>'distinct(aps.aps_test_date)'))
						->where("at_appl_id= '".$appl_id."'")						
						->where("at_appl_type = 1");
						
			if($txn_id!=0){
				$select_date->where("at_trans_id != '".$txn_id."'");
			}			
		
		    $select = $db ->select()
						->from(array('aps'=>$this->_name))
						->where("aps_test_date > curdate()");
						//->where("aps_test_date NOT IN (?)",$select_date);

			//echo $select;
									
	        $stmt = $db->query($select);
	        $row = $stmt->fetchAll();
		    return $row;
	}
	
	
	
	
}
?>