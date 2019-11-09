<?php 
class App_Model_Application_DbTable_ApplicantEducationDetail extends Zend_Db_Table_Abstract
{
    protected $_name = 'applicant_education_detl';
	protected $_primary = 'aed_id';
	
	public function getData($id=0){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		if($id!=0){
	    	$select = $db ->select()
				->from($this->_name)
				->where("aed_id = '".$id."'");
				
			$row = $db->fetchRow($select);
		}else{
			$select = $db ->select()
				->from($this->_name);
				
			$row = $db->fetch($select);
		}
       
        if($row){
        	return $row;
        }else{
        	return null;
        }
	}
	
	public function getDataByapplID($appl_id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
	     $select = $db ->select()
					->from($this->_name)
					->where("ae_appl_id = '".$appl_id."'");
       
        $row = $db->fetchRow($select);
        
        if($row){
        	return $row;
        }else{
        	return null;
        }
	}
	
	
	public function addData($data){		
	   $id = $this->insert($data);
	   return $id;
	}
	
	public function updateData($data,$id){
		 $this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	
	
	
	
	
}
?>