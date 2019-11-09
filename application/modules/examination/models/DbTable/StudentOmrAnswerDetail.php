<?php
class Examination_Model_DbTable_StudentOmrAnswerDetail extends Zend_Db_Table { 

	protected $_name = 'student_omr_ans_detl';
	protected $_primary = 'soad_id';
		
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
	
	public function deleteDetailData($id){		
	
	  $this->delete("soad_soa_id = '".$id."'");
	}
	
	public function getData($soa_id,$IdMarksDistributionMaster){	
	
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
		  			 ->from(array('soa'=>'student_omr_answers'))
		  			 ->join(array('soad'=>$this->_name),'soa.soa_id=soad.soad_soa_id')
		  			 ->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration=soa.soa_IdStudentRegistration',array('registrationId'))
		  			 ->join(array('sp'=>'student_profile'),'sp.appl_id=sr.idApplication',array('student_name'=>"CONCAT(appl_fname,' ',appl_mname,' ',appl_lname)"))
				     ->where('soa.soa_IdMarksDistributionMaster = ?',$IdMarksDistributionMaster)
				     ->where('soad.soad_soa_id = ?',$soa_id)
				     ->order("sr.registrationId");
		 $rows = $db->fetchAll($select);
		 
		 return $rows;
		 
	}
	
	public function getAnswer($gid,$IdStudentRegistration){	
	
		$db = Zend_Db_Table::getDefaultAdapter();
		
		  $select = $db->select()
		  			 ->from(array('soa'=>'student_omr_answers'))
		  			 ->join(array('soad'=>$this->_name),'soa.soa_id=soad.soad_soa_id')
		  			 ->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration=soa.soa_IdStudentRegistration',array('registrationId'))
		  			 ->where('soa.soa_IdStudentRegistration= ?',$IdStudentRegistration)
		  			 ->where('soa.soa_exam_groupid = ?',$gid)
		  			 ->order("soad.soad_ques_no");
				  
		 $rows = $db->fetchAll($select);
		 
		 return $rows;
		 
	}
	
	
	public function getOmrAnswer($soa_id){		
	
		$db = Zend_Db_Table::getDefaultAdapter();
		
		  $select = $db->select()
		  			 ->from(array('soad'=>$this->_name))		  			
		  			 ->where('soad.soad_soa_id = ?',$soa_id)
		  			 ->order("soad.soad_ques_no");
				  
		 $rows = $db->fetchAll($select);
		 
		 return $rows;
		 
	}
	
	
}
?>