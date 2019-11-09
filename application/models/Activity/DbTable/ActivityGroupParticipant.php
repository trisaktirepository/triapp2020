<?php 

class App_Model_Activity_DbTable_ActivityGroupParticipant extends Zend_Db_Table_Abstract {
	
	protected $_name = 'activity_participant_group';
	protected $_primary = "IdActivityParticipantGroup";
	
	
	public function addData($data){		
	   $id = $this->insert($data);
	   return $id;
	}
	
	public function updateData($data,$id){
		 $this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function updateDataByAttrib($data,$where){
		$this->update($data, $where);
	}
	
	public function deleteData($id){		
	  $this->delete($this->_primary .' =' . (int)$id);
	}
	
	public function deleteDataByAttrib($idparticipant,$idgrp){
		$this->delete('IdParticipant='.$idparticipant.' and IdGroup='.$idgrp.' and AttendanceStatus is null');
	}
	
	public function getTotalStudentParticipant($idgroup){
		
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		 $select = $db ->select()		
					  ->from(array('gsm'=>$this->_name))
					  ->join(array('ap'=>'activity_participant'),'ap.idParticipant=gsm.idParticipant')
					  ->join(array('sr'=>'tbl_studentregistration'), 'sr.IdStudentRegistration = ap.IdStudentRegistration')
					  ->where('gsm.idGroup = ?',$idgroup);					  
		 $row = $db->fetchAll($select);	
		 
		 if($row)
		 	return count($row);
		 else
		 return 0;
	}
	
	
	public function getTotalStaffParticipant($idgroup){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('gsm'=>$this->_name))
		->join(array('ap'=>'activity_participant'),'ap.idParticipant=gsm.idParticipant')
		->join(array('sr'=>'tbl_staffmaster'), 'sr.IdStaff = ap.IdStaff')
		->where('gsm.idGroup = ?',$idgroup);
		$row = $db->fetchAll($select);
			
		if($row)
			return count($row);
		else
			return 0;
	}
	
	
	public function isIn($idGroup,$idparticipant){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('gsm'=>$this->_name))
		
		->where('gsm.IdParticipant = ?',$idparticipant);
		if ($idGroup!=null) $select->where('gsm.idGroup = ?',$idGroup);
		$row = $db->fetchRow($select);
			
		return $row;
	}
	
	public function removeStudent($idStdMinor){		
		
	  $this->delete("IdStudentMinor='". $idStdMinor ."'");
	}
	
	public function removeStudentByGroupById($grp,$stdid){
	
		$this->delete("IdCourseTaggingGroupMinor='". $grp ."' and IdStudentRegistration=".$stdid);
	}
	
	public function getStudentPerticipant($idGroup){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->distinct()
		->from(array('gsm'=>$this->_name),array())
		->join(array('ap'=>'activity_participant'),'gsm.IdParticipant=ap.IdParticipant',array('mark'))
		->join(array('sr'=>'tbl_studentregistration'), 'sr.IdStudentRegistration = ap.IdStudentRegistration',array('ParticipantId'=>'registrationId'))
		->join(array('sp'=>'student_profile'), 'sp.appl_id = sr.IdApplication',array('ParticipantName'=>'CONCAT(appl_fname," ",appl_mname," ",appl_lname)'))
		->where('gsm.IdGroup = ?',$idGroup)
		->order('sr.registrationId');
	
		$row = $db->fetchAll($select);
		return $row;
	}
	 
	public function getStaffParticipant($idGroup){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->distinct()
		->from(array('gsm'=>$this->_name))
		->join(array('ap'=>'activity_participant'),'gsm.IdParticipant=ap.IdParticipant')
		->join(array('sr'=>'tbl_staffmaster'), 'sr.IdStaff = ap.IdStaff',array('ParticipantId'=>'StaffId','ParticipantName'=>'CONCAT(FirstName," ",SecondName," ",ThirdName)','mark'))
		->where('gsm.IdGroup = ?',$idGroup)
		->order('sr.registrationId');
	
		$row = $db->fetchAll($select);
		return $row;
	}
	
	public function getExternalParticipant($idGroup){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->distinct()
		->from(array('gsm'=>$this->_name),array())
		->join(array('ap'=>'activity_participant'),'gsm.IdParticipant=ap.IdParticipant',array('ParticipantName','ParticipantId'=>'IdStaff','mark'))
		->where('gsm.IdGroup = ?',$idGroup);
		 
	
		$row = $db->fetchAll($select);
		return $row;
	}
	
	
	 
	 
     
    
     
    
}