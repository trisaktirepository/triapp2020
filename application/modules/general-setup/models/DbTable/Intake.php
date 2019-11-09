<?php
class GeneralSetup_Model_DbTable_Intake extends Zend_Db_Table {
	protected $_name = "tbl_intake";
	private $lobjDbAdpt;

	/**
	 *
	 * @see Zend_Db_Table_Abstract::init()
	 */
	public function init() {
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}

	/**
	 *
	 * Method to get all the branch list
	 */
	public function fngetBranchList() {
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("a"=>"tbl_branchofficevenue"),array("key"=>"a.IdBranch","value"=>"a.BranchName"))
		->where("a.Active = 1")
		->where("a.IdType = 1")
		->order("a.BranchName");
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fnaddIntake($formData) {
		unset($formData['Save']);
		unset($formData['IdProgram']);
		unset($formData['IdBranch']);
		unset($formData['Program']);
		unset($formData['Branch']);
		unset($formData['Faculty']);
		unset($formData['IdFaculty']);
		//asd($formData);
		$appSD = $formData['ApplicationStartDate'];
		$appED = $formData['ApplicationEndDate'];

		//$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $this->lobjDbAdpt->query("SELECT * FROM tbl_intake WHERE (  ( '".$appSD."' BETWEEN ApplicationStartDate AND ApplicationEndDate ) OR ( '".$appED."' BETWEEN ApplicationStartDate AND ApplicationEndDate ) ) ");
		$rows = $lstrSelect->fetchAll();

		//echo "SELECT * FROM tbl_intake WHERE (  ( '".$appSD."' BETWEEN ApplicationStartDate AND ApplicationEndDate ) OR ( '".$appED."' BETWEEN ApplicationStartDate AND ApplicationEndDate ) ) ";
		//echo "</br>";
		//echo "SELECT * FROM tbl_intake WHERE (  ( ApplicationStartDate BETWEEN '".$appSD."' AND '".$appED."' ) OR ( ApplicationEndDate BETWEEN '".$appSD."' AND '".$appED."' ) ) ";
		//die;

		$lstrSelect2 = $this->lobjDbAdpt->query("SELECT * FROM tbl_intake WHERE (  ( ApplicationStartDate BETWEEN '".$appSD."' AND '".$appED."' ) OR ( ApplicationEndDate BETWEEN '".$appSD."' AND '".$appED."' ) ) ");
		$rows2 = $lstrSelect2->fetchAll();

		if(count($rows)=='0' && count($rows2)=='0') {
			$this->insert($formData);
			$insertId = $this->lobjDbAdpt->lastInsertId('tbl_intake','IdIntake');
			return $insertId; } else {  return 'mismatch'; }
	}

	public function fnupdateIntake($formData, $IdIntake) {
		unset($formData['Save']);
		unset($formData['IdProgram']);
		unset($formData['IdBranch']);
		unset($formData['Program']);
		unset($formData['Branch']);
		unset($formData['Faculty']);
		unset($formData['IdFaculty']);
		$where = 'IdIntake = '.$IdIntake;
		$this->update($formData,$where);
	}

	public function fngetIntakeList() { //Function to get the Activity details
		$lstrSelect = $this->lobjDbAdpt->select()->from($this->_name);
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fnSearchIntake($post = array()) { //Function for searching the Activity details
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		->from(array("a"=>"tbl_intake"),array("a.*"))
		->where('a.IntakeId like "%" ? "%"',$post['field3'])
		->where('a.IntakeDefaultLanguage like "%" ? "%"',$post['field28'])
		->where('a.IntakeDesc like "%" ? "%"',$post['field4']);
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fngetallIntake() {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()->from(array('a' => $this->_name),array("key" => "a.IdIntake" , "value" => "a.IntakeId"));
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fngetallIntakelist() {
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()->from(array('a' => $this->_name),array("key" => "a.IdIntake" , "name" => "a.IntakeId"));
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fngetIntakeDetails($IdIntake="") { //Function to get the user details
		$select = $this->select()
		->setIntegrityCheck(false)
		->join(array('a' => $this->_name),array("$IdIntake"))
		->where("IdIntake = $IdIntake");
		$result = $this->fetchAll($select);
		return $result->toArray();
	}

	public function fngetIntakes() {
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("a"=>"tbl_intake"),array("key"=>"a.IdIntake","value"=>"a.IntakeDesc"));
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

	public function fnfetchIntakeCode($lintintakeid) {
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("a"=>"tbl_intake"),array("a.IntakeId"))
		->where("IdIntake = ?",$lintintakeid);
		$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}
}