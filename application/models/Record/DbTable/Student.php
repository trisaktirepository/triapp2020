<?php


class App_Model_Record_DbTable_Student extends Zend_Db_Table_Abstract {
	
	/**
	 * The default table name 
	 */
	protected $_name = 'r015_student';
	protected $_primary = "ID";
	
	public function getStudent($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('s'=>$this->_name))
					->joinLeft(array('state'=>'g002_state'),'state.id=s.ARD_STATE',array('state_name'=>'name'))
					->joinLeft(array('country'=>'g001_country'),'country.id=s.ARD_COUNTRY',array('country_name'=>'name'))
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
	
	public function getStudent2($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('s'=>$this->_name))
					->joinLeft(array('state'=>'g002_state'),'state.id=s.ARD_STATE',array('state_name'=>'name'))
					->joinLeft(array('country'=>'g001_country'),'country.id=s.ARD_COUNTRY',array('country_name'=>'name'))
					->joinLeft(array('race'=>'g017_definationms'),'race.idDefinition=s.ARD_RACE',array('student_race'=>'race.DefinitionDesc'))
					->joinLeft(array('religion'=>'g017_definationms'),'religion.idDefinition=s.ARD_RELIGION',array('student_religion'=>'religion.DefinitionDesc'))
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
					 ->where("s.ARD_IC='".$icno."'");
        $row = $db->fetchRow($select);
	      
		return $row;
	}
	
	
	public function getStudentProfile($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('s'=>$this->_name))
					->where('s.id = '.$id)
					->join(array('p'=>'r006_program'),'p.id=s.program_id',array('program_id'=>'id'))
					->join(array('mp'=>'r005_program_main'),'mp.id=p.program_main_id',array('main_name'=>'name'))
					->join(array('ct'=>'g001_country'),'s.nationality=ct.id',array('citizen_name'=>'name'))
					->join(array('app'=>'a001_applicant'),'s.application_id=app.ID');
							
			$row = $db->fetchRow($select);
		}else{
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select()
					->from(array('s'=>$this->_name))
					->join(array('p'=>'r006_program'),'p.id=s.program_id',array('program_id'=>'id'))
					->join(array('mp'=>'r005_program_main'),'mp.id=p.program_main_id',array('main_name'=>'name'));
								
			$row = $db->fetchAll($select);
		}
//		if(!$row){
//			throw new Exception("There is No Student Information");
//		}
		return $row;
		
	}
	
	public function getPaginateData($condition=NULL){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
	            ->from(array('s' => $this->_name));

		return $select;	 
	}
	
	public function getPaginateCompanyStudentData($company=0, $condition=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
	            ->from(array('s' => $this->_name))
	            ->joinLeft(array('to'=>'g013_takafuloperator'),'to.id=s.ARD_COMPANY_ID',array('company_name'=>'to.name','company_short_name'=>'to.shortName'));;
	            
	    if($company!=0){        
	    	$select->where("s.ARD_COMPANY_ID = ?", $company);
	    }

		return $select;	 
	}
	
	public function getCompanyStudentData($company=0, $condition=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
	            ->from(array('s' => $this->_name))
	            ->joinLeft(array('to'=>'g013_takafuloperator'),'to.id=s.ARD_COMPANY_ID',array('company_name'=>'to.name','company_short_name'=>'to.shortName'));;
	            
	    if($company!=0){        
	    	$select->where("s.ARD_COMPANY_ID = ?", $company);
	    }
	    
	    if($condition!=null){
	    	$select->where($condition);
	    }
	    
	    $row = $db->fetchAll($select);

		return $row;	 
	}
	
	
	/* =========================================================
        Function   : To get registered student list ( paginator)
        Created by : Yatie April 2012
       ========================================================= */
	
	public function search($condition){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
				->from(array('s'=>$this->_name))
				->joinLeft(array('srd'=>'r016_registrationdetails'),'srd.idApplication=s.id')
				->joinLeft(array('v'=>'g009_venue'),'v.id=srd.idVenue',array('venue'=>'v.name'))				
				->where('paymentStatus=1')
				->order('s.ARD_NAME');
				
		if($condition!=null){	
			if($condition['keyword']!=""){
				$select->where("s.ARD_NAME like '%" .$condition['keyword']."%'");
				$select->Orwhere("s.ARD_IC like '%" .$condition['keyword']."%'");
			}

			if($condition['program_id']!=""){
				$select->where("srd.idProgram =".$condition['program_id']);
			}
			
			if($condition['course_id']!=""){
				$select->where("srd.idCourse =".$condition['course_id']);
			}
			
			if($condition['idVenue']!=""){
				$select->where("srd.idVenue =".$condition['idVenue']);
			}		
		}		
		
		//echo $select;
		$row = $db->fetchAll($select);
		 
		
		return $row;
	}
	
	public function PaginateSearch($condition=null){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
				->from(array('s'=>$this->_name))
				->joinLeft(array('srd'=>'r016_registrationdetails'),'srd.idApplication=s.id')
				->joinLeft(array('v'=>'g009_venue'),'v.id=srd.idVenue',array('venue'=>'v.name'))				
				->where('paymentStatus=1')
				->order('s.ARD_NAME');
				
		if($condition!=null){	
			if($condition['keyword']!=""){
				$select->where("s.ARD_NAME like '%" .$condition['keyword']."%'");
				$select->Orwhere("s.ARD_IC like '%" .$condition['keyword']."%'");
			}

			if($condition['program_id']!=""){
				$select->where("srd.idProgram =".$condition['program_id']);
			}
			
			if($condition['course_id']!=""){
				$select->where("srd.idCourse =".$condition['course_id']);
			}
			
			if($condition['idVenue']!=""){
				$select->where("srd.idVenue =".$condition['idVenue']);
			}		
		}		
		
	//	echo $select;
		 
		
		return $select;
	}
	
	
	
	public function addStudent($data){
		$db = Zend_Db_Table::getDefaultAdapter();
        
        $this->insert($data);
        $id = $db->lastInsertId();
        
        return $id;
	}
	
	
	
	 /* =========================================================
        Function   : To get student list ( paginator)
        Created by : Yatie April 2012
       ========================================================= */
   
	
	public function getPaginateStudentProgramCourse($condition){		
			
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
				->from(array('s'=>$this->_name))
				->join(array('srd'=>'r016_registrationdetails'),'srd.idApplication=s.id');
				
				
	   if($condition!=null){	
			if($condition['keyword']!=""){
				$select->where("s.ARD_NAME like '%" .$condition['keyword']."%'");
				$select->Orwhere("s.ARD_IC like '%" .$condition['keyword']."%'");
			}

			if($condition['program_id']!=""){
				$select->where("srd.idProgram =".$condition['program_id']);
			}
			
			if($condition['course_id']!=""){
				$select->where("srd.idCourse =".$condition['course_id']);
			}		
		}
		
		return $select;
	}
	
	
	
	public function updatePic($id_student,$idpic)
    {
        $data = array(
            'idpic' => $idpic
        );
        
        $this->update($data, $this->_primary .' = '. (int)$id_student);
    }
    
	public function updateStudent($data,$id)
    {
        $this->update($data, $this->_primary .' = '. (int)$id);
    }
    
    
     /* =========================================================
        Function   : To get student list who had atttend the Exam #PAGINATION
        Created by : Yatie April 2012
       ========================================================= */
    
    public function getPaginateStudentAttendExam($program_id=0,$course_id=0,$idVenue=0,$keyword=null){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		 $select = $db->select()
		             ->from(array('s' => $this->_name))					
					 ->join(array('rd' => 'r016_registrationdetails'),
					        's.id=rd.idApplication',array('rd_id' => 'rd.id','regId' => 'rd.regId','courseid'=>'rd.idCourse','programid'=>'rd.idProgram'))
					  ->where("rd.attendance='1'");			
		
		if($program_id) $select->where('rd.idProgram = ?', $program_id);
		if($course_id)  $select->where('rd.idCourse = ?', $course_id);
		if($idVenue   ) $select->where('rd.idVenue = ?', $idVenue);
			 
		if($keyword) {
			$select->where('s.ARD_NAME LIKE ?', '%'.$keyword.'%');
			$select->Orwhere('s.ARD_IC LIKE ?', '%'.$keyword.'%');
		}			
		
		return $select;
	}
	
	
	/* =========================================================
        Function   : To get distinct student list with verified mark #PAGINATION
        Created by : Yatie April 2012
        Modify     : 12 June 2012
       ========================================================= */
    
    public function getPaginateDistinct($program_id=0,$company_id=0,$idVenue=0,$keyword=null,$exam_date=''){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		 $select = $db->select()		             
		             ->from(array('s' => $this->_name))					
					 ->join(array('rd' => 'r016_registrationdetails'),
					        's.id=rd.idApplication',array('rd_id' => 'rd.id','regId' => 'rd.regId','courseid'=>'rd.idCourse','programid'=>'rd.idProgram'))
 					->joinLeft(array('v'=>'g009_venue'),'v.id=rd.idVenue',array('idVenue'=>'v.id','venue'=>'v.name'))					
					->joinLeft(array('sc'=>'s001_schedule'),'sc.id = rd.idSchedule',array('idSchedule'=>'sc.id','exam_date'=>'sc.exam_date'))
					->joinLeft(array('to'=>'g013_takafuloperator'),'to.id = s.ARD_COMPANY_ID',array('company'=>'to.name'))
					->where("rd.attendance='1'")	
					->where("rd.mark_verified=1")					
					->group('s.ARD_IC');	
		
		    if($program_id) $select->where('rd.idProgram = ?', $program_id);
			if($company_id) $select->where('s.ARD_COMPANY_ID  = ?', $company_id);
			if($exam_date)  $select->where("sc.exam_date ='".$exam_date."'");
		
				 
			if($keyword) {
				$select->where('s.ARD_NAME LIKE ?', '%'.$keyword.'%');
				$select->Orwhere('s.ARD_IC LIKE ?', '%'.$keyword.'%');
			}	
		
		    if($idVenue){
				$select->where("rd.idVenue =".$idVenue);
				$select->where("sc.exam_center =".$idVenue);
			}
					
		//echo $select;
		return $select;
	}
	
	
    
    /* =========================================================
        Function : To get student list who had atttend the Exam
        Created by : Yatie April 2012
       ========================================================= */
    
    public function getStudentAttendExam($program_id=0,$course_id=0,$idVenue=0,$keyword=null,$exam_date=""){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		 $select = $db->select()
		             ->from(array('s' => $this->_name))					
					 ->joinLeft(array('rd' => 'r016_registrationdetails'),
					        's.id=rd.idApplication',array('rd_id' => 'rd.id','regId' => 'rd.regId','courseid'=>'rd.idCourse','programid'=>'rd.idProgram','course_mark'=>'rd.course_mark','course_grade'=>'rd.course_grade','grade_symbol'=>'rd.grade_symbol'))
					 ->joinLeft(array('v'=>'g009_venue'),'v.id=rd.idVenue',array('idVenue'=>'v.id','venue'=>'v.name'))	
					 ->joinLeft(array('c'=>'r010_course'),'c.id=rd.idCourse',array('courseid'=>'c.id','cname'=>'c.name','mark_distribution_type'=>'c.mark_distribution_type'))	
					 ->joinLeft(array('sc'=>'s001_schedule'),'sc.id = rd.idSchedule',array('idSchedule'=>'sc.id','exam_date'=>'sc.exam_date'))
					 ->where("rd.mark_verified IS NULL OR rd.mark_verified=0")		
					 ->where("rd.attendance='1'");		
		
			if($program_id) $select->where('rd.idProgram = ?', $program_id);
			if($course_id)  $select->where('rd.idCourse  = ?', $course_id);
			if($exam_date)  $select->where("sc.exam_date ='".$exam_date."'");
		
				 
			if($keyword) {
				$select->where('s.ARD_NAME LIKE ?', '%'.$keyword.'%');
				$select->Orwhere('s.ARD_IC LIKE ?', '%'.$keyword.'%');
			}	
		
		    if($idVenue){
				$select->where("rd.idVenue =".$idVenue);
				$select->where("sc.exam_center =".$idVenue);
			}
							
			
		//echo $select;
			
        $stmt = $db->query($select);
        $row = $stmt->fetchAll();
        
		return $row;
	}
	
	public function checkUsername($username){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		 $select = $db->select()
		             ->from(array('s' => $this->_name))					
					 ->where('s.username="'.$username.'"');
					 
				
	 
        $row = $db->fetchRow($select);
	      
		return $row;
	}
	
	
	/* =========================================================
        Function : To get Registered Student nfo
        Created by : Yatie 2 May 2012
       ========================================================= */
	
	public function getRegisteredStudentInfo($id=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
	            ->from(array('s' => $this->_name))
	            ->joinLeft(array('rd' => 'r016_registrationdetails'),'s.id=rd.idApplication')				
	            ->joinLeft(array('to'=>'g013_takafuloperator'),'to.id=s.ARD_COMPANY_ID',array('company_name'=>'to.name','company_short_name'=>'to.shortName'));
	            
	    if($id){ $select->where("s.ID = ?", $id);  }
	    
	    $row = $db->fetchRow($select);

		return $row;	 
	}
	
	
	
	
	/*
	SELECT s.`ARD_NAME` , s.`ARD_IC` , rd.`idCourse` , rd.`regId` , c.name
FROM `r015_student` AS s
LEFT JOIN `r016_registrationdetails` AS rd ON rd.`idApplication` = s.id
LEFT JOIN `r010_course` AS c ON c.id = rd.idCourse
WHERE rd.regId IS NOT NULL
ORDER BY `rd`.`idCourse` ASC
	*/
	
	/* =========================================================
       Function : To get Registered Student Info whith condition pagination
       Created by : Yatie 31 May 2012
        ========================================================= */
	
	public function getRegisteredStudent($condition){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
	            ->from(array('s' => $this->_name))
	            ->joinLeft(array('rd' => 'r016_registrationdetails'),'s.id=rd.idApplication',array('regId'=>'rd.regId','exam_set_id'=>'exam_set_id'))				
	            ->joinLeft(array('c'=>'r010_course'),'c.id = rd.idCourse',array('ccode'=>'c.code','cname'=>'c.name','courseid'=>'c.id'))
	            ->joinLeft(array('sc'=>'s001_schedule'),'sc.id = rd.idSchedule',array('idSchedule'=>'sc.id'))
	            ->where('rd.regId IS NOT NULL')
	            //->where('rd.exam_set_id IS NULL')
	            ->order('rd.idCourse ASC');
	            
	     if($condition!=null){	
			
			if($condition['idVenue']!=""){
				$select->where("rd.idVenue =".$condition['idVenue']);
				$select->where("sc.exam_center =".$condition['idVenue']);
			}
			
			if($condition['exam_date']!=""){
				$select->where("sc.exam_date ='".$condition['exam_date']."'");
			}		
		}
		
		 $row = $db->fetchAll($select);

		
		
	   return $row;	 
	}
	
	/* =========================================================
       Function : To get Student List with generated exam question
       Created by : Yatie 5 June 2012
        ========================================================= */
	
	public function getQuestStudent($condition=NULL){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
	            ->from(array('s' => $this->_name))
	            ->joinLeft(array('rd' => 'r016_registrationdetails'),'s.id=rd.idApplication',array('regId'=>'rd.regId'))				
	            ->joinLeft(array('c'=>'r010_course'),'c.id = rd.idCourse',array('ccode'=>'c.code','cname'=>'c.name','courseid'=>'c.id'))	   
	            ->joinLeft(array('sc'=>'s001_schedule'),'sc.id = rd.idSchedule',array('exam_date'=>'exam_date'))	 
	             ->joinLeft(array('v'=>'g009_venue'),'v.id = rd.idVenue',array('venue'=>'name'))	         
	            ->where('rd.regId IS NOT NULL')
	            ->where('rd.exam_set_id IS NOT NULL')
	            ->order('rd.idCourse ASC');
	            
	     if($condition!=null){	
			
			if($condition['idCourse']!=""){
				$select->where("rd.idCourse =".$condition['idCourse']);				
			}
			
			if($condition['idVenue']!=""){
				$select->where("rd.idVenue =".$condition['idVenue']);				
			}
			
			if($condition['keywords']!=""){
				$select->where("s.ARD_NAME LIKE '%".$condition['keywords']."%'");
				$select->whereOr("s.ARD_IC LIKE '%".$condition['keywords']."%'");
			}		
		}
		
//		echo $select;
		
		
	   return $select;	 
	}
	
	public function getQuestStudent2(){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$candidate = array(101,102);
		
		$select = "SELECT `s`.*, `rd`.`regId`, `c`.`code` AS `ccode`, `c`.`name` AS `cname`, `c`.`id` AS `courseid` 
		FROM `r015_student` AS `s` 
		LEFT JOIN `r016_registrationdetails` AS `rd` ON s.id=rd.idApplication 
		LEFT JOIN `r010_course` AS `c` ON c.id = rd.idCourse 
		WHERE s.id IN ($candidate) 
		ORDER BY `rd`.`idCourse` ASC";

		 $row = $db->fetchAll($select);

		
	   return $select;	 
	}
	
	
	public function getCompanyStudentProgress($company=0, $condition=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
	            ->from(array('s' => $this->_name))
	            ->joinLeft(array('to'=>'g013_takafuloperator'),'to.id=s.ARD_COMPANY_ID',array('company_name'=>'to.name','company_short_name'=>'to.shortName'))
	            ->order('s.ARD_NAME');
	            
	    if($company!=0){        
	    	$select->where("s.ARD_COMPANY_ID = ?", $company);
	    }

		return $select;	 
	}
	
	public function getTOStudentProgress($company=0, $condition=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
	            ->from(array('s' => $this->_name))
	            ->joinLeft(array('to'=>'g013_takafuloperator'),'to.id=s.ARD_COMPANY_ID',array('company_name'=>'to.name','company_short_name'=>'to.shortName'))
	            ->order('s.ARD_NAME');
	            
	    if($company!=0){        
	    	$select->where("s.ARD_TAKAFUL = ?", $company);
	    }

		return $select;	 
	}
	
	
	/* =========================================================
        Function   : To get student list for printable version (mark entered)
        Created by : Yatie 12 June 2012
       ========================================================= */
    
    public function getPreviewList($program_id=0,$course_id=0,$idVenue=0,$keyword=null,$exam_date=""){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		 $select = $db->select()
		             ->from(array('s' => $this->_name))					
					 ->joinLeft(array('rd' => 'r016_registrationdetails'),
					        's.id=rd.idApplication',array('rd_id' => 'rd.id','regId' => 'rd.regId','courseid'=>'rd.idCourse','programid'=>'rd.idProgram','course_mark'=>'rd.course_mark','course_grade'=>'rd.course_grade','grade_symbol'=>'rd.grade_symbol','verify'=>'rd.mark_verified'))
					 ->joinLeft(array('v'=>'g009_venue'),'v.id=rd.idVenue',array('idVenue'=>'v.id','venue'=>'v.name'))	
					 ->joinLeft(array('c'=>'r010_course'),'c.id=rd.idCourse',array('courseid'=>'c.id','cname'=>'c.name','mark_distribution_type'=>'c.mark_distribution_type'))	
					 ->joinLeft(array('sc'=>'s001_schedule'),'sc.id = rd.idSchedule',array('idSchedule'=>'sc.id','exam_date'=>'sc.exam_date'))					 	
					 ->where("rd.attendance='1'")	
					 ->order("s.ARD_NAME asc");		
		
			if($program_id) $select->where('rd.idProgram = ?', $program_id);
			if($course_id)  $select->where('rd.idCourse  = ?', $course_id);
			if($exam_date)  $select->where("sc.exam_date ='".$exam_date."'");
		
				 
			if($keyword) {
				$select->where('s.ARD_NAME LIKE ?', '%'.$keyword.'%');
				$select->Orwhere('s.ARD_IC LIKE ?', '%'.$keyword.'%');
			}	
		
		    if($idVenue){
				$select->where("rd.idVenue =".$idVenue);
				$select->where("sc.exam_center =".$idVenue);
			}
							
			
		//echo $select;
			
        $stmt = $db->query($select);
        $row = $stmt->fetchAll();
        
		return $row;
	}
	
	
}

?>