<?php
class App_Model_Exam_DbTable_MarkDistribution extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'tbl_marksdistributionmaster';
	protected $_primary = 'IdMarksDistributionMaster';
		
	
	public function getListMainComponent($idSemester,$idProgram,$idSubject,$IdBranch=0) {
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$sql =  $db->select()->from(array("mdm" =>"tbl_marksdistributionmaster"))
								->joinLeft(array('eat'=>'tbl_examination_assessment_type'),'eat.IdExaminationAssessmentType=mdm.IdComponentType',array('component_name'=>'DescriptionDefaultlang'))
							    ->where("mdm.semester = ?",$idSemester)
								->where("mdm.IdProgram = ?",$idProgram)
								->where("mdm.IdCourse = ?",$idSubject) 
								->where("mdm.IdBranch = ?",$IdBranch);

		$result = $db->fetchAll($sql);
		if (!$result) {
			$sql =  $db->select()->from(array("mdm" =>"tbl_marksdistributionmaster"))
			->joinLeft(array('eat'=>'tbl_examination_assessment_type'),'eat.IdExaminationAssessmentType=mdm.IdComponentType',array('component_name'=>'DescriptionDefaultlang'))
			->where("mdm.semester = ?",$idSemester)
			->where("mdm.IdProgram = ?",$idProgram)
			->where("mdm.IdCourse = ?",$idSubject);
		
			$result = $db->fetchAll($sql);
		}
		
		return $result;
	}
	
	public function getComponentInfo($id) {
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$sql =  $db->select()->from(array("mdm" =>"tbl_marksdistributionmaster"))
								->joinLeft(array('eat'=>'tbl_examination_assessment_type'),'eat.IdExaminationAssessmentType=mdm.IdComponentType',array('component_name'=>'DescriptionDefaultlang'))
							    ->where("mdm.IdMarksDistributionMaster = ?",$id);

		$result = $db->fetchRow($sql);
		
		return $result;
	}
}
?>