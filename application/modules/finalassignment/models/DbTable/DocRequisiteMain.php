<?php 
class Finalassignment_Model_DbTable_DocRequisiteMain extends Zend_Db_Table_Abstract { //Model Class for Users Details

	protected $_name = 'tbl_TA_syaratberkas';
	protected $_primary='IdTASyaratBerkas';

	public function init() {
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}
	
	public function addData($postData){
		 
		return $this->insert($postData);
	}
	
	public function updateData($postData, $id){
	
		 
		$this->update($postData, $this->_primary .' = '. (int) $id);
	}
	 
	public function deleteData($id){
		$this->delete($this->_primary . " = " . (int)$id);
	}
	
	public function getData($id){
			 
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->where('p.IdTASyaratBerkas = ?', $id);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	
	public function getAllDocRequisiteMain(){
		$session = new Zend_Session_Namespace('sis');
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->join(array('pm'=>'tbl_program'),'p.IdProgram=pm.IdProgram',array('Program'=>'ArabicName'))
		->joinLeft(array('m'=>'tbl_programmajoring'), 'p.IdMajoring=m.IdProgramMajoring',array('Major'=>'BahasaDescription'))
		->joinLeft(array('b'=>'tbl_branchofficevenue'),'p.IdBranch=b.IdBranch',array('Branch'=>'BranchName'))
		->join(array('def'=>'tbl_definationms'),'def.IdDefinition=p.ProcessCode',array('ProcessName'=>'def.BahasaIndonesia'))
		->join(array('sm'=>'tbl_semestermaster'),'sm.IdSemesterMaster=p.IdSemester',array('SemesterMainName'))
		->join(array('def1'=>'tbl_definationms'),'def1.IdDefinition=p.Activity_Code',array('ActivityName'=>'def1.BahasaIndonesia'));
		
		 

		if ($session->IdRole == 605 || $session->IdRole == 311 || $session->IdRole == 298 || $session->IdRole == 579 || $session->IdRole == 851){ //FACULTY DEAN atau FACULTY ADMIN nampak faculty dia sahaja
			$lstrSelect->where("pm.IdCollege='".$session->idCollege."'");
		}
		if ($session->IdRole == 470 || $session->IdRole == 480){ //FACULTY DEAN atau FACULTY ADMIN nampak faculty dia sahaja
			$lstrSelect->where("pm.IdProgram='".$session->idDepartment."'");
		}
		
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	
	
	public function getDocRequisiteMainByProgram($program,$major=null,$branch=null,$processcode){
	
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("f"=>$this->_name))
		->join(array('p'=>'tbl_program'),'f.IdProgram=p.IdProgram')
		->joinLeft(array('m'=>'tbl_programmajoring'), 'f.IdMajoring=m.IdProgramMajoring')
		->joinLeft(array('b'=>'tbl_branchofficevenue'),'f.IdBranch=b.IdBranch')
		->join(array('sm'=>"tbl_subjectmaster"),'f.IdSubject=sm.IdSubject',array('Subject'=>'CONCAT(sm.shortname,"-",sm.BahasaIndonesia)'))
		->join(array('def'=>'tbl_definationms'),'def.IdDefinition=f.ProcessCode',array('ProcessName'=>'def.BahasaIndonesia'))
		->join(array('def1'=>'tbl_definationms'),'def1.IdDefinition=f.Activity_Code',array('ActivityName'=>'def1.BahasaIndonesia'))
		->where('f.IdProgram= ?', $program)
		->where('def.DefinitionCode= ?', $processcode);
		if ($major!=null) $lstrSelect->where('f.IdMajoring=?',$major);
		if ($branch!=null) $lstrSelect->where('f.IdBranch=?',$branch);
		//echo $lstrSelect;exit;
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	public function getDocRequisiteMainById($id){
	
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("f"=>$this->_name))
		->join(array('p'=>'tbl_program'),'f.IdProgram=p.IdProgram')
		->joinLeft(array('m'=>'tbl_programmajoring'), 'f.IdMajoring=m.IdProgramMajoring')
		->joinLeft(array('b'=>'tbl_branchofficevenue'),'f.IdBranch=b.IdBranch')
		->join(array('sm'=>"tbl_semestermaster"),'f.IdSemester=sm.IdSemesterMaster',array('SemesterMainName'))
		->join(array('def'=>'tbl_definationms'),'def.IdDefinition=f.ProcessCode',array('ProcessName'=>'def.BahasaIndonesia'))
		->join(array('def1'=>'tbl_definationms'),'def1.IdDefinition=f.Activity_Code',array('ActivityName'=>'def1.BahasaIndonesia'))
		->where('f.IdTASyaratBerkas= ?', $id);
		 
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	
}

?>