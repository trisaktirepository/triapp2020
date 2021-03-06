<?php

class App_Model_Skpm_DbTable_Talent extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'tbl_SKPM_Minat';
	protected $_primary = "idMinat";
		
	public function getData($id=0){
		$id = (int)$id;
		
		if($id!=0){
			$row = $this->fetchRow($this->_primary .' = '. $id);
		}else{
			$row = $this->fetchAll();
		}
		 
		return $row->toArray();
	}
	public function getDatabyStudent($idstundetregistration,$sem,$status=null){
			$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
			$select = $lobjDbAdpt->select()
			->from(array('a'=>$this->_name))
			->join(array('def'=>'tbl_definationms'),'a.level=def.idDefinition',array('levelname'=>'def.BahasaIndonesia','leveleng'=>'def.DefinitionDesc'))
			->join(array('def1'=>'tbl_definationms'),'a.field=def1.idDefinition',array('fieldid'=>'a.field','field'=>'def1.BahasaIndonesia','fieldeng'=>'def1.DefinitionDesc'))
			->joinLeft(array('def2'=>'tbl_definationms'),'a.role=def2.idDefinition',array('roleid'=>'a.role','role'=>'def2.BahasaIndonesia','roleeng'=>'def2.DefinitionDesc'))
			->joinLeft(array('def3'=>'tbl_definationms'),'a.achievment=def3.idDefinition',array('achievmentid'=>'a.achievment','acheivmentname'=>'def3.BahasaIndonesia','achievmenteng'=>'def3.DefinitionDesc'))
			->joinLeft(array('def4'=>'tbl_definationms'),'a.category=def4.idDefinition',array('categoryid'=>'a.category','categoryname'=>'def4.BahasaIndonesia','categoryeng'=>'def4.DefinitionDesc'))
			->joinLeft(array('smt'=>'tbl_semestermaster'),'smt.IdSemesterMaster=a.IdSemesterMain') 
			->where('IdStudentRegistration =? ', $idstundetregistration);
			if ($status!=null) $select->where('a.Approved=?',$status); 
			if ($sem!='') $select->where('a.IdSemesterMain=?',$sem);//echo  $select;exit;
			
			$row = $lobjDbAdpt->fetchAll($select);
		 
	
	
		return $row;
	}
	
	public function getDataApprovalbyStudent($idstundetregistration){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array('a'=>$this->_name),array('count'=>'count(*)','status'=>'Approved'))
		//->join(array('def'=>'tbl_definationms'),'a.level=def.idDefinition',array('level'=>'BahasaIndonesia'))
		//->join(array('def1'=>'tbl_definationms'),'a.field=def1.idDefinition',array('fieldid'=>'a.field','field'=>'BahasaIndonesia'))
		->where('IdStudentRegistration =? ', $idstundetregistration)
		->group('a.Approved');
		$row = $lobjDbAdpt->fetchAll($select);
			
	
	
		return $row;
	}
	public function addData($postData){
		$data = array(
				'title' => $postData['title_minat'],
				'title_bahasa' => $postData['title_minat_bahasa'],	
				'date_of_honor' => $postData['date_minat_of_honor'],	
				'given_by' => $postData['given_minat_by'],	
				'level' => $postData['level_minat'],
				'level_entry' => 2,
				'field' => $postData['field_minat'],
				'role' => $postData['role_minat'],
				'category' => $postData['category_minat'],
				'achievment' => $postData['achievment_minat'],
				'dt_entry' => date('Y-m-d H:i:sa'),
				'idUser' =>  $postData['idUser'],
				'score' => $postData['score'],
				'IdSemesterMain'=>$postData['IdSemester'],
				'BuktiFisik'=>$postData['IdBukti'],
				'idStudentRegistration' => $postData['idStudentRegistration']
				);
			
		return $this->insert($data);
	}
	
	public function updateData($postData, $id){
		
		$data = array(
				'title' => $postData['title_minat'],
				'title_bahasa' => $postData['title_minat_bahasa'],	
				'date_of_honor' => $postData['date_minat_of_honor'],	
				'given_by' => $postData['given_minat_by'],	
				'level' => $postData['level_minat'],
				'field' => $postData['field_minat'],
				'role' => $postData['role_minat'],
				'category' => $postData['category_minat'],
				'achievment' => $postData['achievment_minat'],
				'dt_update' => date('Y-m-d H:i:sa'),
				'score' => $postData['score'],
				'IdSemesterMain'=>$postData['IdSemester'],
				'BuktiFisik'=>$postData['IdBukti'],
				 
				);
		 
		$this->update($data, $this->_primary .' = '. (int) $id);
	}
	public function rejectData($by,$id){
	
		$data = array(
				'approved'=>'0',
				'rejected_by'=>$by,
				'dt_update' => date('Y-m-d H:i:s')
					
					
		);
			
		$this->update($data, $this->_primary .' = '. (int) $id);
	}
	public function approvedData($by,$id){
	
		$data = array(
				'approved'=>'1',
				'rejected_by'=>0,
				'approved_by'=>$by,
				'dt_approved' => date('Y-m-d H:i:s')
					
					
		);
			
		$this->update($data, $this->_primary .' = '. (int) $id);
	}
	public function deleteData($id){
		$this->delete($this->_primary . " = " . (int)$id);
	}
	
	 
}

