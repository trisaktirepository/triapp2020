<?php

class Examination_Model_DbTable_Assessmentitem extends Zend_Db_Table_Abstract { //Model Class for Users Details

	protected $_name = 'tbl_examination_assessment_item';

	public function init() {
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}

	public function fnaddAssessmentItemtype($larrformData) { //Function for adding the user details to the table
		$this->insert($larrformData);
	}

	public function fngetAllassessmentItemType() {
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array('a' => 'tbl_examination_assessment_item'), array('a.IdExaminationAssessmentType','a.IdDescription','a.Description','a.Description','a.DescriptionDefaultlang'));
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function getdropdownforasseementitem(){
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array('a' => 'tbl_examination_assessment_item'), array('key' => 'a.IdExaminationAssessmentType','value' => 'a.Description'));
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function deleteAlldataItem(){
		$this->lobjDbAdpt->delete('tbl_examination_assessment_item');
	}

    //Added by Jasdy
    public function deleteAllArray($data){
        $sql = array(
            'IdExaminationAssessmentType IN ('.$data.')'
        );
		$this->lobjDbAdpt->delete('tbl_examination_assessment_item',$sql);
	}
    
	public function fnGetitemListList() {
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array('a' => 'tbl_examination_assessment_item'), array("key" => "a.IdExaminationAssessmentType", "value" => "a.Description"));                        ;

		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

}