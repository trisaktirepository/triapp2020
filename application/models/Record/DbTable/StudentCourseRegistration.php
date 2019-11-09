<?php


class App_Model_Record_DbTable_StudentCourseRegistration extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'r024_student_course_registration';
	protected $_primary = "id";
	
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('scr'=>$this->_name))
					->where('scr.id = ?',$id)
					->join(array('c'=>'r010_course'),'c.id = scr.course_id', array('course_name'=>'c.name','course_code'=>'c.code','course_credit_hour'=>'c.credit_hour'));
					
			$stmt = $db->query($select);						
			$row = $stmt->fetch();		
		}else{
			$row = $this->fetchAll();
			$row=$row->toArray();
		}
		
		if(!$row){
			throw new Exception("There is No Course");
		}
		return $row;
	}
	
		
	public function getRegistrationData($student_id,$semester_id){
		$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('scr'=>$this->_name))
					->where('scr.student_id = ?',$student_id)
					->where('scr.semester_id = ?',$semester_id)
					->where('scr.course_status_id= 1')
					->join(array('c'=>'r010_course'),'c.id = scr.course_id', array('course_name'=>'c.name','course_code'=>'c.code','course_credit_hour'=>'c.credit_hour'))
					->join(array('cs'=>'r026_course_status'),'cs.id = scr.course_status_id', array('status'=>'cs.name'));
									
			$row = $db->fetchAll($select);
			
			return $row;
	}
	
	public function isRegister($student_id,$semester_id, $course_id){
		$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('scr'=>$this->_name))
					->where('scr.student_id = ?',$student_id)
					->where('scr.semester_id = ?',$semester_id)
					->where('scr.course_id = ?',$course_id)
					->join(array('c'=>'r010_course'),'c.id = scr.course_id', array('course_name'=>'c.name','course_code'=>'c.code','course_credit_hour'=>'c.credit_hour'));
		//	echo $select;
			$stmt = $db->query($select);
			$row = $stmt->fetch();
			
			return $row;
	}
	
//	ID 	ARD_NAME 	ARD_NAME_ARAB 	ARD_PROGRAM type of programme award	ARD_MARITAL 	ARD_SEX 	ARD_DOB 	ARD_TYPE_IC 1: Personal ID; 2: Family ID; 3: Passport	ARD_IC 	ARD_IC_PLACE 	ARD_IC_DATE 	ARD_IC_EXPIRE 	ARD_CITIZEN 	ARD_STATUS 	ARD_BATCH 	ARD_COUNTRY 	ARD_CONFIRM_ACCEPT 	ARD_RACE 	ARD_RELIGION 	ARD_ADDRESS1 	ARD_ADDRESS2 	ARD_TOWN 	ARD_POSTCODE 	ARD_STATE 	ARD_TELEPHONE 	ARD_OFF_TEL 	ARD_HPHONE 	ARD_FAX 	ARD_EMAIL 	ARD_DATE_APP month day, year, hour:minute 24hrs	ARD_OFFERED offer or not by system	ARD_PROC_FEE_PAID 	ARD_BRANCH_ID 	ARD_CREDIT_TRANS 	ARD_INTAKE 	ARD_OFFERED_BY 	ARD_MIGRATED
	
	public function addData($postData){
		$auth = Zend_Auth::getInstance();
		
		$data = array(
				'student_id' => $postData['student_id'],
				'semester_id' => $postData['semester_id'],	
				'course_id' => $postData['course_id'],
				'course_status_id' => $postData['course_status_id'],
				'entry_date' => date('Y-m-d H:i:a')
				);
		
		$historyDB = new App_Model_Record_DbTable_StudentCourseRegistrationHistory();
				
		try{
			$id = $this->insert($data);
			$historyDB->insert(array(
				'student_course_registration_id' => $id,
				'student_id' => $postData['student_id'],
				'semester_id' => $postData['semester_id'],		
				'course_id' => $postData['course_id'],
				'course_status_id' => $postData['course_status_id'],
				'entry_by' => $auth->getIdentity()->id,
				'entry_date' => date('Y-m-d H:i:a')
				));
			
			return $id;
			
		}catch (Exception $e){
			throw new Exception("Error: "+$e);
		}
	}
	
	public function updateData($postData,$id){
		$data = array(
				'student_id' => $postData['student_id'],
				'semester_id' => $postData['semester_id'],	
				'course_id' => $postData['course_id'],
				'course_status_id' => $postData['course_status_id'],
				'entry_date' => date('Y-m-d H:i:a')
				);
		
		$this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function getStudentCourse($student_id){
		    $db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('scr'=>$this->_name))					
					->join(array('c'=>'r010_course'),'c.id = scr.course_id', array('course_name'=>'c.name','course_code'=>'c.code','course_credit_hour'=>'c.credit_hour'))
					->where('scr.course_status_id = 1')
					->orWhere('scr.course_status_id =2');
			        
			if($student_id)	$select->where('scr.student_id = ?',$student_id);			
					
					
			
			$stmt = $db->query($select);
			$row = $stmt->fetch();
	}
}

