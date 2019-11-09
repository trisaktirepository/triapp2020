<?php
class App_Model_General_DbTable_TranscriptProfileDetail extends Zend_Db_Table { //Model Class for Users Details
	protected $_name = 'transcript_profile_detail';

	public function init()
	{
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}

	
	public function fhinsertGrupMk($idProfile,$idgrp,$data)
	{
		$larrformdata['IdTranscriptProfile']=$idProfile;
		$larrformdata['IdGroup']=$idgrp;
		$larrformdata['IdSubject']=$data['IdSubject'];
		if ($data['order']==null) {
			$larrformdata['order']=0;
		} else {
			$larrformdata['order']=$data['order'];
		}
		//cek for current Data
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
		->from(array('tr'=>'transcript_profile_detail'),array('IdProfile'=>'IdTranscriptProfile'))
		->where('tr.IdTranscriptProfile=?',$idProfile)
		->where('tr.IdGroup=?',$idgrp)
		->where('tr.IdSubject=?',$data['IdSubject']);
		//echo var_dump($select);
		//exit;
		$row = $db->fetchAll($select);
		if (!$row) {
			$lastinsertid = $this->insert($larrformdata);
		}
		else $lastinsertid=0;
		return $lastinsertid;
	
	}
	public function fnDelTranscriptProfileDetail($id) {
		$db = Zend_Db_Table::getDefaultAdapter();
		$where = $db->quoteInto('idTranscriptProfileDetail = ?', $id);
		$db->delete($this->_name, $where);
		
	}
	public function fnGetTranscriptProfileDetail($idProfile) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->join(array('a' => 'transcript_profile_detail'),array('a.idGroup','a.order'))
		->joinLeft(array('grp'=>'transcript_profile_grp'),'a.idTranscriptProfile=grp.idTranscriptProfile and a.idGroup=grp.idGroup')
		->join(array('b' => 'tbl_definationms'),'a.idGroup = b.idDefinition',array('grpname'=>'b.BahasaIndonesia'))
		->join(array('c'=> 'tbl_subjectmaster'),'a.idSubject=c.idSubject',array('SubjectName'=>'BahasaIndonesia','sks'=>'CreditHours','SubCode'))
		->where('a.idTranscriptProfile in (?)',$idProfile)
		->order('grp.order','a.order');
		$result = $lobjDbAdpt->fetchAll($select);
		return $result;
	}
	public function fnGetGroupName($idGrp) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->join(array('b' => 'tbl_definationms'),array('grpname'=>'b.BahasaIndonesia'))
		->where('b.idDefinition=?',$idGrp);
		$result = $lobjDbAdpt->fetchRow($select);
		return $result;
	}
	public function fnGetTranscriptProfileGrpSubject($idProfile,$grp) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->join(array('a' => 'transcript_profile_detail'),array('a.order'))
		->join(array('b' => 'transcript_profile'),'a.idTranscriptProfile = b.idTranscriptProfile',array('idProgram','idLandscape','idMajoring','ProfileName'))
		->join(array('c'=> 'tbl_subjectmaster'),'a.idSubject=c.idSubject',array('IdSubject','SubjectName'=>'BahasaIndonesia','sks'=>'CreditHours','SubCode'))
		->where('b.idTranscriptProfile=?',$idProfile)
		->where('a.idGroup = ?',$grp)
		->order('a.order');
		
		$result = $lobjDbAdpt->fetchAll($select);
		//echo var_dump($result);
		//exit;
		if (!$result) {
			return $result=null;
		}
		else return $result;
	}
		
	public function fnGetTranscriptProfileSubject($idProfile,$idGrp) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->join(array('a' => 'transcript_profile_detail'),array('a.IdTranscriptProfileDetail','a.idGroup','a.order'))
		->join(array('b' => 'tbl_definationms'),'a.idGroup = b.idDefinition',array('grpname'=>'b.BahasaIndonesia'))
		->join(array('c'=> 'tbl_subjectmaster'),'a.idSubject=c.idSubject',array('SubjectName'=>'BahasaIndonesia','sks'=>'CreditHours','SubCode'))
		->where('a.idTranscriptProfile =?',$idProfile)
		->where('a.idGroup=?',$idGrp)
		->order('a.order');
		$result = $lobjDbAdpt->fetchAll($select);
		return $result;
	}

	public function fndeleteProfileDetail($IdProfile)
	{
		$thjdbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrwhere = "IdTranscriptProfile = ".$IdProfile;

		$thjdbAdpt->delete('transcript_profile_detail',$lstrwhere);
	}

	public function fnGetTranscriptProfileName($idProfile) {
		
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select = $lobjDbAdpt->select()
		->join(array('a' => 'transcript_profile_detail'),array('a.idGroup'))
		->joinLeft(array('grp'=>'transcript_profile_grp'),'a.idTranscriptProfile=grp.idTranscriptProfile and a.idGroup=grp.idGroup',array('grp.order'))
		->join(array('b' => 'tbl_definationms'),'a.idGroup = b.idDefinition',array('grpname'=>'b.BahasaIndonesia','kategori'=>'b.BahasaIndonesia','category'=>'b.DefinitionDesc'))
		->where('a.idTranscriptProfile =?',$idProfile)
		->group('a.idGroup')
		->order('grp.order');
		
		//echo ($select);
		//exit;
		$result = $lobjDbAdpt->fetchAll($select);
		return $result;
	}
	
}