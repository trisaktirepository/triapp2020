<?php
class App_Model_Record_DbTable_StudentSemester extends Zend_Db_Table_Abstract
{
    protected $_name = 'r021_student_semester_status';
    protected $_primary = 'id';
	
    protected $_referenceMap = array (
		
    );
    
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from($this->_name)
					->where($this->_name.".".$this->_primary .' = '. $id);
					
				$row = $db->fetchRow($select);
		}else{
			
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from($this->_name);
								
			$row = $db->fetchAll($select);
		}
		
		if(!$row){
			throw new Exception("There is No Data");
		}
		
		return $row;
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
				->from($this->_name);
								
		return $select;
	}
	
	public function addData($student_id, $semester_id, $sem_status, $current_level=0){
		$data = array(
					'student_id'=>$student_id,
					'semester_id'=>$semester_id,
					'semester_status_id'=>$sem_status,
					'entry_date'=>date('Y-m-d H:i:s')
				);
		
		$id = $this->insert($data);
		
		return $id;
	}
	
	public function isRegistered($student_id, $semester_id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
				->from($this->_name)
				->where('student_id = ?',$student_id)
				->where('semester_id = ?',$semester_id);
			
		$row = $db->fetchAll($select);

		if(!$row){
			return false;
		}else{
			return $row;	
		}
			
	}
	
	//exam
	public function getStudentSemester($student_id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
				  ->from(array('student_sem'=>$this->_name))				
				  ->join(array('semester'=>'r001_semester'),'student_sem.semester_id=semester.id',array('semester_name'=>'name'))
				  ->where('student_sem.student_id = ?', $student_id);
		$row = $db->fetchAll($select);
		//echo $select;
		return $row;
	}
	
	
}