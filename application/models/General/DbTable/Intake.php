<?php
class App_Model_General_DbTable_Intake extends Zend_Db_Table {
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
			//print_r($formData);

			$this->insert($formData);
			$insertId = $this->lobjDbAdpt->lastInsertId('tbl_intake','IdIntake');
			
			$this->fnAddperiod($insertId,$appSD,$appED);
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
		$lstrSelect = $this->lobjDbAdpt->select()->from($this->_name)
						->order("intakeid desc");
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
		$lstrSelect = $lobjDbAdpt->select()
				->from(array('a' => $this->_name),array("key" => "a.IdIntake" , "name" => "a.IntakeId"))
				->order('a.IntakeId DESC');
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
	
	public function fngetIntakeById($IdIntake="") { //Function to get the user details
		$select = $this->select()
		->setIntegrityCheck(false)
		->from(array('a' => $this->_name))
		->where("IdIntake = ?", $IdIntake);
		$result = $this->fetchRow($select);
		return $result->toArray();
	}

	public function fngetIntakesId($intakeid) {
		
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("a"=>"tbl_intake"),array("key"=>"a.IdIntake","value"=>"a.IntakeDesc"))
		->where('a.IntakeId=?',$intakeid);
		$larrResult = $this->lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
	}
	public function fngetIntakes() {
		$lstrSelect = $this->lobjDbAdpt->select()
		->from(array("a"=>"tbl_intake"),array("key"=>"a.IdIntake","value"=>"a.IntakeDesc"))
		->order('a.IntakeDesc DESC');
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
	
	public function fnAddperiod($intakeid,$sdate,$edate){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$ux_sdate = strtotime($sdate);
		$ux_edate = strtotime($edate);			
			
		$montharray=array(
			1 => "Januari",
			2 => "Februari",
			3 => "Maret",
			4 => "April",
			5 => "Mei",
			6 => "Juni",
			7 => "Juli",
			8 => "Agustus",
			9 => "September",
			10 => "Oktober",
			11 => "November",
			12 => "Desember",
		);
		
		$syear = date("Y",$ux_sdate);
		$smonthint = date("n",$ux_sdate);
		$smonth = date("m",$ux_sdate);
		//echo $smonth.$syear;
		$eyear = date("Y",$ux_edate);
		$emonthint = date("n",$ux_edate);
		$emonth = date("m",$ux_edate);
		
		if($syear<$eyear){
			for($i=$smonthint;$i<=12;$i++){
				$years[$syear][$i]=$montharray[$i];
			}
			for($i=1;$i<=$emonthint;$i++){
				$years[$eyear][$i]=$montharray[$i];
			}				
		}
		$num =1;
		foreach($years as $year=>$months){
			foreach($months as $month=>$monthname){
				if(strlen($month)==1){
					$month2="0".$month;
				}else{
					$month2=$month;
				}
				$period = array(
					'ap_intake_id' =>"$intakeid",
					'ap_month' =>$month,
					'ap_year' =>"$year",
					'ap_code' =>"$month2/$year",
					'ap_desc' =>"Periode $monthname $year",
					'ap_number' =>"$num",				
				);
			$lobjDbAdpt->insert("tbl_academic_period",$period);
			$num++;
			}		
		}

	}
	
	public function getCurrentIntakeBydate($curdate){
		$db = Zend_Db_Table::getDefaultAdapter();
		$sql = $db->select()
					->from(array("a"=>"tbl_intake"))
					->where("`ApplicationStartDate` <= ?",$curdate)
					->where("`ApplicationEndDate` >= ?",$curdate);
		$result = $db->fetchrow($sql);
		
		if(!is_array($result)){
			return false;
		}else{
			return $result["IdIntake"];
		}
	}
	public function getRangeIntakeBydate($startIntake,$endIntake){
		$db = Zend_Db_Table::getDefaultAdapter();
		//get start intake
		$sql = $db->select()
		->from(array("a"=>"tbl_intake"))
		->where("IdIntake=?",$startIntake);
		$result = $db->fetchrow($sql);
		$startdate=$result['ApplicationStartDate'];
		
		// get end intake
		$sql = $db->select()
		->from(array("a"=>"tbl_intake"))
		->where("IdIntake=?",$endIntake);
		$result = $db->fetchrow($sql);
		$enddate=$result['ApplicationStartDate'];
		
		//get range
		$sql = $db->select()
		->distinct()
		->from(array("a"=>"tbl_intake"),array("Intake"=>'left(IntakeId,9)'))
		->where("`ApplicationStartDate` >= ?",$startdate)
		->where("`ApplicationStartDate` <= ?",$enddate)
		->order("left(IntakeId,4)");
		$result = $db->fetchAll($sql);
	
		 return $result;
	}
	
}