<?php


class App_Model_Record_DbTable_Course extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'r010_course';
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
	
	public function getFacultyCourse($departmentID=0){
		$id = (int)$departmentID;
		
		$db = Zend_Db_Table::getDefaultAdapter();
		if($departmentID!=0){
			$selectData = $db ->select()
							->from('r010_course')
	             		    ->where('department_id = ' .$id);
		}else{
			$selectData = $db ->select()
							->from('r010_course');
		}
		
        $row = $db->fetchAll($selectData);             		    
				
		if(!$row){
			return null;
		}else{
			return $row;
		}
	}
	
	public function search($name="", $code="", $department=0, $faculty=0){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		
		$sql = $db ->select()
						->from(array('course'=>'r010_course'));
						
		if($name!=""){
			$sql->where("course.name like '%" .$name."%'");
		}

		if($code!=""){
			$sql->where("course.code like '%" .$code."%'");
		}
		
		if($department!=0){
			$sql->where("course.department = ?" .$department);
		}
		
		if($faculty!=0){
			$sql->where("course.faculty_id = ?" .$faculty);
		}
		
		$sql->joinLeft(array('department'=>'g008_department'), 'department.id = course.department_id', array('department_name'=>'name','department_code'=>'code'))
            ->joinLeft(array('faculty'=>'g005_faculty'), 'faculty.id = department.faculty_id', array('faculty_name'=>'name','faculty_code'=>'code'));
		
		
		$row = $db->fetchAll($sql);
		
		return $row;
	}
		
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$selectData = $db ->select()
						->from(array('course'=>'r010_course'))
             		    ->joinLeft(array('department'=>'g008_department'), 'department.id = course.department_id', array('department_name'=>'name','department_code'=>'code'))
             		    ->joinLeft(array('faculty'=>'g005_faculty'), 'faculty.id = department.faculty_id', array('faculty_name'=>'name','faculty_code'=>'code'))
             		    ->joinLeft(array('faculty2'=>'g005_faculty'), 'faculty2.id = course.faculty_id', array('faculty_name2'=>'name','faculty_code2'=>'code'));
		
		return $selectData;
	}
	
	public function addData($postData){
		$data = array(
				'code' => $postData['code'],
				'name' => $postData['name'],	
				'credit_hour' => $postData['credit_hour'],
				'faculty_id' => $postData['faculty_id'],
				'department_id' => $postData['department_id'],
				'status' => $postData['status']
				);
			
		$this->insert($data);
	}
	
	public function updateData($postData,$id){
		$data = array(
				'code' => $postData['code'],
				'name' => $postData['name'],	
				'credit_hour' => $postData['credit_hour'],
				'faculty_id' => $postData['faculty_id'],
				'department_id' => $postData['department_id'],
				'status' => $postData['status']
				);
		
		$this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary . " = " . (int)$id);
	}
	
	  public function getCourseList($program_main_id)	{
	
	    $select  = $this->select()
	                    ->from($this,array('id','name','code'));
	                    
	   // if($program_main_id) $select->where('component_id= ?',$program_main_id);
        $result = $this->fetchAll($select);    
        $result = $result->toArray();        
        
        return $result;
	  }
	  
	
	public function selectCourse(){
		
		 $select  = $this->select()
	                    ->from($this,array('id','name','code'));
        $result = $this->fetchAll($select);    
        $result = $result->toArray();        
	    $list = array("Please Select..");
		foreach ($result as $value) {
			$list[$value['id']] = $value['name'];
		}
        return $list;
	}
	
	public function selectCourseByFaculty($faculty_id){
		
		 $select  = $this->select()
	                    ->from($this->_name);
	     if($faculty_id)$select->where('faculty_id = ?',$faculty_id);
	     
	            
	     $result = $this->fetchAll($select);    
         $result = $result->toArray();  
         return $result;  
	}

}

