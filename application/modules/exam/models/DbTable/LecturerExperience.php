<?php
class Exam_Model_DbTable_LecturerExperience extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'e022_lecturer_experience';
	protected $_primary = "id";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			
			$select = $db->select()
							->from(array('a'=>$this->_name))
							->where('a.'.$this->_primary .' = ?', $id)
							->join(array('al'=>'e021_academic_level'),'a.student_academic_level = al.id', array('level_name'=>'name'));
			
			$row = $db->fetchRow($select);
		}else{
			$row = $this->fetchAll();
			
			$row = $row->toArray();
		}
		
		if(!$row){
			throw new Exception("There is no data");
		}
			
		return $row;
	}
	
	public function getLecturerData($id=0){
	$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			
			$select = $db->select()
							->from(array('a'=>$this->_name))
							->where('a.lecturer_id = ?', $id)
							->join(array('al'=>'e021_academic_level'),'a.student_academic_level = al.id', array('academic_level_name'=>'name'));
			
			$row = $db->fetchAll($select);
		}
		
		return $row;
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
							->from(array('a'=>$this->_name));
		
		return $select;
	}
	
	public function addData($postData){
		$auth = Zend_Auth::getInstance();
		
		$data = array(
			'lecturer_id'  => $postData['lecturer_id'],
			'organization'  => $postData['organization'],
			'position'  => $postData['position'],
			'subject_taught'  => $postData['subject_taught'],
			'student_academic_level'	=> $postData['student_academic_level'],
			'year_from'	=> $postData['year_from'],	
			'year_to'	=> $postData['year_to']			
			);
		
		try{
			$id = $this->insert($data);
		}catch (Exception $e){
			throw new Exception($e);
		}
		
		return $id;
	}
	
	public function updateData($postData,$id){
		$data = array(
			'lecturer_id'  => $postData['lecturer_id'],
			'organization'  => $postData['organization'],
			'position'  => $postData['position'],
			'subject_taught'  => $postData['subject_taught'],
			'student_academic_level'	=> $postData['student_academic_level'],
			'year_from'	=> $postData['year_from'],	
			'year_to'	=> $postData['year_to']			
			);
			
		$this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary . ' = ' . (int)$id);
	}
}

