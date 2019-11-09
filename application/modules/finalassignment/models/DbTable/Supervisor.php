<?php 
class Finalassignment_Model_DbTable_Supervisor extends Zend_Db_Table_Abstract { //Model Class for Users Details

	protected $_name = 'tbl_TA_Supervisor';
	protected $_primary='IdSupervisor';

	public function init() {
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}
	
	public function addData($postData){
		$data = array(
				'IdTAApplication' => $postData['IdTAApplication'],
				'IdStaff' => $postData['IdStaff'],
				'StaffAs'=> $postData['StaffAs'],
				 'Active'=>'1',
				'dt_entry' => date('Y-m-d H:i:s'),
				'Id_User' =>  $postData['Id_User']
		);
			
		return $this->insert($data);
	}
	
	public function finish($IdTAApplication,$idStaff){
		$where='IdTAApplication='.$IdTAApplication.' and '.'IdStaff='.$idStaff;
		return $this->update(array('finish'=>'1'),$where);
	}
	
	public function isFinish($IdTAApplication,$idStaff){
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->join(array('sv'=>'tbl_TA_Supervision'),'p.IdTAApplication=sv.IdTAApplication and p.IdStaff=sv.IdStaff')
		->where('p.IdTAApplication = ?', $IdTAApplication)
		->where('p.IdStaff=?',$idStaff);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		if ($larrResult) {
			if ($larrResult['finish']=='1') return true; else return false;
		} else return true;
	}
	 
	public function deleteData($id){
		$this->delete($this->_primary . " = " . (int)$id);
	}
	
	public function getData($id){
			 
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		//->join(array('def'=>'tbl_definationms'),'p.StatusAs=def.idDefinition',array('As'=>'BahasaIndonesia','Aseng'=>'DefinitionDesc'))
 		->join(array('s'=>'tbl_staffmaster'), 's.IdStaff=p.IdStaff',array('Fullname','FrontSalutation','BackSalutation','StaffId'))
		->where('p.IdTASupervisor = ?', $id);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	
	public function isNotIn($IdStaff,$IdTAapplication){
	
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->where('p.IdStaff = ?', $IdStaff)
		->where('p.IdTAApplication=?',$IdTAapplication);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		if ($larrResult) return false; else return true;
	}
	public function getSupervisor($idTaApplication,$idstaff){
	
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		//->join(array('def'=>'tbl_definationms'),'p.StatusAs=def.idDefinition',array('As'=>'BahasaIndonesia','Aseng'=>'DefinitionDesc'))
		->join(array('s'=>'tbl_staffmaster'), 's.IdStaff=p.IdStaff',array('Fullname','FrontSalutation','BackSalutation','StaffId'))
		->where('p.IdTAApplication = ?', $idTaApplication)
		->where('p.IdStaff = ?', $idstaff);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	
	public function getAllSupervisor($idta){
	
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
	//	->join(array('app'=>'tbl_TA_Approval'),'p.IdTAApplication=app.IdTAApplication and app.approved_by=p.IdStaff',array('StaffAs'))
		->join(array('s'=>'tbl_staffmaster'), 's.IdStaff=p.IdStaff',array('Fullname','FrontSalutation','BackSalutation','StaffId'))
		->where('p.IdTAApplication= ?', $idta)
		->order('p.StaffAs DESC');
		$larrResult =$this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	public function getSupervisorByOrder($idta,$seq){
	
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->where('p.IdTAApplication= ?', $idta)
		->where('p.Sequence= ?', $seq);
		$larrResult =$this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	
	public function isSupervised($idta){
	
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->where('p.IdTAApplication= ?', $idta)
		->where('p.active= "1"') ;
		$larrResult =$this->lobjDbAdpt->fetchRow($lstrSelect);
		if ($larrResult) return true; else return false;
	}
	
	public function getOpenSupervisionByStaff($idstaff){
	
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->join(array('app'=>'tbl_TA_Application'),'app.IdTAApplication=p.IdTAApplication')
		->join(array("pr"=>'tbl_TA_proposal'),'pr.IdTA=app.IdTA')
		//->join(array('s'=>'tbl_staffmaster'), 's.IdStaff=p.IdStaff',array('Fullname','FrontSalutation','BackSalutation','StaffId'))
		->where('p.IdStaff = ?', $idstaff)
		->where('p.finish!=1');
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	public function getFinishSupervisionByStaff($idstaff){
	
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->join(array('app'=>'tbl_TA_Application'),'app.IdTAApplication=p.IdTAApplication')
		->join(array("pr"=>'tbl_TA_proposal'),'pr.IdTA=app.IdTA')
		//->join(array('s'=>'tbl_staffmaster'), 's.IdStaff=p.IdStaff',array('Fullname','FrontSalutation','BackSalutation','StaffId'))
		->where('p.IdStaff = ?', $idstaff)
		->where('p.finish=1');
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
}

?>