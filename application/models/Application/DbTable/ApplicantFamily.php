<?php 

class App_Model_Application_DbTable_ApplicantFamily extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'applicant_family';
	
	public function addData($data){		
	    
	   $this->_db->insert($this->_name, $data);

	}
	
	public function updateData($data,$id){
		 $this->update($data, 'af_id = '. (int)$id);
	}
	public function updateDataPassword($data,$id){
		$this->update($data, 'af_appl_id = '. (int)$id);
	}
	
	public function deleteData($id){		
	  $this->delete($this->_primary .' =' . (int)$id);
	}
	
	public function getData($id,$type){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db	->select()
					 	->from(array('af'=>$this->_name))
					 	->joinLeft(array('afj'=>'appl_family_job'),'afj.afj_id = af.af_job')
						->where("af.af_appl_id ='".$id."'")
						->where("af.af_relation_type ='".$type."'"); 
				
		$row = $db->fetchRow($select);
		
		if(!$row){
			return null;
		}else{
			return $row;	
		}
		
	}
	
	
	public function fetchdata($id,$type){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
					  ->from($this->_name)
		->where("af_appl_id ='".$id."'")
		->where("af_relation_type ='".$type."'"); 
				
		$row = $db->fetchRow($select);
		return $row;
	}
	
	public function  deletebyprofile($profileID){
		
		$this->delete("af_appl_id=$profileID");
		
	}	
}
?>