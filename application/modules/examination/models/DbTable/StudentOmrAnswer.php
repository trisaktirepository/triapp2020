<?php
class Examination_Model_DbTable_StudentOmrAnswer extends Zend_Db_Table { 

	protected $_name = 'student_omr_answers';
	protected $_primary = 'soa_id';
		
	public function addData($data){		
	   $id = $this->insert($data);
	   return $id;
	}
	
	public function updateData($data,$id){
		 $this->update($data, $this->_primary .' = '. (int)$id);
	}
	
    public function deleteData($id){		
    	
	  $this->delete('soa_id =' . (int)$id);
	}
	
	
	
	
	//nak dapatkan student yg dah ada OMR info
	public function getStudentList($IdMarksDistributionMaster,$IdMarksDistributionDetails,$formData=null){	
	
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
		  			 ->from(array('soa'=>$this->_name))
		  			 ->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration=soa.soa_IdStudentRegistration',array('registrationId'))
		  			 ->join(array('sp'=>'student_profile'),'sp.appl_id=sr.idApplication',array('student_name'=>"CONCAT(appl_fname,' ',appl_mname,' ',appl_lname)"))
				     ->where('soa.soa_IdMarksDistributionMaster = ?',$IdMarksDistributionMaster)
				     ->order("sr.registrationId");
				     							  
		 if(isset($formData)){
		 	
		 	if(isset($formData["student_name"]) && $formData["student_name"]!=''){
		 		$select->where("sp.appl_fname  LIKE '%".$formData["student_name"]."%'");
		 		$select->orwhere("sp.appl_mname  LIKE '%".$formData["student_name"]."%'");
		 		$select->orwhere("sp.appl_lname  LIKE '%".$formData["student_name"]."%'");
		 	}
		 	
		    if(isset($formData["student_id"]) && $formData["student_id"]!=''){
		 		$select->where("sr.registrationId = ?",$formData["student_id"]);
		 	}
		 }
		 
		// echo $select;
		 
		 $rows = $db->fetchAll($select);
		 
		 return $rows;
	}
	
	
	
	public function checkExist($IdStudentRegistration,$set_code){	
	
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
		  			 ->from(array('soa'=>$this->_name))
		  			 ->where('soa.soa_IdStudentRegistration = ?',$IdStudentRegistration)
		  			 ->where('soa.soa_set_code = ?',$set_code);
	
		$rows = $db->fetchRow($select);
		 
		return $rows;
				     
	}
	
	public function getData($IdStudentRegistration,$soa_exam_groupid){	
	
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
		  			 ->from(array('soa'=>$this->_name))
		  			 ->where('soa.soa_IdStudentRegistration = ?',$IdStudentRegistration)
		  			 ->where('soa.soa_exam_groupid = ?',$soa_exam_groupid);
	
		$rows = $db->fetchRow($select);
		 
		return $rows;
				     
	}
	
	
	public function getDatabyid($id){	
	
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
		  			 ->from(array('soa'=>$this->_name))		  		
		  			 ->where('soa.soa_id = ?',$id);	
		$row = $db->fetchRow($select);
		 
		return $row;
				     
	}
	
	
}
?>