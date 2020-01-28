<?php 

class App_Model_Exam_DbTable_ExamGroupStudent extends Zend_Db_Table_Abstract {
	
	protected $_name = 'exam_group_student';
	protected $_primary = "egst_id";
	

	public function getData($id){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('egst'=>$this->_name))
		->where('egst.egst_id = ?',$id);
		
		$row = $db->fetchRow($select);
		
		return $row;
	}
	
	public function backup($id){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('egst'=>$this->_name))
		->where('egst.egst_id = ?',$id);
	
		$row = $db->fetchRow($select);
		unset($row['egst_id']);
		$db->insert('exam_group_student_clash',$row);
		
	}
	
	public function getStudentList($group_id,$idSubject,$idSemester,$course_group_id=null){
		
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		/*$select = $db ->select()
					  ->from(array('egst'=>$this->_name))
					  ->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration=egst.egst_student_id', array('transaction_id'=>'transaction_id','appl_id'=>'IdApplication','IdStudentRegistration'))
					  ->join(array('tp'=>'tbl_program'),'sr.idProgram=tp.idProgram',array('ProgramName'=>'ArabicName', 'ProgramCode'=>'ProgramCode'))
					  ->joinLeft(array('ap'=>'student_profile'),'ap.appl_id=sr.IdApplication',array('appl_fname','appl_mname','appl_lname'))					  
					  ->where('egst.egst_group_id = ?',$group_id);*/
							  
		
		
		/*
		 *  SELECT `egst`.*, `sr`.`transaction_id`, `sr`.`IdApplication` AS `appl_id`, `sr`.`IdStudentRegistration`, `tp`.`ArabicName` AS `ProgramName`, `tp`.`ProgramCode`, `ap`.`appl_fname`, `ap`.`appl_mname`, `ap`.`appl_lname` , srs.`IdSubject`,g.GroupName
			FROM `exam_group_student` AS `egst` 
			INNER JOIN `tbl_studentregistration` AS `sr` ON sr.IdStudentRegistration=egst.egst_student_id 
			INNER JOIN `tbl_program` AS `tp` ON sr.idProgram=tp.idProgram 
			LEFT JOIN `student_profile` AS `ap` ON ap.appl_id=sr.IdApplication
			inner join tbl_studentregsubjects AS srs ON srs.`IdStudentRegistration`=sr.IdStudentRegistration
			JOIN tbl_course_tagging_group as g ON g.IdCourseTaggingGroup=srs.`IdCourseTaggingGroup`
			
			WHERE (egst.egst_group_id = '2')
			AND srs.IdSubject=2338
		 */
		
		//yatie add 4/3/2014 nak paparkan course group
		$select = $db ->select()
					  ->from(array('egst'=>$this->_name))
					  ->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration=egst.egst_student_id', array('transaction_id'=>'transaction_id','appl_id'=>'IdApplication','IdStudentRegistration'))
					  ->join(array('tp'=>'tbl_program'),'sr.idProgram=tp.idProgram',array('ProgramName'=>'ArabicName', 'ProgramCode'=>'ProgramCode','tp.IdProgram'))
					  ->joinLeft(array('ap'=>'student_profile'),'ap.appl_id=sr.IdApplication',array('appl_fname','appl_mname','appl_lname'))	
					  ->join(array('srs'=>'tbl_studentregsubjects'),'srs.`IdStudentRegistration`=sr.IdStudentRegistration',array('IdSubject'))		
					  ->join(array('g'=>'tbl_course_tagging_group'),'g.IdCourseTaggingGroup=srs.`IdCourseTaggingGroup`',array('GroupName','GroupCode','IdCourseTaggingGroup'))		
					  ->where('egst.egst_group_id = ?',$group_id)
					  ->where('srs.IdSubject = ?',$idSubject)
					  ->where('srs.IdSemesterMain = ?',$idSemester)
						->order('sr.RegistrationId');
					  
		if(isset($course_group_id) && $course_group_id!=''){
			$select->where('srs.IdCourseTaggingGroup=?',$course_group_id);
		}			

		//echo $select; 
		$row = $db->fetchAll($select);
		
		return $row;
	}
	
	public function getStudentListAttendance($group_id){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('egst'=>$this->_name))
		->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration=egst.egst_student_id', array('transaction_id'=>'transaction_id','appl_id'=>'IdApplication'))
		->join(array('tp'=>'tbl_program'),'sr.idProgram=tp.idProgram',array('ProgramName'=>'ArabicName', 'ProgramCode'=>'ProgramCode'))
		->joinLeft(array('ap'=>'student_profile'),'ap.appl_id=sr.IdApplication',array('appl_fname','appl_mname','appl_lname'))
		->joinLeft(array('ega'=>'exam_group_attendance'), 'ega.ega_student_id = egst.egst_student_id and ega.ega_eg_id = '.$group_id, array('att_status'=>'ega_status'))
		->where('egst.egst_group_id = ?',$group_id);
			
		$row = $db->fetchAll($select);
	
		return $row;
	}
	
	public function getStudentListClash($idprogram,$idsemester,$examtype){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('egst'=>'exam_group_student_clash'))
		->join(array('eg'=>'exam_group'),'egst.egst_group_id=eg.eg_id')
		->join(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=eg.eg_sub_id',array('subjectname'=>'BahasaIndonesia','subcode'=>'ShortName','sks'=>'CreditHours'))
		->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration=egst.egst_student_id', array('nim'=>'registrationId','transaction_id'=>'transaction_id','appl_id'=>'IdApplication'))
		->join(array('tp'=>'tbl_program'),'sr.idProgram=tp.idProgram',array('ProgramName'=>'ArabicName', 'ProgramCode'=>'ProgramCode'))
		->joinLeft(array('ap'=>'student_profile'),'ap.appl_id=sr.IdApplication',array('appl_fname','appl_mname','appl_lname'))
		->where('sr.IdProgram = ?',$idprogram)
		->where('eg.eg_sem_id = ?',$idsemester)
		->where('eg.eg_assessment_type = ?',$examtype)
		->where('egst.egst_status="1"')
		->order('sr.registrationId');
			
		$row = $db->fetchAll($select);
	
		return $row;
	}
	
	public function getTotalStudentAssigned($group_id){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
					->from($this->_name)
					->where('egst_group_id = ?',$group_id);
		
		$row = $db->fetchAll($select);
			
		if($row)
			return count($row);
		else
			return 0;
	}
	
	public function getTotalStudentAssignedPerCourseGroup($idgroup,$idsub,$idsem,$idprog){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		$std =$db->select()
		->from(array('ct'=>'tbl_studentregsubjects'),array('IdStudentRegistration','IdSemesterMain','IdSubject'))
		->where('ct.IdCourseTaggingGroup = ?',$idgroup);
		
		$select = $db ->select()
		->from(array('egt'=>$this->_name),array('egt.egst_student_id'))
		->join(array('eg'=>'exam_group'),'egt.egst_group_id=eg.eg_id',array('eg.eg_sem_id','eg.eg_sub_id'))
		->join(array('st'=>'tbl_studentregistration'),'st.IdStudentRegistration=egt.egst_student_id',array())
		->where('eg.eg_sub_id = ?',$idsub)
		->where('st.IdProgram = ?',$idprog)
		->where('eg.eg_sem_id = ?',$idsem);
	
		$sql = $db->select()
		->from(array('course'=>$std),array('nstd'=>'count(*)'))
		->join(array('exam'=>$select),'course.IdStudentRegistration=exam.egst_student_id and course.IdSubject=exam.eg_sub_id and course.IdSemesterMain=exam.eg_sem_id',array());
		 
		$row = $db->fetchRow($sql);
			
		if($row)
			return $row['nstd'];
		else
			return 0;
	}
	
	public function getTotalStudentAssignedPerExamGrp($idgroup,$idprogram){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		 
		$select = $db ->select()
		->from(array('egt'=>$this->_name),array('nstd'=>'count(*)'))
		->join(array('eg'=>'exam_group'),'egt.egst_group_id=eg.eg_id',array())
		->join(array('st'=>'tbl_studentregistration'),'st.IdStudentRegistration=egt.egst_student_id',array())
		->where('eg.eg_id = ?',$idgroup)
		->where('st.IdProgram = ?',$idprogram);
	
		 
		$row = $db->fetchRow($select);
			
		if($row)
			return $row['nstd'];
		else
			return 0;
	}
	
	public function getTotalStudentClashPerCourseGroup($idgroup,$idsub,$idsem,$idprog){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		$std =$db->select()
		->from(array('ct'=>'tbl_studentregsubjects'),array('IdStudentRegistration','IdSemesterMain','IdSubject'))
		->where('ct.IdCourseTaggingGroup = ?',$idgroup);
	
		$select = $db ->select()
		->from(array('egt'=>'exam_group_student_clash'),array('egt.egst_student_id'))
		->join(array('eg'=>'exam_group'),'egt.egst_group_id=eg.eg_id',array('eg.eg_sem_id','eg.eg_sub_id'))

		->join(array('st'=>'tbl_studentregistration'),'st.IdStudentRegistration=egt.egst_student_id',array())
		->where('eg.eg_sub_id = ?',$idsub)
		->where('st.IdProgram = ?',$idprog)
		->where('eg.eg_sem_id = ?',$idsem)
		->where('egt.egst_status = "1"');
	
		$sql = $db->select()
		->from(array('course'=>$std),array('nstd'=>'count(*)'))
		->join(array('exam'=>$select),'course.IdStudentRegistration=exam.egst_student_id and course.IdSubject=exam.eg_sub_id and course.IdSemesterMain=exam.eg_sem_id',array());
			
		$row = $db->fetchRow($sql);
			
		if($row)
			return $row['nstd'];
		else
			return 0;
	}
	
	public function checkStudentGroup($IdStudentRegistration,$subject_id,$semester_id,$IdMarksDistributionMaster){
		
		$db = Zend_Db_Table::getDefaultAdapter();	
		
		//dapatkan mark distribution master punya type assessment
		
		  $select_dis = $db ->select()
					  ->from(array('mdm'=>'tbl_marksdistributionmaster'),'IdComponentType')					 
					  ->where('mdm.IdMarksDistributionMaster = ?',$IdMarksDistributionMaster);
							  
		  $row_mdm = $db->fetchRow($select_dis);
		
		  $IdComponentType = $row_mdm["IdComponentType"]; //assessment type id
				
		  $select = $db ->select()
					  ->from(array('egst'=>$this->_name))
					  ->join(array('eg'=>'exam_group'),'eg.eg_id=egst.egst_group_id',array('eg_group_name'))
					  ->where('egst.egst_student_id = ?',$IdStudentRegistration)
					  ->where('egst.egst_subject_id = ?',$subject_id)
					  ->where('egst.egst_semester_id = ?',$semester_id)
					  ->where('eg.eg_assessment_type = ?',$IdComponentType)
					  ->where('eg.eg_repeat_status = 0'); //Bukan type repeat
							  
		$row = $db->fetchRow($select);
		
		return $row;
	}
	
	
	public function getAssessment($subject_id,$semester_id,$idgroup){
			 
		 $db = Zend_Db_Table::getDefaultAdapter();	

		 $select_student = $db ->select()
					           ->from(array('cgsm'=>'tbl_course_group_student_mapping'),array('IdStudent'))
					           ->where('`IdCourseTaggingGroup` = ?',$idgroup);
		
		 $select = $db ->select()
					  ->from(array('eg'=>'exam_group'),array(''))
					  ->join(array('egst'=>$this->_name ),'eg.eg_id=egst.egst_group_id',array())
					  ->join(array('eat'=>'tbl_examination_assessment_type' ),'eat.IdExaminationAssessmentType = eg.eg_assessment_type',array('assessment_id'=>'IdExaminationAssessmentType','assessment_name'=>'DescriptionDefaultlang'))
					  ->where('egst.`egst_subject_id` = ?',$subject_id)
					  ->where('eg.`eg_sem_id` = ?',$semester_id)
					  ->where('egst.`egst_student_id` IN (?)',$select_student)
					  ->group('eg.eg_assessment_type');
							  
		$rows = $db->fetchAll($select);
		
		return $rows;
	}
	
	
	public function getExamGroup($subject_id,$semester_id,$idgroup,$idAssessment){
		
		
		 $db = Zend_Db_Table::getDefaultAdapter();	

		 $select_student = $db ->select()
					           ->from(array('cgsm'=>'tbl_studentregsubjects'),array('IdStudentRegistration'))
					           ->where('IdCourseTaggingGroup = ?',$idgroup);
		
		 $select = $db ->select()
					  ->from(array('eg'=>'exam_group'),array('eg_id','eg_group_name'))
					  ->join(array('egst'=>$this->_name ),'eg.eg_id=egst.egst_group_id',array())
					  ->join(array('eat'=>'tbl_examination_assessment_type' ),'eat.IdExaminationAssessmentType = eg.eg_assessment_type',array())
					  ->where('egst.`egst_subject_id` = ?',$subject_id)
					  ->where('eg.`eg_sem_id` = ?',$semester_id)
					  ->where('eg.eg_assessment_type = ?',$idAssessment)
					  ->where('egst.`egst_student_id` IN (?)',$select_student)
					  ->group('eg.eg_id');
							  
		$rows = $db->fetchAll($select);
		//echo var_dump($row);exit;
		return $rows;
	}
	
	 
	public function getCourseGroup($examgroupid,$semesterid,$idsubject,$lectid=null){
	
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select_student = $db ->select()
		->from(array('egst'=>'exam_group_student'),array('egst.egst_student_id'))
		->where('egst_group_id = ?',$examgroupid);
	
		$select = $db ->select()
		->from(array('srs'=>'tbl_studentregsubjects'),array())
		->join(array('cg'=>'tbl_course_tagging_group' ),'cg.IdCourseTaggingGroup=srs.IdCourseTaggingGroup',array('cg.IdCourseTaggingGroup','IdLecturer','Examiner'))
		->joinLeft(array('sm'=>'tbl_subjectmaster'),'sm.IdSubject=cg.IdSubject',array('subject_code'=>'SubCode','subject_name'=>'subjectMainDefaultLanguage','faculty_id'=>'IdFaculty','credithours'))
		->where('srs.IdSemesterMain=?',$semesterid)
		->where('srs.IdSubject=?',$idsubject)
		->where('srs.`IdStudentRegistration` IN (?)',$select_student)
		->group('srs.IdCourseTaggingGroup');
		if ($lectid!=null) $select->where('cg.IdLecturer=?',$lectid);
		$rows = $db->fetchAll($select);
		//echo var_dump($row);exit;
		return $rows;
	}
	public function getCourseGroupExaminer($examgroupid,$semesterid,$idsubject,$idprogram){
	
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select_student = $db ->select()
		->from(array('egst'=>'exam_group_student'),array('egst.egst_student_id'))
		->join(array('st'=>'tbl_studentregistration'),'st.IdStudentregistration=egst.egst_student_id',array())
		->join(array('egp'=>'exam_group_program'),'egp.egp_eg_id=egst.egst_group_id',array())
		->where('egst_group_id = ?',$examgroupid)
		->where('st.IdProgram = ?',$idprogram)
		->where('egp.dt_payment_created is null');
	
		$select = $db ->select()
		->from(array('srs'=>'tbl_studentregsubjects'),array())
		->join(array('cg'=>'tbl_course_tagging_group' ),'cg.IdCourseTaggingGroup=srs.IdCourseTaggingGroup',array('cg.IdCourseTaggingGroup','IdLecturer','IFNULL(Examiner,IdLecturer)','GroupName'))
		 
		->join(array('sm'=>'tbl_staffmaster'),'sm.IdStaff=IFNULL(cg.Examiner,cg.IdLecturer)',array('IdStaff','StaffGrade','StaffStatus','StaffType'=>'StaffJobType','EduLevel','BankAccountNo'))
		->joinLeft(array('s'=>'tbl_subjectmaster'),'s.IdSubject=cg.IdSubject',array('subject_code'=>'SubCode','subject_name'=>'subjectMainDefaultLanguage','faculty_id'=>'IdFaculty','credithours'))
		->where('srs.IdSemesterMain=?',$semesterid)
		->where('srs.IdSubject=?',$idsubject)
		->where('srs.`IdStudentRegistration` IN (?)',$select_student)
		->group('srs.IdCourseTaggingGroup');
		//echo $select;exit;
		$rows = $db->fetchAll($select);
		//echo var_dump($row);exit;
		return $rows;
	}
	public function getExamGroupSchedule($studentId, $semesterId, $subjectId, $assessmentType=null,$resit=0){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
			->from(array('egs'=>'exam_group_student'))
			->join(array('eg'=>'exam_group'), 'eg.eg_id = egs.egst_group_id')
			->join(array('v'=>'appl_room'), 'v.av_id = eg.eg_room_id')
			->where('egs.egst_student_id = ?',$studentId)
			->where('egs.egst_semester_id = ?', $semesterId)
			->where('egs.egst_subject_id = ?',$subjectId);
		
		if($assessmentType){
			$select->where('eg.eg_assessment_type = ?', $assessmentType);
		}
		
		if($resit){
			$select->where('eg.eg_repeat_status = ?', $resit);
		} else $select->where('eg.eg_repeat_status = "0"');
			
		$rows = $db->fetchAll($select);
		
		return $rows;
		
	}
	
	
	public function getExamGroupScheduleBySubject($subject_id,$semester_id,$assessmentcode,$reguler=null){
	
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		 
		$select = $db ->select()
		->from(array('eg'=>'exam_group'),array('eg_id','eg_group_name'))
		//->join(array('egst'=>'exam_group_schedule' ),'eg.eg_id=egst.egst_group_id',array())
		->join(array('eat'=>'tbl_examination_assessment_type' ),'eat.IdExaminationAssessmentType = eg.eg_assessment_type',array())
		->where('eg.eg_sub_id = ?',$subject_id)
		->where('eg.eg_sem_id = ?',$semester_id)
		->where('eg.eg_assessment_type = ?',$assessmentcode)
		->where('eg.eg_date >= DATE_SUB(CURDATE() , INTERVAL 3 DAY)');
		if ($reguler!=null) $select->where('eg.eg_repeat_status=?',$reguler);
		$rows = $db->fetchRow($select);
		//echo var_dump($rows);
		//echo $select;exit;
		return $rows;
	}
	
	public function canCancel($subject_id,$semester_id,$assessmentcode,$reguler=null){
	
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
			
		$select = $db ->select()
		->from(array('eg'=>'exam_group'),array('eg_id','eg_group_name'))
		//->join(array('egst'=>'exam_group_schedule' ),'eg.eg_id=egst.egst_group_id',array())
		->join(array('eat'=>'tbl_examination_assessment_type' ),'eat.IdExaminationAssessmentType = eg.eg_assessment_type',array())
		->where('eg.eg_sub_id = ?',$subject_id)
		->where('eg.eg_sem_id = ?',$semester_id)
		->where('eg.eg_assessment_type = ?',$assessmentcode)
		->where('eg.eg_date >= DATE_SUB(CURDATE() , INTERVAL 1 DAY)');
		if ($reguler!=null) $select->where('eg.eg_repeat_status=?',$reguler);
		$rows = $db->fetchRow($select);
		//echo var_dump($rows);
		//echo $select;exit;
		return $rows;
	}
	
	
	public function getListCourseGroup($group_id,$idSubject,$idSemester,$course_group_id=null){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db ->select()
					  ->from(array('egst'=>$this->_name),array())
					  ->join(array('srs'=>'tbl_studentregsubjects'),'srs.`IdStudentRegistration`=egst.egst_student_id ',array())		
					  ->join(array('g'=>'tbl_course_tagging_group'),'g.IdCourseTaggingGroup=srs.`IdCourseTaggingGroup`',array('GroupName','GroupCode','IdCourseTaggingGroup'))		
					  ->join(array('s'=>'tbl_staffmaster'),'s.IdStaff=g.IdLecturer',array('Coordinator'=>"FullName"))
					  ->where('egst.egst_group_id = ?',$group_id)
					  ->where('srs.IdSubject = ?',$idSubject)
					  ->where('srs.IdSemesterMain = ?',$idSemester)
					  ->group('g.IdCourseTaggingGroup');
					  
		if(isset($course_group_id) && $course_group_id!=''){
			$select->where('srs.IdCourseTaggingGroup=?',$course_group_id);
		}						  
		$row = $db->fetchAll($select);
		
		return $row;
		
	}
	public function getexamgroupbystudent($studentid,$subjectid,$semesterid){
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$select = $db ->select()
					  ->from(array('egst'=>$this->_name),array())
					  ->join(array('eg'=>'exam_group'), 'eg.eg_id = egst.egst_group_id')
					  ->where("egst_student_id = ?",$studentid)
					  ->where("egst_subject_id = ?",$subjectid)
					  ->where('egst_semester_id = ?',$semesterid);
		//echo $select;					  
		 $row = $db->fetchRow($select);
		 return $row;			
	}	
	
	public function getexamgroupbystudentsemester($studentid,$semesterid){
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db ->select()
		->from(array('egst'=>$this->_name),array())
		->join(array('eg'=>'exam_group'), 'eg.eg_id = egst.egst_group_id')
		->where("egst_student_id = ?",$studentid) 
		->where('egst_semester_id = ?',$semesterid);
		//echo $select;
		$row = $db->fetchAll($select);
		return $row;
	}
	
	public function add($data) {
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->insert($this->_name,$data);
	}
	
	public function update(array $data,$where) {
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->update($this->_name,$data,$where);
	}
	
	public function updateClash($data,$where) {
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->update('exam_group_student_clash',$data,$where);
	}
}