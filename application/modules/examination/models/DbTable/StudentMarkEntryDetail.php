<?php
class Examination_Model_DbTable_StudentMarkEntryDetail extends Zend_Db_Table { //Model Class for Users Details
	
	protected $_name = 'tbl_student_marks_entry_detail';
	protected $_primary = 'IdStudentMarksDetail';

	public function addData($data){		
		$db = Zend_Db_Table::getDefaultAdapter();
		$id = $db->insert($this->_name,$data);
		//echo var_dump($data);exit;
	   return $id;
	}
	
    public function deleteData($id){
    	$db = Zend_Db_Table::getDefaultAdapter();
	  	$db->insert($this->_name,"IdStudentMarksDetail = '".(int)$id."'");
	}
	
	public function updateData($data,$id){
		 $db = Zend_Db_Table::getDefaultAdapter();
		 $db->insert($this->_name,$data, "IdStudentMarksDetail = '".(int)$id."'");
	}
	
	
	public function checkMarkEntry($IdStudentRegistration,$IdMarksDistributionDetail,$idSemester){
		
		 $db = Zend_Db_Table::getDefaultAdapter();
		
		 //check ada x mark entry sebelum ni
	 	 $select_mark = $db->select()
	 	 				  ->from(array('sme'=>'tbl_student_marks_entry_detail'))
	 	 				  ->where('sme.IdStudentRegistration = ?',$IdStudentRegistration)
	 	 				  ->where('sme.IdMarksDistributionDetail = ?',$IdMarksDistributionDetail) 
	 	 				  ->where('sme.IdSemester = ?',$idSemester);
	 	 $entry_list= $db->fetchRow($select_mark);
	 	// echo 'ekke';exit;
	     return $entry_list;
	}
	
	public function getMark($IdStudentRegistration,$IdMarksDistributionMaster,$IdStudentRegSubjects,$idSemester){
		
		 $db = Zend_Db_Table::getDefaultAdapter();
			
		 //check ada x mark entry sebelum ni
	 	 $select_mark = $db->select()
	 	 				  ->from(array('sme'=>'tbl_student_marks_entry_detail'))
	 	 				  ->joinLeft(array('s'=>'tbl_staffmaster'),'s.IdStaff=sme.ApprovedBy',array('ApprovedBy'=>'FullName'))
	 	 				  ->joinLeft(array('d'=>'tbl_definationms'),'d.idDefinition=sme.MarksEntryStatus',array('BahasaIndonesia','DefinitionDesc'))
	 	 				  ->where('sme.IdStudentRegistration = ?',$IdStudentRegistration)
	 	 				  ->where('sme.IdMarksDistributionDetail = ?',$IdMarksDistributionMaster)
	 	 				  ->where('sme.IdStudentRegSubjects = ?',$IdStudentRegSubjects)
	 	 				  ->where('sme.IdSemester = ?',$idSemester);
	 	 				  
	     return $entry_list = $db->fetchRow($select_mark);	
	}
	public function getStudentMark($idStudentMarkEntry){
	
		$db = Zend_Db_Table::getDefaultAdapter();
			
		//check ada x mark entry sebelum ni
		$select_mark = $db->select()
		->from(array('sme'=>'tbl_student_marks_entry_detail'))
		->join(array('cmp'=>'tbl_marksdistributionmaster'),'cmp.IdMarksDistributionMaster=sme.idMarksDistributionMaster',array('Percentage'))
		->join(array('def'=>'tbl_examination_assessment_type'),'def.IdExaminationAssessmentType=sme.component',array('ComponentName'=>'DescriptionDefaultlang'))
		->join(array('sm'=>'tbl_subjectmaster'),'sm.idsubject=sme.course',array('BahasaIndonesia','SubjectName','ShortName','CreditHours'))
		//->joinLeft(array('s'=>'tbl_staffmaster'),'s.IdStaff=sme.ApprovedBy',array('ApprovedBy'=>'FullName'))
		//->joinLeft(array('d'=>'tbl_definationms'),'d.idDefinition=sme.MarksEntryStatus',array('MarkStatus'=>'BahasaIndonesia','DefinitionDesc'))
		->where('sme.IdStudentMarkDetail = ?',$idStudentMarkEntry);
	
		return $entry_list = $db->fetchRow($select_mark);
	}
	
	
	 
	
	
	public function getComponentMark($IdStudentRegistration,$IdMarksDistributionMaster,$idSemester,$idCourse){
		
		 $db = Zend_Db_Table::getDefaultAdapter();
		
	 	 $select_mark = $db->select()
	 	 				  ->from(array('sme'=>'tbl_student_marks_entry_detail'))
	 	 				  ->where('sme.IdStudentRegistration = ?',$IdStudentRegistration)
	 	 				  ->where('sme.IdMarksDistributionDetail = ?',$IdMarksDistributionMaster)
	 	 				  ->where('sme.Course = ?',$idCourse)
	 	 				  ->where('sme.IdSemester = ?',$idSemester);
	 	 	//echo $select_mark .'<br />';	  
	     return $entry_list = $db->fetchRow($select_mark);	
	}
	public function getAllComponentMark($idprogram,$idsemester,$idsubject,$IdStudentRegistration ){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select_mark = $db->select()
		->from(array('sme'=>'tbl_student_marks_entry_detail'))
		->join(array('dst'=>'tbl_markdistributiondetail'),'sme.IdMarksDistributionDetail=dst.IdMarksDistributionDetail',array())
		->where('sme.IdStudentRegistration = ?',$IdStudentRegistration) 
		->where('sme.Course = ?',$idsubject)
		//->where('sme.IdProgram = ?',$idprogram)
		->where('sme.IdSemester = ?',$idsemester);
		//echo $select_mark .'<br />';
		return $entry_list = $db->fetchAll($select_mark);
	}
	
	 
	public function updatePercentage($semester_id,$program_id,$subject_id,$IdStudentRegistration,$IdStudentRegSubjects,$gradecalulation=true,$remark=false,$marks){
		$db = Zend_Db_Table::getDefaultAdapter();
		$dbDistr=new Examination_Model_DbTable_MarkdistributionDetail();
		foreach ($marks as $id) {
			$select=$db->select()
			->from(array('srs'=>"tbl_student_marks_entry_detail"))
			->where('srs.IdStudentMarksDetail=?',$id);
			$row=$db->fetchRow($select);
			$idmarkdist=$row['IdMarksDistributionDetail'];
			
			$component=$dbDistr->fnGetMarksDistributionDetail($idmarkdist);
			if ($component) {
				$percentage=$component['Percentage'];
				$data['FinalTotalMarkObtained']=$row['TotalMarkObtained']*$percentage/100;
				$this->updateData($data, $id);
			}
		}
		//update total
		$this->getStudentTotalMark($semester_id, $program_id, $subject_id, $IdStudentRegistration,true);
	}
	
	 
	
	public function getStudentTotalMarkPerMhs($idmark,$IdStudentRegistration,$remark=false){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		
		
		$sql =  $db->select()
			->from(array("mdm" =>"tbl_markdistributiondetail_header"))
			->join(array('def'=>'tbl_definationms'),'mdm.Calculation_mode=def.IdDefinition',array('calmode'=>'DefinitionCode'))		
			->where("mdm.IdMarksDistributionMaster = ?",$idmark);
	
		$mdm = $db->fetchRow($sql);
		
		if ($mdm['calmode']=='AVG') {
			//get component
			$sql =  $db->select()
			->from(array("mdm" =>"tbl_student_marks_entry_detail"))
			->where("mdm.IdMarksDistributionMaster = ?",$idmark)
			->where("mdm.IdStudentRegistration = ?",$IdStudentRegistration);
			$marks = $db->fetchAll($sql);
			$marktotal=0;
			$i=0;
			foreach ($marks as $value) {
				$marktotal=$marktotal+$value['TotalMarkObtained'];
				$i++;
			}
			$mincom=$mdm['Minimum_Component'];
			if ($i<$mincom) 
				$markfinal=$marktotal/$mincom;
			else 
				$markfinal=$marktotal/$i;
			
			//calculate average
		} else if ($mdm['calmode']=='HG') {
			//tak highest
			$sql =  $db->select()
			->from(array("mdm" =>"tbl_student_marks_entry_detail"))
			->where("mdm.IdMarksDistributionMaster = ?",$idmark)
			->where("mdm.IdStudentRegistration = ?",$IdStudentRegistration);
			$marks = $db->fetchAll($sql);
			$markfinal=0;
			$i=0;
			foreach ($marks as $value) {
				if ($value['TotalMarkObtained']>$markfinal) $markfinal=$value['TotalMarkObtained'];
			}
		} else if ($mdm['calmode']=='WG') {
			//calculate weighted
			$sql =  $db->select()
			->from(array("mdm" =>"tbl_student_marks_entry_detail"))
			->where("mdm.IdMarksDistributionMaster = ?",$idmark)
			->where("mdm.IdStudentRegistration = ?",$IdStudentRegistration);
			$marks = $db->fetchAll($sql);
			$markfinal=0;
			$i=0;
			foreach ($marks as $value) {
				$markfinal=$markfinal+$value['FinalTotalMarkObtained'];
			}
		}
		$dbMarkDist=new Examination_Model_DbTable_Marksdistributionmaster();
		$main_component=$dbMarkDist->getInfoComponent($idmark);
		$markpercen=$markfinal*$main_component['Percentage']/100;
		//save dalam studentregsubject
		$subRegDB = new Examination_Model_DbTable_StudentMarkEntry();
		$markmaster=$subRegDB->getStudentMarkByMarkDistributionMaster($idmark, $IdStudentRegistration);
		if ($markmaster) {
			//update mark total 
			$data=array('TotalMarkObtained'=>$markfinal,'FinalTotalMarkObtained'=>$markpercen);
			$subRegDB->updateData($data, $markmaster['IdStudentMarksEntry']);
		} else {
			//insert data 
			$data["IdStudentRegistration"] = $IdStudentRegistration;
			$data["IdSemester"] = $main_component['semester'];
			$data["Course"] = $main_component['IdCourse'];
			$data["IdMarksDistributionMaster"]=$idmark; 
			$data["MarksTotal"] = $main_component["Marks"];
			$data["Component"]   =  $main_component["IdComponentType"];
			$data["ComponentItem"]   =  '';
			$data["Instructor"] = '';
			$data["AttendanceStatus"] = '';
			$data["MarksEntryStatus"] = 407; // 407=>ENTRY ( id Type= 93 )
			$data["UpdUser"] = $auth->getIdentity()->iduser;
			$data["UpdDate"] = date('Y-m-d H:i:s');
			$markDB = new Examination_Model_DbTable_StudentMarkEntryDetail();
			$data["TotalMarkObtained"] = $markfinal;
			$data["FinalTotalMarkObtained"]=$markpercen;
			$subRegDB->addData($data);
		}
		 	
			
		return $markfinal;
	}
	
	public function getStudentComponentMark($program_id,$semester_id,$subject_id,$IdStudentRegistration,$IdStudentRegSubjects){
			
		$db = Zend_Db_Table::getDefaultAdapter();	

		$sql2 =  $db->select()->from(array("mdm" =>"tbl_marksdistributionmaster"))													
						     ->where("mdm.IdProgram = ?",$program_id)
						     ->where("mdm.semester = ?",$semester_id)
							 ->where("mdm.IdCourse = ?",$subject_id)
							 ->where("mdm.IdComponentType = 38");
		$row2 = $db->fetchRow($sql2);
		
		if($row2){
			$sql =  $db->select()->from(array("sme" =>"tbl_student_marks_entry"),array())	
								 ->join(array('mdm'=>'tbl_marksdistributionmaster'),'mdm.IdMarksDistributionMaster = sme.IdMarksDistributionMaster',array('Component'=>'IdComponentType'))						
							     ->where("sme.IdSemester = ?",$semester_id)
								 ->where("sme.Course = ?",$subject_id)
								 ->where("sme.IdStudentRegistration = ?",$IdStudentRegistration)
								 ->where("sme.IdStudentRegSubjects = ?",$IdStudentRegSubjects)
								 ->where("mdm.IdComponentType = 38");
			
			$row = $db->fetchRow($sql);
			
			if(!$row){
				return false;
			}
		}
		
		
		
		$sql3 =  $db->select()->from(array("mdm" =>"tbl_marksdistributionmaster"))													
						     ->where("mdm.IdProgram = ?",$program_id)
						     ->where("mdm.semester = ?",$semester_id)
							 ->where("mdm.IdCourse = ?",$subject_id)
							 ->where("mdm.IdComponentType = 40");
		$row3 = $db->fetchRow($sql3);
		  		
	    if($row3){
			$sql2 =  $db->select()->from(array("sme" =>"tbl_student_marks_entry"),array())
							 ->join(array('mdm'=>'tbl_marksdistributionmaster'),'mdm.IdMarksDistributionMaster = sme.IdMarksDistributionMaster',array('Component'=>'IdComponentType'))							
						     ->where("sme.IdSemester = ?",$semester_id)
							 ->where("sme.Course = ?",$subject_id)
							 ->where("sme.IdStudentRegistration = ?",$IdStudentRegistration)
							 ->where("sme.IdStudentRegSubjects = ?",$IdStudentRegSubjects)
							 ->where("mdm.IdComponentType = 40");
		
			$row2 = $db->fetchRow($sql2);
			
			if(!$row2){
				return false;
			}
	    }
		    
		return true;
		
	}
    
	public function getStudentMarkByMarkDist($program_id,$semester_id,$subject_id,$IdStudentRegistration,$IdStudentRegSubjects,$idmarkdist){
			
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$sql =  $db->select()->from(array("sme" =>"tbl_student_marks_entry"))
			->join(array('mdm'=>'tbl_marksdistributionmaster'),'mdm.IdMarksDistributionMaster = sme.IdMarksDistributionMaster',array())
			->where("sme.IdSemester = ?",$semester_id)
			->where("sme.Course = ?",$subject_id)
			->where("sme.IdStudentRegistration = ?",$IdStudentRegistration)
			->where("sme.ApprovedBy is not null")
			->where("sme.MarksEntryStatus = '411'")
			->where("sme.IdMarksDistributionMaster = ?",$idmarkdist);
						
			$row = $db->fetchRow($sql);
				
			return $row;
	}
	
	public function getCountStudentMarkByMarkDist($program_id,$semester_id,$idmarkdist,$subject_id){
			
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$sql =  $db->select()->from(array("sme" =>"tbl_student_marks_entry"),array('count'=>'count(*)'))
		->join(array('mdm'=>'tbl_marksdistributionmaster'),'mdm.IdMarksDistributionMaster = sme.IdMarksDistributionMaster',array())
		->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration=sme.IdStudentRegistration',array())
		->where("sme.IdSemester = ?",$semester_id)
		->where("sme.Course = ?",$subject_id)
		->where("sr.IdProgram = ?",$program_id)
		->where("sme.IdMarksDistributionMaster = ?",$idmarkdist);
	
		$row = $db->fetchRow($sql);
	
		return $row['count'];
	}
	
	public function getCountStudentMarkByMarkDistPerProgram($semester_id,$idmarkdist,$subject_id,$grp_id){
			
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$sql =  $db->select()->from(array("sme" =>"tbl_student_marks_entry"),array('count'=>'count(*)'))
		->join(array('mdm'=>'tbl_marksdistributionmaster'),'mdm.IdMarksDistributionMaster = sme.IdMarksDistributionMaster',array())
		->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration=sme.IdStudentRegistration',array('sr.IdProgram'))
		->join(array('srs'=>'tbl_studentregsubjects'),'sme.IdStudentRegSubjects=srs.IdStudentRegSubjects')
		->where("sme.IdSemester = ?",$semester_id)
		->where("sme.Course = ?",$subject_id)
		->where("srs.Idcoursetagginggroup = ?",$grp_id)
		->where("sme.IdMarksDistributionMaster = ?",$idmarkdist)
		->group('sr.IdProgram') ;
	
		$row = $db->fetchAll($sql);
	
		return $row;
	}
	
    public function countComponentMark($IdStudentRegistration,$IdMarksDistributionMaster,$idSemester,$idCourse){
		
		 $db = Zend_Db_Table::getDefaultAdapter();
		
	 	 $select_mark = $db->select()
	 	 				  //->count()
                          ->from('tbl_student_marks_entry',array('total' => "COUNT(*)"))
	 	 				  ->where('IdStudentRegistration IN ('.$IdStudentRegistration.')')
	 	 				  ->where('IdMarksDistributionMaster = ?',$IdMarksDistributionMaster)
	 	 				  ->where('Course = ?',$idCourse)
	 	 				  ->where('IdSemester = ?',$idSemester)
                          ->group('Course');
	 	 		  
	     //echo $select_mark;
         
         $entry_list = $db->fetchRow($select_mark);
         
         return $entry_list['total'];	
	}
    
    public function countComponentMarkApproved($IdStudentRegistration,$IdMarksDistributionMaster,$idSemester,$idCourse){
		
		 $db = Zend_Db_Table::getDefaultAdapter();
		
	 	 $select_mark = $db->select()
	 	 				  //->count()
                          ->from('tbl_student_marks_entry',array('total' => "COUNT(*)"))
	 	 				  ->where('IdStudentRegistration IN ('.$IdStudentRegistration.')')
	 	 				  ->where('IdMarksDistributionMaster = ?',$IdMarksDistributionMaster)
	 	 				  ->where('Course = ?',$idCourse)
	 	 				  ->where('IdSemester = ?',$idSemester)
	 	 				  ->where('MarksEntryStatus = 411')
                          ->group('Course');
	 	 		  
	     //echo $select_mark;
         $entry_list = $db->fetchRow($select_mark);
         
         return $entry_list['total'];	
	}
    
	public function insertMarkOfAttendance($semester,$IdSubject,$groupid,$idregistration,$IdStudentRegSubject,$IdMarksDistributionMaster,$percentage,$MarkMaks) {
	
		$db = Zend_Db_Table::getDefaultAdapter();
		$auth = Zend_Auth::getInstance();
		//
		$courseGroupStudentAttendanceDb = new Examination_Model_DbTable_CourseGroupStudentAttendanceDetail();
		
			$class_session = $courseGroupStudentAttendanceDb->getAttendanceSessionCount($groupid,$idregistration);
			//kehadiran dihitung dari hadir+ijin+sakit
			$hadir = $courseGroupStudentAttendanceDb->getAttendanceStatusCount($groupid,$idregistration,395);
			$ijin  = $courseGroupStudentAttendanceDb->getAttendanceStatusCount($groupid,$idregistration,396);
			$sakit = $courseGroupStudentAttendanceDb->getAttendanceStatusCount($groupid,$idregistration,397);
			$class_attended = $hadir+$ijin+$sakit;
			$mark=0;
			$data=array();
			
			if ($class_session > 0) {
				
				$mark=$class_attended/$class_session*100;
				
				$markentry=$this->checkMarkEntry($idregistration, $IdMarksDistributionMaster, $IdStudentRegSubject, $semester);
				
				
				if ($markentry) {
					//update
					$id=$markentry['IdStudentMarksEntry'];
					$data['TotalMarkObtained'] = $mark;
					$data['FinalTotalMarkObtained'] =$mark*$percentage/100;
					$db->update($this->_name,$data, 'IdStudentMarksEntry='.$id);
				} else {
					
					//insert
					$data["Component"]   =  50;
					$data["ComponentItem"]   =  '';
					$data["Instructor"] = '';
					$data["AttendanceStatus"] = '';
					$data["MarksEntryStatus"] = 407; // 407=>ENTRY ( id Type= 93 )
					$data["UpdUser"] = $auth->getIdentity()->iduser;
					$data["UpdDate"] = date('Y-m-d H:i:s');
					$data["exam_group_id"] = 0;
					$data['MarksTotal']=$MarkMaks;
					$data['TotalMarkObtained'] = $mark;
					$data['FinalTotalMarkObtained'] =$mark*$percentage/100;
					$data['IdMarksDistributionMaster'] =$IdMarksDistributionMaster;
					$data['IdStudentRegSubjects']=$IdStudentRegSubject;
					$data['Course'] = $IdSubject;
					$data['IdStudentRegistration']=$idregistration;
					$data['IdSemester'] =$semester;
					//echo var_dump($data);exit;
					$db->insert($this->_name,$data);
						
				}
			
		}
		
		
	}
	
	public function isAllComponentHasMark($idMarkMaster,$IdStudentRegistration) {
		$db = Zend_Db_Table::getDefaultAdapter();
		
		
		$sql =  $db->select()->from(array("mdm" =>"tbl_marksdistributionDetail"),array('IdMarksDistributionMaster'))
				->where("mdm.IdMarksDistributionMater = ?",$idMarkMaster);
				 
		
		$mdm = $db->fetchAll($sql);
		 
		$mising = null;
		foreach ($mdm as $m){
			$select = $db->select()
			->from(array('sme'=>'tbl_student_marks_entry_detail'),array('FinalTotalMarkObtained','IdStudentMarksEntry'))
			->where('sme.IdMarksDistributionDetail = ?',$m["IdMarksDistributionMaster"])
			->where('sme.IdStudentRegistration = ?',$IdStudentRegistration);
			$dmark = $db->fetchRow($select);
			//echo var_dump($dmark);exit;
			if((!$dmark) || $dmark==array() || !isset($dmark)){
				//echo var_dump($m);echo 'kena';exit;
				return false;
			}
		}
		return true;
	}
	
	 
	
	public function isAllComponentHasVerified($idmarkmaster) {
		$db = Zend_Db_Table::getDefaultAdapter();
	
	
		$sql =  $db->select()->from(array("mdm" =>"tbl_marksdistributionDetail"),array('IdMarksDistributionMaster'))
		->where("mdm.IdMarksdistributionMaster = ?",$idmarkmaster);
		 
	
		$mdm = $db->fetchAll($sql);
			
		$mising = null;
		foreach ($mdm as $m){
			$select = $db->select()
			->from(array('sme'=>'tbl_student_marks_entry_detail'),array('FinalTotalMarkObtained','IdStudentMarksEntry'))
			 ->where('sme.IdMarksDistributionDetail = ?',$m["IdMarksDistributionDetail"])
			 
			->where('sme.ApprovedBy is not null and sme.ApprovedOn is not null');
			$dmark = $db->fetchRow($select);
			//echo var_dump($dmark);exit;
			if((!$dmark) || $dmark==array() || !isset($dmark)){
				//echo 'kena';exit;
				return false;
			}
		}
		return true;
	}
	
	public function getComponentVerifiedStatus($semester_id,$program_id,$subject_id,$idGroup,$idDistribution=null) {
		
		$db = Zend_Db_Table::getDefaultAdapter();
	
		if ($idDistribution==null) {
			$sql =  $db->select()->from(array("mdm" =>"tbl_marksdistributionmaster"),array('IdMarksDistributionMaster'))
			->where("mdm.semester = ?",$semester_id)
			->where("mdm.IdProgram = ?",$program_id)
			->where("mdm.IdCourse = ?",$subject_id);
		
			$mdm = $db->fetchAll($sql);
				
			$mising = 0;$verify=0;
			foreach ($mdm as $m){
				$select = $db->select()
				->from(array('sme'=>'tbl_student_marks_entry'),array('verified'=>'count(*)'))
				->join(array('srs'=>'tbl_studentregsubjects'),'sme.IdStudentRegSubjects=srs.IdStudentRegSubjects',array())
				->where('sme.IdMarksDistributionMaster = ?',$m["IdMarksDistributionMaster"])
				->where('srs.IdCourseTaggingGroup = ?',$idGroup)
				->where('sme.ApprovedBy is not null and sme.ApprovedOn is not null');
				$dmark = $db->fetchRow($select);
				$verify=$verify+$dmark['verified'];
				
				$select = $db->select()
				->from(array('sme'=>'tbl_student_marks_entry'),array('mising'=>'count(*)'))
				->join(array('srs'=>'tbl_studentregsubjects'),'sme.IdStudentRegSubjects=srs.IdStudentRegSubjects',array())
				->where('sme.IdMarksDistributionMaster = ?',$m["IdMarksDistributionMaster"])
				->where('srs.IdCourseTaggingGroup = ?',$idGroup)
				->where('sme.ApprovedBy is null and sme.ApprovedOn is  null');
				$dmark = $db->fetchRow($select);
				//echo var_dump($dmark);exit;
				
				$mising=$mising+$dmark['mising'];
					
				
			}
			$status[0]=$verify;
			$status[1]=$mising;
			return $status;
		}
		else {
			$select = $db->select()
				->from(array('sme'=>'tbl_student_marks_entry'),array('verified'=>'count(*)'))
				->join(array('srs'=>'tbl_studentregsubjects'),'sme.IdStudentRegSubjects=srs.IdStudentRegSubjects',array())
				->where('sme.IdMarksDistributionMaster = ?',$idDistribution)
				->where('srs.IdCourseTaggingGroup = ?',$idGroup)
				->where('sme.ApprovedBy is not null and sme.ApprovedOn is not null');
				$dmark = $db->fetchRow($select);
				$verify=$dmark['verified'];
				
				$select = $db->select()
				->from(array('sme'=>'tbl_student_marks_entry'),array('mising'=>'count(*)'))
				->join(array('srs'=>'tbl_studentregsubjects'),'sme.IdStudentRegSubjects=srs.IdStudentRegSubjects',array())
				->where('sme.IdMarksDistributionMaster = ?',$idDistribution)
				->where('srs.IdCourseTaggingGroup = ?',$idGroup)
				->where('sme.ApprovedBy is null and sme.ApprovedOn is  null');
				$dmark = $db->fetchRow($select);
				//echo var_dump($dmark);exit;
				
				$mising=$dmark['mising'];
				$status[0]=$verify;
				$status[1]=$mising;
				return $status;
				
		}
		
	}
	
	 
}
?>