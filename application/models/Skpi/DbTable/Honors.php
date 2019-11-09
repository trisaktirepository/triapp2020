<?php

class App_Model_Skpi_DbTable_Honors extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'tbl_SKPI_honors';
	protected $_primary = "idHonors";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary .' = '. $id);
		}else{
			$row = $this->fetchAll();
		}
		
	 
		return $row->toArray();
	}
	public function getDatabyStudent($idstundetregistration){
			$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
			$select = $lobjDbAdpt->select()
			->from(array('a'=>$this->_name))
			->join(array('def'=>'tbl_definationms'),'a.level=def.idDefinition',array('level'=>'BahasaIndonesia'))
			->join(array('def1'=>'tbl_definationms'),'a.field=def1.idDefinition',array('fieldid'=>'a.field','field'=>'BahasaIndonesia'))
			->where('IdStudentRegistration =? ', $idstundetregistration);
			$row = $lobjDbAdpt->fetchAll($select);
		 
	 
	
		return $row;
	}
	public function addData($postData){
		$data = array(
				'title' => $postData['title'],
				'title_bahasa' => $postData['title_bahasa'],	
				'date_of_honor' => $postData['date_of_honor'],	
				'given_by' => $postData['given_by'],	
				'level' => $postData['level'],
				'level_entry' => 1,
				'field' => $postData['field'],
				'dt_entry' => date('Y-m-d H:i:s'),
				'idUser' =>  $postData['idUser'],
				'idStudentRegistration' => $postData['idStudentRegistration']
				);
			
		return $this->insert($data);
	}
	
	public function updateData($postData, $id){
		
		$data = array(
				'title' => $postData['title'],
				'title_bahasa' => $postData['title_bahasa'],	
				'date_of_honor' => $postData['date_of_honor'],	
				'given_by' => $postData['given_by'],	
				'level' => $postData['level'],
				'field' => $postData['field'],
				'dt_update' => date('Y-m-d H:i:s')
				 
				 
				);
		 
		$this->update($data, $this->_primary .' = '. (int) $id);
	}
	public function rejectData($by,$id){
	
		$data = array(
				'approved'=>'2',
				'rejected_by'=>$by,
				'dt_update' => date('Y-m-d H:i:s')
					
					
		);
			
		$this->update($data, $this->_primary .' = '. (int) $id);
	}
	public function approvedData($by,$id){
	
		$data = array(
				'approved'=>'1',
				'rejected_by'=>0,
				'dt_update' => date('Y-m-d H:i:s')
					
					
		);
			
		$this->update($data, $this->_primary .' = '. (int) $id);
	}
	public function deleteData($id){
		$this->delete($this->_primary . " = " . (int)$id);
	}
	
	public function fnGetLevelHonors() {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array('a' => 'tbl_definationms'),array('key'=>'idDefinition','value'=>'BahasaIndonesia'))
		->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
		->where('b.defTypeDesc = "Level of Honors"')
		->where('a.Status = 1')
		->where('b.Active = 1')
		->order("b.defTypeDesc");
		$result = $lobjDbAdpt->fetchAll($select);
		//echo $select;exit;
		return $result;
	}
	
	public function fnGetFieldsHonors() {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array('a' => 'tbl_definationms'),array('key'=>'idDefinition','value'=>'BahasaIndonesia'))
		->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
		->where('b.defTypeDesc = "Fields of Honors"')
		->where('a.Status = 1')
		->where('b.Active = 1')
		->order("b.defTypeDesc");
		$result = $lobjDbAdpt->fetchAll($select);
		return $result;
	}
}

