<?php


class App_Model_Schedule_DbTable_Schedule extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 's001_schedule';
	protected $_primary = "id";
	
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select  = $db->select()
	                    ->from(array('s'=>$this->_name))
	                    ->where("s.".$this->_primary.' = ?', $id)
	                    ->joinLeft(array('p'=>'r006_program'), 'p.id = s.program_id', array('program_name'=>'p.program_name'))
	                    ->joinLeft(array('v'=>'g009_venue'), 'v.id = s.exam_center', array('exam_center_name'=>'v.name'));
	                   
			$row = $db->fetchRow($select);
			
			//echo $select;
		}else{
			$row = $this->fetchAll();
			$row = $row->toArray();
		}
		
		if(!$row){
			throw new Exception("There is No Data");
		}
		
		return $row;
	}
	
	public function getDataComplete($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select  = $db->select()
	                    ->from(array('s'=>$this->_name))
	                    ->where("s.".$this->_primary.' = ?', $id)
	                    ->joinLeft(array('p'=>'r006_program'), 'p.id = s.program_id', array('program_name'=>'p.program_name'))
	                    ->joinLeft(array('v'=>'g009_venue'), 'v.id = s.exam_center', array('exam_center_name'=>'v.name','exam_center_id'=>'v.id'));
	                   
			$row = $db->fetchRow($select);
			
			//echo $select;
			if(!$row){
				throw new Exception("There is No Data");
			}else{
				
				//add course and venue detail
				//course
				$scheduleCourseDB = new App_Model_Schedule_DbTable_ScheduleCourse();
				$courses = $scheduleCourseDB->getScheduleData($row['id']);
				$row['course'] = $courses;
				
				//venue detail
				$scheduleVenueDB = new App_Model_Schedule_DbTable_ScheduleVenue();
				$venue = $scheduleVenueDB->getScheduleData($row['id']);
				$row['venue'] = $venue;
			}
			
			return $row;
			
		}else{
			return null;
		}
		
		
	}
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select  = $db->select()
	                    ->from(array('s'=>$this->_name))
	                    ->joinLeft(array('p'=>'r006_program'), 'p.id = s.program_id', array('program_name'=>'p.program_name'))
	                    ->joinLeft(array('v'=>'g009_venue'), 'v.id = s.exam_center', array('exam_center_name'=>'v.name'));
		
		return $select;
	}
	
	public function addData($postData){
		$data = array(
				'exam_date' => $postData['exam_date'],
				'exam_time_start' => $postData['exam_time_start'],	
				'exam_time_end' => $postData['exam_time_end'],	
				'exam_center' => $postData['exam_center'],	
				'program_id' => $postData['program_id'],
				//'course_id' => $postData['course_id']
				);
			
		return $this->insert($data);
	}
	
	public function updateData($postData,$id){
		$data = array(
				'exam_date' => $postData['exam_date'],
				'exam_time_start' => $postData['exam_time_start'],	
				'exam_time_end' => $postData['exam_time_end'],	
				'exam_center' => $postData['exam_center'],	
				'program_id' => $postData['program_id'],
				//'course_id' => $postData['course_id']
				);
		
		$this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary . " = " . (int)$id);
	}
	
	
	public function getSchedule($startDate, $endDate, $examCenter_id=0, $courseId=0){
		$examCenter_id = (int)$examCenter_id;
		
		if($examCenter_id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select  = $db->select()
	                    ->from(array('s'=>$this->_name))
	                    ->where("s.exam_date >= ?", $startDate)
	                    ->where("s.exam_date <= ?", $endDate)
	                    ->where("s.exam_center = ?", $examCenter_id)
	                    ->joinLeft(array('p'=>'r006_program'), 'p.id = s.program_id', array('program_name'=>'p.program_name'))
	                    //->joinLeft(array('c'=>'s003_schedule_course'), 'c.id = s.course_id', array('course_name'=>'c.name'))
	                    ->joinLeft(array('v'=>'g009_venue'), 'v.id = s.exam_center', array('exam_center_name'=>'v.name'));
	        
			
			if($courseId!=0){	
	        	$select->joinLeft(array('cs'=>'s003_schedule_course'), 'cs.schedule_id = s.id', array('cs.course_id'));
	            $select->joinLeft(array('c'=>'r010_course'), 'c.id = cs.course_id', array('course_name'=>'c.name'));
	            $select->where("c.id = ?", $courseId);
	        }
	        
	        //echo $select;
	                    
			$row = $db->fetchAll($select);
			
		}else{
			$db = Zend_Db_Table::getDefaultAdapter();
			$select  = $db->select()
	                    ->from(array('s'=>$this->_name))
	                    ->where("s.exam_date >= ?", $startDate)
	                    ->where("s.exam_date <= ?", $endDate)
	                    ->joinLeft(array('p'=>'r006_program'), 'p.id = s.program_id', array('program_name'=>'p.program_name'))
	                    //->joinLeft(array('c'=>'r010_course'), 'c.id = s.course_id', array('course_name'=>'c.name'))
	                    ->joinLeft(array('v'=>'g009_venue'), 'v.id = s.exam_center', array('exam_center_name'=>'v.name'));
	                    
			if($courseId!=0){	
	        	$select->joinLeft(array('cs'=>'s003_schedule_course'), 'cs.schedule_id = s.id', array('cs.course_id'));
	            $select->joinLeft(array('c'=>'r010_course'), 'c.id = cs.course_id', array('course_name'=>'c.name'));
	            $select->where("c.id = ?", $courseId);
	        }
	        	                    
			//echo $select;
	       	$row = $db->fetchAll($select);
		}
		
		return $row;
	}
	
	public function getScheduleAll($examCenter_id=0){
		$examCenter_id = (int)$examCenter_id;
		
		if($examCenter_id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select  = $db->select()
	                    ->from(array('s'=>$this->_name))
	                    ->where("s.exam_date >= ?", date('y-m-d'))
	                    ->where("s.exam_center = ?", $examCenter_id)
	                    ->joinLeft(array('p'=>'r006_program'), 'p.id = s.program_id', array('program_name'=>'p.program_name'))
	                    //->joinLeft(array('c'=>'r010_course'), 'c.id = s.course_id', array('course_name'=>'c.name'))
	                    ->joinLeft(array('v'=>'g009_venue'), 'v.id = s.exam_center', array('exam_center_name'=>'v.name'));
	        //echo $select;           
			$row = $db->fetchAll($select);
			
		}else{
			$db = Zend_Db_Table::getDefaultAdapter();
			$select  = $db->select()
	                    ->from(array('s'=>$this->_name))
	                    ->where("s.exam_date >= ?", date('y-m-d'))
	                    ->joinLeft(array('p'=>'r006_program'), 'p.id = s.program_id', array('program_name'=>'p.program_name'))
	                    //->joinLeft(array('c'=>'r010_course'), 'c.id = s.course_id', array('course_name'=>'c.name'))
	                    ->joinLeft(array('v'=>'g009_venue'), 'v.id = s.exam_center', array('exam_center_name'=>'v.name'));
			//echo $select;
	       	$row = $db->fetchAll($select);
		}
		
		return $row;
	}

	public function getScheduleId($examDate){
		
			$db = Zend_Db_Table::getDefaultAdapter();
			echo $select  = $db->select()
	                    ->from(array('s'=>$this->_name))
	                    ->where("s.exam_date = ?", $examDate)
	                    ->joinLeft(array('p'=>'r006_program'), 'p.id = s.program_id', array('program_name'=>'p.program_name'))
	                    ->joinLeft(array('c'=>'s003_schedule_course'), 'c.schedule_id = s.id', array('course_id'=>'c.course_id'))
	                     ->joinLeft(array('cn'=>'r010_course'), 'cn.id = c.course_id', array('course_name'=>'cn.name'))
	                    ->joinLeft(array('v'=>'g009_venue'), 'v.id = s.exam_center', array('exam_center_name'=>'v.name'));
	        
	        //echo $select;
	                    
			$row = $db->fetchAll($select);
			
		
		return $row;
	}
	
	//get exam by exam center
	public function getScheduleByExamCenter($examDate,$examCenter_id){
		
			$db = Zend_Db_Table::getDefaultAdapter();
			$select  = $db->select()
	                     ->from(array('s'=>$this->_name))
	                    ->where("s.exam_date = ?", $examDate)
	                    ->where("s.exam_center = ?", $examCenter_id)
	                    ->joinLeft(array('p'=>'r006_program'), 'p.id = s.program_id', array('program_name'=>'p.program_name'))
	                    ->joinLeft(array('c'=>'s003_schedule_course'), 'c.schedule_id = s.id', array('course_id'=>'c.course_id'))
	                     ->joinLeft(array('cn'=>'r010_course'), 'cn.id = c.course_id', array('course_name'=>'cn.name'))
	                    ->joinLeft(array('v'=>'g009_venue'), 'v.id = s.exam_center', array('exam_center_name'=>'v.name'));
	        
	        //echo $select;
	                    
			$row = $db->fetchAll($select);
			
		
		return $row;
	}
	
	public  function  schedule_details($id){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select  = $db->select()
                    ->from(array('s'=>$this->_name))
                    ->where("s.".$this->_primary.' = ?', $id);
        
        $row = $db->fetchRow($select);
                    
        return $row;
	}
	
}

