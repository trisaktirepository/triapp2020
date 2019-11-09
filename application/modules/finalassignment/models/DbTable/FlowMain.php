<?php 
class Finalassignment_Model_DbTable_FlowMain extends Zend_Db_Table_Abstract { //Model Class for Users Details

	protected $_name = 'tbl_TA_flow_main';
	protected $_primary='IdTAFlowMain';

	public function init() {
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}
	
	public function addData($postData){
		$data = array(
				'IdProgram' => $postData['IdProgram'],
				'IdMajoring' => $postData['IdMajoring'],
				'IdBranch'=> $postData['IdBranch'],
				'IdLandscape'=> $postData['IdLandscape'],
				'ProcessCode'=> $postData['ProcessCode'],
				'Sequence'=> $postData['Sequence'],
				'FlowName'=> $postData['FlowName'],
				'Activity_Code'=> $postData['Activity_Code'],
				'SubjectGradeMinimum'=> $postData['SubjectGradeMinimum'],
				'IdSubject'=> $postData['IdSubject'],
				'active'=> $postData['active'],
				'Remark'=> $postData['remark'],
				'dt_entry' => date('Y-m-d H:i:s'),
				'Id_User' =>  $postData['Id_User']
		);
			
		return $this->insert($data);
	}
	
	public function updateData($postData, $id){
	
		$data = array(
				'IdProgram' => $postData['IdProgram'],
				'IdMajoring' => $postData['IdMajoring'],
				'IdBranch'=> $postData['IdBranch'],
				'IdLandscape'=> $postData['IdLandscape'],
				'ProcessCode'=> $postData['ProcessCode'],
				'Sequence'=> $postData['Sequence'],
				'FlowName'=> $postData['FlowName'],
				'Activity_Code'=> $postData['Activity_Code'],
				'SubjectGradeMinimum'=> $postData['SubjectGradeMinimum'],
				'Remark'=> $postData['remark'],
				'dt_entry' => date('Y-m-d H:mm:s'),
				'Id_User' =>  $postData['Id_User']
		);
		echo var_dump($data);exit;
		$this->update($data, $this->_primary .' = '. (int) $id);
	}
	public function rejectData($by,$id){
	
		$data = array(
				'Active' => '0',
				'rejected_by'=>$by,
				'dt_update' => date('Y-m-d H:i:sa')
					
					
		);
			
		$this->update($data, $this->_primary .' = '. (int) $id);
	}
	 
	public function deleteData($id){
		$this->delete($this->_primary . " = " . (int)$id);
	}
	
	public function getData($id){
			 
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->where('p.IdTAFlowMain = ?', $id);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	public function getNextProcessMain($idmain){
	
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->where('p.IdTAFlowMain = ?', $idmain);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		//echo var_dump($larrResult);
		$sequence=$larrResult['Sequence'];
		if ($sequence!='' | $sequence!=null) {
			$lstrSelect = $this->lobjDbAdpt->select()
			->from(array("p"=>$this->_name))
			->where('p.Sequence > ?', $sequence)
			->where('p.IdProgram = ?', $larrResult['IdProgram'])
			->where('p.IdMajoring = ?', $larrResult['IdMajoring'])
			->where('p.IdBranch = ?', $larrResult['IdBranch'])
			->where('p.IdLandscape = ?', $larrResult['IdLandscape'])
			->order('p.Sequence ASC');
		
			$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
			//echo var_dump($larrResult);exit;
			return $larrResult;
		} else return array(); 
	}
	
	public function getAllFlowMain(){
		$session = new Zend_Session_Namespace('sis');
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->join(array('pm'=>'tbl_program'),'p.IdProgram=pm.IdProgram',array('Program'=>'ArabicName'))
		->joinLeft(array('m'=>'tbl_programmajoring'), 'p.IdMajoring=m.IdProgramMajoring',array('Major'=>'BahasaDescription'))
		->joinLeft(array('b'=>'tbl_branchofficevenue'),'p.IdBranch=b.IdBranch',array('Branch'=>'BranchName'))
		->joinLeft(array('ls'=>'tbl_landscape'),'p.IdLandscape=ls.IdLandscape')
		->join(array('def'=>'tbl_definationms'),'def.IdDefinition=p.ProcessCode',array('ProcessName'=>'def.BahasaIndonesia'))
		->join(array('def1'=>'tbl_definationms'),'def1.IdDefinition=p.Activity_Code',array('ActivityName'=>'def1.BahasaIndonesia'))
		->join(array('sm'=>"tbl_subjectmaster"),'p.IdSubject=sm.IdSubject',array('Subject'=>'CONCAT(sm.shortname,"-",sm.BahasaIndonesia)'));

		if ($session->IdRole == 605 || $session->IdRole == 311 || $session->IdRole == 298 || $session->IdRole == 579 || $session->IdRole == 851){ //FACULTY DEAN atau FACULTY ADMIN nampak faculty dia sahaja
			$lstrSelect->where("pm.IdCollege='".$session->idCollege."'");
		}
		if ($session->IdRole == 470 || $session->IdRole == 480){ //FACULTY DEAN atau FACULTY ADMIN nampak faculty dia sahaja
			$lstrSelect->where("pm.IdProgram='".$session->idDepartment."'");
		}
		
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		//echo var_dump($larrResult);exit;
		return $larrResult;
	}
	
	public function getFlowName($id){
	
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("f"=>$this->_name))
		->join(array('p'=>'tbl_program'),'f.IdProgram=p.IdProgram')
		->joinLeft(array('m'=>'tbl_programmajoring'), 'f.IdMajoring=m.IdProgramMajoring')
		->joinLeft(array('b'=>'tbl_branchofficevenue'),'f.IdBranch=b.IdBranch')
		->join(array('ls'=>'tbl_landscape'),'f.IdLandscape=ls.IdLandscape')
		->join(array('sm'=>"tbl_subjectmaster"),'f.IdSubject=sm.IdSubject',array('Subject'=>'CONCAT(sm.shortname,"-",sm.BahasaIndonesia)'))
		->join(array('def'=>'tbl_definationms'),'def.IdDefinition=f.ProcessCode',array('Stage'=>'def.BahasaIndonesia','StageCode'=>'def.DefinitionCode'))
		->join(array('def1'=>'tbl_definationms'),'def1.IDdefinition=f.Activity_Code',array('ActivityName'=>'def1.BahasaIndonesia','ActivityCode'=>'def1.DefinitionCode'))
		->where('f.IdTAFlowMain= ?', $id);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	
	public function getFlowNameByProgram($program,$major=null,$branch=null,$processcode,$idlandscape){
	
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("f"=>$this->_name))
		->join(array('p'=>'tbl_program'),'f.IdProgram=p.IdProgram')
		->joinLeft(array('m'=>'tbl_programmajoring'), 'f.IdMajoring=m.IdProgramMajoring')
		->joinLeft(array('b'=>'tbl_branchofficevenue'),'f.IdBranch=b.IdBranch')
		->join(array('sm'=>"tbl_subjectmaster"),'f.IdSubject=sm.IdSubject',array('Subject'=>'CONCAT(sm.shortname,"-",sm.BahasaIndonesia)'))
		->join(array('def'=>'tbl_definationms'),'def.IdDefinition=f.ProcessCode',array('ProcessName'=>'def.BahasaIndonesia'))
		->join(array('def1'=>'tbl_definationms'),'def1.IdDefinition=f.Activity_Code',array('ActivityName'=>'def1.BahasaIndonesia'))
		->join(array('ls'=>'tbl_landscape'),'f.IdLandscape=ls.IdLandscape')
		->where('f.IdProgram= ?', $program)
		->where('f.IdLandscape=?',$idlandscape)
		->where('def.DefinitionCode= ?', $processcode);
		if ($major!=null) $lstrSelect->where('f.IdMajoring=?',$major);
		if ($branch!=null) $lstrSelect->where('f.IdBranch=?',$branch);
		//echo $lstrSelect;exit;
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		if (!$larrResult) {
			$lstrSelect = $this->lobjDbAdpt->select()
			->from(array("f"=>$this->_name))
			->join(array('p'=>'tbl_program'),'f.IdProgram=p.IdProgram')
			->joinLeft(array('m'=>'tbl_programmajoring'), 'f.IdMajoring=m.IdProgramMajoring')
			->joinLeft(array('b'=>'tbl_branchofficevenue'),'f.IdBranch=b.IdBranch')
			->join(array('sm'=>"tbl_subjectmaster"),'f.IdSubject=sm.IdSubject',array('Subject'=>'CONCAT(sm.shortname,"-",sm.BahasaIndonesia)'))
			->join(array('def'=>'tbl_definationms'),'def.IdDefinition=f.ProcessCode',array('ProcessName'=>'def.BahasaIndonesia'))
			->join(array('def1'=>'tbl_definationms'),'def1.IdDefinition=f.Activity_Code',array('ActivityName'=>'def1.BahasaIndonesia'))
			->join(array('ls'=>'tbl_landscape'),'f.IdLandscape=ls.IdLandscape')
			->where('f.IdProgram= ?', $program)
			->where('f.IdLandscape=?',$idlandscape)
			->where('def.DefinitionCode= ?', $processcode);
			if ($major!=null) $lstrSelect->where('f.IdMajoring=?',$major);
			 
			$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		 if (!$larrResult) {
		 	$lstrSelect = $this->lobjDbAdpt->select()
		 	->from(array("f"=>$this->_name))
		 	->join(array('p'=>'tbl_program'),'f.IdProgram=p.IdProgram')
		 	->joinLeft(array('m'=>'tbl_programmajoring'), 'f.IdMajoring=m.IdProgramMajoring')
		 	->joinLeft(array('b'=>'tbl_branchofficevenue'),'f.IdBranch=b.IdBranch')
		 	->join(array('sm'=>"tbl_subjectmaster"),'f.IdSubject=sm.IdSubject',array('Subject'=>'CONCAT(sm.shortname,"-",sm.BahasaIndonesia)'))
		 	->join(array('def'=>'tbl_definationms'),'def.IdDefinition=f.ProcessCode',array('ProcessName'=>'def.BahasaIndonesia'))
		 	->join(array('def1'=>'tbl_definationms'),'def1.IdDefinition=f.Activity_Code',array('ActivityName'=>'def1.BahasaIndonesia'))
		 	->join(array('ls'=>'tbl_landscape'),'f.IdLandscape=ls.IdLandscape')
		 	->where('f.IdProgram= ?', $program)
		 	->where('f.IdLandscape=?',$idlandscape)
		 	->where('def.DefinitionCode= ?', $processcode);
		 	 
		 	if ($branch!=null) $lstrSelect->where('f.IdBranch=?',$branch);
		 	//echo $lstrSelect;exit;
		 	$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		 	if (!$larrResult) {
		 		$lstrSelect = $this->lobjDbAdpt->select()
		 		->from(array("f"=>$this->_name))
		 		->join(array('p'=>'tbl_program'),'f.IdProgram=p.IdProgram')
		 		->joinLeft(array('m'=>'tbl_programmajoring'), 'f.IdMajoring=m.IdProgramMajoring')
		 		->joinLeft(array('b'=>'tbl_branchofficevenue'),'f.IdBranch=b.IdBranch')
		 		->join(array('sm'=>"tbl_subjectmaster"),'f.IdSubject=sm.IdSubject',array('Subject'=>'CONCAT(sm.shortname,"-",sm.BahasaIndonesia)'))
		 		->join(array('def'=>'tbl_definationms'),'def.IdDefinition=f.ProcessCode',array('ProcessName'=>'def.BahasaIndonesia'))
		 		->join(array('def1'=>'tbl_definationms'),'def1.IdDefinition=f.Activity_Code',array('ActivityName'=>'def1.BahasaIndonesia'))
		 		->join(array('ls'=>'tbl_landscape'),'f.IdLandscape=ls.IdLandscape')
		 		->where('f.IdProgram= ?', $program)
		 		->where('f.IdLandscape=?',$idlandscape)
		 		->where('def.DefinitionCode= ?', $processcode);
		 		 
		 		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		 	}
		 }
		} 
		return $larrResult;
	}
	/*
	public function getNextProcess($currentsequence,$program,$major=null,$branch=null,$processcode){
	
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("f"=>$this->_name))
		->join(array('p'=>'tbl_pogram'),'f.IdProgram=p.IdProgram')
		->joinLeft(array('m'=>'tbl_programMajoring'), 'f.IdMajoring=m.IdProgramMajoring')
		->joinLeft(array('b'=>'tbl_branchofficevenue','f.IdBranch=b.IdBranch'))
		->where('f.IdProgram= ?', $program)
		->where('f.ProcessCode= ?', $processcode)
		->where('f.Sequence > ?', $currentsequence)
		;
		if ($major!=null) $lstrSelect->where('p.IdMajoring=?',$major);
		if ($branch!=null) $lstrSelect->where('p.IdBranch=?',$branch);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	*/
	public function fnGetProcessName() {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array('a' => 'tbl_definationms'),array('key'=>'idDefinition','value'=>'BahasaIndonesia'))
		->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
		->where('b.defTypeDesc = "Process Category"')
		->where('a.Status = 1')
		->where('b.Active = 1')
		->order("b.defTypeDesc");
		$result = $lobjDbAdpt->fetchAll($select);
		//echo $select;exit;
		return $result;
	}
	
	public function fnGetActivityName() {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array('a' => 'tbl_definationms'),array('key'=>'idDefinition','value'=>'BahasaIndonesia'))
		->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
		->where('b.defTypeDesc = "Activity Type"')
		->where('a.Status = 1')
		->where('b.Active = 1')
		->order("b.defTypeDesc");
		$result = $lobjDbAdpt->fetchAll($select);
		//echo $select;exit;
		return $result;
	}
	
	
}

?>