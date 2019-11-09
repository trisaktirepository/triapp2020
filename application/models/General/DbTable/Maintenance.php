<?php
class App_Model_General_DbTable_Maintenance extends Zend_Db_Table {

	protected $_name = 'tbl_definationtypems';

	//Function to Get Maintenance Details
	public function fnGetMaintenanceDetails() {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();

		$lstrSelect = $lobjDbAdpt->select()
							->from('tbl_definationtypems')
							->order('defTypeDesc');
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);

		//$larrResult = $this->fetchAll();
		return $larrResult;
	}

	//Maintenance Search Function
	public function fnSearchMaintenace($searchStr1,$searchStr2) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
								 ->from(array('a'=>'tbl_definationtypems'),array('a.idDefType','a.defTypeDesc','a.BahasaIndonesia','a.Description'));
		if($searchStr1 != "")	$lstrSelect ->where('a.defTypeDesc like "%" ? "%"',$searchStr1);
		if($searchStr2 != "")	$lstrSelect->where('a.BahasaIndonesia like "%" ? "%"',$searchStr2);
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	//Function To Save Maintenance
	public function fnAddMaintenance($post) {
		$lstrMsg = $this->insert($post);
		return $lstrMsg;
	}

    //Function To View Maintenace Type Ms
	public function fnViewMaintenance($idDefinition) {
		$lstrSelect = $this	->select()
						->setIntegrityCheck(false)
						->join(array('a' => 'tbl_definationms'),array('idDefinition'))
						->where('idDefType = ?',$idDefinition)
						->order('DefinitionDesc');
		$larrResult = $this->fetchAll($lstrSelect);
		return $larrResult->toArray();
	}
	public function fnGetEntryLevel() {
		$lstrSelect = $this->select()
				->setIntegrityCheck(false)
				->from(array('a' => 'tbl_qualificationmaster'),array('key' => 'a.IdQualification','value' => 'a.QualificationLevel'));
		$larrResult = $this->fetchAll($lstrSelect);
		return $larrResult->toArray();
	}


	//Function To View Maintenace Ms
	public function fnViewMaintenanceMs($idDefinition) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt	->select()
						->join(array('a' => 'tbl_definationms'),array('idDefinition'))
						->where('idDefinition = ?',$idDefinition);
		$larrResult = $lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}

	//Function To Get Maintenance Ms Details
	public function fnGetMaintenanceMsDetails($idDefType){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
							->from('tbl_definationms',array('idDefinition','idDefType','DefinitionCode','DefinitionDesc'))
							->where("Status='1' and idDefType=?",$idDefType)
							->order('definitionDesc');
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;

	}

	//Function To Check Maintenance Detail
	public function fnCheckMaintenanceDetails($definitionDesc,$idDefinition){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
				  ->from('tbl_definationms','count(*) as num')
				  ->where("DefinitionDesc=?",$definitionDesc)
				  ->where("idDefType =?",$idDefinition);
		$larrResult = $lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;

	}

	//Function To Update Maintenance Ms details
	public function fnUpdateMaintenanceMs($data,$idDefinition){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();

		$lstrTable='tbl_definationms';
		$lstrWhere="idDefinition = '".$idDefinition . " ' ";
		$lstrMsg=$lobjDbAdpt->update($lstrTable,$data,$lstrWhere);

		return $lstrMsg;
	}

	public function fnGetMaintenanceDescriptionDetails() {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();

		$lstrSelect = $lobjDbAdpt->select()
							->from(array('a'=>'tbl_definationtypems'),array('UPPER(a.defTypeDesc) as defTypeDesc'))
							->order('a.defTypeDesc');
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);

		//$larrResult = $this->fetchAll();
		return $larrResult;
	}

	public function fngetDeftype($Deftype) {
    	$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select 	= $lobjDbAdpt->select()
						->from(array("c"=>"tbl_definationtypems"),array("c.*"))
		            	->where("c.defTypeDesc= ?",$Deftype);

		return $result = $lobjDbAdpt->fetchRow($select);
    }

	public function fngetDefCode($DefCode,$Iddef) {

    	$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select 	= $lobjDbAdpt->select()
						->from(array("c"=>"tbl_definationms"),array("c.*"))
						->where("c.idDefType= ?",$Iddef)
		            	->where("c.DefinitionCode= ?",$DefCode);

		return $result = $lobjDbAdpt->fetchRow($select);
    }


    /**
     * Fetch Profile Status
     */
	public function fnfetchProfileStatus($condition=NULL) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
						->from(array('a' => 'tbl_definationms'),array('key' => 'a.idDefinition','value' => 'a.BahasaIndonesia'))
						->where($condition)
						->order('a.DefinitionDesc');
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
    
    //Function To Get Maintenance Ms List
	public function fnGetMaintenanceDisplay($idDefination){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
							->from('tbl_definationms',array('idDefinition','idDefType','DefinitionCode','DefinitionDesc'))
							->where("idDefinition=?",$idDefination)
							->order('definitionDesc');
		$larrResult = $lobjDbAdpt->fetchRow($lstrSelect);
		
        
        return $larrResult;

	}

    static public function getIdByDefCode($DefCode,$Iddef = null) {

        $db = Zend_Db_Table::getDefaultAdapter();
        $select 	= $db->select()
            ->from(array("c"=>"tbl_definationms"),array("c.*"))
            ->where("c.DefinitionCode= ?",$DefCode);

        if(!empty($Iddef)) {
            $select->where("c.idDefType= ?",$Iddef);
        }
        $result = $db->fetchRow($select);
        return $result['idDefinition'];
    }



}
