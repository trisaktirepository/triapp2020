<?php
class Servqual_Model_DbTable_Servqual extends Zend_Db_Table { //Model Class for Users Details
	protected $_name = 'tbl_servqual_group_tagging';
	protected $_primary ='id';
	
	
	public function updateData($lobjFormData,$idserqual){
			$db = Zend_Db_Table::getDefaultAdapter();
			$lobjFormData['update_date']=date('d-m-yyy');
			$where = 'id='.$idserqual;
			$db->update($this->_name,$lobjFormData,$where);
	}
	
	public function insertData($lobjFormData){
		$db = Zend_Db_Table::getDefaultAdapter();
		//$lobjFormData['active']='1';
		$lobjFormData['active_date']=date('d-m-yyy');
		$db->insert($this->_name,$lobjFormData);
		//$lastInsertId = $this->getAdapter()->lastInsertId();
		return $lastInsertId;
	}
	
	public function deleteData($idserqual){
		$db = Zend_Db_Table::getDefaultAdapter();
		$where = 'id='.$idserqual;
		$db->delete($this->_name,$where);
	}
	
	public function getData($idserqual=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
				->from(array('sqt'=>$this->_name))
				->join(array('sq'=>'tbl_servqual'),'sqt.servqual_id=sq.IdServqual')
				->join(array('def'=>'tbl_definationms'),'sq.survey_type=def.IdDefinition',array('surveytype'=>'def.BahasaIndonesia'))
				->join(array('uni'=>'tbl_university'),'uni.IdUniversity=sgt.univ_id',array('Univ_Name'))
				->joinLeft(array('cl'=>'tbl_collegemaster'),'cl.IdCollege=sqt.college_id',array('CollegeName'))
				->joinLeft(array('pr'=>'tbl_program'),'pr.IdProgram=sqt.program_id',array('ProgramCode','ProgramName'))
				->joinLeft(array('br'=>'tbl_branchofficevenue'),'br.IdBranch=sqt.branch_id',array('BranchName'))
				->joinLeft(array('sb'=>'tbl_subjcetmaster'),'sb.IdSubject=sqt.subject_id',array('ShortName','SubjectName'))
				->joinLeft(array('grp'=>'tbl_course_tagging_group'),'grp.IdCoursetaggingGroup=sqt.group_id',array('GroupName'))
				->where('sq.active="1"');
		if ($idserqual!=null) {
			$select->where('id=?',$idserqual);
			$row=$db->fetchRow($select);
		}
		else
		$row=$db->fetchAll($select);
		return $row;
		
	}
	
     
}