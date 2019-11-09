<?php 

class App_Model_General_DbTable_CourseGroupStudentMinor extends Zend_Db_Table_Abstract {
	
	protected $_name = 'course_group_student_minor';
	protected $_primary = "IdStudentMinor";
	
	
	public function addData($data){		
	   $id = $this->insert($data);
	   return $id;
	}
	
	public function updateData($data,$id){
		 $this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteData($id){		
	  $this->delete($this->_primary .' =' . (int)$id);
	}
	
	public function getTotalStudent($idGroup){
		
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		 $select = $db ->select()		
					  ->from(array('gsm'=>$this->_name))
					  ->join(array('sr'=>'tbl_studentregistration'), 'sr.IdStudentRegistration = gsm.IdStudentRegistration')
					  ->where('gsm.IdCourseTaggingGroupMinor = ?',$idGroup);					  
		 $row = $db->fetchAll($select);	
		 
		 if($row)
		 	return count($row);
		 else
		 return 0;
	}
	
	public function removeStudent($idStdMinor){		
		
	  $this->delete("IdStudentMinor='". $idStdMinor ."'");
	}
	
	public function getStudent($idGroup,$component){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->distinct()
		->from(array('gsm'=>$this->_name))
		->join(array('sr'=>'tbl_studentregistration'), 'sr.IdStudentRegistration = gsm.IdStudentRegistration',array('registrationId'))
		->join(array('sp'=>'student_profile'), 'sp.appl_id = sr.IdApplication',array('appl_fname','appl_mname','appl_lname'))
		->where('IdCourseTaggingGroupMinor = ?',$idGroup)
		->order('sr.registrationId');
	
		$row = $db->fetchAll($select);
		return $row;
	}
	public function getStudentMinorMark($idGroup,$component){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
				->distinct()
					->from(array('gsm'=>$this->_name))
					->join(array('sr'=>'tbl_studentregistration'), 'sr.IdStudentRegistration = gsm.IdStudentRegistration',array('registrationId'))
					->join(array('sp'=>'student_profile'), 'sp.appl_id = sr.IdApplication',array('appl_fname','appl_mname','appl_lname'))
					->where('IdCourseTaggingGroupMinor = ?',$idGroup)
					->order('sr.registrationId');

		$rows = $db->fetchAll($select);
		
		foreach($rows as $key=>$student){
			$idstudentmarkentry=0;
			foreach ($component as $item) {
		
				$IdMarksDistributionMaster=$item['IdMarksDistributionMaster'];
				$IdMarksDistributionDetail=$item['IdMarksDistributionDetail'];
				/* //cari exam group student ni
				$examGroupDb =new Examination_Model_DbTable_ExamGroupStudent();
				$exam_group = $examGroupDb->checkStudentGroup($student["IdStudentRegistration"],$idSubject,$idSemester,$IdMarksDistributionMaster);
					
				if(is_array($exam_group)){
		
					$rows[$key][$IdMarksDistributionDetail]['exam_group_id']=$exam_group["egst_group_id"];
		
					//get exam attendance
					$examAttendanceDb = new Examination_Model_DbTable_ExamGroupAttendance();
					$attendance = $examAttendanceDb->getData($exam_group["egst_group_id"],$student["IdStudentRegistration"]);
		
					//jika x hadir atau x ada record  set status tak hadir
					if($attendance["ega_status"]!='395' || !$attendance){ //Defination type=31 Hadir=395
							
						$rows[$key][$IdMarksDistributionDetail]['attendance']='Not Attend';
		
					}else{
		
						$rows[$key][$IdMarksDistributionDetail]['attendance']='Attend';
					}
		
				}else{
					//jika tiada exam group consider student tu tak attend any exam
					$rows[$key][$IdMarksDistributionDetail]['exam_group_id']='';
					$rows[$key][$IdMarksDistributionDetail]['attendance']='No Exam Group';
				}//end exam group exist
					 */
					
					
				//check ada x mark entry
				$select_mark = $db->select()
				->from(array('sme'=>'tbl_student_marks_entry_detail'))
				->where('sme.IdStudentRegistration = ?',$student["IdStudentRegistration"])
				->where('sme.IdMarksDistributionDetail = ?',$IdMarksDistributionDetail);
				$entry_list = $db->fetchRow($select_mark);
				//echo var_dump($entry_list);exit;
					
				if(isset($entry_list["IdStudentMarksEntry"]) && $entry_list["IdStudentMarksEntry"]!=''){
					$rows[$key][$IdMarksDistributionDetail]['MarksEntryStatus']=$entry_list["MarksEntryStatus"];
					$rows[$key][$IdMarksDistributionDetail]['IdStudentMarksEntry']=$entry_list["IdStudentMarksEntry"];
					$rows[$key][$IdMarksDistributionDetail]['IdStudentMarkDetail']=$entry_list["IdStudentMarkDetail"];
					$rows[$key][$IdMarksDistributionDetail]['TotalMarkObtained']=$entry_list["TotalMarkObtained"];
					$rows[$key][$IdMarksDistributionDetail]['FinalTotalMarkObtained']=$entry_list["FinalTotalMarkObtained"];
					$idstudentmarkentry=$entry_list["IdStudentMarksEntry"];
					
						
				}else{
		
					//defaultkan mark entry info null
					$rows[$key][$IdMarksDistributionDetail]['MarksEntryStatus']='';
					$rows[$key][$IdMarksDistributionDetail]['IdStudentMarksEntry']='';
					$rows[$key][$IdMarksDistributionDetail]['IdStudentMarkDetail']='';
					$rows[$key][$IdMarksDistributionDetail]['TotalMarkObtained']='';
					$rows[$key][$IdMarksDistributionDetail]['FinalTotalMarkObtained']='';
					$rows[$key][$IdMarksDistributionDetail]['final_course_mark']='';
					
		
				}
			}
			//end commponent
			if ($idstudentmarkentry!=0) {
				$select = $db ->select()
				->from(array('sme'=>'tbl_student_marks_entry'))
				->where('sme.IdStudentMarksEntry=?',$idstudentmarkentry);
				$mark=$db->fetchRow($select);
				//echo var_dump($mark);exit;
				$rows[$key]['final_course_mark']=$mark["TotalMarkObtained"];
			} else $rows[$key]['final_course_mark']=null;
		}//end foreacch
		//echo var_dump($rows);exit;
		return $rows;
	}
	
	public function getStudentbyGroup($idGroup,$student=null,$idprogram=null){
	
		$db = Zend_Db_Table::getDefaultAdapter();	
		
	    $select = $db ->select()
					->from(array('gsm'=>$this->_name))
					->join(array('ct'=>'tbl_course_tagging_group_minor'),'ct.IdCourseTaggingGroupMinor=gsm.IdCourseTaggingGroupMinor')
					->join(array('sr'=>'tbl_studentregistration'), 'sr.IdStudentRegistration = gsm.IdStudentRegistration')
					->join(array('sp'=>'student_profile'), 'sp.appl_id = sr.IdApplication')
					->join(array('p'=>'tbl_program'), 'p.IdProgram=sr.IdProgram',array('ProgramName'=>'ArabicName','ProgramCode'))
					->where('ct.IdCourseTaggingGroupMinor = ?',$idGroup)				
					->order('sr.registrationId');
		//echo $select;exit;
		if ($idprogram!=null) {
			$select->where('sr.IdProgram=?',$idprogram);
		}	
			
		if(isset($student)){
			$select->where("((sp.appl_fname LIKE '%".$student."%'");
			$select->orwhere("sp.appl_mname LIKE '%".$student."%'");
			$select->orwhere("sp.appl_lname LIKE '%".$student."%')");		
			$select->orwhere("sr.registrationId LIKE '%".$student."%')");
		}
		//echo $select;exit;
		
		$row = $db->fetchAll($select);
		
		if($row)
			return $row;
		else
			return null;
	}
	
	public function getAllStudentbyGroup($idGroup){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('gsm'=>$this->_name))
		->join(array('ct'=>'tbl_course_tagging_group_minor'),'ct.IdCourseTaggingGroupMinor=gsm.IdCourseTaggingGroupMinor')
		->join(array('sr'=>'tbl_studentregistration'), 'sr.IdStudentRegistration = gsm.IdStudentRegistration')
		->join(array('sp'=>'student_profile'), 'sp.appl_id = sr.IdApplication')
		->join(array('p'=>'tbl_program'), 'p.IdProgram=sr.IdProgram',array('ProgramName'=>'ArabicName','ProgramCode'))
		->where('ct.group_id = ?',$idGroup)
		->order('sr.registrationId');
		//echo $select;exit;
		  
	
		$row = $db->fetchAll($select);
	
		if($row)
			return $row;
		else
			return null;
	}
	
	public function getSubjectRegisterMinor($idstd,$idsemester){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('gsm'=>$this->_name))
		->join(array('ct'=>'tbl_course_tagging_group_minor'),'ct.IdCourseTaggingGroupMinor=gsm.IdCourseTaggingGroupMinor')
		->join(array('cs'=>'course_group_schedule_minor'),'cs.IdGroup=ct.IdCourseTaggingGroupMinor')
		->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=ct.IdSubject',array('sm.IdSubject','SubCode'=>'ShortName','SubjectName'=>'BahasaIndonesia','CreditHours'))
		->join(array('sr'=>'tbl_studentregistration'), 'sr.IdStudentRegistration = gsm.IdStudentRegistration')
		->join(array('sp'=>'student_profile'), 'sp.appl_id = sr.IdApplication')
		->join(array('p'=>'tbl_program'), 'p.IdProgram=sr.IdProgram',array('ProgramName'=>'ArabicName','ProgramCode'))
		->where('gsm.IdStudentRegistration = ?',$idstd)
		->where('ct.IdSemester=?',$idsemester);
		//echo $select;exit;
	
	
		$row = $db->fetchAll($select);
	
		if($row)
			return $row;
		else
			return null;
	}
	
	public function getScheduleMinor($idsemester,$idstd,$idgrpparent){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('gsm'=>$this->_name))
		->join(array('ct'=>'tbl_course_tagging_group_minor'),'ct.IdCourseTaggingGroupMinor=gsm.IdCourseTaggingGroupMinor',array('GroupName'))
		->join(array('cs'=>'course_group_schedule_minor'),'cs.IdGroup=ct.IdCourseTaggingGroupMinor')
		//->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=ct.IdSubject',array('sm.IdSubject','SubCode'=>'ShortName','SubjectName'=>'BahasaIndonesia','CreditHours'))
		//->join(array('sr'=>'tbl_studentregistration'), 'sr.IdStudentRegistration = gsm.IdStudentRegistration',array())
		//->join(array('sp'=>'student_profile'), 'sp.appl_id = sr.IdApplication')
		//->join(array('p'=>'tbl_program'), 'p.IdProgram=sr.IdProgram',array('ProgramName'=>'ArabicName','ProgramCode'))
		->where('gsm.IdStudentRegistration = ?',$idstd)
		->where('ct.IdSemester=?',$idsemester)
		->where('ct.group_id=?',$idgrpparent);
		//echo $select;exit;
	
	
		$row = $db->fetchAll($select);
	
		if($row)
			return $row;
		else
			return null;
	}
	
	public function checkStudentCourseGroup($IdStudentRegistration,$idSemester,$idSubject){
		
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		 $select = $db ->select()		
					  ->from(array('cgsm'=>$this->_name))
					  ->join(array('ctg'=>'tbl_course_tagging_group_minor'),'ctg.IdCourseTaggingGroupMinor=cgsm.IdCourseTaggingGroupMinor')
					  ->where('cgsm.IdStudentRegistration = ?',$IdStudentRegistration)
					  ->where('ctg.IdSemester = ?',$idSemester)
					  ->where('ctg.IdSubject = ?',$idSubject);					  
		 $row = $db->fetchRow($select);	
		 
		 return $row;
	}
	
	 
	
	
	public function checkStudentMappingGroup($idGroup,$IdStudentRegistration){
		
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		 $select = $db ->select()		
					  ->from(array('cgsm'=>$this->_name))					
					  ->where('cgsm.IdStudentRegistration = ?',$IdStudentRegistration)					
					  ->where('cgsm.IdCourseTaggingGroupMinor = ?',$idGroup);					  
		 $row = $db->fetchRow($select);	
		 
		 if($row)
			return $row;
		else
			return null;
	}
    
    public function listStudentMappingGroup($idGroup){
		
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		 $select = $db ->select()		
					  ->from(array('cgsm'=>$this->_name))								
					  ->where('cgsm.IdCourseTaggingGroupMinor = ?',(int)$idGroup);					  
		 $row = $db->fetchAll($select);	
		 
		 if($row)
         {
            $student_ids = '';
            foreach ($row as $key => $value)
            {
                $student_ids .= $value['IdStudent'].',';
            }
         
			return rtrim($student_ids,',');
         }
		else
			{
            return null;
            }
	}
    
    public function getMaxApprovalDate($student_ids){
		
		$db = Zend_Db_Table::getDefaultAdapter();		
		
        
		 
         $select = $db ->select()		
					  ->from(array('cgsm'=>$this->_name))					
					  //->from(array('stdreg'=>'tbl_studentregsubjects',array(new Zend_Db_Expr('max(mark_approveddt) as approvedt'))))					
					  //->where('cgsm.IdStudent = stdreg.IdStudentRegistration')					
					  ->where("stdreg.IdStudentRegistration IN ($student_ids)");					  
		 $row = $db->fetchRow($select);	
		 
		 if($row)
            return $row;
		else
			return null;
            
	}
    
    public function getStudentMarkByCourseGroup($course_id,$program=null) {
        $db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
					//->from(array('gsm'=>$this->_name))
					->from(array('sr'=>'tbl_studentregistration'))
					->joinLeft(array('sp'=>'student_profile'), 'sp.appl_id = sr.IdApplication')
					->joinLeft(array('srs'=>$this->_name), 'srs.IdStudentRegistration = sr.IdStudentRegistration')
					//->joinLeft(array('sm'=>'tbl_student_marks_entry'), 'sm.IdStudentRegSubjects = srs.IdStudentRegSubjects')
					//->where('gsm.IdCourseTaggingGroup = srs.IdCourseTaggingGroup')
					->where('srs.IdCourseTaggingGroupMinor = ?',$course_id)
					->order('sr.registrationId');
		
		if ($program!=null) $select->where('sr.IdProgram=?',$program);
		
        $row = $db->fetchAll($select);
			
		return $row;
    }
    
    public function getStudentListByCourseGroup($course_id) {
        $db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
                      ->from(array('a'=>$this->_name))
					  ->where('a.IdCourseTaggingGroupMinor = ?',$course_id);		
	
		$row = $db->fetchAll($select);
			
		//echo $select.'<br />';
        if($row)
         {
            $student_ids = '';
            foreach ($row as $key => $value)
            {
                $student_ids .= $value['IdStudentRegistration'].',';
            }
         
			return rtrim($student_ids,',');
         }
		else
			{
            return null;
            }
    }
    
     
    
}