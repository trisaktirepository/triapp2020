<?php

/**
 * CoursePrerequisites
 * 
 * @author Muhamad Alif Muhammad
 * @date Oct 12, 2010
 * @version 
 */

class App_Model_Record_DbTable_CoursePrerequisites extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'r011_course_prerequisites';
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
	
	public function getCourseData($courseid=0){
		
		if($courseid!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
		
			$select = $db->select()
						->from(array('c_pre'=>$this->_name))
						->where('c_pre.course_id = '.$courseid)
						->join(array('course'=>'r010_course'), 'course.id = c_pre.required_course_id');

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
	
	public function getCourseList($courseid=0){
		
		if($courseid!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			
			//get prerequisites course
			$precourselist = $this->getCourseData($courseid);
			
			
			$precourse = "";
			if(isset($precourselist)){
				foreach ($precourselist as $course){
					if($precourse!=""){
						$precourse .= ",".$course['id'];
					}else{
						$precourse .=$course['id'];
					}
				}
				
				$select = $db->select()
						->from('r010_course')
						->where('id not in ('.$precourse.')');
			}else{
				$select = $db->select()
						->from('r010_course');
			}
					
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
	
	public function getPaginateData(){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
							->from($this->_name)
							->order($this->_primary);
		
		return $select;
	}
	
	public function addData($postData){
		$data = array(
				'course_id' => $postData['course_id'],
				'required_course_id' => $postData['required_course_id']
				);
			
		$this->insert($data);
	}
	
	public function updateData($postData,$id){
		$data = array(
				'course_id' => $postData['course_id'],
				'required_course_id' => $postData['required_course_id']
				);
			
		$this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteData($id){
		$this->delete($this->_primary . ' = ' . (int)$id);
	}
	
	public function deleteCourseData($courseid){
		$this->delete('course_id = ' . (int) $courseid);
	}

}

