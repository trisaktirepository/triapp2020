<?php 
class Finalassignment_Model_DbTable_Process extends Zend_Db_Table_Abstract { //Model Class for Users Details

	protected $_name = 'tbl_TA_Approval';
	protected $_primary='IdTAApproval';

	public function init() {
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}
	
	public function addData($postData){
		$data = array(
				'IdTAApplication' => $postData['IdTAApplication'],
				'IdProcess' => $postData['IdProcess'],
				'remark' => $postData['remark'],
				'Sequence' => $postData['Sequence'],
				'dt_entry' => date('Y-m-d H:i:s'),
				'Id_User' =>  $postData['id_user']
		);
			
		return $this->insert($data);
	}
	
	public function updateData($postData, $id){
	
		$data = array(
				'remark' => $postData['remark'],
				'dt_update' => date('Y-m-d H:i:s'),
				'Id_User' =>  $postData['id_user']
		);
		$this->update($data, $this->_primary .' = '. (int) $id);
	}
	
	public function approveData($postData, $id){
	
		$data = array(
				'Approved_by' => $postData['Approved_by'],
				'dt_Approved' => $postData['dtApproved'],
				'Id_User' =>  $postData['id_user']
		);
		$this->update($data, $this->_primary .' = '. (int) $id);
	}
	 
	 
	public function deleteData($id){
		$this->delete($this->_primary . " = " . (int)$id);
	}
	
	public function getData($id){
			 
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->where('p.IdTAApproval = ?', $id);
		$larrResult = $lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	
	public function getCurrentProcess($idapp,$IdStudentRegistration){
			 
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->where('p.IdTAApplication = ?', $idapp)
		->where('p.IdProcess = 0')
		->where('p.IdStudentRegistration = ?', $IdStudentRegistration);
		$larrResult = $lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	
	
	
}

?>