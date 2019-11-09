<?php

class App_Model_Exam_DbTable_ResitMaster extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'tbl_student_resit_master';
	protected $_primary = "sr_id_master";
		
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
					   ->from(array('a'=>$this->_name))
					   ->join(array('ms'=>'tbl_student_resit'),'a.sr_id_master=ms.sr_id_master')
					   ->join(array('eat'=>'tbl_examination_assessment_type'),'eat.IdExaminationAssessmentType=ms.sr_idComponentType',array('component_name'=>'DescriptionDefaultlang'))
					   ->where('a.sr_idStudentRegistration = ?',$idStudent)
					   ->where('a.sr_idSemester = ?',$idSemester)
					   ->where('a.sr_idSubject = ?',$idSubject)
					   ->where('ms.sr_idComponent = ?',$idComponent);
		
		return $rowSet = $db->fetchRow($select);
		
	}
	
	function isInMaster($idStudent,$idSemester,$idSubject){
	
		$db = Zend_Db_Table::getDefaultAdapter();
			
		$select = $db->select()
		 
		->from(array('ms'=>'tbl_student_resit_master'))
		->where('ms.sr_idStudentRegistration = ?',$idStudent)
		->where('ms.sr_idSemester = ?',$idSemester)
		->where('ms.sr_idSubject = ?',$idSubject);
	//echo $select;exit;
		return $rowSet = $db->fetchRow($select);
		
	
	}
	function hasChild($id){
	
		$db = Zend_Db_Table::getDefaultAdapter();
			
		$select = $db->select()
			
		->from(array('ms'=>'tbl_student_resit_master')) 
		->where('ms.sr_id_master = ?',$id);
		//echo $select;exit;
		return $rowSet = $db->fetchRow($select);
	
	
	}

    
}

