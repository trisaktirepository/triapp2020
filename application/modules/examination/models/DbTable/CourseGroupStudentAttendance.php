<?php
class Examination_Model_DbTable_CourseGroupStudentAttendance extends Zend_Db_Table_Abstract {
	
	protected $_name = 'course_group_attendance';
	protected $_primary = "id";
	
	public function getData($id=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
						->from(array('cga'=>$this->_name));
			
		if($id!=null){
			$selectData->where("cga.id = '".$id."'");
				
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
	
	public function getPaginateData($group_id){
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$selectData = $db->select()
		->from(array('cga'=>$this->_name))
		->joinLeft(array('tu'=>'tbl_user'),'tu.iduser = cga.create_by', array())
		->joinLeft(array('ts'=>'tbl_staffmaster'),'ts.IdStaff = tu.IdStaff', array("creator"=>"FullName"))
		->joinLeft(array('tsm'=>'tbl_staffmaster'),'tsm.IdStaff = cga.lecturer_id', array('FullName'))
		->where('cga.group_id =?',$group_id)
		->order('cga.class_date desc');

		return $selectData;
	}
	
	public function getCourseData($semester,$college=null,$program=null,$dtstart,$dtstop){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$auth = Zend_Auth::getInstance();
		
		
		$selectData = $db->select()
		->from(array('cga'=>$this->_name),array('total_registered_student'))
		->join(array('ct'=>'tbl_course_tagging_group'),'ct.IdCourseTaggingGroup=cga.group_id',array('count'=>'count(*)','GroupName'=>'GroupName','idgroup'=>'IdCourseTaggingGroup'))
		->join(array('sm'=>'tbl_subjectmaster'),'ct.IdSubject=sm.IdSubject',array('SubCode'=>'ShortName','SubjectName'=>'BahasaIndonesia'))
		->join(array('tu'=>'tbl_user'),'tu.iduser = cga.create_by', array())
		->join(array('ts'=>'tbl_staffmaster'),'ts.IdStaff = tu.IdStaff', array("creator"=>"FullName"))
		->join(array('tsm'=>'tbl_staffmaster'),'tsm.IdStaff = cga.lecturer_id', array('Lecturer'=>'FullName','idlecturer'=>'cga.lecturer_id'))
		->where('ct.IdSemester=?',$semester)
		->where('cga.class_date >=?',$dtstart)
		->where('cga.class_date <=?',$dtstop)
		->where('cga.paid_status="0"')
		 
		->group('ct.IdCourseTaggingGroup')
		->group('cga.lecturer_id');
		if ($program!=null) $selectData->where('ts.IdDepartment=?',$program);
		if ($college!=null) $selectData->where('ts.IdCollege=?',$college);
		$row=$db->fetchAll($selectData);
		return $row;
	}
	
	public function insert($data=array()){
	
		if( !isset($data['create_by']) ){
			$auth = $auth = Zend_Auth::getInstance();
				
			$data['create_by'] = $auth->getIdentity()->iduser;
		}
	
		if( !isset($data['create_date']) ){
			$data['create_date'] = date('Y-m-d H:i:a');
		}
	
		return parent::insert($data);
	}
	
	public function getAttendanceByGroup($group_id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
		->from(array('cga'=>$this->_name))
		->joinLeft(array('tu'=>'tbl_user'),'tu.iduser = cga.create_by', array())
		->joinLeft(array('ts'=>'tbl_staffmaster'),'ts.IdStaff = tu.IdStaff', array("creator"=>"FullName"))
		->joinLeft(array('tsm'=>'tbl_staffmaster'),'tsm.IdStaff = cga.lecturer_id', array('FullName'))
		->where('cga.group_id =?',$group_id);
			
		
		$row = $db->fetchAll($selectData);
		
			
		if($row){
			return $row;
		}else{
			return null;
		}
	}
	public function getAttByGroupAndRange($group_id,$lecturer,$startdt,$stopdt,$starttime,$stoptime){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
		->from(array('cga'=>$this->_name))
		->joinLeft(array('ts'=>'tbl_staffmaster'),'ts.IdStaff = cga.lecturer_id', array("StaffName"=>"FullName"))
		->where('cga.group_id =?',$group_id)
		->where('cga.lecturer_id =?',$lecturer)
		->where('cga.class_date >=?',date('Y-m-d',strtotime($startdt)))
		->where('cga.class_date <=?',date('Y-m-d',strtotime($stopdt)));
		if ($starttime!='00:00:00' && $stoptime!='00:00:00') {
				$selectData->where('cga.class_time_start >=?',$starttime);
				$selectData->where('cga.class_time_end <= ?',$stoptime);
		}
		//echo $selectData;exit;
		$row = $db->fetchAll($selectData);
		return $row; 
	}
	
	public function getLectAttBySemester($program,$semesterid,$startdt,$stopdt,$starttime,$stoptime){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
		->from(array('cga'=>$this->_name))
		->join(array('c'=>'tbl_course_tagging_group'),'c.IdCourseTaggingGroup=cga.group_id',array('GroupName','IdSubject'))
		->join(array('pr'=>'tbl_program'),'pr.IdProgram=c.ProgramCreator',array())
		 
		->where('c.IdSemester =?',$semesterid)
		->where('pr.IdProgram =?',$program)
		->where('CONCAT(cga.class_date," ",cga.class_time_start) >=?',date('Y-m-d hh:mm:ss',strtotime(str_replace('/', '-', $startdt).' '.$starttime)))
		->where('CONCAT(cga.class_date," ",cga.class_time_end) <=?',date('Y-m-d hh:mm:ss',strtotime(str_replace('/', '-', $stopdt).' '.$stoptime)))
	 	->order('cga.lecturer_id')
		->order('cga.group_id');
		 
		$row = $db->fetchAll($selectData);
		
		return $row;
		
	}
}
?>