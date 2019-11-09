<?php

class App_Model_Skpm_DbTable_ScoreMain extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'tbl_SKPM_Skore_main';
	protected $_primary = "idScoreMain";
		
	public function getData($id=0){
		
		$session = new Zend_Session_Namespace('sis');
		$id = (int)$id;
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array('a'=>$this->_name))
		->join(array('pr'=>'tbl_program'),'pr.IdProgram=a.IdProgram',array('ProgramName'=>'pr.ArabicName'))
		->join(array('sm'=>'tbl_semestermaster'),'a.IdSemesterMain=sm.IdSemesterMaster',array('SemesterName'=>'sm.SemesterMainName'))
		->join(array('br'=>'tbl_branchofficevenue'),'a.IdBranch=br.IdBranch',array('BranchName'=>'BranchName'));
		if($session->IdRole == 311 || $session->IdRole == 298 || $session->IdRole==851){
			$select->where("pr.IdCollege =?",$session->idCollege);
		}
		if($session->IdRole == 470 || $session->IdRole == 480 ){ //FACULTY DEAN atau FACULTY ADMIN nampak faculty dia sahaja
			$select->where("pr.IdProgram='".$session->idDepartment."'");
		}
		 
		if($id!=0){
			$select->where($this->_primary .' = '. $id);
			$row = $lobjDbAdpt->fetchRow($select);
		}else{
			$row = $lobjDbAdpt->fetchAll($select);
		}
		 
		return $row;
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
	
	public function getScore($post) {
		
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array('a'=>$this->_name))
		->join(array('b'=>'tbl_SKPM_Skore_main'),'a.IdScoreMain=b.IdScoreMain')
		->where('b.IdProgram=?',$post['IdProgram'])
		->where('b.IdBranch=?',$post['IdBranch']);
		
		
		if (isset($post['level']) && $post['level']!='') $select->where('a.level=?',$post['level']);
		if (isset($post['field']) && $post['field']!='') $select->where('a.field=?',$post['field']);
		if (isset($post['role']) && $post['role']!='') $select->where('a.role=?',$post['role']);
		if (isset($post['achievment']) && $post['achievment']!='') $select->where('a.achievment=?',$post['achievment']);
		
		$row=$lobjDbAdpt->fetchRow($select);
		return $row;
	
	}
		
	 
}

