<?php

class App_Model_Skpm_DbTable_Other extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'tbl_SKPM_Other';
	protected $_primary = "idOther";
		
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
			->joinLeft(array('def'=>'tbl_definationms'),'a.level=def.idDefinition',array('level'=>'def.BahasaIndonesia','leveleng'=>'DefinitionDesc'))
			->joinLeft(array('def1'=>'tbl_definationms'),'a.field=def1.idDefinition',array('fieldid'=>'a.field','field'=>'def1.BahasaIndonesia','fieldeng'=>'DefinitionDesc'))
			->joinLeft(array('def2'=>'tbl_definationms'),'a.role=def2.idDefinition',array('roleid'=>'a.role','role'=>'def2.BahasaIndonesia','roleeng'=>'DefinitionDesc'))
			->joinLeft(array('def3'=>'tbl_definationms'),'a.achievment=def3.idDefinition',array('achievmentid'=>'a.achievment','achievmentname'=>'def3.BahasaIndonesia','achievmenteng'=>'DefinitionDesc'))
			->joinLeft(array('def4'=>'tbl_definationms'),'a.category=def4.idDefinition',array('categoryid'=>'a.category','categoryname'=>'def4.BahasaIndonesia','categoryeng'=>'def4.DefinitionDesc'))
			->joinLeft(array('doc'=>'tbl_SKPM_upload_file'),'a.url_certificate=doc.auf_id')
			->joinLeft(array('smt'=>'tbl_semestermaster'),'smt.IdSemesterMaster=a.IdSemesterMain') 
			->where('IdStudentRegistration =? ', $idstundetregistration);
			if ($status!=null) $select->where('a.Approved=?',$status); 
			if ($sem!='') $select->where('a.IdSemesterMain=?',$sem);
			$row = $lobjDbAdpt->fetchAll($select);
		// echo var_dump($row);exit;
	
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
				'title' => $postData['title_other'],
				'title_bahasa' => $postData['title_other_bahasa'],	
				'date_of_honor' => $postData['date_other_of_honor'],	
				'given_by' => $postData['given_other_by'],	
				'level' => $postData['level_other'],
				'level_entry' => 2,
				'field' => $postData['field_other'],
				'role' => $postData['role_other'],
				'Category' =>"0",
				'achievment' => $postData['achievment_other'],
				'dt_entry' => date('Y-m-d H:i:sa'),
				'idUser' =>  $postData['idUser'],
				'IdSemesterMain'=>$postData['IdSemester'],
				'BuktiFisik'=>$postData['IdBukti'],
				'idStudentRegistration' => $postData['idStudentRegistration']
				);
		//echo var_dump($data)	;exit;
		return $this->insert($data);
	}
	
	public function updateData($postData, $id){
		
		$data = array(
				'title' => $postData['title_other'],
				'title_bahasa' => $postData['title_other_bahasa'],	
				'date_of_honor' => $postData['date_other_of_honor'],	
				'given_by' => $postData['given_other_by'],	
				'level' => $postData['level_other'],
				'field' => $postData['field_other'],
				'role' => $postData['role_other'],
				'achievment' => $postData['achievment_other'],
				'dt_update' => date('Y-m-d H:i:sa'),
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
	
	public function fnGetFieldsActivity($type) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array('a' => 'tbl_definationms'),array('key'=>'idDefinition','value'=>'BahasaIndonesia'))
		->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
		->where('b.defTypeDesc = "Field Of Academic Activity"')
		->where('a.Status = 1')
		->where('b.Active = 1')
		->where('left(a.DefinitionCode,1) = ?',$type)
		->order("a.DefinitionCode");
		$result = $lobjDbAdpt->fetchAll($select);
		return $result;
	}
	public function fnGetRole($type) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array('a' => 'tbl_definationms'),array('key'=>'idDefinition','value'=>'BahasaIndonesia'))
		->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
		->where('b.defTypeDesc = "Role in Activity"')
		->where('a.Status = 1')
		->where('b.Active = 1')
		->where('left(a.DefinitionCode,1) in (?)',$type)
		->order("a.DefinitionCode");
		$result = $lobjDbAdpt->fetchAll($select);
		return $result;
	}
	public function fnGetAchievment() {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array('a' => 'tbl_definationms'),array('key'=>'idDefinition','value'=>'BahasaIndonesia'))
		->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
		->where('b.defTypeDesc = "Achievment of Activity"')
		->where('a.Status = 1')
		->where('b.Active = 1')
		->order("a.DefinitionCode");
		$result = $lobjDbAdpt->fetchAll($select);
		return $result;
	}
}

