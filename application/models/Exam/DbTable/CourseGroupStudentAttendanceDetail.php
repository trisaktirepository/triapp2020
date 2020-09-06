<?php
class App_Model_Exam_DbTable_CourseGroupStudentAttendanceDetail extends Zend_Db_Table_Abstract {
	
	protected $_name = 'course_group_attendance_detail';
	protected $_primary = "id";
	
	public function getData($id=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
						->from(array('cgad'=>$this->_name));
			
		if($id!=null){
			$selectData->where("cgad.id = '".$id."'");
				
			$row = $db->fetchRow($selectData);
		}else{
			$row = $db->fetchAll($selectData);
		}
			
		if($row){
			return $row;
		}else{
			return null;
		}
	}
	
	public function getDetailData($att_head_id){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$selectData = $db->select()
				->from(array('cgad'=>$this->_name))
				->joinLeft(array('sr'=>'tbl_studentregistration'), 'sr.IdStudentRegistration = cgad.student_id')
				->joinLeft(array('sp'=>'student_profile'), 'sp.appl_id = sr.IdApplication')
				->joinLeft(array('lkpt'=>'tbl_definationms'), 'lkpt.idDefinition = cgad.status', array('status_name'=>'BahasaIndonesia'))
				->where('cgad.course_group_att_id=?',$att_head_id);
			
		
		$row = $db->fetchAll($selectData);
		
			
		if($row){
			return $row;
		}else{
			return null;
		}
	}
	
	public function getStatusCount($att_head_id,$status=array()){
		$null_status = false;
		
		foreach ($status as $index=>$stat){
			if($stat==null){
				$null_status = true;
				unset($status[$index]);
			}
		}
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
			->from(array('cgad'=>$this->_name),array("num"=>"COUNT(*)"))
			->where('cgad.course_group_att_id=?',$att_head_id);
		
		if($null_status){
			$selectData->where('cgad.status in ('.implode(",",$status).') or cgad.status is null ');
		}else{
			$selectData->where('cgad.status in ('.implode(",",$status).')');
		}
		
		$row = $db->fetchRow($selectData);
		
			
		if($row){
			return $row['num'];
		}else{
			return 0;
		}
	}
	
	public function insert(array $data = array()){
	
		if( !isset($data['last_edit_by']) ){
			$auth = $auth = Zend_Auth::getInstance();
				
			$data['last_edit_by'] = $auth->getIdentity()->iduser;
		}
	
		if( !isset($data['last_edit_date']) ){
			$data['last_edit_date'] = date('Y-m-d H:i:a');
		}
	
		return $this->insert($data);
	}
	
	public function update(array $data= array(),$where){
	
		if( !isset($data['last_edit_by']) ){
			$auth = $auth = Zend_Auth::getInstance();
	
			$data['last_edit_by'] = $auth->getIdentity()->iduser;
		}
	
		if( !isset($data['last_edit_date']) ){
			$data['last_edit_date'] = date('Y-m-d H:i:a');
		}
	
		return parent::update($data,$where);
	}
	
	public function getAttendanceStatusCount($group_id,$student_id,$status){
		
		$status=implode(',', $status);
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
						->from(array('cgad'=>$this->_name),array("num"=>"COUNT(*)"))
						->join(array('cga'=>'course_group_attendance'), 'cga.id = cgad.course_group_att_id',array())
						->where('cga.group_id=?',$group_id)
						->where('cgad.student_id=?',$student_id) 
						->where('cgad.status in ('.$status.')');
		
		$row = $db->fetchRow($selectData);
		//echo $selectData;exit;	
		if($row){
			return $row['num'];
		}else{
			return 0;
		}
	}	
	
		public function getAttendanceStatusCountAllGroup($Allgroup,$student_id,$status){
		
			$selectData = $db->select()
			->from(array('cgad'=>$this->_name),array("num"=>"COUNT(*)"))
			->join(array('cga'=>'course_group_attendance'), 'cga.id = cgad.course_group_att_id')
			->where('cga.group_id in (?)',$Allgroup)
			->where('cgad.student_id=?',$student_id)
			->where('cgad.status=?',$status);
		
			$row = $db->fetchRow($selectData);
				
			if($row){
				return $row['num'];
			}else{
				return 0;
			}
		}
	
	public function getAttendanceStatusCountPerSession($group_id,$student_id,$status,$attid){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
		->from(array('cgad'=>$this->_name))
		->join(array('cga'=>'course_group_attendance'), 'cga.id = cgad.course_group_att_id')
		->where('cga.group_id=?',$group_id)
		->where('cga.id=?',$attid)
		->where('cgad.student_id=?',$student_id);
	
	
	
		$row = $db->fetchRow($selectData);
		$dateattendance=$row['last_edit_date'];
	
		$selectData = $db->select()
		->from(array('cgad'=>$this->_name),array("num"=>"COUNT(*)"))
		->join(array('cga'=>'course_group_attendance'), 'cga.id = cgad.course_group_att_id')
		->where('cga.group_id=?',$group_id)
		->where('cgad.student_id=?',$student_id)
		->where('cgad.last_edit_date<=?',$dateattendance)
		->where('cgad.status=?',$status);
	
		$row = $db->fetchRow($selectData);
	
			
		if($row){
			return $row['num'];
		}else{
			return 0;
		}
	
	}
	
	public function getAttendanceSessionCount($group_id,$student_id){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$selectData = $db->select()
		->from(array('cgad'=>$this->_name),array("num"=>"COUNT(*)"))
		->join(array('cga'=>'course_group_attendance'), 'cga.id = cgad.course_group_att_id')
		->where('cga.group_id=?',$group_id)
		->where('cgad.student_id=?',$student_id);
	
		$row = $db->fetchRow($selectData);
	
			
		if($row){
			return $row['num'];
		}else{
			return 0;
		}
	
	}
	public function getAttendanceByStd($group_id,$student_id){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$selectData = $db->select()
		->from(array('cgad'=>$this->_name))
		->join(array('cga'=>'course_group_attendance'), 'cga.id = cgad.course_group_att_id')
		->join(array('c'=>'tbl_definationms'),'cgad.status=c.iddefinition',array('status'=>'BahasaIndonesia'))
		->where('cga.group_id=?',$group_id)
		->where('cgad.student_id=?',$student_id)
		->order('cga.class_date');
	
		$row = $db->fetchAll($selectData);
	
		return $row;
	
	}
	
	public function getAllgroup($group_id,$program) {
		
		$db = Zend_Db_Table::getDefaultAdapter();
		//select all group id
		$selectGroupId = $db->select()
		->from(array('tg'=>'tbl_course_tagging_group'))
		->join(array('cp'=>'course_group_program'),'cp.group_id=tg.IdCourseTaggingGroup')
		->where('tg.IdCourseTaggingGroup=?',$group_id)
		->where('cp.program_id=?',$program);
			
		$row=$db->fetchRow($selectGroupId);
		$idsubject=$row['IdSubject'];
		$idsemester=$row['IdSemester'];
		
		$selectGroupId = $db->select()
		->from(array('tbl_course_tagging_group'),array('IdCourseTaggingGroup'))
		->join(array('cp'=>'course_group_program'),'cp.group_id=tg.IdCourseTaggingGroup')
		->where('IdSubject=?',$idsubject)
		->where('IdSemester=?',$idsemester)
		->where('IdProgram=?',$program);
		$rows=$db->fetchAll($selectGroupId);
		foreach ($rows as $key=>$row)
			$groupid[$key]=$row['IdCourseTaggingGroup'];
		return $groupid;
	}
}
?>