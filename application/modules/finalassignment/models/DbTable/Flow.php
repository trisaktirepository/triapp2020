<?php 
class Finalassignment_Model_DbTable_Flow extends Zend_Db_Table_Abstract { //Model Class for Users Details

	protected $_name = 'tbl_TA_FLOW';
	protected $_primary='IdTAFlow';

	public function init() {
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}
	
	public function addData($postData){
		$data = array(
				'IdTAFlowMain' => $postData['idFlowMain'],
				'IdStaffApproving' => $postData['IdStaffApproving'],
				'Sequence'=> $postData['Sequence'],
				'StaffAs'=> $postData['StaffAs'],
				'Auto'=> $postData['Auto'],
				'approval_type'=> $postData['approval_type'],
				'ProcessName'=> $postData['ProcessName'],
				'n_of_span'=> $postData['nspan'],
				'url'=> $postData['url'],
				'report_profile'=> $postData['report_profile'],
				'dt_entry' => date('Y-m-d H:i:s'),
				'Id_User' =>  $postData['Id_User']
		);
			
		return $this->insert($data);
	}
	
	public function updateData($postData, $id){
	
		$data = array(
				//'IdFlowMain' => $postData['IdFlowMain'],
				'IdStaffApproving' => $postData['IdStaffApproving'],
				'Sequence'=> $postData['Sequence'],
				'ProcessName'=> $postData['ProcessName'],
				'Auto'=> $postData['Auto'],
				'approval_type'=> $postData['approval_type'],
				'StaffAs'=> $postData['StaffAs'],
				'url'=> $postData['url'],
				'n_of_span'=> $postData['nspan'],
				'report_profile'=> $postData['report_profile'],
				'dt_update' => date('Y-m-d H:i:s'),
				'Id_User' =>  $postData['Id_User']
		);
		$this->update($data, $this->_primary .' = '. (int) $id);
	}
	public function rejectData($by,$id){
	
		$data = array(
				'Active' => '0',
				'rejected_by'=>$by,
				'dt_update' => date('Y-m-d H:i:s')
					
					
		);
			
		$this->update($data, $this->_primary .' = '. (int) $id);
	}
	 
	public function deleteData($id){
		$this->delete($this->_primary . " = " . (int)$id);
	}
	
	public function getData($id){
			 
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->where('p.IdTAFlow = ?', $id);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	public function getFlowList($idprogram,$idmajor,$idbranch){
	
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name),array('key'=>'IdTAFlow'))
		->join(array('fm'=>'tbl_TA_flow_main'),'p.IdTAFlowMain=fm.IdTAFlowMain',array('value'=>'CONCAT(pm.ArabicName,"-",m.BahasaDescription,"-",BranchName,"-",def1.BahasaIndonesia,"-",def.BahasaIndonesia,"-",p.ProcessName)'))
		->join(array('pm'=>'tbl_program'),'fm.IdProgram=pm.IdProgram',array('Program'=>'ArabicName'))
		->joinLeft(array('m'=>'tbl_programmajoring'), 'fm.IdMajoring=m.IdProgramMajoring',array('Major'=>'BahasaDescription'))
		->joinLeft(array('b'=>'tbl_branchofficevenue'),'fm.IdBranch=b.IdBranch',array('Branch'=>'BranchName'))
		->join(array('def'=>'tbl_definationms'),'def.IdDefinition=fm.ProcessCode',array('ProcessName'=>'def.BahasaIndonesia'))
		->join(array('def1'=>'tbl_definationms'),'def1.IdDefinition=fm.Activity_Code',array('ActivityName'=>'def1.BahasaIndonesia'))
		->where('fm.IdProgram = ?', $idprogram)
		->where('fm.IdMajoring = ?', $idmajor)
		->where('fm.IdBranch = ?', $idbranch);
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	
	public function getFlowListByIdFlow($IdFlow){
	
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name), array('p.*','Process_name'=>'p.ProcessName'))
		->join(array('fm'=>'tbl_TA_flow_main'),'p.IdTAFlowMain=fm.IdTAFlowMain',array('value'=>'CONCAT(pm.ArabicName,"-",m.BahasaDescription,"-",BranchName,"-",def1.BahasaIndonesia,"-",def.BahasaIndonesia,"-",p.ProcessName)'))
		->join(array('pm'=>'tbl_program'),'fm.IdProgram=pm.IdProgram',array('Program'=>'ArabicName'))
		->joinLeft(array('m'=>'tbl_programmajoring'), 'fm.IdMajoring=m.IdProgramMajoring',array('Major'=>'BahasaDescription'))
		->joinLeft(array('b'=>'tbl_branchofficevenue'),'fm.IdBranch=b.IdBranch',array('Branch'=>'BranchName'))
		->join(array('def'=>'tbl_definationms'),'def.IdDefinition=fm.ProcessCode',array('ProcessName'=>'def.BahasaIndonesia'))
		->join(array('def1'=>'tbl_definationms'),'def1.IdDefinition=fm.Activity_Code',array('ActivityName'=>'def1.BahasaIndonesia'))
		->where('p.IdTAFlow= ?', $IdFlow);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	
	public function getFlow($id=0){
			 
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->join(array('def'=>'tbl_definationms'),'p.ProcessName=def.idDefinition',array('As'=>'BahasaIndonesia','Aseng'=>'DefinitionDesc'))
 		->join(array('s'=>'tbl_staffmaster'), 's.IdStaff=p.IdStaffApproving',array('Fullname','FrontSalutation','BackSalutation','StaffId'))
		->where('p.IdTAFlow = ?', $id);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	
	public function getFlowByFlowMain($id){
	
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		//->join(array('def'=>'tbl_definationms'),'p.ProcessName=def.idDefinition',array('As'=>'BahasaIndonesia','Aseng'=>'DefinitionDesc'))
 		->joinLeft(array('s'=>'tbl_staffmaster'), 's.IdStaff=p.IdStaffApproving',array('Fullname','FrontSalutation','BackSalutation','StaffId'))
		->where('p.IdTAFlowMain= ?', $id)
		->order('p.Sequence');
		//echo $lstrSelect;exit;
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	public function getFlowByFlowMainSeq($id,$seq){
	
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->where('p.IdTAFlowMain= ?', $id)
		->where('p.Sequence= ?', $seq)
		->where('p.Active= "1"'); 
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		//echo $lstrSelect;exit;
		return $larrResult;
	}
	
	public function getNextFlow($id,$seq){
	
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->where('p.IdTAFlowMain= ?', $id)
		->where('p.Sequence > ?', $seq)
		->where('p.Active= "1"')
		->order('p.Sequence ASC');
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		//echo $lstrSelect;exit;
		return $larrResult;
	}
	
	public function fnGetApprovalType() {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array('a' => 'tbl_definationms'),array('key'=>'idDefinition','value'=>'BahasaIndonesia'))
		->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
		->where('b.defTypeDesc = "Approval Type"')
		->where('a.Status = 1')
		->where('b.Active = 1')
		->order("b.defTypeDesc");
		$result = $lobjDbAdpt->fetchAll($select);
		//echo $select;exit;
		return $result;
	}
	
	
}

?>