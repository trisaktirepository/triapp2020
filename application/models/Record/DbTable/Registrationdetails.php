<?php


class App_Model_Record_DbTable_Registrationdetails extends Zend_Db_Table_Abstract {
	
	/**
	 * The default table name 
	 */
	protected $_name = 'r016_registrationdetails';
	protected $_primary = "id";
	
	public function getStudent($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('s'=>$this->_name))
					->where('s.id = '.$id);
							
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
	
	public function checkStudent($icno){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		  $select = $db->select()
		             ->from(array('s' => $this->_name))					
					 ->where('s.ARD_IC='.$icno);
	 
        $row = $db->fetchRow($select);
	      
		return $row;
	}
	
	
	public function getStudentProfile($id=0){
		$id = (int)$id;
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
				->from(array('s'=>$this->_name))
				->where('s.id = '.$id)
				->join(array('p'=>'r015_student'),'p.ID=s.idApplication',array('*'))
				->joinLeft(array('c'=>'r010_course'),'c.id=s.idCourse',array('course_name'=>'name'))
				->joinLeft(array('f'=>'f001_paymentmode'),'f.id=s.paymentMode',array('paymentMode'=>'name'))
				->joinLeft(array('v'=>'g009_venue'),'v.id=s.idVenue',array('centername'=>'name'))
				->joinLeft(array('sc'=>'s001_schedule'),'sc.id = s.idSchedule',array('exam_date'=>'exam_date'))
				->joinLeft(array('inv'=>'r019_invoicedetail'),'inv.idApplication = s.id',array('receiptNo'=>'receiptNo'));
						
		$row = $db->fetchRow($select);
		
		return $row;
		
	}
	
	public function getPaginateData($condition=NULL){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
	            ->from(array('s' => $this->_name));		
		
		return $select;	 
	}
	
	public function getPaginateDataFinance($condition=NULL,$where){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		echo $select = $db->select()
	            ->from(array('p'=>'r015_student'))
	            ->join(array('s'=>$this->_name),'p.ID=s.idApplication',array('idApplication'=>'idApplication','dateApplied'=>'dateApplied','paymentStatus'=>'s.paymentStatus'))
	            ->joinLeft(array('c'=>'r010_course'),'c.id=s.idCourse',array('course_name'=>'name'))
	            ->joinLeft(array('f'=>'f001_paymentmode'),'f.id=s.paymentMode',array('paymentMode'=>'name'))
	            ->joinLeft(array('sc'=>'s001_schedule'),'sc.id = s.idSchedule',array('exam_date'=>'exam_date'))
	            
	            ->order('sc.exam_date DESC');
	            
	            if($condition == "list"){
	            	$select->group('p.ARD_NAME');
	            }
	            
	            if($where){
	            	$select->where($where);
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
	            ->joinLeft(array('sc'=>'s001_schedule'),'sc.id = s.idSchedule',array('exam_date'=>'exam_date'))
	            ->joinLeft(array('inv'=>'r019_invoicedetail'),'inv.idApplication = s.id',array('receiptNo'=>'receiptNo'))
	            	//->order('sc.exam_date DESC')
//	            	->order(array('sc.exam_date DESC',
//                           'c.name ASC'))
	            	
					->where('s.idApplication = '.$id);
				
			$row = $db->fetchAll($select);
			
		
		return $row;
		
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
    
    
    
    public function getDataByApplicantID($appID){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
				->from(array ('rd'=>$this->_name))
				->joinLeft(array('c'=>'r010_course'),'c.id=rd.idCourse');
			
		if($appID) $select->where('rd.idApplication = ?', $appID);
		
		$row = $db->fetchAll($select);
		
		return $row;
	}
    
   public function getApplication($idSchedule){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		echo $select = $db->select()
				->from(array ('a'=>$this->_name))
				->join(array('b'=>'r015_student'),'b.id = a.idApplication')
				->where('a.paymentStatus = 1')
				->where('a.course_mark IS NULL')
				->where('a.idSchedule = '.$idSchedule)
				->order('a.id DESC');
			
		$row = $db->fetchAll($select);
		
		return $row;
	}
    
	
	public function updateExamSet($data,$id)
    {
        $this->update($data,'regId = '. (int)$id);
    }
    
	public function checkRegister($id){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
		             ->from(array('s' => $this->_name))	
		             ->joinLeft(array('c'=>'r010_course'),'c.id = s.idCourse',array('course_name'=>'name'))
		             ->joinLeft(array('p'=>'r006_program'),'p.id = s.idProgram',array('program_name'=>'program_name'))	
		             ->joinLeft(array('v'=>'g009_venue'),'v.id = s.idVenue',array('venue_name'=>'name'))		
		             ->joinLeft(array('sc'=>'s001_schedule'),'sc.id = s.idSchedule',array('exam_date'=>'exam_date','exam_time_start'=>'exam_time_start','exam_time_end'=>'exam_time_end'))			
		             ->joinLeft(array('f'=>'f001_paymentmode'),'f.id = s.paymentMode',array('paymentmode'=>'name'))
		             		
					 ->where('s.idApplication ='.$id)
					 ->where('s.idProgram = 1');
	 
        $row = $db->fetchAll($select);
	      
		return $row;
	}
	
public function checkRegisterApplication($id, $course){
		
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
        if($row){
        	return 1;
        }else{
        	return 0;
        }
	      
	}
	
public function countSeatRegister($idSchedule,$idCourse,$idVenue){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		 $select = $db->select()
		             ->from(array('s' => $this->_name))				
					 ->where('s.idSchedule = ?', $idSchedule)
					 ->where('s.idCourse= ?', $idCourse)
					 ->where('s.idVenue= ?', $idVenue)
					 ->where('s.seatStatus = 1');
		
		$result = $db->fetchAll($select);  
	    return $result;
	        
	}
	
	public function getCourseGrade($courseid,$applicantid){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
	            ->from(array('regis' => $this->_name))
	            ->where("regis.idApplication = ?", $applicantid)
	            ->where("regis.idCourse = ?", $courseid);
	            
	    $result = $db->fetchRow($select); 

		return $result;	 
	}
    
	
}

?>