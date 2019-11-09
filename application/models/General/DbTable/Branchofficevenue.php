<?php
class App_Model_General_DbTable_Branchofficevenue extends Zend_Db_Table_Abstract
{
	protected $_name = 'tbl_branchofficevenue';
	private $lobjDbAdpt;
	private $lobjprogram;
	private $lobjRegistration;

	public function init()
	{
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$this->lobjprogram = new GeneralSetup_Model_DbTable_Program();
		//$this->lobjRegistration = new Application_Model_DbTable_Registrationlocation();
	}
	public function fngetBranchDetails($lintidbrc) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("tbl_branchofficevenue"=>"tbl_branchofficevenue"),array("tbl_branchofficevenue.IdBranch","tbl_branchofficevenue.BranchName","tbl_branchofficevenue.Arabic"))
		->join(array("tbl_countries"=>"tbl_countries"),'tbl_branchofficevenue.idCountry = tbl_countries.idCountry',array("tbl_countries.CountryName"))
		->join(array("tbl_state"=>"tbl_state"),'tbl_branchofficevenue.idState = tbl_state.idState',array("tbl_state.StateName"))
		->where("tbl_branchofficevenue.IdType = ?",$lintidbrc)
		->where("tbl_branchofficevenue.Active = 1")
		->order("tbl_branchofficevenue.BranchName");
		//echo $lstrSelect;die();
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	public function fnGetAllBranchList(){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("a"=>$this->_name),array("key"=>"a.IdBranch","value"=>"a.BranchName","code"=>"BranchCode","Arabic"))
		->where("a.IdType = 1")
		->where("a.Active = 1")
		->where("a.BranchName != 'xxxxxx'")
		->order("BranchCode");
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
	
		return $larrResult;
	}
	
	public function getData($branchId=null){
			
			$db = Zend_Db_Table::getDefaultAdapter();
			
			$select = $db->select()
						 ->from(array('br'=>$this->_name))			
						 ->join(array('pb'=>'appl_program_branch'),'pb.IdBranch=br.IdBranch')
					     ->where("pb.IdProgramBranch = ?",$branchId);			
	
			$row = $db->fetchRow($select);
			return $row;
		}
	
	 

	public function getDatabyProgram($branchId=null){
	
		$db = Zend_Db_Table::getDefaultAdapter();
	
		$select = $db->select()
		->from(array('br'=>$this->_name))
		->join(array('pb'=>'appl_program_branch'),'pb.IdBranch=br.IdBranch')
		->where("pb.IdProgram = ?",$branchId);
	
		$row = $db->fetchAll($select);
		return $row;
	}

public function fnviewBranchofficevenueDtls($lintIdBranch) { //Function for the view user

		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$select 	= $lobjDbAdpt->select()
		->from(array("ct"=>"tbl_branchofficevenue"),array("ct.*"))
		->join(array("tbl_countries"=>"tbl_countries"),'ct.idCountry = tbl_countries.idCountry',array("tbl_countries.CountryName"))
		->join(array("tbl_state"=>"tbl_state"),'ct.idState = tbl_state.idState',array("tbl_state.StateName"))
		->where("ct.IdBranch = ?",$lintIdBranch);
		return $result = $lobjDbAdpt->fetchRow($select);
	}

}
?>