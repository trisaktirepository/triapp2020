<?php 
class Finalassignment_Model_DbTable_Supervisormaster extends Zend_Db_Table_Abstract { //Model Class for Users Details

	protected $_name = 'tbl_TA_Supervisormaster';
	protected $_primary='IdTASupervisormaster';

	public function init() {
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}
	
	public function addData($postData){
		$data = array(
				'IdStaff' => $postData['IdStaff'],
				'IdDeanDecree' => $postData['IdDeanDecree'],
				'Status_supervisor'=> $postData['Status_supervisor'],
				'Status_examiner'=> $postData['Status_examiner'],
				'active'=> '1', 
				'dt_entry' => date('Y-m-d H:i:sa'),
				'Id_User' =>  $postData['Id_User']
		);
			
		return $this->insert($data);
	}
	
	public function updateData($postData, $id){
	
		$data = array(
				'IdStaff' => $postData['IdStaff'],
				'IdDeanDecree' => $postData['IdDeanDecree'],
				'Status_supervisor'=> $postData['Status_supervisor'],
				'Status_examiner'=> $postData['Status_examiner'],
				'active'=> $postData['active'], 
				'dt_update' => date('Y-m-d H:i:sa')
		);
		//echo var_dump($data); echo $id;exit;
		$where='IdTASupervisormaster = '.(int) $id;
		//echo $where;exit;
		$this->update($data,$where);
	}
	public function rejectData($by,$id){
	
		$data = array(
				'Active' => '0',
				 
				'dt_update' => date('Y-m-d H:i:sa')
					
					
		);
			
		$this->update($data, $this->_primary .' = '. (int) $id);
	}
	 
	public function deleteData($id){
		$this->delete($this->_primary . " = " . (int)$id);
	}
	
	public function getData($iddeandecree){
			 
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->inner(array('sm'=>'tbl_staffmaster'),'p.IdStaff=sm.IdStaff')
		->where('p.IdDeanDecree = ?', $iddeandecree);
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	public function getSupervisorData($status=null){
		$session = new Zend_Session_Namespace('sis');
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->join(array('d'=>'tbl_TA_DeanDecree'),'d.Id =p.IdDeanDecree')
		->join(array('pm'=>'tbl_program'),'d.IdProgram=pm.IdProgram')
		->join(array('sm'=>'tbl_staffmaster'),'p.IdStaff=sm.IdStaff',array('key'=>'sm.IdStaff','value'=>'CONCAT(FirstName," ",SecondName," ",ThirdName)'))
		->order('sm.FirstName');
		if ($status!=null) $lstrSelect->where('p.Status_supervisor = ?',$status);
		if($session->IdRole == 605 || $session->IdRole == 311 || $session->IdRole == 298 || $session->IdRole == 579 || $session->IdRole == 851){ //FACULTY DEAN atau FACULTY ADMIN nampak faculty dia sahaja
			$lstrSelect->where("pm.IdCollege='".$session->idCollege."'");
		}
		if($session->IdRole == 470 || $session->IdRole == 480){ //FACULTY DEAN atau FACULTY ADMIN nampak faculty dia sahaja
			$lstrSelect->where("pm.IdProgram='".$session->idDepartment."'");
		}
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	
	public function getExaminerData($status){
		$session = new Zend_Session_Namespace('sis');
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->join(array('d'=>'tbl_TA_DeanDecree'),'d.Id =p.IdDeanDecree')
		->join(array('pm'=>'tbl_program'),'d.IdProgram=pm.IdProgram')
		->join(array('def'=>'tbl_definationms'),'p.Status_examiner=def.IdDefinition')
		->join(array('sm'=>'tbl_staffmaster'),'p.IdStaff=sm.IdStaff',array('key'=>'sm.IdStaff','value'=>'CONCAT(FirstName," ",SecondName," ",ThirdName)'))
		->where('def.DefinitionCode= ?',$status)
		->order('sm.FirstName');
		 
		if($session->IdRole == 605 || $session->IdRole == 311 || $session->IdRole == 298 || $session->IdRole == 579 || $session->IdRole == 851){ //FACULTY DEAN atau FACULTY ADMIN nampak faculty dia sahaja
			$lstrSelect->where("pm.IdCollege='".$session->idCollege."'");
		}
		if($session->IdRole == 470 || $session->IdRole == 480){ //FACULTY DEAN atau FACULTY ADMIN nampak faculty dia sahaja
			$lstrSelect->where("pm.IdProgram='".$session->idDepartment."'");
		}
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	 
	
	public function getStaffStatus($iddeandecree,$idStaff){
	
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->where('p.IdDeanDecree = ?', $iddeandecree)
		->where('p.IdStaff = ?', $idStaff);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	 
	public function fnGetSupervisorStatus() {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array('a' => 'tbl_definationms'),array('key'=>'idDefinition','value'=>'BahasaIndonesia'))
		->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
		->where('b.defTypeDesc = "Supervisor Status"')
		->where('a.Status = 1')
		->where('b.Active = 1')
		->order("b.defTypeDesc");
		$result = $lobjDbAdpt->fetchAll($select);
		//echo $select;exit;
		return $result;
	}
	public function fnGetExaminerStatus() {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array('a' => 'tbl_definationms'),array('key'=>'idDefinition','value'=>'BahasaIndonesia'))
		->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
		->where('b.defTypeDesc = "Examiner Status"')
		->where('a.Status = 1')
		->where('b.Active = 1')
		->order("b.defTypeDesc");
		$result = $lobjDbAdpt->fetchAll($select);
		//echo $select;exit;
		return $result;
	}
	
	
}

?>