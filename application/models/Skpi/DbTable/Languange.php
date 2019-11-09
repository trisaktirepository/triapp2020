<?php

class App_Model_Skpi_DbTable_Languange extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'tbl_SKPI_Language';
	protected $_primary = "idLanguage";
		
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
			->join(array('def'=>'tbl_definationms'),'a.Language_standart=def.idDefinition',array('Standartid'=>'a.Language_standart','Standart'=>'BahasaIndonesia'))
			->join(array('def1'=>'tbl_definationms'),'a.Language =def1.idDefinition',array('Languageid'=>'a.Language','Language'=>'DefinitionDesc','Bahasa'=>'BahasaIndonesia'))
			->where('IdStudentRegistration =? ', $idstundetregistration);
			$row = $lobjDbAdpt->fetchAll($select);
		 
	
		 
	
		return $row;
	}
	
	public function addData($postData){
		$data = array(
				'Language' => $postData['languageid'],
				'Language_standart' => $postData['language_standart'],	
				'date_of_taken' => $postData['date_of_taken'],		
				'Skore' => $postData['Skore'],
				'level_entry'=>1,
				'dt_entry' => date('Y-m-d H:i:s'),
				'id_user' =>  $postData['id_user'],
				'idStudentRegistration' => $postData['idStudentRegistration']
				); 
		//echo var_dump($data);exit;
 
		return $this->insert($data);
	}
	
	public function updateData($postData, $id){
		
		$data = array(
				'Language' => $postData['languageid'],
				'Language_standart' => $postData['language_standart'],	
				'date_of_taken' => $postData['date_of_taken'],		
				'Skore' => $postData['Skore'],
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
	 
	
	public function approvedData($by,$id) {
	
		$data = array(
				'approved'=>'1',
				'approved_by'=>$by,
				'dt_approved' => date('Y-m-d H:i:s')
				);
		//echo var_dump($data);exit;
		$this->update($data, $this->_primary .' = '. (int) $id);
	} 
	
	public function deleteData($id){
		$this->delete($this->_primary . " = " . (int)$id);
	}
	
	public function fnGetLanguageStandart() {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array('a' => 'tbl_definationms'),array('key'=>'idDefinition','value'=>'BahasaIndonesia'))
		->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
		->where('b.defTypeDesc = "Languange Standart"')
		->where('a.Status = 1')
		->where('b.Active = 1')
		->order("b.defTypeDesc");
		$result = $lobjDbAdpt->fetchAll($select);
		//echo $select;exit;
		return $result;
	}
	public function fnGetLanguage() {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array('a' => 'tbl_definationms'),array('key'=>'idDefinition','value'=>'BahasaIndonesia'))
		->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
		->where('b.defTypeDesc = "Foreign Language"')
		->where('a.Status = 1')
		->where('b.Active = 1')
		->order("b.defTypeDesc");
		$result = $lobjDbAdpt->fetchAll($select);
		//echo $select;exit;
		return $result;
	}
	  
	 
}

