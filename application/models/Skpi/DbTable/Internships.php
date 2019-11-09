<?php

class App_Model_Skpi_DbTable_Internships extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'tbl_SKPI_Internship';
	protected $_primary = "idInternship";
		
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
			->where('IdStudentRegistration =? ', $idstundetregistration);
			$row = $lobjDbAdpt->fetchAll($select);
		 
	
		 
	
		return $row;
	}
	public function addData($postData){
		$data = array(
				'title' => $postData['title_internship'],
				'title_bahasa' => $postData['title_bahasa_internship'],	
				'datestart' => $postData['datestart_internship'],	
				'datestop' => $postData['datestop_internship'], 
				'position' => $postData['position_internship'],
				'position_eng' => $postData['position_internshipeng'],
				'level_entry'=>1,
				'dt_entry' => date('Y-m-d H:i:s'),
				'id_user' =>  $postData['id_user'],
				'idStudentRegistration' => $postData['idStudentRegistration']
				);
			
		return $this->insert($data);
	}
	
	public function updateData($postData, $id){
		
		$data = array(
				'title' => $postData['title_internship'],
				'title_bahasa' => $postData['title_bahasa_internship'],	
				'datestart' => $postData['datestart_internship'],	
				'datestop' => $postData['datestop_internship'], 
				'position' => $postData['position_internship'],
				'position_eng' => $postData['position_internshipeng'],
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
	
}

