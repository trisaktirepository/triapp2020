<?php
class Servqual_Model_DbTable_ServqualDimension extends Zend_Db_Table { //Model Class for Users Details
	protected $_name = 'tbl_servqual_dimension';
	protected $_primary ='IdServqualDimension';
	
	
	public function updateData($lobjFormData,$idserqual){
			$db = Zend_Db_Table::getDefaultAdapter();
			$where = 'IdServqualDimension='.$idserqual;
			$db->update($this->_name,$lobjFormData,$where);
	}
	
	public function insertData($lobjFormData){
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->insert($this->_name,$lobjFormData);
		$lastInsertId = $this->getAdapter()->lastInsertId();
		return $lastInsertId;
	}
	
	public function deleteData($idserqual){
		$db = Zend_Db_Table::getDefaultAdapter();
		$where = 'IdServqualDimension='.$idserqual;
		$db->delete($this->_name,$where);
	}
	public function deleteDimension($idserqual,$iddemension){
		$db = Zend_Db_Table::getDefaultAdapter();
		$where = 'IdServqual='.$idserqual.' and IdDimension='.$iddemension;
		$db->delete($this->_name,$where);
	}
	public function getData($idserqual=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
				->from(array('sq'=>$this->_name))
				->join(array('def'=>'tbl_definationms'),'sq.IdDimension=def.IdDefinition',array('Dimension'=>'def.BahasaIndonesia'));
				//->where('sq.active="1"');
		if ($idserqual!=null) {
			$select->where('IdServqualDimension=?',$idserqual);
		}
		
		$row=$db->fetchAll($select);
		return $row;
		
	}
	public function getRows($idserqual=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('sq'=>$this->_name))
		->join(array('def'=>'tbl_definationms'),'sq.IdDimension=def.IdDefinition',array('Dimension'=>'def.BahasaIndonesia'));
		//->where('sq.active="1"');
		if ($idserqual!=null) {
			$select->where('IdServqual=?',$idserqual);
		}
	
		$row=$db->fetchAll($select);
		return $row;
	
	}
     
}