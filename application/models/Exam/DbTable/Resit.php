<?php

class App_Model_Exam_DbTable_Resit extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'tbl_student_resit';
	protected $_primary = "sr_id";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary .' = '. $id);
		}else{
			$row = $this->fetchAll();
		}
		
		if(!$row){
			throw new Exception("There is No Student Info");
		}			
		return $row->toArray();
	}
	
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
	
	
	function getInfo($idmaster,$idComponent){
		
		$db = Zend_Db_Table::getDefaultAdapter();
			
		$select = $db->select()
					   ->from(array('a'=>$this->_name))
					   ->where('a.sr_id_master = ?',$idmaster)
					   ->where('a.sr_idComponent = ?',$idComponent);
		
		
		return $rowSet = $db->fetchRow($select);
		
	}
	
	function isStudentResit($idstd,$idsem,$idsubject,$idComponent){
	
		$db = Zend_Db_Table::getDefaultAdapter();
			
		$select = $db->select()
		->from(array('a'=>$this->_name))
		->join(array('b'=>'tbl_student_resit_master'),'a.sr_id_master=b.sr_id_master')
		->where('b.sr_idSemester = ?',$idsem)
		->where('b.sr_idSubject = ?',$idsubject)
		->where('b.sr_idStudentRegistration = ?',$idstd)
		->where('a.sr_idComponent = ?',$idComponent);
	
	
		return $rowSet = $db->fetchRow($select);
	
	}
	

    
}

