<?php 

class App_Model_Application_DbTable_Sms extends Zend_Db_Table_Abstract {
	
	protected $_name = 'tbl_sms_pdpt';
	protected $_primary = "id_sms";
	
	
	public function addData($data){		
	   $id = $this->insert($data);
	   return $id;
	}
	
	public function updateData($data,$id){
		// echo var_dump($data);echo $id;exit;
		 $this->update($data, $this->_primary .' = "'. $id.'"');
	}
	
	public function deleteData($id){		
	  $this->delete($this->_primary .' =' . (int)$id);
	}
	public function isIn($sms) {
		 
	 
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
		->from(array('a'=>$this->_name))
		->where('a.id_sms=?',$sms) ;
		return $db->fetchRow($select);
		
	}
	public function getDataByPT($idpt) {
			
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('a'=>$this->_name),array('key'=>'id_sms','value'=>"nm_lemb","nm_jenjang"))
		->where('TRIM(a.id_sp)=?',$idpt)
		->order('a.nm_lemb');
		return $db->fetchAll($select);
	
	}
	  
	
	
}
?>