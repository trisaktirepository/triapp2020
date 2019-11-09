<?php 

class App_Model_Activity_DbTable_ActivityParticipant extends Zend_Db_Table_Abstract {
	
	protected $_name = 'activity_participant';
	protected $_primary = "IdParticipant";
	
	
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
	
	public function getTotalParticipantStudent($idActivity){
		
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		 $select = $db ->select()		
					  ->from(array('gsm'=>$this->_name))
					  ->join(array('sr'=>'tbl_studentregistration'), 'sr.IdStudentRegistration = gsm.IdStudentRegistration')
					  ->where('gsm.IdActivity = ?',$idActivity);					  
		 $row = $db->fetchAll($select);	
		 
		 if($row)
		 	return count($row);
		 else
		 return 0;
	}
	
	public function getTotalParticipantStaff($idActivity){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('gsm'=>$this->_name))
		->join(array('sr'=>'tbl_staffmaster'), 'sr.IdStaff = gsm.IdStaff')
		->where('gsm.IdActivity = ?',$idActivity);
		$row = $db->fetchAll($select);
			
		if($row)
			return count($row);
		else
			return 0;
	}
	
	
	public function isInStudent($idAct,$stdid,$trx){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('gsm'=>$this->_name))
		->where('gsm.IdActivity = ?',$idAct)
		->where('gsm.appl_id = ?',$stdid)
		->where('gsm.at_trans_id=?',$trx);
		$row = $db->fetchRow($select);
		 
		return $row;
	}
	
	 
	
	public function isInStaff($idAct,$stdid){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('gsm'=>$this->_name))
		->where('gsm.IdActivity = ?',$idAct)
		->where('gsm.IdStaff = ?',$stdid);
		$row = $db->fetchRow($select);
			
		return $row;
	}
	
	public function removeStudent($idStdMinor){		
		
	  $this->delete("IdStudentMinor='". $idStdMinor ."'");
	}
	
	public function removeStudentByGroupById($grp,$stdid){
	
		$this->delete("IdCourseTaggingGroupMinor='". $grp ."' and IdStudentRegistration=".$stdid);
	}
	
	public function getStudent($idGroup){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->distinct()
		->from(array('gsm'=>$this->_name))
		->join(array('sr'=>'applicant_profile'), 'sr.appl_id = gsm.appl_id',array('appl_fname','appl_mname','appl_lname')) 
		->where('IdCourseTaggingGroupMinor = ?',$idGroup)
		->order('sr.appl_fname');
	
		$row = $db->fetchAll($select);
		return $row;
	}
	
	public function getStudentById($idGroup,$applid){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->distinct()
		->from(array('gsm'=>$this->_name))
		->join(array('g'=>'activity_participant_group'),'g.IdParticipant=gsm.IdParticipant')
		->join(array('sr'=>'applicant_profile'), 'sr.appl_id = gsm.appl_id',array('appl_fname','appl_mname','appl_lname'))
		->join(array('at'=>'applicant_transaction'),'at.at_trans_id=gsm.at_trans_id',array('at_pes_id'))
		->join(array('ap'=>'applicant_program'),'at.at_trans_id=ap.ap_at_trans_id',array())
		->join(array('p'=>'tbl_program'),'p.ProgramCode=ap.ap_prog_code',array('ProgramName'=>'p.ArabicName'))
		->join(array('c'=>'tbl_collegemaster'),'c.IdCollege=p.IdCollege',array('CollegeName'=>'c.ArabicName'))
		->where('g.IdGroup = ?',$idGroup)
		->where('sr.appl_id=?',$applid)
		->where('ap.ap_usm_status=1')
		->order('sr.appl_fname');
	
		$row = $db->fetchRow($select);
		return $row;
	}
	 
	
	public function getStudentbyGroup($idGroup,$student=null,$idprogram=null){
	
		$db = Zend_Db_Table::getDefaultAdapter();	
		
	    $select = $db ->select()
					->from(array('gsm'=>$this->_name))
					->join(array('ct'=>'tbl_activity_tagging_group'),'ct.IdCourseTaggingGroupMinor=gsm.IdCourseTaggingGroupMinor')
					->join(array('sr'=>'tbl_studentregistration'), 'sr.IdStudentRegistration = gsm.IdStudentRegistration')
					->join(array('sp'=>'student_profile'), 'sp.appl_id = sr.IdApplication')
					->join(array('p'=>'tbl_program'), 'p.IdProgram=sr.IdProgram',array('ProgramName'=>'ArabicName','ProgramCode'))
					->where('ct.IdCourseTaggingGroupMinor = ?',$idGroup)				
					->order('sr.registrationId');
		//echo $select;exit;
		if ($idprogram!=null) {
			$select->where('sr.IdProgram=?',$idprogram);
		}	
			
		if(isset($student)){
			$select->where("((sp.appl_fname LIKE '%".$student."%'");
			$select->orwhere("sp.appl_mname LIKE '%".$student."%'");
			$select->orwhere("sp.appl_lname LIKE '%".$student."%')");		
			$select->orwhere("sr.registrationId LIKE '%".$student."%')");
		}
		//echo $select;exit;
		
		$row = $db->fetchAll($select);
		
		if($row)
			return $row;
		else
			return null;
	}
	
	 
     
     
    
}