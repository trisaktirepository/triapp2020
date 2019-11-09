<?php


class App_Model_Record_DbTable_StudentCourseRegistrationHistory extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'r025_student_course_registration_history';
	protected $_primary = "id";
	
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary.' = ' .$id);
		}else{
			$row = $this->fetchAll();
		}
		
		if(!$row){
			throw new Exception("There is No Course");
		}
		return $row->toArray();
	}
	
	public function addData($data,$student_course_registration_id){
		try{
			$auth = Zend_Auth::getInstance();
			
			$this->insert(array(
				'student_course_registration_id' => $student_course_registration_id,
				'student_id' => $data['student_id'],
				'semester_id' => $data['semester_id'],	
				'course_id' => $data['course_id'],
				'course_status_id' => $data['course_status_id'],
				'entry_by' => $auth->getIdentity()->id,
				'entry_date' => date('Y-m-d H:i:a')
				));
			
			return $id;
			
		}catch (Exception $e){
			throw new Exception("Error: "+$e);
		}
	}
	
	public function getRegistrationHistoryData($student_id=null,$semester_id=null){
		
		$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('scrh'=>$this->_name))
					->join(array('c'=>'r010_course'),'c.id = scrh.course_id', array('course_name'=>'c.name','course_code'=>'c.code','course_credit_hour'=>'c.credit_hour'))
					->join(array('cs'=>'r026_course_status'),'cs.id = scrh.course_status_id', array('status'=>'cs.name'))
					->join(array('s'=>'r001_semester'),'s.id = scrh.semester_id',array('semester_id'=>'s.id','semester_name'=>'s.name'));
					
		if($student_id)			$select->where('scrh.student_id = ?',$student_id);
		if($semester_id)		$select->where('scrh.semester_id = ?',$semester_id);
					
		$select->order('scrh.entry_date asc');
		
								
			$row = $db->fetchAll($select);
		
			return $row;
	}
}

