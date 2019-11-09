<?php 

class App_Model_Application_DbTable_ApplicantDocument extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'applicant_documents';
	protected $_primary = "ad_id";
	
	public function addData($data){		
	   $id = $this->insert($data);
	   return $id;
	}
	
	public function updateData($data,$id){		
		 $this->update($data,"ad_id =".(int)$id); 		 
	}
	
	public function updateDocument($data,$ad_appl_id,$ad_type){
		 $this->update($data, 'ad_appl_id  = '. (int)$ad_appl_id . ' and ad_type ='. (int)$ad_type);
	}
	
	public function deleteData($id){		
	  $this->delete($this->_primary .' =' . (int)$id);
	}
	
	
	public function getData($appl_id,$type){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
					  ->from($this->_name)
					  ->where("ad_type = '".$type."'")
					  ->where("ad_appl_id='".$appl_id."'")
					  ->order('ad_id desc limit 1');
		 $row = $db->fetchRow($select);	
		
		 return $row;
	}
	
	public function getDataArray($appl_id,$type){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
					  ->from(array('a'=>$this->_name))
					  ->joinLeft(array('b'=>'sis_setup_detl'), 'b.ssd_id = a.ad_type')
					  ->where("a.ad_type = '".$type."'")
					  ->where("a.ad_appl_id='".$appl_id."'")
					  ->order('a.ad_id desc limit 1');
		 $row = $db->fetchAll($select);	
		
		 return $row;
	}
	
}	
?>