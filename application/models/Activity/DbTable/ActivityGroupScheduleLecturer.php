<?php 

class App_Model_Activity_DbTable_ActivityGroupScheduleLecturer extends Zend_Db_Table_Abstract {
	
	protected $_name = 'activity_group_schedule_lecturer';
	protected $_primary = "cgsl_id";
	
	
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
	
	public function getScheduleLecturer($idGroup){
		
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$select = $db ->select()
					  ->from(array("a"=>$this->_name))
					  ->where('cgsl_cglId = ?',$idGroup);	
		 				  
		 $row = $db->fetchAll($select);	
		// echo $select;exit;
		 return $row;
	}
	
	public function getData($id){
		
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$select = $db ->select()
					  ->from($this->_name)
					  ->where('cgsl_id = ?',$id);					  
		 $row = $db->fetchRow($select);	
		 
		 return $row;
	}
	
	public function getDetailsAll($idSchedule){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('s'=>$this->_name))
		->where('cgsl_cglId = ?',$idSchedule);
		$row = $db->fetchAll($select);
			
		return $row;
	}
	
	public function getDetailsInfo($id){
		
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$select = $db ->select()
					  ->from(array('s'=>$this->_name))
					   ->joinLeft(array('sf'=>'tbl_staffmaster'),'sf.IdStaff=s.cgl_IdLecturer',array('sFullName'=>'FullName'))
					  ->where('cgsl_id = ?',$id);					  
		 $row = $db->fetchRow($select);	
		 
		 return $row;
	}
	
	 
	
	public function GetGroupScheduleByLecturer($idlec,$idsemester) {
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
			
		$select = $db ->select()
		//->distinct()
		->from(array('s'=>'activity_group_schedule'),array('sc_date','sc_date_end','sc_day' , 'sc_start_time','sc_end_time','sc_venue'))
		->joinLeft(array('sl'=>'activity_group_schedule_lecturer'),'sl.cgsl_cglid=s.sc_id')
		->join(array('cg'=>'tbl_activity_tagging_group'),'cg.IdCourseTaggingGroup=s.idGroup',array('GroupName'))
		->join(array('c'=>'tbl_subjectmaster'),'c.IdSubject=cg.IdSubject',array('SubjectName'=>'c.BahasaIndonesia','SubCode','subjectMainDefaultLanguage'))
		->where('cg.IdSemester =?',$idsemester)
		->where('cg.IdLecturer='.$idlec.' or s.IdLecturer='.$idlec.' or sl.cgsl_cglId='.$idlec);
	
		$row = $db->fetchAll($select);
			
		return $row;
	
		//
	}
	
	public function isIn($idgroup,$idlec,$name) {
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('cgl'=>$this->_name))
		->where('cgsl_cglId=?',$idgroup)
		->where('cgl_IdLecturer=?',$idlec)
		->where('speaker_name=?',$name);
	
		return $db->fetchRow($select);
	}
}