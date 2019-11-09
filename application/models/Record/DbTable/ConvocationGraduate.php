<?php
class App_Model_Record_DbTable_ConvocationGraduate extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'convocation_graduate';
	protected $_primary = "id";
		
	public function getData($id=0){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('cg'=>$this->_name))
					->join(array('c'=>'convocation'), 'c.c_id = cg.convocation_id')
					//->joinLeft(array('tu'=>'tbl_user'),'tu.iduser = cg.add_by', array('add_by_name'=>"concat_ws(' ',tu.fname,tu.mname,tu.lname)"))
					//->join(array('ay'=>'tbl_academic_year'), 'ay.ay_id = c.c_academic_year_id', array('ay_code'))
					;
		
		if($id!=0){
			$selectData->where("cg.idStudentRegistration = '".$id."'");
			
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
	
	
	
	public function insert(array $data){
		
		$auth = Zend_Auth::getInstance();
		
		if(!isset($data['add_by'])){
			$data['add_by'] = $auth->getIdentity()->iduser;
		}
		
		$data['add_date'] = date('Y-m-d H:i:s');
			
        return parent::insert($data);
	}	

	public function getGraduateList($convocation_id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
		->from(array('cg'=>$this->_name))
		->joinLeft(array('tu'=>'tbl_user'),'tu.iduser = cg.add_by', array('add_by_name'=>"concat_ws(' ',tu.fname,tu.mname,tu.lname)"))
		->join(array('c'=>'convocation'), 'c.c_id = cg.convocation_id')
		->join(array('ay'=>'tbl_academic_year'), 'ay.ay_id = c.c_academic_year_id', array('ay_code'))
		->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration = cg.IdStudentRegistration', array('registrationId', 'idIntake'))
		->join(array('sp'=>'student_profile'),'sp.appl_id = sr.IdApplication', array('appl_fname', 'appl_mname', 'appl_lname'))
		->join(array('pr'=>'tbl_program'), 'pr.IdProgram = sr.IdProgram', array('program_name'=>'ArabicName'))
		->where('convocation_id = ?', $convocation_id);
		
		$row = $db->fetchAll($selectData);
		
		return $row;
	}
	
}

?>