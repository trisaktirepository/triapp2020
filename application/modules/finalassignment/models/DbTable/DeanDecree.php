<?php 
class Finalassignment_Model_DbTable_DeanDecree extends Zend_Db_Table_Abstract { //Model Class for Users Details

	protected $_name = 'tbl_TA_DeanDecree';
	protected $_primary='Id';

	public function init() {
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}
	
	public function addData($postData){
		//cek if any
		$id=$this->isIn($postData['decree'],$postData['IdSemesterMain']);
		if (!$id)
			return $this->insert($postData);
	}
	
	public function updateData($postData, $id){
	
		$data = array(
				'decree' => $postData['dialog_nomor'],
				'dt_effective'=> $postData['dialog_decree_date'],
				'IdSemesterMain'=>$postData['IdSemester'],
				'IdProgram'=>$postData['IdProgram'],
				'active'=> '1' 
		);
		$this->update($data, $this->_primary .' = '. (int) $id);
	}
	public function rejectData($by,$id){
	
		$data = array(
				'active' => '0',
					
					
		);
			
		$this->update($data, $this->_primary .' = '. (int) $id);
	}
	 
	public function deleteData($id){
		$this->delete($this->_primary . " = " . (int)$id);
	}
	
	public function getData($iddeandecree=null){
			 
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->join(array('pr'=>'tbl_program'),'p.IdProgram=pr.IdProgram',array('ProgramName'=>'CONCAT(ProgramCode,"-",ArabicName)'))
		->join(array('smt'=>'tbl_semestermaster'),'p.IdSemesterMain=smt.IdSemesterMaster',array('SemesterName'=>'SemesterMainName'));
		
		if ($iddeandecree!=null) {
			$lstrSelect->where('p.Id = ?', $iddeandecree);
			$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		} else $larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		
		return $larrResult;
		
	}
	public function isIn($decree,$idsem=null){
	
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->where('p.decree=?',$decree);
		if ($idsem!=null) $lstrSelect->where('IdSemesterMain=?',$idsem);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		 return $larrResult; 
	}
	
	 
}

?>