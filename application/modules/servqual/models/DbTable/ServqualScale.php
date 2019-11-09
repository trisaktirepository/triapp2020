<?php
class Servqual_Model_DbTable_ServqualScale extends Zend_Db_Table { //Model Class for Users Details
	
	protected $_name = 'tbl_servqual_scale';
	protected $_primary ='IdScaleDetail';
	
	
	public function updateData($lobjFormData,$idserqual){
			$db = Zend_Db_Table::getDefaultAdapter();
			$where = 'IdScaleDetail='.$idserqual;
			$db->update($this->_name,$lobjFormData,$where);
	}
	
	public function insertData($lobjFormData){
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->insert($this->_name,$lobjFormData);
	}
	
	public function deleteData($idserqual){
		$db = Zend_Db_Table::getDefaultAdapter();
		$where = 'IdScaleDetail='.$idserqual;
		$db->delete($this->_name,$where);
	}
	
	public function getData($idserqual=0){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
				->from(array('sq'=>$this->_name))
				->join(array('def'=>'tbl_definationms'),'sq.idScale=def.IdDefinition',array('surveytype'=>'def.BahasaIndonesia'))
				->where('sq.Active="1"');
				
		//if ($idserqual!=null) {
			$select->where('IdScaleDetail=?',$idserqual);
		//}
		
		$row=$db->fetchAll($select);
		return $row;
		
	}
	public function getDataPerScale($idscale=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('sq'=>$this->_name))
		//->join(array('def'=>'tbl_definationms'),'sq.idScale=def.IdDefinition',array('scalename'=>'def.BahasaIndonesia','scalerange'=>'def.Description'))
		->where('sq.Active="1"');
	
		if ($idscale!=null) {
			$select->where('IdScale=?',$idscale);
		}
	
		$row=$db->fetchAll($select);
		return $row;
	
	}
     
}