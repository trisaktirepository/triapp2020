<?php 
class Finalassignment_Model_DbTable_SubjectPrerequisite extends Zend_Db_Table_Abstract { //Model Class for Users Details

	protected $_name = 'tbl_TA_NILAISYARAT';
	protected $_primary='IdTANilaiSyarat';

	public function init() {
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}
	
	public function addData($postData){
		$data = array(
				//'IdStudentRegistration' => $postData['IdStudentRegistration'],
				'IdSubject' => $postData['IdSubject'],
				'IdTA'=> $postData['IdTA'],
				'Grade_name'=> $postData['grade_name'],
				//'Grade_min'=> $postData['grade_min'],
				'dt_entry' => date('Y-m-d H:i:s'),
				'Id_User' =>  $postData['Id_User']
		);
			
		return $this->insert($data);
	}
	
	public function updateData($postData, $id){
	
		$data = array(
				//'IdFlowMain' => $postData['IdFlowMain'],
				'IdSubject' => $postData['IdSubject'],
				'Grade_name'=> $postData['grade_name'],
				'dt_update' => date('Y-m-d H:i:s'),
				'Id_User' =>  $postData['Id_User']
		);
		$this->update($data, $this->_primary .' = '. (int) $id);
	}
	
	public function updateBySubject($postData, $idsubject,$idproposal){
	
		$data = array(
				 
				'Grade_name'=> $postData['grade_name'],
				'dt_update' => date('Y-m-d H:i:s'),
				'Id_User' =>  $postData['Id_User']
		);
		$this->update($data, 'IdSubject='.$idsubject.' and IdTA='.$idproposal);
	}
	
	 
	 
	public function deleteData($id){
		$this->delete($this->_primary . " = " . (int)$id);
	}
	public function deleteDataAll($id){
		$this->delete("IdTA = " . (int)$id);
	}
	
	public function deleteDataSubject($id,$idsubject){
		
		$this->delete("IdSubject=".$idsubject." and IdTA = " . (int)$id);
	}
	public function getData($id){
			 
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
		->where('p.IdTA = ?', $id);
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	
 
	
	public function getAllSubjectPrerequisite($id ){
	
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("p"=>$this->_name))
 		->join(array('sm'=>'tbl_subjectmaster'), 'sm.IdSubject=p.IdSubject')
		->where('p.IdTA= ?', $id); 
		
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	
	 
	 
	
}

?>