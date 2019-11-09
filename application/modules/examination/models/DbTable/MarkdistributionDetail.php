<?php

class Examination_Model_DbTable_MarkdistributionDetail extends Zend_Db_Table {

	protected $_name = 'tbl_markdistributiondetail';
	protected $_primary = 'IdMarksDistributionDetail';

	public function init() {
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}

	public function addData($data){
		$this->lobjDbAdpt->insert($this->_name,$data);   
	   	return $lintgroupid = $this->lobjDbAdpt->lastInsertId();
	}
	
	public function updateData($data,$id){
		$this->update($data, $this->_primary .' = '. (int)$id);
	}
	
	public function deleteData($id){
		//deleted row
		$sql = $this->lobjDbAdpt
		->select()
		->from(array('mdm'=>$this->_name))
		->join(array('nm'=>'tbl_student_marks_entry_detail'),'nm.IdMarksDistributionMaster=mdm.IdMarksDistributionMaster')
		->where('mdm.IdMarksDistributionMaster =' . (int)$id);
		$result = $this->lobjDbAdpt->fetchRow($sql);
		//delete row
		if (!$result) $this->delete($this->_primary .' =' . (int)$id);
		else {
			//update allow copy to not allow copy for this component in previous semester
			$where='IdProgram='.$result['IdProgram'].' and IdCourse='.$result['IdCourse'].' and IdComponentType='.$result['IdComponentType'].' and IdComponentItem='.$result['IdComponentItem'];
			$this->update(array('allow_copy'=>"0"), $where);
		}
	}
	
	public function fnGetMarksDistributionDetailAll($idMaster) { //Function for the view user
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		
		$select = $lobjDbAdpt->select()
		->from(array("a" => $this->_name))
		->joinLeft(array("f" => "tbl_examination_assessment_type"), 'a.IdComponentType = f.IdExaminationAssessmentType', array('component_name'=>'f.DescriptionDefaultlang'))
		->where('a.IdMarksDistributionMaster=?',$idMaster);
		return $result = $lobjDbAdpt->fetchAll($select);
	}
	
	public function fnGetMarksDistributionDetail($idMasterDistributionDetail) { //Function for the view user
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	
		$select = $lobjDbAdpt->select()
		->from(array("a" => $this->_name))
		->joinLeft(array("f" => "tbl_examination_assessment_type"), 'a.IdComponentType = f.IdExaminationAssessmentType', array('component_name'=>'f.DescriptionDefaultlang'))
		->where('a.IdMarksDistributionDetail=?',$idMasterDistributionDetail);
		return $result = $lobjDbAdpt->fetchRow($select);
	}

	
	public function fnGetCalculationType() {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array('a' => 'tbl_definationms'),array('key'=>'idDefinition','value'=>'BahasaIndonesia'))
		->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
		->where('b.defTypeDesc = "Calculation Mode"')
		->where('a.Status = 1')
		->where('b.Active = 1')
		->order("b.defTypeDesc");
		$result = $lobjDbAdpt->fetchAll($select);
		return $result;
	}

	 

}