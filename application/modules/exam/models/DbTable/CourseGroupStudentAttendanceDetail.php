<?php
class Exam_Model_DbTable_CourseGroupStudentAttendanceDetail extends Zend_Db_Table_Abstract {
	
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
	
public function getAttendanceStatusCount($group_id,$student_id,$status){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
						->from(array('cgad'=>$this->_name),array("num"=>"COUNT(*)"))
						->join(array('cga'=>'course_group_attendance'), 'cga.id = cgad.course_group_att_id',array())
						->where('cga.group_id=?',$group_id)
						->where('cgad.student_id=?',$student_id)
						//->where('cgad.last_edit_date<=?',$dateattendance)
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
}
?>