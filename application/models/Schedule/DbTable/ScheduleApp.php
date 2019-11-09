<?php


class App_Model_Schedule_DbTable_ScheduleApp extends Zend_Db_Table_Abstract {
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
			
//			echo $select;
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
	
	
public function getSchedule($startDate, $endDate, $examCenter_id=0,$course_id=0){
		$examCenter_id = (int)$examCenter_id;
		
		//checking 14 days
		$today = date("Y-m-d");
		$newdate = strtotime ( '+14 day' , strtotime ( $today ) ) ;
		$newdate = date ( 'Y-m-j' , $newdate );
 

		
		if($examCenter_id!=0 || $course_id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select  = $db->select()
	                    ->from(array('s'=>$this->_name))
	                    ->where("s.exam_date >= ?", $newdate)
	                    //->where("s.exam_date <= ?", $endDate)
	                    ->where("s.exam_center = ?", $examCenter_id)
	                    ->where("c.course_id = ?", $course_id)
	                    ->joinLeft(array('p'=>'r006_program'), 'p.id = s.program_id', array('program_name'=>'p.program_name'))
	                    ->joinLeft(array('c'=>'s003_schedule_course'), 'c.schedule_id = s.id', array('course_id'=>'c.course_id'))
	                    ->join(array('cr'=>'r010_course'), 'c.course_id = cr.id', array('course_name'=>'cr.name'))
	                    ->joinLeft(array('v'=>'g009_venue'), 'v.id = s.exam_center', array('exam_center_name'=>'v.name'));
//	        echo $select;           
			$row = $db->fetchAll($select);
			
		}else{
			$db = Zend_Db_Table::getDefaultAdapter();
			$select  = $db->select()
	                    ->from(array('s'=>$this->_name))
	                    ->where("s.exam_date >= ?", $newdate)
	                    ->where("s.exam_date <= ?", $endDate)
	                    ->joinLeft(array('p'=>'r006_program'), 'p.id = s.program_id', array('program_name'=>'p.program_name'))
	                    ->joinLeft(array('c'=>'s003_schedule_course'), 'c.schedule_id = s.id', array('course_id'=>'c.course_id'))
	                    ->join(array('cr'=>'r010_course'), 'c.course_id = cr.id', array('course_name'=>'cr.name'))
	                    ->joinLeft(array('v'=>'g009_venue'), 'v.id = s.exam_center', array('exam_center_name'=>'v.name'));
			
	       	$row = $db->fetchAll($select);
		}
//		echo $select;
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
	
public function getSchedulebyCourse($courseID=0){
		$courseID = (int)$courseID;
		
			$db = Zend_Db_Table::getDefaultAdapter();
			$select  = $db->select()
	                    ->from(array('s'=>'s003_schedule_course'))
	                    ->where("s.course_id = ?", $courseID)
	                    ->joinLeft(array('p'=>'s001_schedule'), 'p.id = s.schedule_id', array('*'))
	                    ->join(array('v'=>'g009_venue'), 'v.id = p.exam_center', array('center_name'=>'v.name'))
	                    ->group('p.exam_center');
	        //echo $select;           
			$row = $db->fetchAll($select);
			
		
		
		return $row;
	}
	
public function getScheduleDataView($id=0,$course=0){
		$id = (int)$id;
		$course = (int)$course;
		
		if($id!=0 || $course!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select  = $db->select()
	                    ->from(array('sc'=>$this->_name))
	                    ->where("sc.id = ?", $id)
	                    ->where("c.course_id = ?", $course)
	                    ->joinLeft(array('c'=>'s003_schedule_course'), 'c.schedule_id = sc.id', array('*'))
	                    ->join(array('cr'=>'r010_course'), 'c.course_id = cr.id', array('course_name'=>'cr.name'));
	                 
			$row = $db->fetchAll($select);
		}
		
		return $row;
	}

}

