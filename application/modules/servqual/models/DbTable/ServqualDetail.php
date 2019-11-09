<?php
class Servqual_Model_DbTable_ServqualDetail extends Zend_Db_Table { //Model Class for Users Details
	protected $_name = 'tbl_servqual_detail';
	protected $_primary ='IdServqualDetail';
	
	
	public function updateData($lobjFormData,$idserqual){
			$db = Zend_Db_Table::getDefaultAdapter();
			$where = 'IdServqualDetail='.$idserqual;
			$db->update($this->_name,$lobjFormData,$where);
	}
	
	public function insertData($lobjFormData){
		$db = Zend_Db_Table::getDefaultAdapter();
		$lobjFormData['active']='1';
		$db->insert($this->_name,$lobjFormData);
	}
	
	public function deleteData($idserqual){
		$db = Zend_Db_Table::getDefaultAdapter();
		$where = 'IdServqualDetail='.$idserqual;
		$db->delete($this->_name,$where);
	}
	
	public function getData($idserqual=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
				->from(array('sq'=>$this->_name))
				->join(array('def'=>'tbl_definationms'),'sq.question_id=def.IdDefinition',array('Question'=>'def.BahasaDesc'))
				->where('sq.active="1"');
		if ($idserqual!=null) {
			$select->where('IdServqualDetail=?',$idserqual);
		}
		
		$row=$db->fetchAll($select);
		return $row;
		
	}
	public function getDataDetail($idserqual,$iddimension){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('sq'=>$this->_name))
		//->join(array('def'=>'tbl_definationms'),'sq.question_id=def.IdDefinition',array('Question'=>'def.BahasaDesc'))
		->join(array('qs'=>'tbl_servqual_questions'),'qs.IdServqualQuestion=sq.question_id')
		->where('sq.active="1"')
		->where('sq.Category=?',$iddimension)
		->where('IdServqual=?',$idserqual)
		->order('Question_seq ASC');
		$row=$db->fetchAll($select);
		return $row;
	
	}
	public function getRows($idserqual=null){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('sq'=>$this->_name))
		//->join(array('def'=>'tbl_definationms'),'sq.question_id=def.IdDefinition',array('Question'=>'def.BahasaDesc'))
		->where('sq.active="1"');
		if ($idserqual!=null) {
			$select->where('IdServqual=?',$idserqual);
		}
	
		$row=$db->fetchAll($select);
		return $row;
	
	}
	public function getDetailQuetioner($idserqual){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array('sq'=>$this->_name))
		->join(array('sd'=>'tbl_servqual_dimension'),'sq.IdServqual=sd.IdServqual and sq.Category=sd.IdDimension')
		->join(array('qs'=>'tbl_servqual_questions'),'qs.IdServqualQuestion=sq.question_id',array('idquestion'=>'IdServqualQuestion','Question'))
		
		->where('sq.active="1"')
		
		->where('sq.IdServqual=?',$idserqual)
		->order('sd.Order ASC')
		->order('Question_seq ASC');
		$row=$db->fetchAll($select);
		return $row;
	
	}
     
}