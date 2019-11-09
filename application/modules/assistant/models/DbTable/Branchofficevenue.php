<?php
class Assistant_Model_DbTable_Branchofficevenue extends Zend_Db_Table_Abstract
{
	protected $_name = 'tbl_branchofficevenue';
	private $lobjDbAdpt;
	private $lobjprogram;
	private $lobjRegistration;

	public function init()
	{
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$this->lobjprogram = new GeneralSetup_Model_DbTable_Program();
		$this->lobjRegistration = new Application_Model_DbTable_Registrationlocation();
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

	public function fnGetCountryList(){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("a"=>"tbl_countries"),array("key"=>"a.idCountry","value"=>"CountryName"))
		->where("a.Active = 1")
		->order("a.CountryName");
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);

		return $larrResult;
	}

	public function fnGetBranchList(){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("a"=>$this->_name),array("BranchCode","id"=>"a.IdBranch","value"=>"a.BranchName","Arabic"))
		->where("a.IdType = 1")
		->where("a.Active = 1");
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		//echo var_dump($larrResult);exit;
		return $larrResult;
	}


	public function fnGetStateList(){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("a"=>"tbl_state"),array("key"=>"a.idState","value"=>"StateName"))
		->where("a.Active = 1")
		->order("a.StateName");
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
	
	public function fnSearchBranchDetails($post = array(),$lintidbrc) {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$field7 = "tbl_branchofficevenue.Active = ".$post["field7"];
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("tbl_branchofficevenue"=>"tbl_branchofficevenue"),array("tbl_branchofficevenue.IdBranch","tbl_branchofficevenue.BranchName","tbl_branchofficevenue.Arabic"))
		->join(array("tbl_countries"=>"tbl_countries"),'tbl_branchofficevenue.idCountry = tbl_countries.idCountry',array("tbl_countries.CountryName"))
		->join(array("tbl_state"=>"tbl_state"),'tbl_branchofficevenue.idState = tbl_state.idState',array("tbl_state.StateName"))
		->where('tbl_branchofficevenue.BranchName like "%" ? "%"',$post['field2'])
		->where($field7)
		->where("tbl_branchofficevenue.IdType = ?",$lintidbrc)
		->order("tbl_branchofficevenue.BranchName");

		if(isset($post['field5']) && !empty($post['field5']) ){
			$lstrSelect = $lstrSelect->where("tbl_countries.idCountry = ?",$post['field5']);

		}
		if(isset($post['field8']) && !empty($post['field8']) ){
			$lstrSelect = $lstrSelect->where("tbl_state.idState = ?",$post['field8']);
		}
		if(isset($post['field28']) && !empty($post['field28']) ){
			$lstrSelect = $lstrSelect->where("tbl_branchofficevenue.Arabic LIKE ?", '%'.$post['field28'].'%');
		}

		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fnaddBranchDetails($larrformData) {
		$larrformData['Phone'] = $larrformData['countrycode']."-".$larrformData['statecode']."-".$larrformData['Phone1'];
		unset($larrformData['countrycode']);
		unset($larrformData['statecode']);
		unset($larrformData['Phone1']);
		unset($larrformData['IdRegistration']);
		unset($larrformData['RegistrationLoc']);
		unset($larrformData['Programme']);
		unset($larrformData['RegistrationLocgrid']);
		unset($larrformData['Programmegrid']);
		unset($larrformData['Save']);
		$this->lobjDbAdpt->insert('tbl_branchofficevenue',$larrformData);
		return ($this->lobjDbAdpt->lastInsertId());
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

	public function fnupdateBranchofficevenueDtls($lintIdBranch,$larrformData,$larrregistration) { //Function for updating the user
		$larrformData['Phone'] = $larrformData['countrycode']."-".$larrformData['statecode']."-".$larrformData['Phone'];
		unset($larrformData['countrycode']);
		unset($larrformData['statecode']);
		unset($larrformData['IdRegistration']);
		unset($larrformData['RegistrationLoc']);
		unset($larrformData['Programme']);
		unset($larrformData['RegistrationLocgrid']);
		unset($larrformData['Programmegrid']);
		unset($larrformData['Save']);
		//		echo "<pre>";
		//		print_r($larrformData);die;
		$where = 'IdBranch = '.$lintIdBranch;
		$this->update($larrformData,$where);
	}

	public function fnGetRegistrationlocList(){
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("a"=>"tbl_registrationlocation"),array("key"=>"a.IdRegistrationLocation","value"=>"RegistrationLocationCode"))
		->where("a.Status = 1")
		->order("a.RegistrationLocationCode");
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fnviewBranchRegistrationMap($IdBranch) { // Function to edit Purchase order details
		$select = $this->select()
		->setIntegrityCheck(false)
		->join(array('a' => 'tbl_branchregistrationmap'),array('a.*'))
		->join(array('b' => 'tbl_registrationlocation'),"a.RegistrationLoc = b.IdRegistrationLocation",array("b.IdRegistrationLocation","b.RegistrationLocationCode"))
		->join(array('c' => 'tbl_program'),"c.IdProgram = a.Programme",array("c.IdProgram","c.ProgramName"))
		->where("a.idBranch  = '$IdBranch'");
		$result = $this->fetchAll($select);
		return $result;
	}
	public function fnDeleteBranchregistration($larrformData){//function to delete from  database
		if($larrformData['IdRegistration']){
			$id=$larrformData['IdRegistration'];
			$arrid=explode(",",$id);
			for($i=0;$i<count($arrid);$i++){
				$where = "idRegistration =$arrid[$i]";
				$db->delete('tbl_branchregistrationmap',$where);
			}
			return;
		}
		return;
	}

	public function fnDeleteRegLoc($idRegLoc) {  // function to delete payment details
		$db = Zend_Db_Table::getDefaultAdapter();
		$table = 'tbl_branchregistrationmap';
		$where = $db->quoteInto('IdRegistration = ?', $idRegLoc);
		$db->delete($table, $where);
	}

	private function fncheckmappingexists($IdBranch,$IdRegistrationLoc,$Programme) {
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("a"=>"tbl_branchregistrationmap"),array("id"=>"a.IdBranch"))
		->where("a.Programme = $Programme")
		->where("a.IdBranch = $IdBranch")
		->where("a.RegistrationLoc = $IdRegistrationLoc");
		$larrresult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return (count($larrresult) == 0);
	}

	public function fninsertBranchregistration($larrregistration,$lintIdBranch) {  // function to insert po details
		$db = Zend_Db_Table::getDefaultAdapter();
		unset ($larrregistration['IdRegistration']);
		$larrprogramlist = $this->lobjprogram->fnGetProgramList();
		$larrregistrationlist = $this->fnGetRegistrationlocList();
		$countofregistration = count($larrregistration['RegistrationLocgrid']);
		for($i=0;$i<$countofregistration;$i++) {

			if($larrregistration['Programmegrid'][$i] == "all") {
				foreach ($larrprogramlist as $program) {
					$larrbranchregistrationmap = array(

										'IdBranch'=>$lintIdBranch,
										'RegistrationLoc'=>$larrregistration['RegistrationLocgrid'][$i],
										'Programme'=>$program['key']

					);
					if($this->fncheckmappingexists($lintIdBranch, $larrregistration['RegistrationLocgrid'][$i], $program['key'])) {
						$db->insert('tbl_branchregistrationmap',$larrbranchregistrationmap);
					}
				}
			}
			else {
				$larrbranchregistrationmap = array(

										'IdBranch'=>$lintIdBranch,
										'RegistrationLoc'=>$larrregistration['RegistrationLocgrid'][$i],
										'Programme'=>$larrregistration['Programmegrid'][$i]

				);
				if($this->fncheckmappingexists($lintIdBranch, $larrregistration['RegistrationLocgrid'][$i], $larrregistration['Programmegrid'][$i])) {
					$db->insert('tbl_branchregistrationmap',$larrbranchregistrationmap);
				}

			}


		}

	}

	/**
	 * Function to fetch Reg location based on branch ID
	 * @author vipul
	 */
	public function fnviewBranchRegData($IdBranch) { // Function to edit Purchase order details
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array("a"=>"tbl_branchregistrationmap"),array(''))
		->join(array('b' => 'tbl_registrationlocation'),"a.RegistrationLoc = b.IdRegistrationLocation",array("b.IdRegistrationLocation as key","b.RegistrationLocationCode as value"))
		->where("a.idBranch  = '$IdBranch'");
		$result = $this->fetchAll($select)->toArray();
		return $result;
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
	
	public function fnGetStdBranchList($program){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->distinct()
		->from(array("a"=>$this->_name),array("key"=>"a.IdBranch","value"=>"a.BranchName","code"=>"BranchCode"))
		->join(array('std'=>'tbl_studentregistration'),'a.IdBranch=std.IdBranch',array())
		->where("a.IdType = 1")
		->where("a.Active = 1")
		->where("std.IdProgram=?",$program)
		->where("a.BranchName != 'xxxxxx'")
		->order("BranchCode");
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
	
		return $larrResult;
	}
	
	
	public function getData($branchId=null){
		
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select()
					 ->from($this->_name)			
					 	 
				     ->where("IdBranch = ?",$branchId);			

		$row = $db->fetchRow($select);
		return $row;
	}


}
?>