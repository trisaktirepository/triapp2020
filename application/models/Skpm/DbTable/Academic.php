<?php

class App_Model_Skpm_DbTable_Academic extends Zend_Db_Table_Abstract {
	/**
	 * The default table name 
	 */
	protected $_name = 'tbl_SKPM_Ilmiah';
	protected $_primary = "idIlmiah";
		
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
			->join(array('def'=>'tbl_definationms'),'a.level=def.idDefinition',array('level'=>'def.BahasaIndonesia','leveleng'=>'def.DefinitionDesc'))
			->join(array('def1'=>'tbl_definationms'),'a.field=def1.idDefinition',array('fieldid'=>'a.field','field'=>'def1.BahasaIndonesia','fieldeng'=>'def1.DefinitionDesc'))
			->joinLeft(array('def2'=>'tbl_definationms'),'a.role=def2.idDefinition',array('roleid'=>'a.role','role'=>'def2.BahasaIndonesia','roleeng'=>'def2.DefinitionDesc'))
			->joinLeft(array('def3'=>'tbl_definationms'),'a.achievment=def3.idDefinition',array('achievmentid'=>'a.achievment','achievmentname'=>'def3.BahasaIndonesia','achievmenteng'=>'def3.DefinitionDesc'))
			->joinLeft(array('def4'=>'tbl_definationms'),'a.category=def4.idDefinition',array('categoryid'=>'a.category','categoryname'=>'def4.BahasaIndonesia','categoryeng'=>'def4.DefinitionDesc'))
			->joinLeft(array('smt'=>'tbl_semestermaster'),'smt.IdSemesterMaster=a.IdSemesterMain') 
			->where('IdStudentRegistration =? ', $idstundetregistration);
			if ($status!=null) $select->where('a.Approved=?',$status); 
			if ($sem!='') $select->where('a.IdSemesterMain=?',$sem); 
			$row = $lobjDbAdpt->fetchAll($select);
		// echo var_dump($row);exit;
	
		return $row;
	}
	
	public function getDataMainbyStudent($idstundetregistration,$main,$type=null,$sem,$status=null){
		
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		//echo $sem;exit;
		$row=$row1=$row2=$row3=$row4=array();
		
		if ($type=='1' || $type=='') {
			$select = $lobjDbAdpt->select()
			->from(array('a'=>$this->_name),array('title'=>'title','title_bahasa'=>'title_bahasa','waktu'=>'date_of_honor','score','Approved'=>'approved'))
			->join(array('def'=>'tbl_definationms'),'a.level=def.idDefinition',array('level'=>'def.BahasaIndonesia','leveleng'=>'def.DefinitionDesc'))
			->join(array('def1'=>'tbl_definationms'),'a.field=def1.idDefinition',array('fieldid'=>'a.field','field'=>'def1.BahasaIndonesia','fieldeng'=>'def1.DefinitionDesc'))
			->joinLeft(array('def2'=>'tbl_definationms'),'a.role=def2.idDefinition',array('roleid'=>'a.role','role'=>'def2.BahasaIndonesia','roleeng'=>'def2.DefinitionDesc'))
			->joinLeft(array('def3'=>'tbl_definationms'),'a.achievment=def3.idDefinition',array('achievmentid'=>'a.achievment','achievmentname'=>'def3.BahasaIndonesia','achievmenteng'=>'def3.DefinitionDesc'))
			->joinLeft(array('def4'=>'tbl_definationms'),'a.category=def4.idDefinition',array('categoryid'=>'a.category','categoryname'=>'def4.BahasaIndonesia','categoryeng'=>'def4.DefinitionDesc'))
			->joinLeft(array('def5'=>'tbl_definationms'),'a.BuktiFisik=def5.idDefinition',array('BuktiFisik'=>'a.BuktiFisik','NamaBuktiFisik'=>'def5.BahasaIndonesia','NamaBuktiFisikEng'=>'def5.DefinitionDesc'))
			->joinLeft(array('doc'=>'tbl_SKPM_upload_file'),'a.url_certificate=doc.auf_id')
			->where('def4.BahasaIndonesia=?',$main)
			->where('IdStudentRegistration =? ', $idstundetregistration);
			if ($status!=null) $select->where('a.Approved=?',$status);
			if ($sem!=null) $select->where('a.IdSemesterMain=?',$sem);
			 
			$row = $lobjDbAdpt->fetchAll($select);
		}
		//echo $sem.'-'. $select;
		// echo var_dump($row);exit;
		//minat
		if ($type=='2' || $type=='') {
			$select = $lobjDbAdpt->select()
			->from(array('a'=>'tbl_SKPM_Minat'),array('title'=>'title','title_bahasa'=>'title_bahasa','waktu'=>'date_of_honor','score','Approved'=>'approved'))
			->join(array('def'=>'tbl_definationms'),'a.level=def.idDefinition',array('level'=>'def.BahasaIndonesia','leveleng'=>'def.DefinitionDesc'))
			->join(array('def1'=>'tbl_definationms'),'a.field=def1.idDefinition',array('fieldid'=>'a.field','field'=>'def1.BahasaIndonesia','fieldeng'=>'def1.DefinitionDesc'))
			->joinLeft(array('def2'=>'tbl_definationms'),'a.role=def2.idDefinition',array('roleid'=>'a.role','role'=>'def2.BahasaIndonesia','roleeng'=>'def2.DefinitionDesc'))
			->joinLeft(array('def3'=>'tbl_definationms'),'a.achievment=def3.idDefinition',array('achievmentid'=>'a.achievment','achievmentname'=>'def3.BahasaIndonesia','achievmenteng'=>'def3.DefinitionDesc'))
			->joinLeft(array('def4'=>'tbl_definationms'),'a.category=def4.idDefinition',array('categoryid'=>'a.category','categoryname'=>'def4.BahasaIndonesia','categoryeng'=>'def4.DefinitionDesc'))
			->joinLeft(array('def5'=>'tbl_definationms'),'a.BuktiFisik=def5.idDefinition',array('BuktiFisik'=>'a.BuktiFisik','NamaBuktiFisik'=>'def5.BahasaIndonesia','NamaBuktiFisikEng'=>'def5.DefinitionDesc'))
			->joinLeft(array('doc'=>'tbl_SKPM_upload_file'),'a.url_certificate=doc.auf_id')
			->where('def4.BahasaIndonesia=?',$main)
			->where('IdStudentRegistration =? ', $idstundetregistration);
			if ($status!=null) $select->where('a.Approved=?',$status);
			if ($sem!=null) $select->where('a.IdSemesterMain=?',$sem);
			$row1 = $lobjDbAdpt->fetchAll($select);
		}
		//get org
		if ($type=='3' || $type=='') {
			$select = $lobjDbAdpt->select()
			->from(array('a'=>'tbl_SKPM_Organisasi'),array('title'=>'title','title_bahasa'=>'title_bahasa','waktu'=>"CONCAT(year_start,'-',year_stop)",'score','Approved'=>'approved'))
			->join(array('def'=>'tbl_definationms'),'a.level=def.idDefinition',array('level'=>'def.BahasaIndonesia','leveleng'=>'def.DefinitionDesc'))
			->join(array('def1'=>'tbl_definationms'),'a.field=def1.idDefinition',array('fieldid'=>'a.field','field'=>'def1.BahasaIndonesia','fieldeng'=>'def1.DefinitionDesc'))
			->joinLeft(array('def2'=>'tbl_definationms'),'a.role=def2.idDefinition',array('roleid'=>'a.role','role'=>'def2.BahasaIndonesia','roleeng'=>'def2.DefinitionDesc'))
			->joinLeft(array('def3'=>'tbl_definationms'),'a.achievment=def3.idDefinition',array('achievmentid'=>'a.achievment','achievmentname'=>'def3.BahasaIndonesia','achievmenteng'=>'def3.DefinitionDesc'))
			->joinLeft(array('def4'=>'tbl_definationms'),'a.category=def4.idDefinition',array('categoryid'=>'a.category','categoryname'=>'def4.BahasaIndonesia','categoryeng'=>'def4.DefinitionDesc'))
			->joinLeft(array('def5'=>'tbl_definationms'),'a.BuktiFisik=def5.idDefinition',array('BuktiFisik'=>'a.BuktiFisik','NamaBuktiFisik'=>'def5.BahasaIndonesia','NamaBuktiFisikEng'=>'def5.DefinitionDesc'))
			->joinLeft(array('doc'=>'tbl_SKPM_upload_file'),'a.url_certificate=doc.auf_id')
			->where('def4.BahasaIndonesia=?',$main)
			->where('IdStudentRegistration =? ', $idstundetregistration);
			if ($status!=null) $select->where('a.Approved=?',$status);
			If ($sem!=null) $select->where('a.IdSemesterMain=?',$sem);
			$row2 = $lobjDbAdpt->fetchAll($select);
		}
		
		//pkm
		if ($type=='4' || $type=='') {
			$select = $lobjDbAdpt->select()
			->from(array('a'=>'tbl_SKPM_Pkm'),array('title'=>'title','title_bahasa'=>'title_bahasa','waktu'=>"CONCAT(datestart,'-',datestop)",'score','Approved'=>'approved'))
			->join(array('def'=>'tbl_definationms'),'a.level=def.idDefinition',array('level'=>'def.BahasaIndonesia','leveleng'=>'def.DefinitionDesc'))
			->joinLeft(array('def1'=>'tbl_definationms'),'a.field=def1.idDefinition',array('fieldid'=>'a.field','field'=>'def1.BahasaIndonesia','fieldeng'=>'def1.DefinitionDesc'))
			->joinLeft(array('def2'=>'tbl_definationms'),'a.role=def2.idDefinition',array('roleid'=>'a.role','role'=>'def2.BahasaIndonesia','roleeng'=>'def2.DefinitionDesc'))
			->joinLeft(array('def3'=>'tbl_definationms'),'a.achievment=def3.idDefinition',array('achievmentid'=>'a.achievment','achievmentname'=>'def3.BahasaIndonesia','achievmenteng'=>'def3.DefinitionDesc'))
			->join(array('def4'=>'tbl_definationms'),'a.category=def4.idDefinition',array('categoryid'=>'a.category','categoryname'=>'def4.BahasaIndonesia','categoryeng'=>'def4.DefinitionDesc'))
			->joinLeft(array('def5'=>'tbl_definationms'),'a.BuktiFisik=def5.idDefinition',array('BuktiFisik'=>'a.BuktiFisik','NamaBuktiFisik'=>'def5.BahasaIndonesia','NamaBuktiFisikEng'=>'def5.DefinitionDesc'))
			->joinLeft(array('doc'=>'tbl_SKPM_upload_file'),'a.url_certificate=doc.auf_id')
			->where('def4.BahasaIndonesia=?',$main)
			->where('IdStudentRegistration =? ', $idstundetregistration);
			if ($status!=null) $select->where('a.Approved=?',$status);
			if ($sem!=null) $select->where('a.IdSemesterMain=?',$sem);
			$row3 = $lobjDbAdpt->fetchAll($select);
		}
		//Other
		if ($type=='5' ) {
			$select = $lobjDbAdpt->select()
			->from(array('a'=>'tbl_SKPM_Other'),array('title'=>'title','title_bahasa'=>'title_bahasa','waktu'=>"date_of_honor",'score','Approved'=>'approved'))
			->join(array('def'=>'tbl_definationms'),'a.level=def.idDefinition',array('level'=>'def.BahasaIndonesia','leveleng'=>'def.DefinitionDesc'))
			->join(array('def1'=>'tbl_definationms'),'a.field=def1.idDefinition',array('fieldid'=>'a.field','field'=>'def1.BahasaIndonesia','fieldeng'=>'def1.DefinitionDesc'))
			->joinLeft(array('def2'=>'tbl_definationms'),'a.role=def2.idDefinition',array('roleid'=>'a.role','role'=>'def2.BahasaIndonesia','roleeng'=>'def2.DefinitionDesc'))
			->joinLeft(array('def3'=>'tbl_definationms'),'a.achievment=def3.idDefinition',array('achievmentid'=>'a.achievment','achievmentname'=>'def3.BahasaIndonesia','achievmenteng'=>'def3.DefinitionDesc'))
			->join(array('def4'=>'tbl_definationms'),'a.category=def4.idDefinition',array('categoryid'=>'a.category','categoryname'=>'def4.BahasaIndonesia','categoryeng'=>'def4.DefinitionDesc'))
			->joinLeft(array('def5'=>'tbl_definationms'),'a.BuktiFisik=def5.idDefinition',array('BuktiFisik'=>'a.BuktiFisik','NamaBuktiFisik'=>'def5.BahasaIndonesia','NamaBuktiFisikEng'=>'def5.DefinitionDesc'))
			->joinLeft(array('doc'=>'tbl_SKPM_upload_file'),'a.url_certificate=doc.auf_id')
			->where('def4.BahasaIndonesia=?',$main)
			->where('IdStudentRegistration =? ', $idstundetregistration);
			if ($status!=null) $select->where('a.Approved=?',$status);
			if ($sem!=null) $select->where('a.IdSemesterMain=?',$sem);
			$row4 = $lobjDbAdpt->fetchAll($select);
		}
		
		$row=array_merge($row,$row1,$row2,$row3,$row4);
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
				'title' => $postData['title'],
				'title_bahasa' => $postData['title_bahasa'],	
				'date_of_honor' => $postData['date_of_honor'],	
				'given_by' => $postData['given_by'],	
				'level' => $postData['level'],
				'level_entry' => 2,
				'field' => $postData['field'],
				'category' => $postData['category'],
				'role' => $postData['role'],
				'achievment' => $postData['achievment'],
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
				'title' => $postData['title'],
				'title_bahasa' => $postData['title_bahasa'],	
				'category' => $postData['category'],
				'date_of_honor' => $postData['date_of_honor'],	
				'given_by' => $postData['given_by'],	
				'level' => $postData['level'],
				'field' => $postData['field'],
				'role' => $postData['role'],
				'achievment' => $postData['achievment'],
				'dt_update' => date('Y-m-d H:i:sa'),
				'score' => $postData['score'],
				'BuktiFisik'=>$postData['IdBukti'],
				'IdSemesterMain'=>$postData['IdSemester'],
				);
		 
		$this->update($data, $this->_primary .' = '. (int) $id);
	}
	public function rejectData($by,$id){
	
		$data = array(
				'approved'=>'0',
				'rejected_by'=>$by,
				'dt_update' => date('Y-m-d H:i:sa')
					
					
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
	
	public function fnGetFieldsActivity($type=null) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->distinct()
		->from(array('a' => 'tbl_definationms'),array('key'=>'idDefinition'))
		->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
		->join(array('nl'=>'tbl_SKPM_Skore'),'nl.field=a.idDefinition',array('value'=>"CONCAT(nl.ActivityName,'(',a.BahasaIndonesia,')')"))
		->where('b.defTypeDesc = "Field Of Academic Activity"')
		->where('a.Status = 1')
		->where('b.Active = 1')
		
		->order("nl.ActivityName");
		
		if ($type!=null) $select->where('left(a.DefinitionCode,1) = ?',$type);
		
		$result = $lobjDbAdpt->fetchAll($select);
		return $result;
	}
	public function fnGetFieldsActivitySetup($type=null) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->distinct()
		->from(array('a' => 'tbl_definationms'),array('key'=>'idDefinition','value'=>'BahasaIndonesia'))
		->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
		//->join(array('nl'=>'tbl_SKPM_Skore'),'nl.field=a.idDefinition',array('value'=>'ActivityName'))
		->where('b.defTypeDesc = "Field Of Academic Activity"')
		->where('a.Status = 1')
		->where('b.Active = 1')
	
		->order("a.DefinitionCode");
	
		if ($type!=null) $select->where('left(a.DefinitionCode,1) = ?',$type);
	
		$result = $lobjDbAdpt->fetchAll($select);
		return $result;
	}
	public function fnGetRole($type=null) {
		
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array('a' => 'tbl_definationms'),array('key'=>'idDefinition','value'=>'BahasaIndonesia'))
		->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
		->where('b.defTypeDesc = "Role in Activity"')
		->where('a.Status = 1')
		->where('b.Active = 1')
		
		->order("a.DefinitionCode");
		
		if ($type!=null) $select->where("left(a.DefinitionCode,1) in (?)",$type);
		
		$result = $lobjDbAdpt->fetchAll($select);
		//echo $select;exit;
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
	
	public function fnGetBukti($type=null) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array('a' => 'tbl_definationms'),array('key'=>'idDefinition','value'=>'BahasaIndonesia'))
		->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
		->where('b.defTypeDesc = "Physical Proof"')
		->where('a.Status = 1')
		->where('b.Active = 1')
		->order("a.DefinitionCode");
		if ($type!=null) $select->where("left(a.DefinitionCode,1) in (?)",$type);
		$result = $lobjDbAdpt->fetchAll($select);
		return $result;
	}
	
	public function fnGetCategory() {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->from(array('a' => 'tbl_definationms'),array('key'=>'idDefinition','value'=>'BahasaIndonesia'))
		->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
		->where('b.defTypeDesc = "Category"')
		->where('a.Status = 1')
		->where('b.Active = 1')
		->order("a.DefinitionCode");
		$result = $lobjDbAdpt->fetchAll($select);
		return $result;
	}
	

	
}

