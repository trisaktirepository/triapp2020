<?php


class App_Model_Record_DbTable_Registrationdetailsbooking extends Zend_Db_Table_Abstract {
	
	/**
	 * The default table name 
	 */
	protected $_name = 'r017_registrationdetailsbooking';
	protected $_primary = "id";
	
	public function getStudent($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('s'=>$this->_name))
					->where('s.idApplication = '.$id);
							
			$row = $db->fetchRow($select);
		}else{
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('s'=>$this->_name));
								
			$row = $db->fetchAll($select);
		}
		
		if(!$row){
			throw new Exception("There is No Student Information");
		}
		return $row;
		
	}
	
	
	
	public function checkRegister($id, $course){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
		             ->from(array('s' => $this->_name))	
		             ->joinLeft(array('c'=>'r010_course'),'c.id = s.idCourse',array('course_name'=>'name'))
		             ->joinLeft(array('p'=>'r006_program'),'p.id = s.idProgram',array('program_name'=>'program_name'))	
		             ->joinLeft(array('v'=>'g009_venue'),'v.id = s.idVenue',array('venue_name'=>'name'))		
		             ->joinLeft(array('sc'=>'s001_schedule'),'sc.id = s.idSchedule',array('exam_date'=>'exam_date','exam_time_start'=>'exam_time_start','exam_time_end'=>'exam_time_end'))			
		             ->joinLeft(array('f'=>'f001_paymentmode'),'f.id = s.paymentMode',array('paymentmode'=>'name'))		
					 ->where('s.idApplication ='.$id)
					 ->where('s.idCourse ='.$course)
					 ->where('s.idProgram = 1');
	 
        $row = $db->fetchRow($select);
	      
		return $row;
	}
	
	
	public function getStudentProfile($id=0,$idApp=0){
		
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('s'=>$this->_name))
					->where('s.idApplication = '.$id)
					->where('s.id= '.$idApp)
					->join(array('p'=>'r015_student'),'p.ID=s.idApplication',array('*'))
					->joinLeft(array('c'=>'r010_course'),'c.id=s.idCourse',array('course_name'=>'name'))
					->joinLeft(array('v'=>'g009_venue'),'v.id=s.idVenue',array('venue_name'=>'name'))
					->joinLeft(array('sc'=>'s001_schedule'),'sc.id=s.idSchedule',array('exam_date'=>'exam_date','exam_time_start'=>'exam_time_start','exam_time_end'=>'exam_time_end'))
					->joinLeft(array('f'=>'f001_paymentmode'),'f.id=s.paymentMode',array('paymentMode'=>'name'))
					->joinLeft(array('gc'=>'g001_country'),'gc.id=p.ARD_COUNTRY',array('country_name'=>'name'))
					->joinLeft(array('st'=>'g002_state'),'st.id=p.ARD_STATE',array('state_name'=>'name'));
							
					
			$row = $db->fetchRow($select);
		return $row;
	}
	
	public function getPaginateData($condition=NULL){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		echo $select = $db->select()
	            ->from(array('p'=>'r015_student'))
	            ->join(array('s'=>$this->_name),'p.ID=s.idApplication',array('idApplication'=>'idApplication','dateApplied'=>'dateApplied'))
	            ->joinLeft(array('c'=>'r010_course'),'c.id=s.idCourse',array('course_name'=>'name'))
	            ->joinLeft(array('f'=>'f001_paymentmode'),'f.id=s.paymentMode',array('paymentMode'=>'name'))
	            ->order('s.dateApplied DESC');
	            
	            if($condition == "list"){
	            	$select->group('p.ARD_NAME');
	            }
		
		return $select;	 
	}
	
	public function listStudent($id=0){
		
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('p'=>'r015_student'))
	            	->join(array('s'=>$this->_name),'p.ID=s.idApplication',array('*'))
	            					->joinLeft(array('c'=>'r010_course'),'c.id=s.idCourse',array('course_name'=>'name'))
	            ->joinLeft(array('f'=>'f001_paymentmode'),'f.id=s.paymentMode',array('paymentMode'=>'name'))
	            	->order(array('s.dateApplied DESC',
                           'c.name ASC'))
	            	
					->where('s.idApplication = '.$id);
				
			$row = $db->fetchAll($select);
			
		
		return $row;
		
	}
	
	public function countSeatRegister($idSchedule,$idCourse,$idVenue){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
		             ->from(array('s' => $this->_name))				
					 ->where('s.idSchedule = ?', $idSchedule)
					 ->where('s.idCourse= ?', $idCourse)
					 ->where('s.idVenue= ?', $idVenue)
					 ->where('s.seatStatus != 1');
		
		$result = $db->fetchAll($select);  
	    return $result;
	        
	}
	
	public function add($data){
		$db = Zend_Db_Table::getDefaultAdapter();
        
        $this->insert($data);
        $id = $db->lastInsertId();
        
        return $id;
	}
	
    
	public function updateStudent($data,$id)
    {
        $this->update($data, $this->_primary .' = '. (int)$id);
    }
	
	
}

?>