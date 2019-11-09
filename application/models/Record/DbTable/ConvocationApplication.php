<?php
/**
 * @author Muhamad Alif
 * @date Jun 11, 2014
 */

class App_Model_Record_DbTable_ConvocationApplication extends Zend_Db_Table_Abstract {
	/**
	 * The default table name
	 */
	protected $_name = 'convocation_application';
	protected $_primary = "id";

	public function getData($id=0){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
		->from(array('ca'=>$this->_name));

		if($id!=0){
			$selectData->where("ca.id = '".$id."'");
				
			$row = $db->fetchRow($selectData);
		}else{
				
			$row = $db->fetchAll($selectData);
		}
			
		if(!$row){
			return null;
		}else{
			return $row;
		}

	}
	public function isIn($id=0){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
		->from(array('ca'=>$this->_name));
	
		if($id!=0){
			$selectData->where("ca.IdStudentRegistration = '".$id."'");
	
			$row = $db->fetchRow($selectData);
			if ($row) return true;else return false;
		} 
		return false;
	
	}
	
	public function getDataByStd($id=0){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
		->from(array('ca'=>$this->_name))
		->joinLeft(array('c'=>'convocation'), 'c.c_id = ca.convocation_id')
		->joinLeft(array('ay'=>'tbl_academic_year'), 'ay.ay_id = c.c_academic_year_id', array('ay_code'));
		;
	
		if($id!=0){
			$selectData->where("ca.IdStudentRegistration = '".$id."'");
	
			$row = $db->fetchRow($selectData);
			return $row;
		}
		return false;
	
	}
	
	public function getApplication($status=null,$program=null){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
						->from(array('ca'=>$this->_name))
						->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration = ca.IdStudentRegistration', array('registrationId', 'idIntake'))
						->join(array('sp'=>'student_profile'),'sp.appl_id = sr.IdApplication', array('appl_fname', 'appl_mname', 'appl_lname'))
						->join(array('pr'=>'tbl_program'), 'pr.IdProgram = sr.IdProgram', array('program_name'=>'ArabicName'))
						->join(array('c'=>'convocation'), 'c.c_id = ca.convocation_id')
						->join(array('ay'=>'tbl_academic_year'), 'ay.ay_id = c.c_academic_year_id', array('ay_code'));
		
		if($status){
			$selectData->where('ca.status = ?', $status);
		}else{
			$selectData->where('ca.status is null');
		}
		
		if ($program!=null) $selectData->where('sr.IdProgram = ?', $program);
		
		$row = $db->fetchAll($selectData);
		
			
		if(!$row){
			return null;
		}else{
			return $row;
		}
	}
	
	public function getProgram($status=null){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
		->from(array('ca'=>$this->_name),array())
		->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration = ca.IdStudentRegistration', array())
		->join(array('pr'=>'tbl_program'), 'pr.IdProgram = sr.IdProgram', array('key'=>'IdProgram','value'=>"CONCAT(ProgramCode,'-',ArabicName)"))
		->group('pr.IdProgram');
		
		if($status){
			$selectData->where('ca.status = ?', $status);
		}else{
			$selectData->where('ca.status is null');
		}
	 
		$row = $db->fetchAll($selectData);
	
			
		if(!$row){
			return null;
		}else{
			return $row;
		}
	}
	
	public function getApplicationHistory(){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
		->from(array('ca'=>$this->_name))
		->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration = ca.IdStudentRegistration', array('registrationId', 'idIntake'))
		->join(array('sp'=>'student_profile'),'sp.appl_id = sr.IdApplication', array('appl_fname', 'appl_mname', 'appl_lname'))
		->join(array('pr'=>'tbl_program'), 'pr.IdProgram = sr.IdProgram', array('program_name'=>'ArabicName'))
		->join(array('c'=>'convocation'), 'c.c_id = ca.convocation_id', array('c_academic_year_id','c_semester_count_type','c_date_from', 'c_date_to', 'c_session', 'c_capacity'))
		->join(array('ay'=>'tbl_academic_year'), 'ay.ay_id = c.c_academic_year_id', array('ay_code'))
		->where('ca.status is not null');
	
	
		$row = $db->fetchAll($selectData);
	
			
		if(!$row){
			return null;
		}else{
			return $row;
		}
	}
	
	public function insert(array $data){
	
		$auth = Zend_Auth::getInstance();
	
		if(!isset($data['apply_date'])){
			$data['apply_date'] = date('Y-m-d H:i:s');
		}
		
		return parent::insert($data);
	}
}
?>