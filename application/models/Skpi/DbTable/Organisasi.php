<?php

class App_Model_Skpi_DbTable_Organisasi extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'tbl_SKPI_Organisasi';
	protected $_primary = "idOrganisasi";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary .' = '. $id);
		}else{
			$row = $this->fetchAll();
		}
		
		if(!$row){
			throw new Exception("There is No Activity");
		}
		
		return $row->toArray();
	}
	public function getDatabyStudent($idstundetregistration){
			$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
			$select = $lobjDbAdpt->select()
			->from(array('a'=>$this->_name))
			->join(array('def'=>'tbl_definationms'),'a.level=def.idDefinition',array('levelid'=>'a.level','level'=>'BahasaIndonesia'))
			->join(array('def1'=>'tbl_definationms'),'a.category=def1.idDefinition',array('categoryid'=>'a.category','category'=>'BahasaIndonesia'))
			->join(array('def2'=>'tbl_definationms'),'a.occupation=def2.idDefinition',array('occupacyid'=>'a.occupation','occupacy'=>'BahasaIndonesia'))
			->where('IdStudentRegistration =? ', $idstundetregistration);
			$row = $lobjDbAdpt->fetchAll($select);
		 
	
		 
	
		return $row;
	}
	public function addData($postData){
		$data = array(
				'title' => $postData['title_org'],
				'title_bahasa' => $postData['title_bahasa_org'],	
				'year_start' => $postData['yearstart'],	
				'year_stop' => $postData['yearstop'],	
				'level' => $postData['level_org'],
				'category' => $postData['category'],
				'occupation' => $postData['occupacy'],
				'level_entry'=>1,
				'dt_entry' => date('Y-m-d H:i:s'),
				'id_user' =>  $postData['id_user'],
				'idStudentRegistration' => $postData['idStudentRegistration']
				);
			
		return $this->insert($data);
	}
	
	public function updateData($postData, $id){
		
		$data = array(
				'title' => $postData['title_org'],
				'title_bahasa' => $postData['title_bahasa_org'],	
				'year_start' => $postData['yearstart'],	
				'year_stop' => $postData['yearstop'],	
				'level' => $postData['level_org'],
				'category' => $postData['category'],
				'occupation' => $postData['occupacy'],
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
	
	public function fnGetCategory() {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array('a' => 'tbl_definationms'),array('key'=>'idDefinition','value'=>'BahasaIndonesia'))
		->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
		->where('b.defTypeDesc = "Category of Organisasi"')
		->where('a.Status = 1')
		->where('b.Active = 1')
		->order("b.defTypeDesc");
		$result = $lobjDbAdpt->fetchAll($select);
		return $result;
	}
	
	public function fnGetyear() {
		 
		$year = date('Y');
		//echo $year;echo date();exit;
		for( $i = 2000; $i < $year+1; $i++){
			$yearlist[]= $i;
		}
		return $yearlist;
	}
	public function fnGetOccupacy() {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array('a' => 'tbl_definationms'),array('key'=>'idDefinition','value'=>'BahasaIndonesia'))
		->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
		->where('b.defTypeDesc = "Occupacy"')
		->where('a.Status = 1')
		->where('b.Active = 1')
		->order("b.defTypeDesc");
		$result = $lobjDbAdpt->fetchAll($select);
		return $result;
	}
}

