<?php 
class Finalassignment_Model_DbTable_Application extends Zend_Db_Table_Abstract { //Model Class for Users Details

	protected $_name = 'tbl_TA_Application';
	protected $_primary='IdTAApplication';

	public function init() {
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}
	
	public function addData($postData){
		$data = array(
				'IdStudentRegistration' => $postData['IdStudentRegistration'],
				'IdTA' => $postData['IdTA'],
				'STS_ACC' => $postData['IdStatusApproval'],
				'TGL_START'=> $postData['dtstart'],
				'TGL_selesai'=> $postData['dtstop'],
				//'StaffAs' => $postData['StaffAs'],
				'remark' => $postData['remark'],
				'IdSemester_start' => $postData['IdSemStart'],
				'IdSemester_stop' => $postData['IdSemStop'],
				'dt_entry' => date('Y-m-d H:i:s'),
				'Id_User' =>  $postData['Id_User']
		);
			
		return $this->insert($data);
	}
	
	public function updateData($postData, $id){
	
		$data = array(
				'STS_ACC' => $postData['IdStatusApproval'],
				'TGL_START'=> $postData['dtstart'],
				'TGL_selesai'=> $postData['dtstop'],
				'current_approval' => $postData['IdProcess'],
				'ipk' => $postData['ipk'],
				'sks' => $postData['sks'],
				'remark' => $postData['remark'],
				'IdSemester_start' => $postData['IdSemStart'],
				'IdSemester_stop' => $postData['IdSemStop'],
				'dt_entry' => date('Y-m-d H:i:sa'),
				'Id_User' =>  $postData['id_user']
		);
		$this->update($data, $this->_primary .' = '. (int) $id);
	}
	
	public function updatefile($postData, $id){
		$this->update($postData, $this->_primary .' = '. (int) $id);
	}
	
	public function finish($id){
		$dbSem=new GeneralSetup_Model_DbTable_Semestermaster();
		$semester=$dbSem->getSemesterByDate(date('Y-m-d H:i:sa'));
		$idsemester=$semester['IdSemesterMaster'];
		$this->update(array('IdSemester_stop'=>$idsemester,'TGL_selesai'=>date('Y-m-d H:i:sa')), $this->_primary .' = '. (int) $id);
	}
	
	public function rejectData($by,$id){
	
		$data = array(
				'current_proses' => $postData['IdProcess'],
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
		->where('p.IdTAApplication = ?', $id);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	
	public function getApprovedApplicationByStudent($id){
	
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->join(array('pro'=>'tbl_TA_proposal'),'p.IdTA = pro.IdTA')
		->where('p.IdStudentRegistration = ?', $id)
		->where('p.STS_ACC = "1"');
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	
	public function getApplicationByStudent($idregistration,$idta=null){
	
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->join(array('pro'=>'tbl_TA_proposal'),'p.IdTA = pro.IdTA')
		->where('p.IdStudentRegistration = ?', $idregistration);
		if ($idta!=null) $lstrSelect->where('p.IdTA=?',$idta);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	public function getApplication($idta){
	
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->join(array('pro'=>'tbl_TA_proposal'),'p.IdTA = pro.IdTA'); 
		if ($idta!=null) $lstrSelect->where('p.IdTAApplication=?',$idta);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	public function getListofApplicant($idta,$idstudent){
	
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name)) 
		->join(array('sr'=>'tbl_studentregistration'),'sr.IdStudentRegistration=p.IdStudentRegistration','registrationId')
		->join(array('sp'=>'student_profile'),'sr.IdApplication=sp.appl_id',array('studentname'=>'CONCAT(appl_fname," ",appl_mname," ",appl_lname)'))
		->where('p.IdStudentRegistration!=?',$idstudent);
		if ($idta!=null) $lstrSelect->where('p.IdTAApplication=?',$idta);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	public function getCountApplicant($idta){
	
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name),array('count'=>'COUNT(*)'))
		->join(array('pro'=>'tbl_TA_proposal'),'p.IdTA = pro.IdTA')
		->where('p.IdTA=?',$idta);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult['count'];
	}
	public function getApplicationByOwner($id){
	
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->join(array('pro'=>'tbl_TA_proposal'),'p.IdTA = pro.IdTA')
		->where('pro.IdPengaju = ?', $id);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	
	 
}

?>