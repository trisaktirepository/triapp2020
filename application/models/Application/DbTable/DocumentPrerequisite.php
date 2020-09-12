<?php 

class App_Model_Application_DbTable_DocumentPrerequisite extends Zend_Db_Table_Abstract {
	
	protected $_name = 'tbl_prerequisite_document';
	protected $_primary = "idPreReq";
	
	
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
	public function getDataByProgram($testCode,$prog1,$prog2=null,$prog3=null,$prog4=null,$addmission="1") {
			
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('a'=>$this->_name),array('IdDocument'))
		->join(array('def'=>'sis_setup_detl'),'a.IdDocument=def.ssd_id',array('document_name'=>'def.ssd_name_bahasa','code'=>'ssd_name_bahasa','ssd_id'))
		->where('a.test_code=?',$testCode)
		->where('a.for_admission=?',$addmission)
		->order('def.ssd_seq');
		
		if ($prog2!=null && $prog3==null && $prog4==null) {
			$select->distinct();
			$select->where("a.IdProgram='".$prog2."' or a.IdProgram='".$prog1."'");
			//$select->where('a.IdProgram=?',$prog1);
		} else if ($prog2!=null && $prog3!=null && $prog4==null) {
			$select->distinct();
			$select->where("a.IdProgram='".$prog2."' or a.IdProgram='".$prog1."' or a.IdProgram='".$prog3."'");
			//$select->where('a.IdProgram=?',$prog1);
		} else if ($prog2!=null && $prog3!=null && $prog4!=null) {
			$select->distinct();
			$select->where("a.IdProgram='".$prog2."' or a.IdProgram='".$prog1."' or a.IdProgram='".$prog3."' or a.IdProgram='".$prog4."'");
			//$select->where('a.IdProgram=?',$prog1);
		} else {
			$select->where('a.IdProgram=?',$prog1);
		}
		//echo $select;exit;
		return $db->fetchAll($select);
	
	}
	  
	
	
}
?>