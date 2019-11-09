<?php
use Zend\Validator\File\Md5;
class App_Model_Common {

		public function fnPagination($larrresult,$page,$lintpagecount) { // Function for pagination
			$paginator = Zend_Paginator::factory($larrresult); //instance of the pagination
			$paginator->setItemCountPerPage($lintpagecount);
			$paginator->setCurrentPageNumber($page);
			return $paginator;
		}

		public function fnCovertDateToLocalFormat($date,$type=null) { // Function for pagination
			
			$months = array('01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'Nopember','12'=>'Desember');
			$months2 = array('01'=>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'Mei','06'=>'Jun','07'=>'Jul','08'=>'Ags','09'=>'Sep','10'=>'Okt','11'=>'Nop','12'=>'Des');
			$timestamp = strtotime($date);
			$day=date("d", $timestamp);
			if ($type!=null) $month = $months2[date ("m",$timestamp)];
			else $month = $months[date ("m",$timestamp)];
			$year=date('Y',$timestamp);
			$dateformat=$day.' '.$month.' '.$year;
			
			return $dateformat;
			
		}
		
		public function fnCovertDateToEnglishFormat($date,$type=null) { // Function for pagination
				
			$months = array('01'=>'January','02'=>'February','03'=>'March','04'=>'April','05'=>'May','06'=>'June','07'=>'July','08'=>'August','09'=>'September','10'=>'October','11'=>'November','12'=>'December');
			$months2 = array('01'=>'Jan','02'=>'Feb','03'=>'Mar','04'=>'Apr','05'=>'May','06'=>'Jun','07'=>'Jul','08'=>'Aug','09'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Des');
			$timestamp = strtotime($date);
			$day=date("d", $timestamp);
			if ($type!=null) $month = $months2[date ("m",$timestamp)];
			else $month = $months[date ("m",$timestamp)];
			$year=date('Y',$timestamp);
			$dateformat=$month.' '.$day.', '.$year;
				
			return $dateformat;
				
		}
		
		public function fnCovertMonthToRomawi($date) { // Function for pagination
				
			$months = array('01'=>'I','02'=>'II','03'=>'III','04'=>'IV','05'=>'V','06'=>'VI','07'=>'VII','08'=>'VIII','09'=>'IX','10'=>'X','11'=>'XI','12'=>'XII');
			$timestamp = strtotime($date);
			$month = $months[date("m",$timestamp)];
			//echo $month; echo date("m",$timestamp);
			//exit;
			return $month;
		}
		//Get Student Id
		public function fnGetStudentId($IdStudent){
			$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
			$lstrSelect = $lobjDbAdpt->select()
    	   							->from(array("a" => "tbl_studentapplication"),array("StudentId"))
    	   							->where("a.IDApplication = ?",$IdStudent)
    	   							->where("a.Termination = 0")
    	   							->where("a.Active = 1");
			$larrResult = $lobjDbAdpt->fetchRow($lstrSelect);
			return $larrResult['StudentId'];
		}
		//get academic year
		public function fnGetAcademicYear(){
			$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
			$lstrSelect = $lobjDbAdpt->select()
			->from(array("a" => "tbl_academic_year"),array("a.ay_id","a.ay_code"))
			->order("a.ay_code DESC");
			$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
			return $larrResult;
		}
		//Get Student Name
		public function fnGetStudentNamebyid($IdStudent){
			$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
			$lstrSelect = $lobjDbAdpt->select()
    	   							->from(array("a" => "tbl_studentapplication"),array("CONCAT(a.FName,' ', IFNULL(a.MName,' '),' ',IFNULL(a.LName,' ')) AS StudentName"))
    	   							->where("a.IDApplication = ?",$IdStudent)
    	   							->where("a.Termination = 0")
    	   							->where("a.Active = 1");
			$larrResult = $lobjDbAdpt->fetchRow($lstrSelect);
			return $larrResult['StudentName'];
		}

		//Get Student Details
		public function fnGetStudentDetailsByid($IdStudent){
			$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
			$lstrSelect = $lobjDbAdpt->select()
    	   							->from(array("a" => "tbl_studentapplication"),array("CONCAT(a.FName,' ', IFNULL(a.MName,' '),' ',IFNULL(a.LName,' ')) AS StudentName","a.StudentId"))
    	   							->where("a.IDApplication = ?",$IdStudent)
    	   							->where("a.Termination = 0")
    	   							->where("a.Active = 1");
			$larrResult = $lobjDbAdpt->fetchRow($lstrSelect);
			return $larrResult;
		}

		//Get Registration Offer Template
		public function fnGetRegistrationOffer(){
			$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
			$lstrSelect = $lobjDbAdpt->select()
    	   							->from(array("a"=>"tbl_emailtemplate"))
       								->join(array("b" => "tbl_definationms"),"a.idDefinition = b.idDefinition",array(""))
       								->where("b.DefinitionDesc LIKE ?","%"."Student Registration");
       		$larrResult = $lobjDbAdpt->fetchRow($lstrSelect);
       		return $larrResult;
		}

	public function fnGetTranscriptGroup() {
			$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
			$select = $lobjDbAdpt->select()
				->join(array('a' => 'tbl_definationms'),array('a.idDefinition','a.BahasaIndonesia','a.DefinitionCode'))
				->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
				->where('b.defTypeDesc = "Transcript Profile"')
				->where('a.Status = 1')
                ->where('b.Active = 1')
                ->order("b.defTypeDesc");
			$result = $lobjDbAdpt->fetchAll($select);
			return $result;
		}
		public function generateSecureCode($text) {
		
			$code = md5($text);
			$code = substr($code, 0,8).'-'.substr($code, 8,8).'-'.substr($code, 16,8).'-'.substr($code, 24,8);
			return $code;
		}
		public function fnGetSalutation($id) {
			$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
			$select = $lobjDbAdpt->select()
			->join(array('a' => 'tbl_definationms'),array('a.BahasaIndonesia','a.DefinitionCode'))
			->where('a.Status = 1')
			->where('a.idDefinition=?',$id);
			$result = $lobjDbAdpt->fetchRow($select);
			return $result;
		}
		
		public function fnGetSupervisor() {
			$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
			$select = $lobjDbAdpt->select()
				->join(array('a' => 'tbl_definationms'),array('a.idDefinition','a.BahasaIndonesia','a.DefinitionCode'))
				->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
				->where('b.defTypeDesc = "Supervisor"')
				->where('a.Status = 1')
                ->where('b.Active = 1')
                ->order("b.defTypeDesc");
			$result = $lobjDbAdpt->fetchAll($select);
			return $result;
		}
		
		public function fnGetSurveyType() {
			$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
			$select = $lobjDbAdpt->select()
			->join(array('a' => 'tbl_definationms'),array('key'=>'idDefinition','value'=>'BahasaIndonesia'))
			->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
			->where('b.defTypeDesc = "Survey Type"')
			->where('a.Status = 1')
			->where('b.Active = 1')
			->order("b.defTypeDesc");
			$result = $lobjDbAdpt->fetchAll($select);
			return $result;
		}
		
		public function fnGetFrontSalutation() {
			$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
			$select = $lobjDbAdpt->select()
			->join(array('a' => 'tbl_definationms'),array('key'=>'idDefinition','value'=>'BahasaIndonesia'))
			->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
			->where('b.defTypeDesc = "Salutation"')
			->where('a.Status = 1')
			->where('b.Active = 1')
			->order("b.defTypeDesc");
			$result = $lobjDbAdpt->fetchAll($select);
			return $result;
		}
		public function fnGetBackSalutation() {
			$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
			$select = $lobjDbAdpt->select()
			->join(array('a' => 'tbl_definationms'),array('key'=>'idDefinition','value'=>'BahasaIndonesia'))
			->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
			->where('b.defTypeDesc = "Back Salutation"')
			->where('a.Status = 1')
			->where('b.Active = 1')
			->order("b.defTypeDesc");
			$result = $lobjDbAdpt->fetchAll($select);
			return $result;
		}
		public function fnGetTypeOfSurvey() {
			$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
			$select = $lobjDbAdpt->select()
			->join(array('a' => 'tbl_definationms'),array('key'=>'idDefinition','value'=>'BahasaIndonesia'))
			->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
			->where('b.defTypeDesc = "Type of Survey"')
			->where('a.Status = 1')
			->where('b.Active = 1')
			->order("b.defTypeDesc");
			$result = $lobjDbAdpt->fetchAll($select);
			return $result;
		}
		
public function fnGetProcessCategory($categories=null) {
			$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
			$select = $lobjDbAdpt->select()
			->join(array('a' => 'tbl_definationms'),array('key'=>'idDefinition','value'=>'BahasaIndonesia'))
			->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
			->where('b.defTypeDesc = "Process Category"')
			->where('a.Status = 1')
			->where('b.Active = 1')
			->order("b.defTypeDesc");
			if ($categories!=null) {
				$dimensions='(';
				foreach ($categories as $dimension) {
					
					if ($dimensions=='(') $dimensions=$dimensions.$dimension['IdDimension'];
					else $dimensions=$dimensions.','.$dimension['IdDimension'];
				}
				$dimensions=$dimensions.')';
				//echo $dimensions;exit;
				$select->where('a.idDefinition in  '.$dimensions);
			}
			$result = $lobjDbAdpt->fetchAll($select);
			return $result;
		}
		public function fnGetScaleType() {
			$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
			$select = $lobjDbAdpt->select()
			->join(array('a' => 'tbl_definationms'),array('key'=>'idDefinition','value'=>'BahasaIndonesia'))
			->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
			->where('b.defTypeDesc = "Scale Type"')
			->where('a.Status = 1')
			->where('b.Active = 1')
			->order("b.defTypeDesc");
			$result = $lobjDbAdpt->fetchAll($select);
			return $result;
		}
		public function fnGetScaleName($scaleid) {
			$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
			$select = $lobjDbAdpt->select()
			->from(array('a' => 'tbl_definationms'))
			//->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
			->where('a.idDefinition = ?',$scaleid)
			->where('a.Status = 1');
			//->where('b.Active = 1')
			//->order("b.defTypeDesc");
			$result = $lobjDbAdpt->fetchRow($select);
			return $result;
		}
		public function fnGetQuestionCategory($categories=null) {
			$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
			$select = $lobjDbAdpt->select()
			->join(array('a' => 'tbl_definationms'),array('key'=>'idDefinition','value'=>'BahasaIndonesia'))
			->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
			->where('b.defTypeDesc = "Question Category"')
			->where('a.Status = 1')
			->where('b.Active = 1')
			->order("b.defTypeDesc");
			if ($categories!=null) {
				$dimensions='(';
				foreach ($categories as $dimension) {
					
					if ($dimensions=='(') $dimensions=$dimensions.$dimension['IdDimension'];
					else $dimensions=$dimensions.','.$dimension['IdDimension'];
				}
				$dimensions=$dimensions.')';
				//echo $dimensions;exit;
				$select->where('a.idDefinition in  '.$dimensions);
			}
			$result = $lobjDbAdpt->fetchAll($select);
			return $result;
		}
			//Get Registration Email Template
		public function  fnGetRegistrationEmailTemplate(){
			$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
			$lstrSelect = $lobjDbAdpt->select()
    	   							->from(array("a"=>"tbl_emailtemplate"))
       								->join(array("b" => "tbl_definationms"),"a.idDefinition = b.idDefinition",array(""))
       								->where("b.DefinitionDesc LIKE ?","%"."Portal Login Template");
       		$larrResult = $lobjDbAdpt->fetchRow($lstrSelect);
       		return $larrResult;
		}

		//Function To Get SMTP Settings From Initial Config
		public function fnGetInitialConfigDetails($iduniversity){
			$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
			$lstrSelect = $lobjDbAdpt->select()
       								->from(array("a"=>"tbl_config"),array("a.SMTPServer","a.SMTPUsername","a.SMTPPassword","a.SMTPPort","a.SSL","a.DefaultEmail") )
       								->where("a.idUniversity = ?",$iduniversity);
			$larrResult = $lobjDbAdpt->fetchRow($lstrSelect);
			return $larrResult;
		}

		//Get List Of States From Country's Id
		public function fnGetCountryStateList($idCountry){
			$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	   		$lstrSelect = $lobjDbAdpt->select()
					 				 ->from(array("a"=>"tbl_state"),array("key"=>"a.idState","value"=>"StateName"))
					 				 ->where("a.idCountry = ?",$idCountry)
					 				 ->where("a.Active = 1")
					 				 ->order("a.StateName");
			$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
			return $larrResult;
		}
		public function fnGetCityList($lintidState){
			$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
			$lstrSelect = $lobjDbAdpt->select()
				 				 ->from(array("a"=>"tbl_city"),array("key"=>"a.idCity","value"=>"a.CityName"))
				 				  ->where("a.idState= ?",$lintidState)
				 				 ->where("a.Active = 1")
				 				 ->order("a.CityName");
			$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
			return $larrResult;
		}
		//Get Countries List
		public function fnGetCountryList(){
			$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	   		$lstrSelect = $lobjDbAdpt->select()
					 				 ->from(array("a"=>"tbl_countries"),array("key"=>"a.idCountry","value"=>"CountryName"))
					 				 ->where("a.Active = 1")
					 				 ->order("a.CountryName");
			$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
			return $larrResult;
		}
		//Get School Namres

	public function fnGetSchoolList(){
			$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	   		$lstrSelect = $lobjDbAdpt->select()
					 				 ->from(array("a"=>"tbl_schoolmaster"),array("key"=>"a.idSchool","value"=>"SchoolName"))
					 				 ->where("a.Active = 1")
					 				 ->order("a.SchoolName");
			$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
			return $larrResult;
		}

		//Get All Active Student Names List
		public function fnGetAllActiveStudentNamesList() {
			$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	   		$lstrSelect = $lobjDbAdpt->select()
       								->from(array("a"=>"tbl_studentapplication"),array("key"=>"a.IDApplication","value"=>"CONCAT(a.fName,' ', IFNULL(a.mName,' '),' ',IFNULL(a.lName,' '))") )
					 				->where("a.Active = 1")
					  				->where("a.Termination = 0");
			$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
			return $larrResult;
		}

		//Get All Active Student Ids List
		public function fnGetAllActiveStudentIdsList() {
			$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	   		$lstrSelect = $lobjDbAdpt->select()
       								->from(array("a"=>"tbl_studentapplication"),array("key"=>"a.IDApplication","value"=>"a.StudentId") )
					 				->where("a.Active = 1")
					  				->where("a.Termination = 0");
			$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
			return $larrResult;
		}

		//Get State List
		public function fnGetStateList(){
			$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	   		$lstrSelect = $lobjDbAdpt->select()
					 				 ->from(array("a"=>"tbl_state"),array("key"=>"a.idState","value"=>"StateName"))
					 				 ->where("a.Active = 1")
					 				 ->order("a.StateName");
			$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
			return $larrResult;
		}


		//Function To Reset The Array ie., from name to key
		public function fnResetArrayFromNamesToValues($OrginialArray){
			$larrNewArr = array();
			$OrgnArray = @array_values($OrginialArray);
			for($lintI=0;$lintI<count($OrgnArray);$lintI++){
				$larrNewArr[$lintI]["value"] = $OrgnArray[$lintI]["name"];
				$larrNewArr[$lintI]["key"] = $OrgnArray[$lintI]["key"];
			}
			return $larrNewArr;
		}

		//Function To Reset The Array ie., from Value to Name
		public function fnResetArrayFromValuesToNames($OrginialArray){
			$larrNewArr = array();
			$OrgnArray = @array_values($OrginialArray);
			for($lintI=0;$lintI<count($OrgnArray);$lintI++){
				$larrNewArr[$lintI]["name"] = $OrgnArray[$lintI]["value"];
				$larrNewArr[$lintI]["key"] = $OrgnArray[$lintI]["key"];
			}
			return $larrNewArr;
		}

		public function fngetLanguage($lintIdCountry){
			$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
			$lstrSelect = $lobjDbAdpt->select()
       								->from(array("a"=>"tbl_countries"),array("a.DefaultLanguage"))
       								->join(array('b' => 'tbl_definationms'),'a.DefaultLanguage = b.idDefinition',array('b.DefinitionDesc'))
       								->where('a.idCountry = ?',$lintIdCountry)
       								->order("b.DefinitionDesc");
			$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
			return $larrResult;
		}


		//Function To Reset The Array ie., from Value to Name Along With Status
		public function fnResetArrayFromValuesToNamesWithStatus($OrginialArray){
			$larrNewArr = array();
			$OrgnArray = @array_values($OrginialArray);
			for($lintI=0;$lintI<count($OrgnArray);$lintI++){
				$larrNewArr[$lintI]["key"] = $OrgnArray[$lintI]["key"];
				$larrNewArr[$lintI]["name"] = $OrgnArray[$lintI]["value"];
				$larrNewArr[$lintI]["Status"] = $OrgnArray[$lintI]["Status"];
			}
			return $larrNewArr;
		}

		public function fnGetRoleDetails() {
			$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
			$select = $lobjDbAdpt->select()
				->join(array('a' => 'tbl_definationms'),array('idDefinition'))
				->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
				->where('b.defTypeDesc = "Role"')
				->where('a.Status = 1')
                ->where('b.Active = 1')
                ->order("b.defTypeDesc");
			$result = $lobjDbAdpt->fetchAll($select);
			return $result;
		}

		public function fnGetdocumentuploadDetails() {
			$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
			$select = $lobjDbAdpt->select()
				->join(array('a' => 'tbl_definationms'),array('idDefinition'))
				->join(array('b' => 'tbl_definationtypems'),'a.idDefType = b.idDefType',array('b.idDefType'))
				->where('b.defTypeDesc = "Documents Upload Type"')
				->where('a.Status = 1')
                ->where('b.Active = 1')
                ->order("b.defTypeDesc");
			$result = $lobjDbAdpt->fetchAll($select);
			return $result;
		}

		public function fnGetRoleName($idrole){
			$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
			$lstrSelect = $lobjDbAdpt->select()
       								->from(array("a"=>"tbl_definationms"),array("a.DefinitionDesc") )
       								->where('a.idDefinition = ?',$idrole);
			$larrResult = $lobjDbAdpt->fetchRow($lstrSelect);
			return $larrResult;
		}

		public function fnGetStaff($idStaff){
			$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
			$lstrSelect = $lobjDbAdpt->select()
       								->from(array("a"=>"tbl_staffmaster"),array("a.StaffType","a.IdCollege","a.IdDepartment") )
       								->where('a.IdStaff = ?',$idStaff);
       								
			$larrResult = $lobjDbAdpt->fetchRow($lstrSelect);
			return $larrResult;
		}

		public function fnGetUniversity($idCollege){
			$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
			$lstrSelect = $lobjDbAdpt->select()
       								->from(array("a"=>"tbl_collegemaster"),array("a.AffiliatedTo"))
       								->where('a.IdCollege = ?',$idCollege);
			$larrResult = $lobjDbAdpt->fetchRow($lstrSelect);
			return $larrResult;
		}


		public function fnGetStaffDetails($idStaff){
			$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
			$lstrSelect = $lobjDbAdpt->select()
       								->from(array("a"=>"tbl_staffmaster"),array("a.StaffType","a.IdCollege"))
       								->where('a.IdStaff = ?',$idStaff);
			$larrResult = $lobjDbAdpt->fetchRow($lstrSelect);
			return $larrResult;
		}

		public function fnGetCollegeDetails($idCollege=null){
			$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
			$lstrSelect = $lobjDbAdpt->select()
       								->from(array("a"=>"tbl_collegemaster"),array("a.AffiliatedTo","a.IdCollege","a.CollegeName"));
       								
			if ($idCollege!=null) {
				$lstrSelect->where('a.IdCollege = ?',$idCollege);
				$larrResult = $lobjDbAdpt->fetchRow($lstrSelect);
			}
			else 	$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
			
			return $larrResult;
		}

		public function fnGetApplicationEmailTemplate(){
			$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
			$lstrSelect = $lobjDbAdpt->select()
    	   							->from(array("a"=>"tbl_emailtemplate"))
       								->join(array("b" => "tbl_definationms"),"a.idDefinition = b.idDefinition",array(""))
       								->where("b.DefinitionDesc LIKE ?","%"."Student Approval");
       		$larrResult = $lobjDbAdpt->fetchRow($lstrSelect);
       		return $larrResult;
		}

	}