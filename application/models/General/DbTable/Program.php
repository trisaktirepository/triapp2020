<?php

class App_Model_General_DbTable_Program extends Zend_Db_Table_Abstract {

  protected $_name = 'tbl_program';
  private $lobjDbAdpt;

  public function init() {
    $this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
  }

 public function fngetProgramData($program_id=0) { //Function to get the user details
  	
  	if($program_id!=0){
  		$lstrSelect = $this->lobjDbAdpt->select()
                    ->from(array("a" => "tbl_program"))
                    ->where("a.Active = 1")
                    ->where("a.IdProgram =?", $program_id)
                    ->order("a.ProgramName");
  
    	$result = $this->lobjDbAdpt->fetchRow($lstrSelect);
    	
  	}
  	
  	if($result){
  		return $result;	
  	}else{
  		return null;
  	}
  	
    
  }
  
  public function fngetProgramDetails($collegeId=0) { //Function to get the user details
  	
  	if($collegeId!=0){
  		$lstrSelect = $this->lobjDbAdpt->select()
                    ->from(array("a" => "tbl_program"))
                    ->where("a.Active = 1")
                    ->where("a.IdCollege =?", $collegeId)
                    ->order("a.ProgramName");
  
    	$result = $this->lobjDbAdpt->fetchAll($lstrSelect);
    	
  	}else{
  		$result = $this->fetchAll();
  	}
  	
    return $result;
  }

  public function fngethistory($IdProgram) {
    $lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
    $lstrSelect = $lobjDbAdpt->select()
                    ->from(array("a" => "tbl_programhistory"), array("a.*"))
                    ->join(array("b" => "tbl_user"), 'a.UpdUser = b.iduser', array("b.loginName"))
                    ->where("a.IdProgram = ?", $IdProgram)
                    ->order("b.loginName");
    $larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
    return $larrResult;
  }

  public function fnGetAccreditationList() {
    $lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
    $lstrSelect = $lobjDbAdpt->select()
                    ->from(array("a" => "tbl_definationms"), array("key" => "a.idDefinition", "value" => "a.DefinitionDesc"))
                    ->join(array("b" => "tbl_definationtypems"), "a.idDefType = b.idDefType AND defTypeDesc='Accreditation Type'")
                    ->where("a.Status = 1")
                    ->order('a.DefinitionDesc');
    $larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
    return $larrResult;
  }

  //---------------
  public function fnGetSalutationList() {
    $lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
    $lstrSelect = $lobjDbAdpt->select()
                    ->from(array("a" => "tbl_definationms"), array("key" => "a.idDefinition", "value" => "a.DefinitionCode"))
                    ->join(array("b" => "tbl_definationtypems"), "a.idDefType = b.idDefType AND defTypeDesc='Program Salutation'")
                    ->where("a.Status = 1")
                    ->order("a.DefinitionDesc");
    $larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
    return $larrResult;
  }

  //--------------


  public function fnaddProgram($formData) { //Function for adding the University details to the table
    unset($formData['IdProgramMajoring']);
    unset($formData['IdMajorggrid']);
    unset($formData['IdMajor']);
    unset($formData['IdDepartment']);
    unset($formData['programSalutation']);
    unset($formData['IdQuota']);
    unset($formData['Quota']);
    unset($formData['IdQuotagrid']);
    unset($formData['Quotagrid']);
    $this->insert($formData);
    $lobjdb = Zend_Db_Table::getDefaultAdapter();
    return $lobjdb->lastInsertId();
  }

  public function fnupdateProgram($formData, $lintIdProgram) { //Function for updating the university
    unset($formData['IdProgramMajoring']);
    unset($formData['IdMajorggrid']);
    unset($formData['IdMajor']);
    unset($formData['IdDepartment']);
    unset($formData['programSalutation']);
    unset($formData['IdQuota']);
    unset($formData['Quota']);
    unset($formData['IdQuotagrid']);
    unset($formData['Quotagrid']);
    unset($formData ['Save']);
    $where = 'IdProgram = ' . $lintIdProgram;
    $this->update($formData, $where);
  }

  public function fnupdateHistory($history, $lintIdProgram) {
    $db = Zend_Db_Table::getDefaultAdapter();
    $table = "tbl_programhistory";
    $sessionID = Zend_Session::getId();
    $larrprogramhistory = array('IdProgram' => $lintIdProgram,
        'UpdDate' => $history['UpdDate'],
        'UpdUser' => $history['UpdUser'],
        'Active' => $history['Active'],
        'Remarks' => $history['Remarks']);
    $db->insert($table, $larrprogramhistory);
  }

  public function fnSearchProgram($post = array()) { //Function for searching the university details
    $field7 = "Active = " . $post["field7"];
    $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->join(array('a' => 'tbl_program'), array('IdProgram'))
                    ->where('a.ProgramName  like "%" ? "%"', $post['field3'])
                    ->where('a.ShortName  like "%" ? "%"', $post['field2'])
                    ->where('a.ArabicName   like "%" ? "%"', $post['field4'])
                    ->where($field7);
    if (isset($post['field5']) && !empty($post['field5'])) {
      $select = $select->where("a.Award  = ?", $post['field5']);
    }
    $select->order("a.ProgramName");
    $result = $this->fetchAll($select);
    return $result->toArray();
  }

  public function fnGetProgramList() {
    $lstrSelect = $this->lobjDbAdpt->select()
                    ->from(array("a" => "tbl_program"), array("key" => "a.IdProgram", "value" => "CONCAT(ProgramName,'(',Strata,')'"))
                    ->where("a.Active = 1")
                    ->order("a.ProgramName");
    $larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
    return $larrResult;
  }
  
  public function fnGetProgramListCreditTransfer() {
  	$lstrSelect = $this->lobjDbAdpt->select()
  	->from(array("a" => "tbl_program"), array("key" => "a.IdProgram", "value" => "CONCAT(ProgramName,'(',Strata,')')"))
  	->where("a.Active = 1")
  	->where("a.CreditTransferOffer = '1'")
  	->order("a.ProgramName");
  	//echo $lstrSelect;exit;
  	$larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
  	return $larrResult;
  }

  //Get
  public function fnProgramedit($IdProgram) { //Function to get the Program Branch details
    $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->join(array('a' => 'tbl_program'), array('IdProgram'))
                    ->join(array('b' => 'tbl_programquota'), 'a.IdProgram  = b.IdProgram')
                    ->join(array('c' => 'tbl_definationms'), 'b.IdQuota = c.idDefinition')
                    ->where('a.IdProgram = ?', $IdProgram);
    $result = $this->fetchAll($select);
    return $result->toArray();
  }

  public function fnViewProgramquota($IdProgram) { // Function to view the Purchase Order details based on id
    $result = $this->fetchRow("IdProgram = '$IdProgram'");
    return $result;
  }

  public function fninserttempprogramquotadetails($larrprogramquotaresult) {  // function to insert po details
    $db = Zend_Db_Table::getDefaultAdapter();
    $table = "tbl_tempprogramquota";
    $sessionID = Zend_Session::getId();
    foreach ($larrprogramquotaresult as $formData) {
      $larrprogramquota = array('IdProgramQuota' => $formData['IdProgramQuota'],
          'IdProgram' => $formData['IdProgram'],
          'IdQuota' => $formData['IdQuota'],
          'Quota' => $formData['Quota'],
          'UpdDate' => $formData['UpdDate'],
          'UpdUser' => $formData['UpdUser'],
          'Date' => date("Y-m-d"),
          'sessionId' => $sessionID,
          'deleteFlag' => 1,
          'idExists' => $formData['IdProgramQuota']
      );

      $db->insert($table, $larrprogramquota);
    }
  }

  public function fnGetTempprogramquotaDetails($IdProgram) { // Function to edit Purchase order details
    $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->join(array('a' => 'tbl_tempprogramquota'), array('a.idTemp'))
                    ->join(array('c' => 'tbl_definationms'), 'a.IdQuota = c.idDefinition')
                    ->where('a.deleteFlag =1')
                    ->where("a.IdProgram  = '$IdProgram'");
    $result = $this->fetchAll($select);
    return $result;
  }

  public function fnUpdateTempprogramquotadetails($idTemp) {  // function to update po details
    $db = Zend_Db_Table::getDefaultAdapter();
    $table = "tbl_tempprogramquota";
    $larramounts = array('deleteFlag' => '0');
    $where = "idTemp = '" . $idTemp . "'";
    $db->update($table, $larramounts, $where);
  }

  public function fnDeleteTempprogramquotaDetailsBysession($sessionID) { //Function for Delete Purchase order terms
    $db = Zend_Db_Table::getDefaultAdapter();
    $table = "tbl_tempprogramquota";
    $where = $db->quoteInto('sessionId = ?', $sessionID);
    $db->delete($table, $where);
  }

  public function fnaddAccreditiondetails($larrformData, $result, $count) {  // function to insert po details
    $db = Zend_Db_Table::getDefaultAdapter();
    $table = "tbl_programaccreditiondetails";
    if (isset($larrformData['AccDtlgrid'])) {
      for ($k = 1; $k <= $count; $k++) {
        $accredictioncount = count($larrformData['AccDtlgrid' . $k]);
      }
      for ($i = 0; $i < $accredictioncount; $i++) {
        $larrreglst['IdProgram'] = $result;


        for ($j = 1; $j <= $count; $j++) {
          $larrreglst['AccDtl' . $j] = $larrformData['AccDtlgrid' . $j][$i];
        }
        $larrreglst['AccreditionType'] = $larrformData['AccreditionTypegrid'][$i];
        $larrreglst['AccredictionReferences'] = $larrformData['AccreditionRferencegrid'][$i];
        $larrreglst['AccredictionDate'] = $larrformData['AccreditionDategrid'][$i];
        $larrreglst['UpdUser'] = $larrformData['UpdUser'];
        $larrreglst['UpdDate'] = $larrformData['UpdDate'];
        //print_r($larrreglst);die;
        $db->insert($table, $larrreglst);
      }
    } else {
      return;
    }
  }

  public function fnaddMohedetails($larrformData, $result, $count) {  // function to insert po details
    $db = Zend_Db_Table::getDefaultAdapter();
    $table = "tbl_programmohedetails";

    $larrreglst['IdProgram'] = $result;
    for ($i = 1; $i <= $count; $i++) {
      $larrreglst['MoheDtl' . $i] = $larrformData['MoheDtl' . $i];
    }

    if ($larrformData['MoheDate'] == "") {
      $larrreglst['MoheDate'] = NULL;
    } else {
      $larrreglst['MoheDate'] = $larrformData['MoheDate'];
    }
    $larrreglst['MoheReferences'] = $larrformData['MoheReferences'];
    $larrreglst['UpdUser'] = $larrformData['UpdUser'];
    $larrreglst['UpdDate'] = $larrformData['UpdDate'];
    $db->insert($table, $larrreglst);
  }

  public function fnaddLearningMode($larrformData, $idprogram) {  // function to insert po details
    $db = Zend_Db_Table::getDefaultAdapter();
    $table = "tbl_programlearningmodedetails";
    $countofLearningmode = count($larrformData['LearningMode']);
    for ($i = 0; $i < $countofLearningmode; $i++) {
      $larrcharges = array('IdProgram' => $idprogram,
          'IdLearningMode' => $larrformData['LearningMode'][$i],
          'UpdDate' => $larrformData['UpdDate'],
          'UpdUser' => $larrformData['UpdUser']
      );
      $db->insert($table, $larrcharges);
    }
  }

  public function fnviewAccredentialDetails($IdProgram) { //Function for the view user
    $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from(array('a' => 'tbl_programaccreditiondetails'), array('a.*'))
                    ->where("a.IdProgram= ?", $IdProgram);
    $result = $this->fetchAll($select);
    return $result;
  }

  public function fnviewMoheDetails($IdProgram) { //Function for the view user
    $lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
    $select = $lobjDbAdpt->select()
                    ->from(array("a" => "tbl_programmohedetails"), array("a.*"))
                    ->where("a.IdProgram= ?", $IdProgram);
    return $result = $lobjDbAdpt->fetchRow($select);
  }

  public function fnviewLearningModeDetails($IdProgram) { // Function to edit Purchase order details
    $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->join(array('a' => 'tbl_programlearningmodedetails'), array('a.*'))
                    ->where("a.IdProgram  = '$IdProgram'");
    //echo $select;die();
    $result = $this->fetchAll($select);
    return $result;
  }

  public function fnUpdateAccarditionDetails($formData, $lintIdProgram, $count) {  // function to update po details
    $db = Zend_Db_Table::getDefaultAdapter();
    $table = "tbl_programaccreditiondetails";
    for ($i = 1; $i <= $count; $i++) {
      $larrreglst['AccDtl' . $i] = $formData['AccDtl' . $i];
    }
    $larrreglst['AccreditionType'] = $formData['AccreditionType'];
    $larrreglst['UpdUser'] = $formData['UpdUser'];
    $larrreglst['UpdDate'] = $formData['UpdDate'];
    $where = "IdProgram = '" . $lintIdProgram . "'";
    $db->update($table, $larrreglst, $where);
  }

  public function fnUpdateMoheDetails($formData, $lintIdProgram, $count) {  // function to update po details
    $db = Zend_Db_Table::getDefaultAdapter();
    $table = "tbl_programmohedetails";
    for ($i = 1; $i <= $count; $i++) {
      $larrreglst['MoheDtl' . $i] = $formData['MoheDtl' . $i];
    }

    if ($formData['MoheDate'] == "") {
      $larrreglst['MoheDate'] = NULL;
    } else {
      $larrreglst['MoheDate'] = $formData['MoheDate'];
    }
    $larrreglst['MoheReferences'] = $formData['MoheReferences'];
    $larrreglst['UpdUser'] = $formData['UpdUser'];
    $larrreglst['UpdDate'] = $formData['UpdDate'];
    $where = "IdProgram = '" . $lintIdProgram . "'";
    $db->update($table, $larrreglst, $where);
  }

  public function fnDeleteLearningMode($lintIdProgram) { //Function for Delete Purchase order terms
    $db = Zend_Db_Table::getDefaultAdapter();
    $table = "tbl_programlearningmodedetails";
    $where = $db->quoteInto('IdProgram = ?', $lintIdProgram);
    $db->delete($table, $where);
  }

  public function fnGetAwardCode($idaward) {
    $db = Zend_Db_Table::getDefaultAdapter();
    $select = $db->select()
                    ->from(array('c' => 'tbl_definationms'), array('c.*'))
                    ->where("c.idDefinition  = '$idaward'");
    $result = $db->fetchRow($select);
    return $result;
  }

  public function fninserttempAccredictitionrequriments($accredintialdtls, $idprogram) {  // function to insert po details
    $db = Zend_Db_Table::getDefaultAdapter();
    $table = "tbl_tempprogramaccreditiondetails";
    $sessionID = Zend_Session::getId();
    foreach ($accredintialdtls as $formData) {
      $larrtepaccridiction = array(
          'AccredictionDate' => $formData['AccredictionDate'],
          'AccredictionReferences' => $formData['AccredictionReferences'],
          'AccDtl1' => $formData['AccDtl1'],
          'AccDtl2' => $formData['AccDtl2'],
          'AccDtl3' => $formData['AccDtl3'],
          'AccDtl4' => $formData['AccDtl4'],
          'AccDtl5' => $formData['AccDtl5'],
          'AccDtl6' => $formData['AccDtl6'],
          'AccDtl7' => $formData['AccDtl7'],
          'AccDtl8' => $formData['AccDtl8'],
          'AccDtl9' => $formData['AccDtl9'],
          'AccDtl10' => $formData['AccDtl10'],
          'AccDtl11' => $formData['AccDtl11'],
          'AccDtl12' => $formData['AccDtl12'],
          'AccDtl13' => $formData['AccDtl13'],
          'AccDtl14' => $formData['AccDtl14'],
          'AccDtl15' => $formData['AccDtl15'],
          'AccDtl16' => $formData['AccDtl16'],
          'AccDtl17' => $formData['AccDtl17'],
          'AccDtl18' => $formData['AccDtl18'],
          'AccDtl19' => $formData['AccDtl19'],
          'AccDtl20' => $formData['AccDtl20'],
          'AccreditionType' => $formData['AccreditionType'],
          'UpdDate' => $formData['UpdDate'],
          'UpdUser' => $formData['UpdUser'],
          'unicode' => $idprogram,
          'Date' => date("Y-m-d"),
          'sessionId' => $sessionID,
          'idExists' => $formData['IdProgramAccreditionDetails'],
          'deleteFlag' => '1'
      );

      $db->insert($table, $larrtepaccridiction);
    }
  }

  public function fnGetTempaccredicationDetails($lintIdProgram) { // Function to edit Purchase order details
    $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->join(array('a' => 'tbl_tempprogramaccreditiondetails'), array('a.*'))
                    ->join(array('b' => 'tbl_definationms'), 'a.AccreditionType =b.idDefinition', array('b.DefinitionDesc'))
                    ->where('a.deleteFlag =1')
                    ->where("a.unicode  = '$lintIdProgram'");
    $result = $this->fetchAll($select);
    return $result;
  }

  public function fnDeleteTempAccredictionDetailsBysession($sessionID) { //Function for Delete Purchase order terms
    $db = Zend_Db_Table::getDefaultAdapter();
    $table = "tbl_tempprogramaccreditiondetails";
    $where = $db->quoteInto('sessionId = ?', $sessionID);
    $db->delete($table, $where);
  }

  public function fnUpdateTempAccridictionDetails($lintidtemp) {  // function to update po details
    $db = Zend_Db_Table::getDefaultAdapter();
    $table = "tbl_tempprogramaccreditiondetails";
    $larramounts = array('deleteFlag' => '0');
    $where = "IdTempProgramAccreditionDetails = '" . $lintidtemp . "'";
    $db->update($table, $larramounts, $where);
  }

  public function fnGetAccordictionTemDetails($idprogram, $sessionID) { // Function to edit Purchase order details
    $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->join(array('a' => 'tbl_tempprogramaccreditiondetails'), array('a.*'))
                    ->where("a.unicode = '$idprogram'")
                    ->where("a.sessionId = '$sessionID'");
    $result = $this->fetchAll($select);
    return $result;
  }

  public function fnDeleteMainAccridctionDetails($IdProgramAccreditionDetails) { //Function for Delete Purchase order terms
    $db = Zend_Db_Table::getDefaultAdapter();
    $table = "tbl_programaccreditiondetails";
    $where = $db->quoteInto('IdProgramAccreditionDetails = ?', $IdProgramAccreditionDetails);
    $db->delete($table, $where);
  }

  public function fnDeleteTempAccrdictiononSubmitDetails($lintIdProgram, $sessionID) { //Function for Delete Purchase order terms
    $db = Zend_Db_Table::getDefaultAdapter();
    $table = "tbl_tempprogramaccreditiondetails";
    $where = $db->quoteInto('unicode = ?', $lintIdProgram);
    $where = $db->quoteInto('sessionId = ?', $sessionID);
    $db->delete($table, $where);
  }

  public function fninsertMajoring($formData, $result) {  // function to insert po details
    $db = Zend_Db_Table::getDefaultAdapter();
    $table = "tbl_programmajoring";
    if (isset($formData['IdMajorggrid'])) {
      $countofLearningmode = count($formData['IdMajorggrid']);
      for ($i = 0; $i < $countofLearningmode; $i++) {
        $larrtepaccridiction = array(
            'idProgram' => $result,
            'IdMajor' => $formData['IdMajorggrid'][$i],
            'EnglishDescription' => $formData['EnglishDescriptiongrid'][$i],
            'BahasaDescription' => $formData['BahasaDescriptiongrid'][$i]
        );

        $db->insert($table, $larrtepaccridiction);
      }
    } else {
      return;
    }
  }

  public function fnviewProgramMajoring($IdProgram) { // Function to edit Purchase order details
    $select = $this->select()
                    ->setIntegrityCheck(false)
                    ->from(array('a' => 'tbl_programmajoring'), array('a.*'))
                    ->where("a.idProgram  = '$IdProgram'");
    //echo $select;die();
    $result = $this->fetchAll($select);
    return $result;
  }
  
  public function fnviewMajoring($id) { // Function to edit Purchase order details
  	 $db = Zend_Db_Table::getDefaultAdapter();

	
	$select = $db->select() 
  	->from(array('a' => 'tbl_programmajoring'))
  	->join(array("tbl_countries"=>"tbl_countries"),'a.Country = tbl_countries.idCountry',array("tbl_countries.CountryName"))
  	->join(array("tbl_state"=>"tbl_state"),'a.State = tbl_state.idState',array("tbl_state.StateName"))
  	->where("a.IDProgramMajoring  = ?",$id);
  	//echo $select;die();
  	$result = $db->fetchRow($select);
  	return $result;
  }
  
  public function getData($id) { // Function to edit Purchase order details
  	$db = Zend_Db_Table::getDefaultAdapter();
  
  
  	$select = $db->select()
  	->from(array('a' => 'tbl_program'))
  	->join(array("tbl_countries"=>"tbl_countries"),'a.Country = tbl_countries.idCountry',array("tbl_countries.CountryName"))
  	->join(array("tbl_state"=>"tbl_state"),'a.State = tbl_state.idState',array("tbl_state.StateName"))
  	->where("a.IdProgram  = ?",$id);
  	//echo $select;die();
  	$result = $db->fetchRow($select);
  	return $result;
  }

  public function fnDeleteProgramMajoring($formData) {//function to delete from  database
    $db = Zend_Db_Table::getDefaultAdapter();
    if ($formData['IdProgramMajoring']) {
      $id = $formData['IdProgramMajoring'];
      $arrid = explode(",", $id);
      for ($i = 0; $i < count($arrid); $i++) {
        $where = "IDProgramMajoring =$arrid[$i]";
        $db->delete('tbl_programmajoring', $where);
      }
      return;
    }
    return;
  }

  public function fnGetDepartmentFromColl($lintidCollege) {
    $lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
    $lstrSelect = $lobjDbAdpt->select()
                    ->from(array("a" => "tbl_departmentmaster"), array("key" => "a.IdDepartment", "value" => "a.DepartmentName"))
                    ->where("a.IdCollege = ?", $lintidCollege)
                    ->where("a.Active = 1")
                    ->order("a.DepartmentName");
    //echo $lstrSelect;die();
    $larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
    return $larrResult;
  }

  /**
   *
   * Method to get the list of all the programs for a given Faculty
   * @param int $lintidCollege
   */
  public function fngetProgramFromColl($lintidCollege) {
    $lstrSelect = $this->lobjDbAdpt->select()
                    ->from(array("a" => "tbl_program"), array("key" => "a.IdProgram", "name" => "a.ProgramName"))
                    ->where("a.IdCollege = ?", $lintidCollege);
    $larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
    if (count($larrResult) > 0) {
      $larrResult[] = array("key" => "all", "name" => "All");
    }
    return $larrResult;
  }

  public function fngetprogrambyScheme($idScheme) {
    $lstrSelect = $this->lobjDbAdpt->select()
                    ->from(array("a" => "tbl_program"), array("key" => "a.IdProgram", "name" => "ProgramName"))
                    ->where("a.IdScheme = ?", $idScheme);
    $larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
    return $larrResult;
  }

  public function fngetcatagory($lintidsubject){
  	$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
  	if($lintidsubject != 0){
    	$lstrSelect = $lobjDbAdpt->select()
                    ->from(array("a" => "tbl_subject"), array())
                    ->join(array("b" => "tbl_definationms"),'a.SubjectCatagory = b.idDefinition',array("key"=>"b.idDefinition","name"=>"DefinitionDesc"))
                    ->where("a.IdSubject = ?", $lintidsubject);
    	$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
    	return $larrResult;
  	}
  	else{
  		 $lstrSelect = $lobjDbAdpt->select()
                    ->from(array("a" => "tbl_definationtypems"),array())
                   	->join(array("b" => "tbl_definationms"),'a.idDefType = b.idDefType',array('key'=>'b.idDefinition','name'=>'b.DefinitionDesc'))
    				->where('a.defTypeDesc = ?',"Field Of Study")
                   	->where('a.Active = 1');
        $larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
    	return $larrResult;
  	}
  }

  /**
      * Function to get the program faculty
      * @author: Vipul
      */
	public function fnfetchProgramFaculty($IdProgram){
     	$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("a"=>"tbl_program"),array("a.IdProgram","a.IdCollege"))
		 				 ->join(array("b"=>"tbl_collegemaster"),'a.IdCollege = b.IdCollege',array("b.CollegeName"))
		 				 ->where("a.IdProgram = ?",$IdProgram);
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
     }


      /**
      * Function to get the Award
      * @author: Vipul
      */
	public function fnfetchProgramAward($IdProgram){
     	$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("a"=>"tbl_program"),array("a.IdProgram","a.Award"))
		 				 ->where("a.IdProgram = ?",$IdProgram);
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
     }
     
     public function fnfetchProgramShortName($lintidprogram) {
         $lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("a"=>"tbl_program"),array("a.IdProgram","a.ShortName"))
		 				 ->where("a.IdProgram = ?",$lintidprogram);
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
     }
             

    public function getCollegeDean($idProgram) {
         $lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		 $lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("p"=>"tbl_program"),array("p.IdProgram","p.ShortName"))
		 				 ->joinLeft(array("c"=>"tbl_collegemaster"),'c.IdCollege = p.IdCollege',array("c.CollegeName"))
		 				 ->joinLeft(array("s"=>"tbl_staffmaster"),'s.IdStaff = c.Vicedean1',array("FullName","FrontSalutation","BackSalutation"))
		 				 ->where("p.IdProgram = ?",$idProgram);
		$larrResult = $lobjDbAdpt->fetchRow($lstrSelect);
		return $larrResult;
     }
    public function fnGetProgramListName() {
    $lstrSelect = $this->lobjDbAdpt->select()
                    ->from(array("a" => "tbl_program"), array("key" => "a.IdProgram", "en_US" => "ProgramName", 'id_ID' => "ArabicName", 'code' => "ProgramCode"))
                    ->where("a.Active = 1")
                    ->order("a.ProgramName");
    $larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
    return $larrResult;
  }
    /**
    list based on id
    **/
    public function fnGetProgramListId($ids) {
        $lstrSelect = $this->lobjDbAdpt->select()
                        ->from(array("a" => "tbl_program"), array("key" => "a.IdProgram", "en_US" => "ProgramName", 'id_ID' => "ArabicName", 'code' => "ProgramCode",'strata'))
                        ->where("a.IdProgram IN ($ids)")
                        ->where("a.Active = 1")
                        ->order("a.ProgramName");
        $larrResult = $this->lobjDbAdpt->fetchAll($lstrSelect);
        return $larrResult;
    }
    
    public function fnGetProgramDetailCode($programCode)
    {
        $lstrSelect = $this->lobjDbAdpt->select()
                    ->from(array("a" => "tbl_program"))
                    ->where("a.Active = 1")
                    ->where("a.ProgramCode =?", $programCode)
                    ->order("a.ProgramName");
  
    	$result = $this->lobjDbAdpt->fetchRow($lstrSelect);
        
        return $result;
    }
}

?>