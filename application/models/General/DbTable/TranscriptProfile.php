<?php
class App_Model_General_DbTable_TranscriptProfile extends Zend_Db_Table { //Model Class for Users Details
	protected $_name = 'transcript_profile';

	public function init()
	{
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		//$this->lobjForm = new GeneralSetup_Model_DbTable_Program();
	}

	public function fngetProgramDetails()
	{
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array('i' =>'tbl_program'),array('key'=>'i.IdProgram','value'=>'i.ProgramName','name'=>'i.ArabicName'))
		->where('i.Active = 1');
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	public function fngetMajoringDetails($idprogram)
	{
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array('i' =>'tbl_programmajoring'),array('i.IDProgramMajoring','i.BahasaDescription'))
		->where('i.idProgram = ?',$idprogram);
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fnGetTranscriptGroupName($grpid) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->join(array('a' => 'tbl_definationms'),array('a.idDefinition','a.BahasaIndonesia','a.DefinitionCode'))
		->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
		->where('a.idDefinition in (?)',$grpid);
		$result = $lobjDbAdpt->fetchAll($select);
		return $result;
	}
	public function fnViewSubjectregistrationlist($idProgram){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array('sr' =>'tbl_subjectregistration'),array('sr.*'))
		->join(array('p'=>'tbl_program'),'sr.IdProgram = p.IdProgram',array('p.ProgramName','p.IdProgram'))
		->join(array('d'=>'tbl_definationms'),'sr.TerminateStatus = d.idDefinition',array('d.DefinitionCode'))
		->where('sr.IdProgram=?',$idProgram)
		->where('sr.Active = 1');
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;

	}

	public function fngetGradeNameCombo()
	{
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("pg"=>"tbl_gradesetup"),array("pg.IdGradeSetUp","pg.GradeDesc"))
		->where("pg.Active = 1")
		->order("pg.GradeDesc");
			
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fnSearchProgramDetails($post) { //Function to get the Program Branch details
		$field7 = "i.Active = ".$post["field7"];

		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('i' => 'tbl_program'),array('i.IdProgram','i.ProgramName','i.ArabicName'))
		->where('i.ArabicName  like "%" ? "%"',$post['field2'])
		// ->where("i.IdProgram  = ?",$post['field5'])
		->where($field7);
		if(isset($post['field5']) && !empty($post['field5']) ){
			$select = $select->where("i.IdProgram = ?",$post['field5']);
		}
		//	echo $select;
		$result = $this->fetchAll($select);
		return $result->toArray();
	}


	public function fhinsertTranscriptProfileName($IdLandscape,$IdProgram,$IdMajoring,$name)
	{
		$larrformdata['IdProgram'] = $IdProgram;
		$larrformdata['IdLandscape'] = $IdLandscape;
		$larrformdata['IdMajoring'] = $IdMajoring;
		$larrformdata['ProfileName'] = $name;
		//cek for current Data
			$db = Zend_Db_Table::getDefaultAdapter();
		
			$select = $db->select()
			->from(array('tr'=>'transcript_profile'),array('IdProfile'=>'IdTranscriptProfile','ProfileName'))
			->where('tr.idProgram=?',$IdProgram)
			->where('tr.idMajoring=?',$IdMajoring)
			->where('tr.idLandscape=?',$IdLandscape)
			->where('tr.ProfileName=?',$name);
			//echo $select;
			//exit;
			$row = $db->fetchRow($select);
			//echo var_dump($row[0]['IdProfile']);
			//exit;
			if (!$row) {
				$lastinsertid = $this->insert($larrformdata);
			}else {
				$lastinsertid = $row['IdProfile'];
			}
				
		return $lastinsertid;

	}
	
	public function fninsertGradesSubjects($larrformdata){

		unset($larrformdata['Save']);
		unset($larrformdata['IdStudent']);
		unset($larrformdata['SemesterNumber']);
		unset($larrformdata['UpdUser']);
		unset($larrformdata['UpdDate']);

		$db = Zend_Db_Table::getDefaultAdapter();
		$table = "tbl_idstudgrade";
		$db->insert($table,$larrformdata);

	}

	public function getSubjectDetail($idSubject, $order,$id=null){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
		->from(array("ls"=>"tbl_subjectmaster"),array('ls.IdSubject','ls.SubCode','SubjectName'=>'ls.BahasaIndonesia','sks'=>"ls.CreditHours"))
		->where("ls.IdSubject = ?",$idSubject);
		$row = $db->fetchRow($select);
		$row = array_merge($row,array('order'=>$order));
		$row = array_merge($row,array('idTranscriptProfileDetail'=>$id));
		echo var_dump($row);
		//exit;
		return $row;
	}

	public function getTranscriptProfileAll(){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db->select()
		->from(array('tr'=>'transcript_profile'),array('IdProfile'=>'IdTranscriptProfile','ProfileName'))
		->join(array("pr"=>"tbl_program"),'tr.IdProgram=pr.IdProgram',array('ProgramName'))
		->join(array("ls"=>"tbl_landscape"),'ls.IdLandscape=tr.IdLandscape',array('ProgramDescription'))
		->join(array("mj"=>"tbl_programmajoring"),'mj.IdProgramMajoring=tr.IdMajoring',array('BahasaDescription'));
		
		$row = $db->fetchAll($select);
		//$row = array_merge($row,array('order'=>$order));
		//echo var_dump($row);
		//exit;
		return $row;
	}
	public function getTranscriptProfileList(){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db->select()
		->from(array('tr'=>'transcript_profile'),array('IdTranscriptProfile','ProfileName'))
		->join(array("pr"=>"tbl_program"),'tr.IdProgram=pr.IdProgram',array('ProgramName'))
		->join(array("ls"=>"tbl_landscape"),'ls.IdLandscape=tr.IdLandscape',array('ProgramDescription'))
		->join(array("mj"=>"tbl_programmajoring"),'mj.IdProgramMajoring=tr.IdMajoring',array('BahasaDescription'));
	
		$row = $db->fetchAll($select);
		//$row = array_merge($row,array('order'=>$order));
		//echo var_dump($row);
		//exit;
		return $row;
	}
	public function getTranscriptProfile($idProfile){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db->select()
		->from(array('tr'=>'transcript_profile'),array('idLandscape','idMajoring','IdProfile'=>'IdTranscriptProfile','ProfileName'))
		->join(array("pr"=>"tbl_program"),'tr.IdProgram=pr.IdProgram',array('ProgramName','idCollege','IdProgram'))
		->join(array("ls"=>"tbl_landscape"),'ls.IdLandscape=tr.IdLandscape',array('ProgramDescription'))
		->join(array("mj"=>"tbl_programmajoring"),'mj.IdProgramMajoring=tr.IdMajoring',array('BahasaDescription'))
		->where('tr.IdTranscriptProfile=?',$idProfile);
		$row = $db->fetchRow($select);
		//$row = array_merge($row,array('order'=>$order));
		//echo var_dump($row);
		//exit;
		return $row;
	}
	public function getStdTranscriptProfile($idProgram,$idMajor,$idLandscape){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db->select()
		->from(array('tr'=>'transcript_profile'),array('IdProfile'=>'IdTranscriptProfile','ProfileName'))
		->join(array("pr"=>"tbl_program"),'tr.IdProgram=pr.IdProgram',array('ProgramName'))
		->join(array("ls"=>"tbl_landscape"),'ls.IdLandscape=tr.IdLandscape',array('ProgramDescription'))
		->join(array("mj"=>"tbl_programmajoring"),'mj.IdProgramMajoring=tr.IdMajoring',array('BahasaDescription'))
		->where('tr.IdProgram=?',$idProgram)
		->where('tr.IdMajoring=?',$idMajor)
		->where('tr.IdLandscape=?',$idLandscape);
		$row = $db->fetchAll($select);
		//$row = array_merge($row,array('order'=>$order));
		//echo var_dump($row);
		//exit;
		return $row;
	}
	public function getStdTranscriptProfilebyProgram($idProgram){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db->select()
		->from(array('tr'=>'transcript_profile'),array('key'=>'IdTranscriptProfile','value'=>'ProfileName','name'=>'ProfileName'))
		->where('tr.IdProgram=?',$idProgram);
		$row = $db->fetchAll($select);
		return $row;
	}
	public function getTranscriptProfileGrp($idProfile){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db->select()
		->from(array('tr'=>'transcript_profile_grp'))
		->join(array('b' => 'tbl_definationms'),'tr.idGroup = b.idDefinition',array('grpname'=>'b.BahasaIndonesia','kategori'=>'b.BahasaIndonesia','category'=>'b.DefinitionDesc'))
		->where('tr.idTranscriptProfile=?',$idProfile)
		->order('tr.order');
		
		$row = $db->fetchAll($select);
		return $row;
	}
	public function getTranscriptProfilePerGrp($idProfile,$idgrp){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db->select()
		->from(array('tr'=>'transcript_profile_grp'))
		->where('tr.idTranscriptProfile=?',$idProfile)
		->where('tr.idGroup=?',$idgrp);
		$row = $db->fetchAll($select);
		return $row;
	}
	
	public function insertTranscriptProfileGrp($data){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		$order= $this->getTranscriptProfilePerGrp($data['idTranscriptProfile'], $data['idGroup']);
		if ($order==array()) {
			$row = $db->insert('transcript_profile_grp',$data);}
		else {
			$row = $this->updateTranscriptProfileGrp(array('order'=>$data['order']), $data['idTranscriptProfile'], $data['idGroup']);
			//echo var_dump($data);
			//echo var_dump(@row);
			//exit;
		}
		return $row;
	}
	public function updateTranscriptProfileGrp($data,$idprofile,$idgrp){
	
		$db = Zend_Db_Table::getDefaultAdapter();
		$where= 'idTranscriptProfile='.$idprofile.' and idGroup='.$idgrp;
		$row = $db->update('transcript_profile_grp',$data,$where);
		return $row;
	}
}






