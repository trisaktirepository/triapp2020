<?php 

class App_Model_Registration_DbTable_RegDateSetup extends Zend_Db_Table_Abstract {
	
	protected $_name = 'reg_date_setup';
	protected $_primary = "rds_id";
	
	
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
	
	public function getData($intake){
		
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$select = $db ->select()
					  ->from($this->_name)					 
					  ->where('rds_intake = ?',$intake);					  
		 $row = $db->fetchAll($select);	
		 
		 if($row)
		 	return $row;
		 else
		 return null;
	}
	
	public function getDataById($id){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from($this->_name)
		->where('rds_id = ?',$id);
		$row = $db->fetchRow($select);
			
		if($row)
			return $row;
		else
			return null;
	}
	
	public function getInfo($txn_id){
		
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$select = $db ->select()
					  ->from(array('rds'=>$this->_name))		
					  ->join(array('at'=>'applicant_transaction'),'at.rds_id=rds.rds_id',array())			 
					  ->where('at.at_trans_id = ?',$txn_id);					  
		 $row = $db->fetchRow($select);	
		 
		 if($row)
		 	return $row;
		 else
		 return null;
	}
	
}
?>