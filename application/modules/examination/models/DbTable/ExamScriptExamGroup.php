<?php 

class Examination_Model_DbTable_ExamScriptExamGroup extends Zend_Db_Table_Abstract {
	
	protected $_name = 'exam_script_exam_group';
	protected $_primary = "IdScriptExam";
	

public function getAllByScript($idScript){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('esg'=>$this->_name))
		->join(array('eg'=>'exam_group'),'esg.IdExamGroup=eg.eg_id')
		->join(array('sc'=>'exam_script_main'),'esg.IdScript=sc.IdScript')
		->where('esg.IdScript = ?',$idScript);
		$row = $db->fetchAll($select);
		
		return $row;
	}
	
	 
	
	public function isIn($idScript,$idexamscript){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('esg'=>$this->_name))
		->where('esg.IdScript = ?',$idScript)
		->where('esg.IdExamGroup=?',$idexamscript);
		$row = $db->fetchRow($select);
	
		return $row;
	}
	  
	public function insertData($data=array()){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		return $db->insert($this->_name,$data);
	}
	
	public function updateData($data=array(),$idGroup){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		return $db->update($this->_name,$data,$this->_primary.'='.(int)$idGroup);
	}
	
	public function deleteData($idGroup){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		return $db->delete($this->_name,$this->_primary.'='.(int)$idGroup);
	}
	
}