<?php 

class App_Model_Application_DbTable_Wilayah extends Zend_Db_Table_Abstract {
	
	protected $_name = 'tbl_wilayah';
	protected $_primary = "id_wil";
	
	
	 
	
	public function isIn($idpt) {
		 
	 
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
		->from(array('a'=>$this->_name))
		->where('a.id_sp=?',$idpt) ;
		return $db->fetchRow($select);
		
	}
	
	public function getByWilayah($idwil='') {
			
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('a'=>$this->_name))
		->order('a.nm_wilayah');
		if ($idwil!='')
			$select->where('TRIM(a.id_induk_wilayah)=?',$idwil) ;
		//echo $select;
		return $db->fetchAll($select);
	
	}
	
	public function getCountry() {
			
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('a'=>$this->_name))
		->where('a.id_level_wil=0')
		->order('a.nm_wilayah');
		  
		return $db->fetchAll($select);
	
	}
	  
	
}
?>