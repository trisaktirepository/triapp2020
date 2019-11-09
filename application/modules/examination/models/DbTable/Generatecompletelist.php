<?php
class Examination_Model_DbTable_Generatecompletelist extends Zend_Db_Table {

	protected $_name = 'tbl_completestudentlist';
	private $lobjDbAdpt;
	private $lobjdeftype;
	private $lobjremarkapps;

	public function init()
	{
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$this->lobjdeftype = new App_Model_Definitiontype();
		$this->lobjremarkapps   = new Examination_Model_DbTable_Remarkapplication();
	}

	public function fnprocessCompleteStudent($larrformData) {

		$select = $this->lobjDbAdpt->select()
		->from(array("a" => "tbl_studentregsubjects"), array("a.*"))
		->join(array("studreg" =>"tbl_studentregistration"),"studreg.IdStudentRegistration = a.IdStudentRegistration AND studreg.Status='198' "  ,array('studreg.registrationId','studreg.Status',"CONCAT_WS(' ',IFNULL(studreg.FName,''),IFNULL(studreg.MName,''),IFNULL(studreg.LName,'')) AS StudentName"))
		->joinLeft(array("d" => "tbl_subjectmaster"), "d.IdSubject = a.IdSubject",array("d.CreditHours"))
		->joinLeft(array("e" => "tbl_definationms"), "studreg.Status = e.idDefinition",array("e.DefinitionDesc"))
		->joinLeft(array('b'=>'tbl_program'),'studreg.IdProgram = b.IdProgram',array('b.ProgramName','b.IdProgram','b.TotalCreditHours as ProgramHours'))
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

			//$semCode =  $this->lobjremarkapps->getSemcode($larrformData['field27']);

		}




		if(isset($larrformData['field2']) && !empty($larrformData['field2']) )
		{
			$select->where("studreg.registrationId = ?",$larrformData['field2']);

		}

		if(isset($larrformData['field24']) && !empty($larrformData['field24']) )
		{
			$select->where("studreg.IdProgram = ?",$larrformData['field24']);

		}


		$select->where("a.IdGrade = ?",'pass' );
		$select->group("a.IdStudentRegistration" );
		//echo $select;  echo '</br>';echo '</br>'; die;
		$result = $this->lobjDbAdpt->fetchAll($select);


		foreach($result as $values) {

			$id  =   $values['IdStudentRegistration'];
			$IdSemesterMain = $values['IdSemesterMain'];
			$IdSemesterDetails = $values['IdSemesterDetails'];
			$ProgramHours = $values['ProgramHours'];

			if($IdSemesterMain!='' && $IdSemesterDetails=='')
			{
				$select2 = $this->lobjDbAdpt->select()
				->from(array("sdme" => "tbl_studentregsubjects"),array('sdme.IdSubject'))
				->joinLeft(array("d" => "tbl_subjectmaster"), "d.IdSubject = sdme.IdSubject",array("d.CreditHours"))
				->where("sdme.IdSemesterMain = ?",$IdSemesterMain)->where("sdme.IdStudentRegistration = ?",$id)->where("sdme.IdGrade = ?", 'pass');
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
				->where("sdme.IdSemesterDetails = ?",$IdSemesterDetails)->where("sdme.IdStudentRegistration = ?",$id)->where("sdme.IdGrade = ?", 'pass');
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



			if($ProgramHours==$TotalMarksF) {

				// delete all with semester combination
				$where_delete = array();
				$where_delete[] = $this->lobjDbAdpt->quoteInto('IdSemester = ?', $semesterCode);
				$this->delete($where_delete);

				$params = array(
						//'IdStudentRegSubjects'=>$values['IdStudentRegSubjects'],
						'IdStudentRegistration'=>$values['IdStudentRegistration'],
						'StudentName'=>$values['StudentName'],
						'registrationId'=>$values['registrationId'],
						'IdSemester'=>$semesterCode,
						'IdProgram'=>$values['IdProgram'],
						'ProgramHours'=>$ProgramHours,
						'CreditHoursTaken'=>$TotalMarksF,
						'Status'=>$values['Status'],
						'UpdUser' => $larrformData['UpdUser'],
						'UpdDate' => $larrformData['UpdDate'],

				);
				$this->insert($params);

			}


		}

	}



	public function fngetCompleteStudentList($larrformData) {
		$remarkApp = new Examination_Model_DbTable_Remarkapplication();
		$semCode = $remarkApp->getSemcode($larrformData['field27']);
		$select = $this->lobjDbAdpt->select()
		->from(array("a" => "tbl_completestudentlist"), array("a.*"))
		->joinLeft(array('b'=>'tbl_program'),'a.IdProgram = b.IdProgram',array('b.ProgramName'))
		->joinLeft(array("e" => "tbl_definationms"), "a.Status = e.idDefinition",array("e.DefinitionDesc"))
		->where("a.IdSemester = ?",$semCode);

		$result = $this->lobjDbAdpt->fetchAll($select);
		return $result;

	}

	public function fngetCompleteStudentListAll($post) {
		$select = $this->lobjDbAdpt->select()
		->from(array("a" => "tbl_completestudentlist"), array("a.*"))
		->join(array('reg'=>'tbl_studentregistration'),'reg.IdStudentRegistration = a.IdStudentRegistration',array(''))
		->joinLeft(array('b'=>'tbl_program'),'a.IdProgram = b.IdProgram',array('b.ProgramName'))
		->joinLeft(array("e" => "tbl_definationms"), "a.Status = e.idDefinition",array("e.DefinitionDesc"));

		if(isset($post['field2']) && $post['field2'] != ''){
			$wh_condition = "a.StudentName like '%".$post['field2']."%'";
			$select = $select->where($wh_condition);
		}
		if(isset($post['field3']) && $post['field3'] != ''){
			$wh_condition = "a.registrationId like '%".$post['field3']."%'";
			$select = $select->where($wh_condition);
		}
		if(isset($post['field27']) && $post['field27'] != ''){
			$select = $select->where('a.IdProgram = ?',$post['field27']);
		}
		if (isset($post['field10']) && !empty($post['field10'])) {
			$select = $select->where('a.IdSemester = ?',$post['field10']);
		}
		$result = $this->lobjDbAdpt->fetchAll($select);
		return $result;

	}



}