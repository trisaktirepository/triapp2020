<?php


class App_Model_Record_DbTable_AcademicLandscapeCourse extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'r013_academic_landscape_course';
	protected $_primary = "id";
	
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary.' = ' .$id);
		}else{
			$row = $this->fetchAll();
		}
		
		if(!$row){
			throw new Exception("There is No Academic Landscape Course");
		}
		return $row->toArray();
	}
			  
	
	public function getCourse($landscape_id){
		 $db = Zend_Db_Table::getDefaultAdapter();
		 $select  = $db->select()
	                      ->from(array('alc' => $this->_name))
	                      ->where('alc.academic_landscape_id = '.$landscape_id)
	                      ->join(array('c'   => 'r010_course'),'c.id=alc.course_id', array('course_name'=>'name','course_code'=>'code','course_credit_hour'=>'credit_hour'))
	                      ->join(array('ct'=>'r009_course_type'),'ct.id = alc.course_type_id',array('course_type'=>'name'))
	                      ->order('alc.course_type_id'); //yatie change order by course_type_id  15/7/2011
	              
        $result = $db->fetchAll($select);         
        	
        return $result;
	}
	
	public function addData($postData){
		$data = array(
				'academic_landscape_id' => $postData['academic_landscape_id'],
				'course_id' => $postData['course_id'],
				'course_type_id' => $postData['course_type_id'],
				'level' => $postData['level'],
				'is_transferable' => $postData['is_transferable']
				);
			
		$this->insert($data);
	}
	
	public function updateData($postData,$id){
		$data = array(
				'academic_landscape_id' => $postData['academic_landscape_id'],
				'course_id' => $postData['course_id'],
				'course_type_id' => $postData['course_type_id'],
				'level' => $postData['level'],
				'is_transferable' => $postData['is_transferable']
				);
			
		$this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary . ' = ' . (int)$id);
	}

}

