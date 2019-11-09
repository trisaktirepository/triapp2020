<?php
class Servqual_Model_DbTable_Servqual extends Zend_Db_Table { //Model Class for Users Details
	protected $_name = 'tbl_servqual';
	protected $_primary ='IdServqual';
	
	
	public function updateData($lobjFormData,$idserqual){
			$db = Zend_Db_Table::getDefaultAdapter();
			$lobjFormData['update_date']=date('d-m-yyy');
			$where = 'IdServqual='.$idserqual;
			$db->update($this->_name,$lobjFormData,$where);
	}
	
	public function insertData($lobjFormData){
		$db = Zend_Db_Table::getDefaultAdapter();
		$lobjFormData['active']='1';
		$lobjFormData['active_date']=date('d-m-yyy');
		$db->insert($this->_name,$lobjFormData);
		$lastInsertId = $this->getAdapter()->lastInsertId();
		return $lastInsertId;
	}
	
	public function deleteData($idserqual){
		$db = Zend_Db_Table::getDefaultAdapter();
		$where = 'IdServqual='.$idserqual;
		$db->delete($this->_name,$where);
	}
	
	public function getData($idserqual=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
				->from(array('sq'=>$this->_name))
				->join(array('def'=>'tbl_definationms'),'sq.survey_type=def.IdDefinition',array('surveytype'=>'def.BahasaIndonesia'))
				->where('sq.active="1"');
		if ($idserqual!=null) {
			$select->where('IdServqual=?',$idserqual);
			$row=$db->fetchRow($select);
		}
		else
			$row=$db->fetchAll($select);
		return $row;
		
	}
	public function getRows($idserqual=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('sq'=>$this->_name))
		//->join(array('def'=>'tbl_definationms'),'sq.survey_type=def.IdDefinition',array('surveytype'=>'def.BahasaIndonesia'))
		->where('sq.active="1"');
		if ($idserqual!=null) {
			$select->where('IdServqual=?',$idserqual);
		}
	
		$row=$db->fetchRow($select);
		return $row;
	
	}
     
}