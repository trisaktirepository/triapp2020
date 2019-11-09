<?php

/**
 * AcademicLandscape
 * 
 * @author Muhamad Alif Muhammad
 * @date Nov 18, 2010
 * @version 
 */

class App_Model_Record_DbTable_AcademicLandscape extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'r012_academic_landscape';
	protected $_primary = "id";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary .' = '. $id);
		}else{
			$row = $this->fetchAll();
		}
		
		if(!$row){
			throw new Exception("There is No Data");
		}
		
		return $row->toArray();
	}
	
	public function getProgramLandscape2($program_id=0){
		
		if($program_id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
		
			$select = $db->select()
						->from(array('al'=>$this->_name))
						->where('al.program_id = '.$program_id)
						->join(array('alc'=>'r013_academic_landscape_course',"alc.academic_landscape_id = al.id"));
			
//			echo $select;
			
			$stmt = $db->query($select);
		
			$row = $stmt->fetchAll();
			
			if($row){
				return $row;	
			}else{
				return null;
			}
			
		}else{
			return null;
		}
		
		
	}
	
	public function getProgramLandscape($program_id){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
							->from(array('l'=>$this->_name))
							->where('l.program_id = '.$program_id)
							->join(array('u'=>'u001_user'),'u.id = l.update_by', array('user'=>'u.fullname'))
							->order($this->_primary);
		
		$stmt = $db->query($select);
		
		$row = $stmt->fetchAll();							
		
		return $row;
	}
	
	public function getActiveProgramLandscape($program_id){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
							->from($this->_name)
							->where('program_id = '.$program_id)
							->where('status = 1')
							->order($this->_primary);
		
		$stmt = $db->query($select);
		
		$row = $stmt->fetch();							
		
		return $row;
	}
	
	public function getAcademicLandscape($landscape_id=0){
		
		if($landscape_id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
		
			$select = $db->select()
						->from(array('al'=>$this->_name))
						->where('al.id = '.$landscape_id)
						->join(array('alc'=>'r013_academic_landscape_course'),'alc.academic_landscape_id = al.id',array('id_landscourse'=>'id','level'=>'level'))
						->join(array('cr'=>'r010_course'),'alc.course_id = cr.id',array('course_name'=>'name','credit_hour'=>'credit_hour','course_code'=>'code','course_id'=>'id'))
						->join(array('ct'=>'r009_course_type'),'alc.course_type_id = ct.id',array('course_type_name'=>'name'))
						->order('alc.level');
						
			
			//echo $select;
			
			$stmt = $db->query($select);
		
			$row = $stmt->fetchAll();
			
			if($row){
				return $row;	
			}else{
				return null;
			}
			
		}else{
			return null;
		}
		return $row;
		
	}
	
	public function getAcademicLandscapeApplication($landscape_id=0){
		
		if($landscape_id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
		
			$select = $db->select()
						->from(array('al'=>$this->_name))
						->where('al.program_id = '.$landscape_id)
						->join(array('alc'=>'r013_academic_landscape_course'),'alc.academic_landscape_id = al.id',array('id_landscourse'=>'id','level'=>'level'))
						->join(array('cr'=>'r010_course'),'alc.course_id = cr.id',array('course_name'=>'name','credit_hour'=>'credit_hour','course_code'=>'code','course_id'=>'id'))
						->join(array('ct'=>'r009_course_type'),'alc.course_type_id = ct.id',array('course_type_name'=>'name'))
						->order('alc.level');
						
			
			//echo $select;
			
			$stmt = $db->query($select);
		
			$row = $stmt->fetchAll();
			
			if($row){
				return $row;	
			}else{
				return null;
			}
			
		}else{
			return null;
		}
		return $row;
		
	}
	
	public function getAcademicTransfer($landscape_id=0,$course=0){
		
		
			$db = Zend_Db_Table::getDefaultAdapter();
		
			$select = $db->select()
						->from(array('al'=>$this->_name))
						->where('al.id = '.$landscape_id)
						->join(array('alc'=>'r013_academic_landscape_course'),'alc.academic_landscape_id = al.id',array('id_landscourse'=>'id','level'=>'level'))
						->where('alc.course_id = '.$course)
						->join(array('cr'=>'r010_course'),'alc.course_id = cr.id',array('course_name'=>'name','credit_hour'=>'credit_hour','course_code'=>'code','course_id'=>'id'))
						->join(array('ct'=>'r009_course_type'),'alc.course_type_id = ct.id',array('course_type_name'=>'name'))
						->order('alc.level');
						
			
			//echo $select;
			
			$row = $db->fetchRow($select);
			
		return $row;
		
	}
	
	public function addData($postData){
		$data = array(
				'program_id' => $postData['program_id'],
				'last_changes' => $postData['last_changes'],
				'update_by' => $postData['update_by'],
				'status' => $postData['status'],
				'landscape_type' => $postData['landscape_type']
				);
			
		$this->insert($data);
	}
	
	public function updateData($postData,$id){
		$data = array(
				'program_id' => $postData['program_id'],
				'last_changes' => $postData['last_changes'],
				'update_by' => $postData['update_by'],
				'status' => $postData['status'],
				'landscape_type' => $postData['landscape_type']
				);
			
		$this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary . ' = ' . (int)$id);
	}
	
	public function getProgramCourse($program_id){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
							->from(array('al'=>$this->_name),array('id'=>'id'))
							->where('program_id = '.$program_id)
							->where('status = 1');
							
		$select2 = $db->select()
							->from(array('alc'=>'r013_academic_landscape_course'))
							->where('alc.academic_landscape_id in ('.$select.')')
							->join(array('c'=>'r010_course'),'c.id = alc.course_id',array('course_name'=>'c.name','course_code'=>'c.code'))
							->order('alc.level ASC');							
		
		$stmt = $db->query($select2);
		
		$row = $stmt->fetchAll();							
		
		return $row;
	}
	
	
    //yatie 
	public function selectCourseAcademicLandscape($program_id){
		 $db = Zend_Db_Table::getDefaultAdapter();
		 
		 //get session user id
	 	 $auth = Zend_Auth::getInstance(); 
	  	 $user_id = $auth->getIdentity()->id; 
	  	 
	  	 $selectIn =  $db ->select()
    	             ->from('e010_exam_user',array('course_id'=>'course_id'))
    	             ->where('user_id = ?',$user_id);
    	
	  
		 $select  = $db->select()
	                      ->from(array('al'  => $this->_name))
	                      ->join(array('alc' => 'r013_academic_landscape_course'),                   
	                    		 			    'al.id=alc.academic_landscape_id')
	                      ->join(array('c'   => 'r010_course'),                   
	                    		 			    'c.id=alc.course_id'); 
	     if($program_id) $select->where('al.program_id =' .$program_id);               
	                     $select->where('c.id IN (?)', $selectIn);
	              
        $result = $db->fetchAll($select);
               
        return $result;
	}
	
	public function getCourseExamUser($program_id,$user_id){
		 $db = Zend_Db_Table::getDefaultAdapter();
		 
		 $selectIN =  $db ->select()
    	             ->from('e010_exam_user',array('course_id'=>'course_id'))
    	             ->where('user_id = ?',$user_id);
    	             
		 $select  = $db->select()
	                      ->from(array('al'  => $this->_name))
	                      ->join(array('alc' => 'r013_academic_landscape_course'),                   
	                    		 			    'al.id=alc.academic_landscape_id')
	                      ->join(array('c'   => 'r010_course'),                   
	                    		 			    'c.id=alc.course_id'); 
	                      
	                     $select->where('al.program_id =' .$program_id);

	     				 $select->where('c.id NOT IN (?)',$selectIN);     

	    echo $select;      
	        
        $result = $db->fetchAll($select);         
      	
        return $result;
	}

}

