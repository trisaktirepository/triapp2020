<?php 
class App_Model_Language_DbTable_ApplicantLang extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'applicant_language';
	protected $_primary = "al_id";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary .' = '. $id);
		}else{
			$row = $this->fetchAll();
		}
		
		if(!$row){
			throw new Exception("There is Information");
		}			
		return $row->toArray();
	}
	
	
}

?>