<?php
class Examination_Model_DbTable_Remarkingconfig extends Zend_Db_Table_Abstract
{
	protected $_name = 'tbl_appealmarks_config';
	private $lobjDbAdpt;

	public function init() {
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$this->lobjRemarkapplication =  new Examination_Model_DbTable_Remarkapplication();
	}

	public function addAppeal($larrformData)
	{
		unset($larrformData['Save']);
		$this->insert($larrformData);
		$getlID = $this->lobjDbAdpt->lastInsertId();
		return $getlID;
	}


	public function getConfigDetails($IdUniversity) {

		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("a" => "tbl_appealmarks_config"), array("a.*"))
		->where('a.IdUniversity =?',$IdUniversity);
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function updateConfigDetails($larrformData,$IdUniversity){

		unset($larrformData['Save']);
		unset($larrformData['IdUniversity']);
		$where = 'IdUniversity = '.$IdUniversity;
		$this->update($larrformData,$where);
	}

	public function fnSearchRemarkingEntry($post = array()) {

		$select = $this->lobjDbAdpt->select()
		->from(array("a" => "tbl_appeal"), array("a.*"))
		->joinLeft(array('nm' => 'tbl_studentregistration'), 'nm.IdStudentRegistration = a.IdRegistration',array("CONCAT_WS(' ',IFNULL(nm.FName,''),IFNULL(nm.MName,''),IFNULL(nm.LName,'')) as StudentName"))
		->joinLeft(array('b'=>'tbl_program'),'a.IdProgram = b.IdProgram',array('b.ProgramName'))
		->joinLeft(array('c'=>'tbl_subjectmaster'),'a.IdCourse = c.IdSubject',array("CONCAT_WS(' - ',IFNULL(c.SubjectName,''),IFNULL(c.SubCode,'')) AS SubjectName"))
		->joinLeft(array('e'=>'tbl_appealmarksentry'),'e.IdAppeal=a.IdAppeal',array('e.IdComponent'))
		->joinLeft(array('f'=>'tbl_examination_assessment_type'),'f.IdExaminationAssessmentType=e.IdComponent',array('f.Description as ComponentName'))
		->joinLeft(array('d'=>'tbl_definationms'),'d.idDefinition = a.Status',array('d.DefinitionCode'));

		if(isset($post['field23']) && !empty($post['field23']) ){
			$select = $select->where("a.IdCourse = ?",$post['field23']);

		}
		if(isset($post['field24']) && !empty($post['field24']) ){
			$select = $select->where("a.IdProgram = ?",$post['field24']);

		}
		if(isset($post['field27']) && !empty($post['field27']) ){
			$semester = $this->lobjRemarkapplication->getSemcode($post['field27']);
			$select = $select->where("a.SemesterCode = ?",$semester);

		}
		if(isset($post['field2']) && !empty($post['field2']) ){
			$select = $select->where("a.IdStudent = ?",$post['field2']);

		}
		if(isset($post['field1']) && !empty($post['field1']) ){
			$select = $select->where("a.ApplicationSource = ?",$post['field1']);

		}
		if(isset($post['field25']) && !empty($post['field25']) ){
			$select = $select->where("a.Status = ?",$post['field25']);

		}
		if(isset($post['field3']) && !empty($post['field3']) ){
			$select = $select->where("a.AppealApplicationID = ?",$post['field3']);

		}
		$select->group("a.AppealApplicationID");
		$result = $this->lobjDbAdpt->fetchAll($select);

		return $result;
	}

	public function showstatus(){
		$select = $this->lobjDbAdpt->select()
		->from(array("a" => "tbl_definationms"), array("key"=>"a.idDefinition","value"=>"a.DefinitionDesc"))
		->where("a.idDefType=55 AND LOWER(a.DefinitionDesc) IN ('draft','approved','Reject','Entry')");
		$result = $this->lobjDbAdpt->fetchAll($select);
		return $result;

	}

	//tbl_studentregsubjects se IdStudentRegSubjects where idCourse=1
	public function showcomponent($courseID){
		$select = $this->lobjDbAdpt->select()

		->from(array("a"=>"tbl_subcredithoursdistrbtn"),array(""))
		->joinLeft(array('b'=>'tbl_examination_assessment_type'),'b.IdExaminationAssessmentType = a.Idcomponents',array('key'=>'b.IdExaminationAssessmentType','value'=>'b.Description'))
		//->joinLeft(array('c'=>'tbl_examination_assessment_item'),'c.IdExaminationAssessmentType = a.IdcomponentItem',array('key'=>"CONCAT_WS('-',IFNULL(b.IdExaminationAssessmentType,''),IFNULL(c.IdExaminationAssessmentType,''))",'value'=>"CONCAT_WS('-',IFNULL(b.Description,''),IFNULL(c.Description,''))"))
		//->joinLeft(array('c'=>'tbl_examination_assessment_item'),'c.IdExaminationAssessmentType = a.IdcomponentItem',array('key'=>"CONCAT_WS('-',IFNULL(b.IdExaminationAssessmentType,''),IFNULL(c.IdExaminationAssessmentType,''))",'value'=>"CONCAT_WS('-',IFNULL(b.Description,''),IFNULL(c.Description,''))"))
		->where("a.IdSubject = ?",$courseID)->group('a.Idcomponents');

		$result = $this->lobjDbAdpt->fetchAll($select);

		return $result;
	}


	public function showdetailcomponent($getIDstudentReg,$courseID,$component){
		$Compdetail = $component;
		$select = $this->lobjDbAdpt->select()
		->from(array("a"=>"tbl_student_marks_entry"),array("a.Component","a.ComponentItem"))
		->joinLeft(array('e'=>'tbl_examination_assessment_type'),'e.IdExaminationAssessmentType = a.Component',array('e.Description as MainCompName'))
		->joinLeft(array('f'=>'tbl_examination_assessment_item'),'f.IdExaminationAssessmentType = a.ComponentItem',array('f.Description as MainCompItemName'))
		->joinLeft(array('b'=>'tbl_student_detail_marks_entry'),'b.IdStudentMarksEntry=a.IdStudentMarksEntry',array(''))
		->joinLeft(array('c'=>'tbl_marksdistributiondetails'),'c.IdMarksDistributionDetails=b.ComponentDetail',array(''))
		->joinLeft(array('d'=>'tbl_examination_assessment_item'),'d.IdExaminationAssessmentType = c.IdComponentItem',array('key'=>'b.ComponentDetail','value'=>'d.Description'))
		->where("a.IdStudentRegistration = ?",$getIDstudentReg)
		->where("a.Course = ?",$courseID)
		->where("a.Component =?",$component)
		//->where("a.ComponentItem =?",$Compdetail[1])
		->where("a.MarksEntryStatus=?",'313');
		$result = $this->lobjDbAdpt->fetchall($select);
		return $result;



		//        if(count($result)>0) {
		//        $resultID = $result[0]['IdStudentMarksEntry'];
		//        if($resultID!='') {
		//        $select = $this->lobjDbAdpt->select()
		//                        ->from(array("a"=>"tbl_student_detail_marks_entry"),array(""))
		//                        ->joinLeft(array('b'=>'tbl_marksdistributiondetails'),'b.IdMarksDistributionDetails=a.ComponentDetail',array(''))
		//                        ->joinLeft(array('c'=>'tbl_examination_assessment_type'),'c.IdExaminationAssessmentType = b.IdComponentType',array('key'=>'a.ComponentDetail','value'=>'c.Description'))
		//                        ->where("a.IdStudentMarksEntry = ?",$resultID)->group('a.ComponentDetail');
		//       // echo $select;
		//        $resultnew = $this->lobjDbAdpt->fetchall($select);
		//        if(count($resultnew)>0) {   return $resultnew; }
		//        else { return $result;  }
		//        }
		//        }
		//        else {  return $resultnew;
		//
		//        }




	}


	public function showdetailcomponentitem($getIDstudentReg,$courseID,$component,$componentitem){

		$select = $this->lobjDbAdpt->select()
		->from(array("a"=>"tbl_student_marks_entry"),array("a.IdStudentMarksEntry"))
		->where("a.IdStudentRegistration = ?",$getIDstudentReg)
		->where("a.Course = ?",$courseID)
		->where("a.Component =?",$component)
		->where("a.ComponentItem =?",$componentitem)
		->where("a.MarksEntryStatus=?",'313');
		$result = $this->lobjDbAdpt->fetchall($select);
		$resultID = $result[0]['IdStudentMarksEntry'];
		//echo $select; die;
		$select = $this->lobjDbAdpt->select()
		->from(array("a"=>"tbl_student_detail_marks_entry"),array(""))
		->joinLeft(array('b'=>'tbl_marksdistributiondetails'),'b.IdMarksDistributionDetails=a.ComponentDetail',array(''))
		->joinLeft(array('c'=>'tbl_examination_assessment_type'),'c.IdExaminationAssessmentType = b.IdComponentType',array('key'=>'a.ComponentDetail','value'=>'c.Description'))
		->where("a.IdStudentMarksEntry = ?",$resultID)->group('a.ComponentDetail');
		// echo $select;
		$resultnew = $this->lobjDbAdpt->fetchall($select);
		//asd($resultnew);
		return $resultnew;



	}




	public function addRemarkappeal($mode,$program,$semester,$larrformData,$getIDstudentReg,$UpdDate,$userId)
	{


		$data = array('IdStudent'=>$larrformData['field2'],'SemesterCode'=>$semester,
				'IdCourse'=>$larrformData['field23'],'IdProgram'=>$program,
				'IdRegistration'=>$getIDstudentReg,'UpdDate'=>$UpdDate,'UpdUser'=>$userId,
				'Status'=>192,'ApplicationSource'=>$mode);

		$this->lobjDbAdpt->insert('tbl_appeal',$data);
		$lastinsertID = $this->lobjDbAdpt->lastInsertId();

		$AppealApplicationID = 'R00'.$lastinsertID;
		$data_up =  array('AppealApplicationID'=>$AppealApplicationID);
		$where_up = "  IdAppeal =' ".$lastinsertID."' ";
		$this->lobjDbAdpt->update('tbl_appeal',$data_up, $where_up);


		$ComponentGrid = array_unique($larrformData['ComponentGrid']);
		$totalData = count($ComponentGrid);
		//asd($ComponentGrid);
		//for($i=0;$i<$totalData;$i++) {
		foreach($ComponentGrid as $key=>$value) {

			$idCompArr = explode('-',$larrformData['ComponentGrid'][$key]);
			$idComp = $idCompArr[0];
			$idCompItem = $idCompArr[1];
			//$idCompDetail = $larrformData['DetailComponentGrid'][$i];

			$lstrSelect = $this->lobjDbAdpt->select()
			->from(array("a" => "tbl_student_marks_entry"), array("a.*"))
			->where('a.IdStudentRegistration =?',$getIDstudentReg)
			->where('a.Course =?',$larrformData['field23'])
			->where('a.Component =?',$idComp)
			->where('a.ComponentItem =?',$idCompItem);
			//echo $lstrSelect;
			$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
			$IdStudentMarksEntry = $larrResult[0]['IdStudentMarksEntry'];
			$lstrSelect1 = $this->lobjDbAdpt->select()
			->from(array("b"=>"tbl_student_detail_marks_entry"),array('b.*'))
			->where('b.IdStudentMarksEntry =?',$IdStudentMarksEntry);
			//->where('b.Component =?',$idComp)
			//->where('b.ComponentDetail =?',$idCompDetail);
			$larrResult1 = $this->lobjDbAdpt->fetchAll($lstrSelect1);
			if(count($larrResult1)>0) {

				foreach($larrResult1 as $values) {

					$oldMarks = $values['MarksObtained'];
					$totalMarks = $values['TotalMarks'];
					$data1 = array('IdAppeal'=>$lastinsertID,
							'IdComponent'=>$values['Component'],
							'IdComponentItem'=>$values['ComponentItem'],
							'IdSubComponent'=>$values['ComponentDetail'],
							'OldMarks'=>$oldMarks,
							'TotalMarks'=>$totalMarks,
							'UpdUser'=>$userId,
							'UpdDate'=>$UpdDate
					);
					$this->lobjDbAdpt->insert('tbl_appealmarksentry',$data1);

				}

			} else {

				$oldMarks = $larrResult[0]['TotalMarkObtained'];
				$totalMarks = $larrResult[0]['MarksTotal'];
				$data2 = array('IdAppeal'=>$lastinsertID,
						'IdComponent'=>$idComp,
						'IdComponentItem'=>$idCompItem,
						'IdSubComponent'=>'0',
						'OldMarks'=>$oldMarks,
						'TotalMarks'=>$totalMarks,
						'UpdUser'=>$userId,
						'UpdDate'=>$UpdDate
				);
				//asd($data2);
				$this->lobjDbAdpt->insert('tbl_appealmarksentry',$data2);


			}

		}


	}

	public function updateRemarkappeal($userId,$UpdDate,$courseID,$idAppeal,$larrformData,$getIDstudentReg)
	{
		$this->lobjDbAdpt->delete('tbl_appealmarksentry','IdAppeal='.$idAppeal);

		$ComponentGrid = array_unique($larrformData['ComponentGrid']);
		$totalData = count($ComponentGrid);
		//asd($ComponentGrid);
		//for($i=0;$i<$totalData;$i++) {
		foreach($ComponentGrid as $key=>$value) {

			$idCompArr = explode('-',$larrformData['ComponentGrid'][$key]);
			$idComp = $idCompArr[0];
			$idCompItem = $idCompArr[1];
			//$idCompDetail = $larrformData['DetailComponentGrid'][$i];

			$lstrSelect = $this->lobjDbAdpt->select()
			->from(array("a" => "tbl_student_marks_entry"), array("a.*"))
			->where('a.IdStudentRegistration =?',$getIDstudentReg)
			->where('a.Course =?',$courseID)
			->where('a.Component =?',$idComp)
			->where('a.ComponentItem =?',$idCompItem);
			//echo $lstrSelect;
			$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
			$IdStudentMarksEntry = $larrResult[0]['IdStudentMarksEntry'];
			$lstrSelect1 = $this->lobjDbAdpt->select()
			->from(array("b"=>"tbl_student_detail_marks_entry"),array('b.*'))
			->where('b.IdStudentMarksEntry =?',$IdStudentMarksEntry);
			//->where('b.Component =?',$idComp)
			//->where('b.ComponentDetail =?',$idCompDetail);
			$larrResult1 = $this->lobjDbAdpt->fetchAll($lstrSelect1);
			if(count($larrResult1)>0) {

				foreach($larrResult1 as $values) {

					$oldMarks = $values['MarksObtained'];
					$totalMarks = $values['TotalMarks'];
					$data1 = array('IdAppeal'=>$idAppeal,
							'IdComponent'=>$values['Component'],
							'IdComponentItem'=>$values['ComponentItem'],
							'IdSubComponent'=>$values['ComponentDetail'],
							'OldMarks'=>$oldMarks,
							'TotalMarks'=>$totalMarks,
							'UpdUser'=>$userId,
							'UpdDate'=>$UpdDate
					);
					$this->lobjDbAdpt->insert('tbl_appealmarksentry',$data1);

				}

			} else {

				$oldMarks = $larrResult[0]['TotalMarkObtained'];
				$totalMarks = $larrResult[0]['MarksTotal'];
				$data2 = array('IdAppeal'=>$idAppeal,
						'IdComponent'=>$idComp,
						'IdComponentItem'=>$idCompItem,
						'IdSubComponent'=>'0',
						'OldMarks'=>$oldMarks,
						'TotalMarks'=>$totalMarks,
						'UpdUser'=>$userId,
						'UpdDate'=>$UpdDate
				);
				//asd($data2);
				$this->lobjDbAdpt->insert('tbl_appealmarksentry',$data2);


			}

		}




		//        $DetailComponentGrid = $larrformData['DetailComponentGrid'];
		//        $ComponentGrid = $larrformData['ComponentGrid'];
		//
		//        foreach($DetailComponentGrid as $values) {
		//
		//            $idCompArr = explode('-',$larrformData['field25']);
		//            $idComp = $idCompArr[0];
		//            $idCompItem = $idCompArr[1];
		//
		//            $idCompDetail = $values;
		//
		//            $lstrSelect = $this->lobjDbAdpt->select()
		//                                    ->from(array("a" => "tbl_student_marks_entry"), array("a.IdStudentMarksEntry"))
		//                                    ->where('a.IdStudentRegistration =?',$getIDstudentReg)
		//                                    ->where('a.Course =?',$courseID)
		//                                    ->where('a.Component =?',$idComp);
		//            $larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		//
		//            $IdStudentMarksEntry = $larrResult[0]['IdStudentMarksEntry'];
		//            echo $IdStudentMarksEntry;
		//            $lstrSelect1 = $this->lobjDbAdpt->select()
		//                                    ->from(array("b"=>"tbl_student_detail_marks_entry"),array('b.MarksObtained','b.TotalMarks'))
		//                                    ->where('b.IdStudentMarksEntry =?',$IdStudentMarksEntry)
		//                                    ->where('b.Component =?',$idComp)
		//                                    ->where('b.ComponentDetail =?',$idCompDetail);
		//            $larrResult1 = $this->lobjDbAdpt->fetchAll($lstrSelect1);
		//
		//            $oldMarks = $larrResult1[0]['MarksObtained'];
		//            $totalMarks = $larrResult1[0]['TotalMarks'];
		//
		//            $data1 = array('IdComponent'=>$idComp,'IdComponentItem'=>$idCompItem,
		//                           'IdSubComponent'=>$idCompDetail,
		//                           'OldMarks'=>$oldMarks,
		//                           'TotalMarks'=>$totalMarks,
		//                           'IdAppeal' =>$idAppeal,
		//                           'UpdUser'=>$userId,
		//                           'UpdDate'=>$UpdDate
		//                           );
		//            $this->lobjDbAdpt->insert('tbl_appealmarksentry',$data1);
		//
		//        }

	}


	public function getTotalAppealperSemester($idstudent,$SemesterCode) {

		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("a" => "tbl_appeal"), array("COUNT(a.IdAppeal) as totalAppeal"))
		->where('a.IdRegistration =?',$idstudent)->where('a.SemesterCode =?',$SemesterCode);
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult[0]['totalAppeal'];
	}



	public function getMaxAppealperSemester($IdUniversity,$SemesterCode) {

		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("a" => "tbl_appealmarks_config"), array("a.MaxAppeal"))
		->where('a.IdUniversity =?',$IdUniversity)->where('a.SemesterCode =?',$SemesterCode);
		//echo $lstrSelect;
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}


	public function getMaxAppealperUniversity($IdUniversity) {

		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("a" => "tbl_appealmarks_config"), array("a.MaxAppeal"))
		->where('a.IdUniversity =?',$IdUniversity);
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}



	public function getChooseMarksperUniversity($IdUniversity) {

		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("a" => "tbl_appealmarks_config"), array("a.ChooseMarks"))
		->where('a.IdUniversity =?',$IdUniversity)->group('a.IdUniversity');
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}


	public function getAppealDetailByID($idAppeal) {

		$select = $this->lobjDbAdpt->select()->from(array("a" =>"tbl_appealmarksentry"),array('a.*'))
		->joinLeft(array("b"=>"tbl_examination_assessment_type"),'b.IdExaminationAssessmentType=a.IdComponent',array('b.Description as Component'))
		->joinLeft(array("e"=>"tbl_examination_assessment_item"),'e.IdExaminationAssessmentType=a.IdComponentItem',array('e.Description as ComponentItem'))
		->joinLeft(array("c"=>"tbl_marksdistributiondetails"),'c.IdMarksDistributionDetails= a.IdSubComponent',array(''))
		->joinLeft(array("d"=>"tbl_examination_assessment_item"),'d.IdExaminationAssessmentType= c.IdComponentItem',array('d.Description as DetailComponent'))
		->where("a.IdAppeal = ?",$idAppeal);
		$result1 = $this->lobjDbAdpt->fetchAll($select);
		return $result1;
	}


	public function fngetIdStudentregistration($studentID) {
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = $db->select()
		->from(array('a' => 'tbl_studentregistration'), array('a.*'))
		->where('a.registrationId = ?', $studentID);
		$result = $db->fetchAll($sql);

		//echo "<pre>";
		//print_r($result); die;
		return $result;

	}

	public function fngetdetails($idAppeal) {
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = $db->select()
		->from(array('a' => "tbl_appeal"), array('a.*'))
		->joinLeft(array("b"=> "tbl_subjectmaster"),'b.IdSubject=a.IdCourse',array("CONCAT_WS(' - ',IFNULL(b.SubjectName,''),IFNULL(b.SubCode,'')) AS SubjectName"))
		->joinLeft(array("c"=> "tbl_appealmarksentry"),'c.IdAppeal=a.IdAppeal',array('c.IdComponent'))
		->joinLeft(array("d"=> "tbl_definationms"),'d.idDefinition=c.IdComponent',array('d.DefinitionCode'))
		->joinLeft(array("e"=> "tbl_program"),'e.IdProgram=a.IdProgram',array('e.ProgramName'))
		->where('a.IdAppeal = ?', $idAppeal);
		$result = $db->fetchAll($sql);

		return $result;

	}


	public function fngetdetailcomponent($idAppeal){

		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = $db->select()
		->from(array('a' => "tbl_appeal"), array('a.*'))
		->joinLeft(array("b"=> "tbl_appealmarksentry"),'b.IdAppeal=a.IdAppeal',array('b.IdComponent','b.IdComponentItem'))
		->where('a.IdAppeal = ?', $idAppeal);

		$result = $db->fetchAll($sql);

		return $result;
	}


	public function fngetAppealStatus($idAppeal) {
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = $db->select()
		->from(array('a' => "tbl_appeal"), array('a.Status'))
		->where('a.IdAppeal = ?', $idAppeal);
		$result = $db->fetchAll($sql);

		return $result;

	}

	public function showProgramName($studentID) {
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = $db->select()
		->from(array('a' => 'tbl_studentregistration'), array(''))
		->join(array('b'=>'tbl_program'),'b.IdProgram = a.IdProgram',array("key"=>"b.IdProgram","value"=>"b.ProgramName"))
		->where('a.registrationId  = ?', $studentID);
		$result = $db->fetchAll($sql);
		return $result;
	}


}
?>