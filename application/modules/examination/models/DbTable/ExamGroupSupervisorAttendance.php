<?php 

class Examination_Model_DbTable_ExamGroupSupervisorAttendance extends Zend_Db_Table_Abstract {
	
	protected $_name = 'exam_group_supervisor_attendance';
	protected $_primary = "ega_id";
	
	public function getData($group_id, $student_id){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
			->from(array('ega'=>$this->_name))
			->where('ega.ega_eg_id = ?',$group_id)
			->where('ega.ega_supervisor_id =?', $student_id);
		
		$row = $db->fetchRow($select);
		
		 
			return $row; 
	}
	
	public function getGroupData($idGroup){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
					->from(array('ega'=>$this->_name))
					->where('ega.ega_eg_id = ?',$idGroup);
		
		$row = $db->fetchAll($select);
		
		if($row){
			return $row;
		}else{
			return null;
		}
		
	}
	
	public function getExamAttendaceStatus($idsemester,$idSubject,$type,$idsupervisor,$status){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('ega'=>$this->_name))
		->join(array('eg'=>'exam_group'),'ega.ega_eg_id=eg.eg_id',array())
		->where('eg.eg_sem_id = ?',$idsemester)
		->where('ega.ega_supervisor_id=?',$idsupervisor)
		->where('ega.ega_status=?',$status)
		->where('eg.eg_assessment_type=?',$type)
		->where('eg.eg_sub_id = ?',$idSubject);
	
		$row = $db->fetchRow($select);
		//echo $select;exit;
		return $row;
		 
	
	}	
	
	public function getExamAttendacePerSupervisor($idsupervisor,$date,$status){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('ega'=>$this->_name),array())
		->join(array('eg'=>'exam_group'),'ega.ega_eg_id=eg.eg_id',array('eg_date','eg_start_time','eg_stop_time'))
		->where('ega.ega_supervisor_id=?',$idsupervisor)
		->where('ega.ega_status=?',$status) 
		->where('eg.eg_date = ?',date('Y-m-d',strtotime($date)));
	
		$row = $db->fetchAll($select);
		//echo $select;exit;
		return $row;
			
	
	}
	
	public function getExamAttendaceTransportByGroup($groupid,$status){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('ega'=>$this->_name))
		->join(array('sm'=>'tbl_staffmaster'),'sm.IdStaff=ega.ega_supervisor_id',array('IdStaff','StaffGrade','StaffStatus','StaffType'=>'StaffJobType','EduLevel','BankAccountNo'))
		->join(array('eg'=>'exam_group'),'ega.ega_eg_id=eg.eg_id',array('Day'=>'DAYOFWEEK(eg_date)','eg_start_time','eg_end_time'))
		->where('eg.eg_id = ?',$groupid) 
		->where('ega.ega_status=?',$status) 
		->where('ega.dt_trans_payment_created is null');
	
		$row = $db->fetchAll($select);
		//echo $select;exit;
		return $row;
			
	
	}
	
	public function getExamAttendaceSupervisiByGroup($groupid,$status){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('ega'=>$this->_name))
		->join(array('sm'=>'tbl_staffmaster'),'sm.IdStaff=ega.ega_supervisor_id',array('IdStaff','StaffGrade','StaffStatus','StaffType'=>'StaffJobType','EduLevel','BankAccountNo'))
		->join(array('eg'=>'exam_group'),'ega.ega_eg_id=eg.eg_id',array('Day'=>'DAYOFWEEK(eg_date)','eg_start_time','eg_end_time'))
		->where('eg.eg_id = ?',$groupid)
		->where('ega.ega_status=?',$status)
		->where('ega.dt_sup_payment_created is null');
	
		$row = $db->fetchAll($select);
		//echo $select;exit;
		return $row;
			
	
	}
	public function insert($data=array()){
	
		if( !isset($data['ega_last_edit_by']) ){
			$auth = $auth = Zend_Auth::getInstance();
				
			$data['ega_last_edit_by'] = $auth->getIdentity()->iduser;
		}
	
		if( !isset($data['ega_last_edit_date']) ){
			$data['ega_last_edit_date'] = date('Y-m-d H:i:a');
		}
	
		return parent::insert($data);
	}
	
	public function update($data=array(),$where){
		if( !isset($data['ega_last_edit_by']) ){
			$auth = $auth = Zend_Auth::getInstance();
		
			$data['ega_last_edit_by'] = $auth->getIdentity()->iduser;
		}
		
		if( !isset($data['ega_last_edit_date']) ){
			$data['ega_last_edit_date'] = date('Y-m-d H:i:a');
		}
		
		return parent::update($data,$where);
	}
	
	public function getExamGroupAttendanceAll($idsemester,$idprogram,$type){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('ega'=>$this->_name),array())
		->join(array('eg'=>'exam_group'),'ega.ega_eg_id=eg.eg_id')
		->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=eg.eg_sub_id',array('subject_code'=>'SubCode','subject_name'=>'subjectMainDefaultLanguage','faculty_id'=>'IdFaculty'))
		->join(array('s'=>'tbl_semestermaster'),'s.IdSemesterMaster=eg.eg_sem_id',array('semester_name'=>'SemesterMainName'))
		->joinLeft(array('r'=>'appl_room'),'r.av_id=eg.eg_room_id')
		->join(array('ep'=>'exam_group_program'),'eg.eg_id=ep.egp_eg_id',array())
		->join(array('p'=>'tbl_program'),'p.IdProgram=ep.egp_program_id',array())
		->where('eg.eg_sem_id = ?',$idsemester)
		->where('eg.eg_assessment_type=?',$type)
		->where('p.IdProgram=?',$idprogram)
		->group('eg.eg_id');
	
		$row = $db->fetchRow($select);
	
		return $row;
			
	
	}
}