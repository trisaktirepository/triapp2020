<?php 

class Assistant_Model_DbTable_CourseGroupStudent extends Zend_Db_Table_Abstract {
	
	protected $_name = 'tbl_course_group_student_mapping';
	protected $_primary = "Id";
	
	
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
					  ->from(array('gsm'=>'tbl_studentregsubjects_assistant'))
					  ->join(array('sr'=>'tbl_studentregistration'), 'sr.IdStudentRegistration = gsm.IdStudentRegistration')
					  ->where('gsm.IdCourseTaggingGroup = ?',$idGroup);					  
		 $row = $db->fetchAll($select);	
		 
		 if($row)
		 	return count($row);
		 else
		 return 0;
	}
	
	public function removeStudent($idGroup,$idStudent){		
		
	  $this->delete("IdCourseTaggingGroup='". $idGroup ."' AND IdStudent = '".$idStudent."'");
	}
	
	public function getStudent($idGroup){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
				->distinct()
					->from(array('gsm'=>'tbl_studentregsubjects_assistant'))
					->join(array('sr'=>'tbl_studentregistration'), 'sr.IdStudentRegistration = gsm.IdStudentRegistration',array('registrationId'))
					->join(array('sp'=>'student_profile'), 'sp.appl_id = sr.IdApplication',array('appl_fname','appl_mname','appl_lname'))
					->where('IdCourseTaggingGroup = ?',$idGroup)
					->order('sr.registrationId');

		$row = $db->fetchAll($select);
			
		if($row)
			return $row;
		else
			return null;
	}
	
	public function getStudentbyGroup($idGroup,$student=null,$idprogram=null){
	
		$db = Zend_Db_Table::getDefaultAdapter();	
		
	    $select = $db ->select()
					->from(array('gsm'=>'tbl_studentregsubjects_assistant'))
					->join(array('sr'=>'tbl_studentregistration'), 'sr.IdStudentRegistration = gsm.IdStudentRegistration')
					->join(array('sp'=>'student_profile'), 'sp.appl_id = sr.IdApplication')
					->join(array('p'=>'tbl_program'), 'p.IdProgram=sr.IdProgram',array('ProgramName'=>'ArabicName','ProgramCode'))
					->where('IdCourseTaggingGroup = ?',$idGroup)				
					->order('sr.registrationId');

		if ($idprogram!=null) {
			$select->where('sr.IdProgram=?',$idprogram);
		}	
			
		if(isset($student)){
			$select->where("((sp.appl_fname LIKE '%".$student."%'");
			$select->orwhere("sp.appl_mname LIKE '%".$student."%'");
			$select->orwhere("sp.appl_lname LIKE '%".$student."%')");		
			$select->orwhere("sr.registrationId LIKE '%".$student."%')");
		}
		//echo $select;
		
		$row = $db->fetchAll($select);
		
		if($row)
			return $row;
		else
			return null;
	}
	
	public function checkStudentCourseGroup($IdStudentRegistration,$idSemester,$idSubject){
		
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		 $select = $db ->select()		
					  ->from(array('cgsm'=>'tbl_studentregsubjects_assistant'))
					  ->join(array('ctg'=>'tbl_course_tagging_group'),'ctg.IdCourseTaggingGroup=cgsm.IdCourseTaggingGroup')
					  ->where('cgsm.IdStudentRegistration = ?',$IdStudentRegistration)
					  ->where('ctg.IdSemester = ?',$idSemester)
					  ->where('ctg.IdSubject = ?',$idSubject);					  
		 $row = $db->fetchRow($select);	
		 
		 return $row;
	}
	
	public function getTotalStudentViaSubReg($idGroup){
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		 $select = $db ->select()		
					  ->from(array('a'=>'tbl_studentregsubjects_assistant'),array('total'=>'count(*)'))
					 	->where('IdCourseTaggingGroup = ?',$idGroup);					  
		 $row = $db->fetchRow($select);	
		 
		 return $row["total"];		
	}
	
	
	public function checkStudentMappingGroup($idGroup,$IdStudentRegistration){
		
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		 $select = $db ->select()		
					  ->from(array('cgsm'=>'tbl_studentregsubjects_assistant'))					
					  ->where('cgsm.IdStudentRegistration = ?',$IdStudentRegistration)					
					  ->where('cgsm.IdCourseTaggingGroup = ?',$idGroup);					  
		 $row = $db->fetchRow($select);	
		 
		 if($row)
			return $row;
		else
			return null;
	}
    
    public function listStudentMappingGroup($idGroup){
		
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		 $select = $db ->select()		
					  ->from(array('cgsm'=>'tbl_studentregsubjects_assistant'))								
					  ->where('cgsm.IdCourseTaggingGroup = ?',(int)$idGroup);					  
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
					  //->from(array('cgsm'=>$this->_name))					
					  ->from(array('stdreg'=>'tbl_studentregsubjects_assistant',array(new Zend_Db_Expr('max(mark_approveddt) as approvedt'))))					
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
					->joinLeft(array('srs'=>'tbl_studentregsubjects_assistant'), 'srs.IdStudentRegistration = sr.IdStudentRegistration')
					//->joinLeft(array('sm'=>'tbl_student_marks_entry'), 'sm.IdStudentRegSubjects = srs.IdStudentRegSubjects')
					//->where('gsm.IdCourseTaggingGroup = srs.IdCourseTaggingGroup')
					->where('srs.IdCourseTaggingGroup = ?',$course_id)
					->order('sr.registrationId');
		
		if ($program!=null) $select->where('sr.IdProgram=?',$program);
		
        $row = $db->fetchAll($select);
			
		return $row;
    }
    
    public function getStudentListByCourseGroup($course_id) {
        $db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
                      ->from(array('a'=>'tbl_studentregsubjects_assistant'))
					  ->where('a.IdCourseTaggingGroup = ?',$course_id);		
	
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
    
    public function getPercentageGrade($group_id)
    {
       
		$db = Zend_Db_Table::getDefaultAdapter();	
       
        $sql =  "SELECT abc.IdSubject,abc.IdCourseTaggingGroup, 
                      abc.A,abc.AMinus,abc.BPlus,abc.B,abc.BMinus,abc.CPlus,abc.C,abc.CMinus,
                      abc.DPlus,abc.D,abc.DMinus,abc.E,abc.MG,abc.IN,abc.NR,abc.W,abc.FR
                FROM 
                (
                    SELECT IdSubject, IdCourseTaggingGroup,
                        SUM(CASE WHEN a.grade_name = 'A' THEN 1 ELSE 0 END) as A,
                        SUM(CASE WHEN a.grade_name = 'A-' THEN 1 ELSE 0 END) as AMinus,
                        SUM(CASE WHEN a.grade_name = 'B+' THEN 1 ELSE 0 END) as BPlus,
                        SUM(CASE WHEN a.grade_name = 'B' THEN 1 ELSE 0 END) as B,
                        SUM(CASE WHEN a.grade_name = 'B-' THEN 1 ELSE 0 END) as BMinus,
                        SUM(CASE WHEN a.grade_name = 'C+' THEN 1 ELSE 0 END) as CPlus,
                        SUM(CASE WHEN a.grade_name = 'C' THEN 1 ELSE 0 END) as C,
                        SUM(CASE WHEN a.grade_name = 'C-' THEN 1 ELSE 0 END) as CMinus,
                        SUM(CASE WHEN a.grade_name = 'D+' THEN 1 ELSE 0 END) as DPlus,
                        SUM(CASE WHEN a.grade_name = 'D' THEN 1 ELSE 0 END) as D,
                        SUM(CASE WHEN a.grade_name = 'D-' THEN 1 ELSE 0 END) as DMinus,
                        SUM(CASE WHEN a.grade_name = 'E' THEN 1 ELSE 0 END) as E,
                        SUM(CASE WHEN a.grade_name = 'MG' THEN 1 ELSE 0 END) as MG,
                        SUM(CASE WHEN a.grade_name = 'IN' THEN 1 ELSE 0 END) as `IN`,
                        SUM(CASE WHEN a.grade_name = 'NR' THEN 1 ELSE 0 END) as NR,
                        SUM(CASE WHEN a.grade_name = 'W' THEN 1 ELSE 0 END) as W,
                        SUM(CASE WHEN a.grade_name = 'FR' THEN 1 ELSE 0 END) as FR
                    FROM tbl_studentregsubjects a
                    WHERE a.IdCourseTaggingGroup = $group_id";
        $sql.=      "            
                    GROUP BY a.IdCourseTaggingGroup
                ) abc";
       //echo $sql;
        $row = $db->fetchRow($sql);
        
        return $row;
    }
    
}