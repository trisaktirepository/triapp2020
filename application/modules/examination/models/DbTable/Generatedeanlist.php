<?php
class Examination_Model_DbTable_Generatedeanlist extends Zend_Db_Table {

	protected $_name = 'tbl_deanlist_examination';
	private $lobjDbAdpt;

	public function init()
	{
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$this->lobjdeftype = new App_Model_Definitiontype();
		$this->lobjremarkapps   = new Examination_Model_DbTable_Remarkapplication();
	}

	public function fnprocessDean($larrformData) {

		$select = $this->lobjDbAdpt->select()
		->from(array("a" => "tbl_studentregsubjects"), array("a.*"))
		->join(array("studreg" =>"tbl_studentregistration"),'studreg.IdStudentRegistration = a.IdStudentRegistration',array('studreg.registrationId',"CONCAT_WS(' ',IFNULL(studreg.FName,''),IFNULL(studreg.MName,''),IFNULL(studreg.LName,'')) AS StudentName"))
		->joinLeft(array("d" => "tbl_subjectmaster"), "d.IdSubject = a.IdSubject",array("d.CreditHours"))
		->joinLeft(array('b'=>'tbl_program'),'studreg.IdProgram = b.IdProgram',array('b.ProgramName','b.IdProgram'))
		;

		if(isset($larrformData['field27']) && !empty($larrformData['field27']) )
		{
			$dataSem = explode('_',$larrformData['field27']);
			if($dataSem[1]=='detail') {
				$semDetail = $dataSem[0];
				$select->where("a.IdSemesterDetails = ?",$semDetail);
			} else {
				$semMain = $dataSem[0];
				$select->where("a.IdSemesterMain = ?",$semMain);
			}

			$semCode =  $this->lobjremarkapps->getSemcode($larrformData['field27']);

		}

		//if(isset($larrformData['field23']) && !empty($larrformData['field23'])  && !empty($larrformData['field2'])  && !empty($larrformData['field2'])  )
		//{
		// $definitionCode = $this->lobjdeftype->fetchDetailAward($larrformData['field23']);
		//$definitionCodeData = $definitionCode['DefinitionCode'];
		//$select->where("a.GradePoint".$definitionCodeData.$larrformData['field2'] );
		//$select->where("d.CreditHours".$definitionCodeData.$larrformData['field2'] );
		// }


		if(isset($larrformData['field3']) && !empty($larrformData['field3']) )
		{
			$select->join(array('gc'=>'tbl_gpacalculation'), " gc.IdStudentRegistration = a.IdStudentRegistration AND gc.IdSemester = '".$semCode."' AND gc.Cgpa >= '".$larrformData['field3']."' ",array('gc.Cgpa','gc.Gpa')) ;

		}

		// only active, defer and dormant students are eligible for dean list.
		$select->where("studreg.profileStatus = ?",'92')
				 ->orwhere("studreg.profileStatus = ?",'248')
				 ->orwhere("studreg.profileStatus = ?",'253');

		$select->where("a.IdGrade = ?",'pass' );
		$select->group("a.IdStudentRegistration" );
		//echo $select;  echo '</br>';echo '</br>'; die;
		$result = $this->lobjDbAdpt->fetchAll($select);


		foreach($result as $values) {

			$id  =   $values['IdStudentRegistration'];
			$IdSemesterMain = $values['IdSemesterMain'];
			$IdSemesterDetails = $values['IdSemesterDetails'];

			if($IdSemesterMain!='' && $IdSemesterDetails=='')
			{
				$select2 = $this->lobjDbAdpt->select()
				->from(array("sdme" => "tbl_studentregsubjects"),array('sdme.IdSubject'))
				->joinLeft(array("d" => "tbl_subjectmaster"), "d.IdSubject = sdme.IdSubject",array("d.CreditHours"))
				->where("sdme.IdSemesterMain = ?",$IdSemesterMain)->where("sdme.IdStudentRegistration = ?",$id);
				$result2 = $this->lobjDbAdpt->fetchAll($select2);
				$TotalMarksF = 0;
				foreach($result2 as $res2)  {
					$TotalMarksF = $TotalMarksF + $res2['CreditHours'];
				}
				$semsql =  $this->lobjDbAdpt->select()->from(array("semmast" =>"tbl_semestermaster"),array('semmast.SemesterMainCode'))
				->where("semmast.IdSemesterMaster = ?",$IdSemesterMain);
				$result = $this->lobjDbAdpt->fetchAll($semsql);
				$semesterCode = $result[0]['SemesterMainCode'];

			} else if($IdSemesterDetails!='' && $IdSemesterMain=='') {

				$select3 = $this->lobjDbAdpt->select()
				->from(array("sdme" => "tbl_studentregsubjects"),array('sdme.IdSubject'))
				->joinLeft(array("d" => "tbl_subjectmaster"), "d.IdSubject = sdme.IdSubject",array("d.CreditHours"))
				->where("sdme.IdSemesterDetails = ?",$IdSemesterDetails)->where("sdme.IdStudentRegistration = ?",$id);
				$result3 = $this->lobjDbAdpt->fetchAll($select3);
				$TotalMarksF = 0;
				foreach($result3 as $res3)  {
					$TotalMarksF = $TotalMarksF + $res3['CreditHours'];
				}
				$semsql = $this->lobjDbAdpt->select()->from(array("semdet" =>"tbl_semester"),array('semdet.SemesterCode'))
				->where("semdet.IdSemester = ?",$IdSemesterDetails);
				$result = $this->lobjDbAdpt->fetchAll($semsql);
				$semesterCode = $result[0]['SemesterCode'];

			}

			if(isset($larrformData['field23']) && !empty($larrformData['field23'])  && !empty($larrformData['field2'])  && !empty($larrformData['field2'])  )
			{
				$definitionCode = $this->lobjdeftype->fetchDetailAward($larrformData['field23']);
				$definitionCodeData = $definitionCode['DefinitionCode'];
				//echo $TotalMarksF.$definitionCodeData.$larrformData['field2']; die;
				if($TotalMarksF.$definitionCodeData.$larrformData['field2']) {


					// delete all students with semester combination
					$where_delete = array();
					//$where_delete[] = $this->lobjDbAdpt->quoteInto('IdStudentRegistration = ?', $values['IdStudentRegistration']);
					$where_delete[] = $this->lobjDbAdpt->quoteInto('IdSemester = ?', $semesterCode);
					$this->delete($where_delete);

					$params = array(
							//'IdStudentRegSubjects'=>$values['IdStudentRegSubjects'],
							'IdStudentRegistration'=>$values['IdStudentRegistration'],
							'StudentName'=>$values['StudentName'],
							'registrationId'=>$values['registrationId'],
							'IdSemester'=>$semesterCode,
							'IdProgram'=>$values['IdProgram'],
							'Cgpa'=>$values['Cgpa'],
							'Gpa'=>$values['Gpa'],
							'GradePoint'=>$values['GradePoint'],
							'CreditHours'=>$TotalMarksF,
							'UpdUser' => $larrformData['UpdUser'],
							'UpdDate' => $larrformData['UpdDate'],

					);
					$this->insert($params);

				}
			}

		}

	}



	public function fngetDeanList($larrformData) {
		$remarkApp = new Examination_Model_DbTable_Remarkapplication();
		$semCode = $remarkApp->getSemcode($larrformData['field27']);
		$select = $this->lobjDbAdpt->select()
		->from(array("a" => "tbl_deanlist_examination"), array("a.*"))
		->joinLeft(array('b'=>'tbl_program'),'a.IdProgram = b.IdProgram',array('b.ProgramName'))
		->where("a.IdSemester = ?",$semCode);

		$result = $this->lobjDbAdpt->fetchAll($select);
		return $result;

	}



}