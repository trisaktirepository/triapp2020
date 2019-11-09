<?php

class Examination_Model_DbTable_Assessmenttype extends Zend_Db_Table_Abstract { //Model Class for Users Details

	protected $_name = 'tbl_examination_assessment_type';

	public function init() {
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}

	public function fnaddAssessmenttype($larrformData) { //Function for adding the user details to the table
		$this->insert($larrformData);
	}

	public function fngetAllassessmentType() {
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array('a' => 'tbl_examination_assessment_type'), array('a.IdExaminationAssessmentType','a.IdDescription','a.Description','a.DescriptionDefaultlang'));
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	
	public function ajaxgetAllassessmentType() {
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array('a' => 'tbl_examination_assessment_type'), array('a.IdExaminationAssessmentType','a.IdDescription','a.Description','a.DescriptionDefaultlang'));
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function getdropdownforasseementtype(){
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array('a' => 'tbl_examination_assessment_type'), array('key' => 'a.IdExaminationAssessmentType', 'value' => 'a.Description'));
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function deleteAlldata(){
		$this->lobjDbAdpt->delete('tbl_examination_assessment_type');
	}
    
    //Added by Jasdy delete in array
    public function deleteAllArray($data){
		$sql = array(
            'IdExaminationAssessmentType IN ('.$data.')'
        );
        $this->lobjDbAdpt->delete('tbl_examination_assessment_type',$sql);
	}
	public function fnGettypeListList() {
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array('a' => 'tbl_examination_assessment_type'), array("key" => "a.IdExaminationAssessmentType", "value" => "a.Description"));                        ;

		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}


	public function fnGetAssesmentTypeNamebyID($idcomp) {
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array('a' => 'tbl_examination_assessment_type'), array("a.Description","ItemName"=>'a.DescriptionDefaultlang'))
		->where('a.IdExaminationAssessmentType = ?',$idcomp);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}

	public function fnGetAssesmentItemNamebyID($idcompitem) {
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array('a' => 'tbl_examination_assessment_item'), array("a.Description"))
		->where('a.IdExaminationAssessmentType = ?',$idcompitem);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}

	public function fnSearchSemester($post = array()) {
		$semList = array();
		$lstrSelect = $this->lobjDbAdpt->select()
			->from(array('a'=>'tbl_semester'))
			->where("a.Active =?",1)
			->where('a.SemesterCode <> ?',' ');
		
		if(isset($post['field2']) && !empty($post['field2']) ){
			//$select = $select->where("a.idsponsor = ?",$post['field5']);
			$lstrSelect = $lstrSelect->where("a.SemesterCode LIKE  '%".$post['field2']."%'  ");
		}
		
		if(isset($post['field14']) && !empty($post['field14'])){
			$lstrSelect = $lstrSelect->where("a.SemesterStartDate >=?",date('Y-m-d',strtotime($post['field14'])));
		}
		
		if(isset($post['field15']) && !empty($post['field15'])){
			$lstrSelect = $lstrSelect->where("a.SemesterEndDate >=?",date('Y-m-d',strtotime($post['field15'])));
				
		}
			
		if(isset($post['field14']) && !empty($post['field14']) && isset($post['field15']) && !empty($post['field15'])){
			$lstrSelect = $lstrSelect->where("a.SemesterStartDate >=?",date('Y-m-d',strtotime($post['field14'])))
			->where("a.SemesterEndDate <=?",date('Y-m-d',strtotime($post['field15'])));
		}
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		
		$Select = $this->lobjDbAdpt->select()
				->from(array('a'=>'tbl_semestermaster'))
				->where("a.IsCountable =?",1)
				->where("a.DummyStatus IS NULL")
				->where('a.SemesterMainCode <> ?',' ');
		
		if(isset($post['field2']) && !empty($post['field2']) ){
			//$select = $select->where("a.idsponsor = ?",$post['field5']);
			$Select = $Select->where("a.SemesterMainCode LIKE  '%".$post['field2']."%'  ");
		}
		
		if(isset($post['field14']) && !empty($post['field14'])){
			$Select = $Select->where("a.SemesterMainStartDate >=?",date('Y-m-d',strtotime($post['field14'])));
		}
		
		if(isset($post['field15']) && !empty($post['field15'])){
			$Select = $Select->where("a.SemesterMainEndDate >=?",date('Y-m-d',strtotime($post['field15'])));
		
		}
			
		if(isset($post['field14']) && !empty($post['field14']) && isset($post['field15']) && !empty($post['field15'])){
			$Select = $Select->where("a.SemesterMainStartDate >=?",date('Y-m-d',strtotime($post['field14'])))
			->where("a.SemesterMainEndDate <=?",date('Y-m-d',strtotime($post['field15'])));
		}
		
		$Result = $this->lobjDbAdpt->fetchAll($Select);
		
		foreach($larrResult as $arr){
			$tem['key'] = $arr['SemesterCode'];
			$tem['value'] = $arr['SemesterCode'];
			$tem['IdSemesterMaster'] = $arr['IdSemester'];
			$tem['SemesterMainCode'] = $arr['SemesterCode'];
			$tem['SemesterMainStartDate'] = $arr['SemesterStartDate'];
			$tem['SemesterMainEndDate'] = $arr['SemesterEndDate'];
			$semList[] = $tem;
		}
		
		foreach($Result as $arr){
			$tem['key'] = $arr['SemesterMainCode'];
			$tem['value'] = $arr['SemesterMainCode'];
			$tem['IdSemesterMaster'] = $arr['IdSemesterMaster'];
			$tem['SemesterMainCode'] = $arr['SemesterMainCode'];
			$tem['SemesterMainStartDate'] = $arr['SemesterMainStartDate'];
			$tem['SemesterMainEndDate'] = $arr['SemesterMainEndDate'];
			$semList[] = $tem;
		}
		
		return $semList;
		
	}
	
	public function fnSemester(){
		$semList = array();
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array('a'=>'tbl_semester'))
		->where("a.Active =?",1)
		->where('a.SemesterCode <> ?',' ');
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		
		$Select = $this->lobjDbAdpt->select()
		->from(array('a'=>'tbl_semestermaster'))
		->where("a.IsCountable =?",1)
		->where("a.DummyStatus IS NULL")
		->where('a.SemesterMainCode <> ?',' ');
		$Result = $this->lobjDbAdpt->fetchAll($Select);
		
		
		foreach($larrResult as $arr){
			$tem['key'] = $arr['SemesterCode'];
			$tem['value'] = $arr['SemesterCode'];
			$tem['IdSemesterMaster'] = $arr['IdSemester'];
			$tem['SemesterMainCode'] = $arr['SemesterCode'];
			$tem['SemesterMainStartDate'] = $arr['SemesterStartDate'];
			$tem['SemesterMainEndDate'] = $arr['SemesterEndDate'];
			$semList[] = $tem;
		}
	
		foreach($Result as $arr){
			$tem['key'] = $arr['SemesterMainCode'];
			$tem['value'] = $arr['SemesterMainCode'];
			$tem['IdSemesterMaster'] = $arr['IdSemesterMaster'];
			$tem['SemesterMainCode'] = $arr['SemesterMainCode'];
			$tem['SemesterMainStartDate'] = $arr['SemesterMainStartDate'];
			$tem['SemesterMainEndDate'] = $arr['SemesterMainEndDate'];
			$semList[] = $tem;
		}
		
		return $semList;
	}
	
	
	public function fngetsemesterid($id){
		
		$semList = array();
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array('a'=>'tbl_semester'))
		->where("a.Active =?",1)
		->where('a.SemesterCode <> ?',' ')
		->where("a.IdSemester =?",$id);
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		
		
		$Select = $this->lobjDbAdpt->select()
		->from(array('a'=>'tbl_semestermaster'))
		->where("a.IsCountable =?",1)
		->where("a.DummyStatus IS NULL")
		->where('a.SemesterMainCode <> ?',' ')
		->where("a.IdSemesterMaster =?",$id);
		$Result = $this->lobjDbAdpt->fetchAll($Select);
		
		foreach($larrResult as $arr){
			$tem['key'] = $arr['SemesterCode'];
			$tem['value'] = $arr['SemesterCode'];
			$tem['IdSemesterMaster'] = $arr['IdSemester'];
			$tem['SemesterMainCode'] = $arr['SemesterCode'];
			$tem['SemesterMainStartDate'] = $arr['SemesterStartDate'];
			$tem['SemesterMainEndDate'] = $arr['SemesterEndDate'];
			$semList[] = $tem;
		}
	
		foreach($Result as $arr){
			$tem['key'] = $arr['SemesterMainCode'];
			$tem['value'] = $arr['SemesterMainCode'];
			$tem['IdSemesterMaster'] = $arr['IdSemesterMaster'];
			$tem['SemesterMainCode'] = $arr['SemesterMainCode'];
			$tem['SemesterMainStartDate'] = $arr['SemesterMainStartDate'];
			$tem['SemesterMainEndDate'] = $arr['SemesterMainEndDate'];
			$semList[] = $tem;
		}
		
		return $semList;
	}
// 	public function fngetsemesterid($id) {
// 		$lstrSelect = $this->lobjDbAdpt->select()
// 						->from(array('a' => 'tbl_semestermaster'), array("a.SemesterMainName","a.SemesterMainStartDate","a.IdSemesterMaster",
// 							"a.SemesterMainEndDate","a.IdSemesterMaster","a.SemesterMainCode"))
// 						->where("a.IdSemesterMaster =?",$id);
// 		$larrResult = $this->lobjDbAdpt->fetchrow($lstrSelect);
// 		return $larrResult;
	
// 	}
	
	public function fngetassessment() {
		$lstrSelect = $this->lobjDbAdpt->select()
					->from(array('a' => 'tbl_examination_assessment_type'), array("a.*"));
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	
	public function fngetassessmentid() {
		$lstrSelect = $this->lobjDbAdpt->select()
				->from(array('a' => 'tbl_examination_assessment_type'), array("a.*"))
				->where("a.Description =?",'Final Exam');
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fnAddExamCalendar($larrformData,$id){
		
		if($larrformData['ActivityGrid']){
			$where_del1 = $this->lobjDbAdpt->quoteInto('Semester = ?', $larrformData['Semester']);
			$this->lobjDbAdpt->delete('tbl_exam_calendar', $where_del1);
			$check = count($larrformData['ActivityGrid']);
			for($i=0;$i<$check;$i++) {
				$paramArray = array(
						'Semester' => $larrformData['Semester'],
						'Activity' => $larrformData['ActivityGrid'][$i],
						'AssessmentType'=> $larrformData['AssessmentTypeGrid'][$i],
						'StartDate'=> date('Y-m-d',strtotime($larrformData['StartDateGrid'][$i])),
						'EndDate'=> date('Y-m-d',strtotime($larrformData['EndDateGrid'][$i])),
						'UpdUser'=>$larrformData['UpdUser'],
						'UpdDate'=>$larrformData['UpdDate'],
						'IdUniversity'=>$larrformData['IdUniversity'],
				);
				
				$this->lobjDbAdpt->insert('tbl_exam_calendar',$paramArray);
			}
		}
	}
	
	
	public function fngetExamcalendarId($lstrsemestercode) {
		//echo $lstrsemestercode;
		$lstrSelect = $this->lobjDbAdpt->select()
					->from(array('a' => 'tbl_exam_calendar'), array("a.*"))
					->joinLeft(array('b'=>'tbl_examination_assessment_type'),'b.IdExaminationAssessmentType = a.AssessmentType',array('b.Description as assessment'))
					->joinLeft(array('c'=>'tbl_definationms'),'c.idDefinition = a.Activity',array('c.DefinitionCode as activitycode'))
					->where("a.Semester =?",$lstrsemestercode);
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	
}
