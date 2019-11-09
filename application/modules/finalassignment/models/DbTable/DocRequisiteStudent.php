<?php 
class Finalassignment_Model_DbTable_DocRequisiteStudent extends Zend_Db_Table_Abstract { //Model Class for Users Details

	protected $_name = 'tbl_TA_syaratberkas_mhs';
	protected $_primary='IdTASyaratBerkasMhs';

	public function init() {
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}
	
	public function addData($postData){
		 
		return $this->insert($postData);
	}
	
	public function updateData($postData, $id){
	
		 
		$this->update($postData, $this->_primary .' = '. (int) $id);
	}
	 
	public function deleteData($id){
		$this->delete($this->_primary . " = " . (int)$id);
	}
	
	public function getData($id){
			 
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->where('p.IdTASyaratBerkasMhs = ?', $id);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	
	  
}

?>