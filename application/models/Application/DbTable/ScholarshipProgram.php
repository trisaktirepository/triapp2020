<?php 

class App_Model_Application_DbTable_Sms extends Zend_Db_Table_Abstract {
	
	protected $_name = 'tbl_scholar_program';
	protected $_primary = "idScholarProgram";
	
	
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
	
	public function isIn($idscholar,$idprogram) {
		 
	 
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
		->from(array('a'=>$this->_name))
		->where('a.idScholar=?',$idscholar)
		->where('a.IdProgram=?',$idprogram) ;
		return $db->fetchRow($select);
		
	}
	public function getDataProgram($scholarid) {
			
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('a'=>$this->_name),array())
		->join(array('p'=>'tbl_program'),'a.IdProgram=p.IdProgram',array('key'=>'IdProgram','value'=>"CONCAT(ArabicName,' (',strata,')')"))
		->where('TRIM(a.idScholar)=?',$scholarid)
		->order('p.ArabicName');
		return $db->fetchAll($select);
	
	}
	
	public function getScholarType() {
			
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('a'=>'sis_setup_detl'),array('key'=>'ssd_id','value'=>"ssd_name_bahasa"))
		->where('TRIM(a.ssd_code)="SCHOLAR"');
		return $db->fetchAll($select);
	
	}
	  
	
	
}
?>