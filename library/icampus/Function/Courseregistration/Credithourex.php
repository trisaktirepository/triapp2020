<?php 
class icampus_Function_Courseregistration_Credithourex{
 
  /*
   * Get payment status and invoice detail
   */
  public function getPembaikanExceptionMaxCh($registration_id,$semid){
  //get exception
    //get student profile info
    $studentRegistrationDb = new App_Model_Record_DbTable_StudentRegistration();
    $profile = $studentRegistrationDb->getStudentInfo($registration_id);  
    if($semid){
      
      $db = Zend_Db_Table::getDefaultAdapter();
      $selectData = $db->select()
                  ->from(array('pe'=>'creg_exception'))
                  ->where("pe.idsemester = ?", (int)$semid)
                  ->where("pe.idreg = ?", $profile['registrationId'])
                  ->where("pe.extype = 1");
      
      $row_ex = $db->fetchAll($selectData);
      
    }
    
    if(is_array($row_ex)){
    	return $row_ex;
    }else{
    	return false;
    }
  }	
}