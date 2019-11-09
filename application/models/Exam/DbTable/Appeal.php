<?php

class App_Model_Exam_DbTable_Appeal extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'tbl_student_appeal';
	protected $_primary = "sa_id";
		
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
	
	
	function getInfo($idStudent,$idSemester,$idSubject,$idComponent){
		
		$db = Zend_Db_Table::getDefaultAdapter();
			
		$select = $db->select()
					   ->from(array('sa'=>$this->_name))
					   ->joinLeft(array('sm'=>'tbl_staffmaster'),'sm.IdStaff=sa.sa_dosen_penilai',array('dosen_penilai'=>'FullName'))
					   ->where('sa_idStudentRegistration = ?',$idStudent)
					   ->where('sa_idSemester = ?',$idSemester)
					   ->where('sa_idSubject = ?',$idSubject)
					   ->where('sa_idComponent = ?',$idComponent);
		
		return $rowSet = $db->fetchRow($select);
		
	}
	

    
}

