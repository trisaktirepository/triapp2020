<?php
class Exam_Model_DbTable_ExaminerTagging extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'e017_exeminer_tagging';
	protected $_primary = "id";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			
			$select = $db->select()
							->from(array('et'=>$this->_name))
							->where('et.'.$this->_primary .' = ?', $id);
			
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
	
	public function getExaminerData($id=0, $semester_id=0){
	$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			
			$select = $db->select()
							->from(array('et'=>$this->_name))
							->where('et.examiner_id = ?', $id)
							->join(array('c'=>'r010_course'),'et.course_id = c.id', array('course_name'=>'name', 'course_code'=>'code'))
							->joinLeft(array('com'=>'e014_assessment_component'),'et.component_id = com.id', array('component_name'=>'component_name'));
							
							
			if($semester_id!=0){
				$select->where('et.semester_id =?', $semester_id);
			}	
			
			$row = $db->fetchAll($select);
		}
		
		return $row;
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
							->from(array('et'=>$this->_name))
							->order('et.examiner_id');
		
		return $select;
	}
	
	public function addData($postData){
		$auth = Zend_Auth::getInstance();
		
		$data = array(
			'semester_id'  => $postData['semester_id'],
			'course_id'  => $postData['course_id'],
			'examiner_id'  => $postData['examiner_id'],
			'component_id'  => $postData['component_id'],
			'last_edit_by'	=> $postData['last_edit_by'],
			'last_edit_date'	=> date('Y-m-d H:i:s'),				
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
			'course_id'  => $postData['course_id'],
			'component_id'  => $postData['component_id'],
			'last_edit_by'	=> $postData['last_edit_by'],
			'last_edit_date'	=> date('Y-m-d H:i:s'),				
			);
			
		$this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary . ' = ' . (int)$id);
	}
}

