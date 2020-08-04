<?php

class  App_Model_Registration_DbTable_RegChannel extends Zend_Db_Table_Abstract {

	protected $_name = 'registration_channel';
	protected $_primary = "id_rc";
	
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
	
	 
	
	public function getData($id){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('a'=>$this->_name))
		->where("id_rc = ?",$id);
		$row = $db->fetchRow($select);
		return $row;
	}
	
	 
	public function isInRegistration($idstd,$rsid){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('a'=>$this->_name))
		->where("a.transaction_id = ?",$idstd)
		->where("a.rsd_id=?",$rsid);
		$row = $db->fetchRow($select);
		return $row;
	}
	
	public function getDataByTrx($idstd){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('a'=>$this->_name))
		->where("a.transaction_id = ?",$idstd)
		->where("a.rsd_id is not null");
		$row = $db->fetchRow($select);
		if ($row) {
			$select = $db ->select()
			->from(array('a'=>'tbl_reg_confirmation'))
			->where("a.transaction_id = ?",$idstd);
			$rowreg = $db->fetchRow($select);
			if ($rowreg) $row['status']="1"; else $row['status']="";
		} 
		return $row;
	}
	
}

?>