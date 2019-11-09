<?php
class App_Model_Record_DbTable_Convocation extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'convocation';
	protected $_primary = "c_id";
		
	public function getData($id=0){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
					->from(array('c'=>$this->_name))
					->joinLeft(array('tu'=>'tbl_user'),'tu.iduser = c.last_edit_by', array('last_edit_by_name'=>"concat_ws(' ',tu.fname,tu.mname,tu.lname)"))
					->join(array('ay'=>'tbl_academic_year'), 'ay.ay_id = c.c_academic_year_id', array('ay_code'))
					;
		
		if($id!=0){
			$selectData->where("c.c_id = '".$id."'");
			
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
	
	
	public function getConvocationData($academicYearId, $semesterType=0){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
			->from(array('c'=>$this->_name))
			->join(array('ay'=>'tbl_academic_year'), 'ay.ay_id = c.c_academic_year_id', array('ay_code'))
			->joinLeft(array('tu'=>'tbl_user'),'tu.iduser = c.last_edit_by', array('last_edit_by_name'=>"concat_ws(' ',tu.fname,tu.mname,tu.lname)"))
			->where('c.c_academic_year_id=?', $academicYearId)
			->where('c.status = 1');
		
		if($semesterType!=0){
			$selectData->where('c_semester_count_type = ?', $semesterType);
		}
				
		$row = $db->fetchAll($selectData);
		
		if($row){
			return $row;
		}else{
			return null;
		}
	}
	public function getConvoDate(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db->select()
		->from(array('c'=>$this->_name))
		->order('c.c_date_from DESC');
		return $row=$db->fetchRow($selectData);
	}
	public function insert(array $data){
		
		$auth = Zend_Auth::getInstance();
		
		if(!isset($data['c_last_edit_by'])){
			$data['last_edit_by'] = $auth->getIdentity()->iduser;
		}
		
		$data['last_edit_date'] = date('Y-m-d H:i:s');
			
        return parent::insert($data);
	}		
		

	public function update(array $data,$where){
		
		$auth = Zend_Auth::getInstance();
		$data['last_edit_by'] = $auth->getIdentity()->iduser;
		$data['last_edit_date'] = date('Y-m-d H:i:s');
		
		return parent::update($data, $where);
	}
	
	public function deleteData($id=null){
		if($id!=null){
			$data = array(
				'status' => 0				
			);
				
			$this->update($data, "c_id = '".$id."'");
		}
	}	
}

?>