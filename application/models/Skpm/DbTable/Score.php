<?php

class App_Model_Skpm_DbTable_Score extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'tbl_SKPM_Skore';
	protected $_primary = "idScore";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary .' = '. $id);
		}else{
			$row = $this->fetchAll();
		}
		 
		return $row->toArray();
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
		
	public function getDataByIdMain($idmain) {
	
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array('a'=>$this->_name))
		->join(array('b'=>'tbl_SKPM_Skore_main'),'a.IdScoreMain=b.IdScoreMain')
		->joinLeft(array('def'=>'tbl_definationms'),'a.level=def.idDefinition',array('levelname'=>'def.BahasaIndonesia','leveleng'=>'def.DefinitionDesc'))
		->joinLeft(array('def1'=>'tbl_definationms'),'a.field=def1.idDefinition',array('fieldid'=>'a.field','field'=>'def1.BahasaIndonesia','fieldeng'=>'def1.DefinitionDesc'))
		->joinLeft(array('def2'=>'tbl_definationms'),'a.role=def2.idDefinition',array('roleid'=>'a.role','role'=>'def2.BahasaIndonesia','roleeng'=>'def2.DefinitionDesc'))
		->joinLeft(array('def3'=>'tbl_definationms'),'a.achievment=def3.idDefinition',array('achievmentid'=>'a.achievment','achievmentname'=>'def3.BahasaIndonesia','achievmenteng'=>'def3.DefinitionDesc'))
		 
		->where('a.IdScoreMain=?',$idmain);
		$row=$lobjDbAdpt->fetchAll($select);
		return $row;
	
	}
	 
}

