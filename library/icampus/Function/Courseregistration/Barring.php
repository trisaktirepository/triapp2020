<?php 
class icampus_Function_Courseregistration_Barring{
	 public function checkBarred($registration_id,$semid,$type){
    	//type:797 Regitration
    	//type:799 Exam Slip
    	//type:801 Exam Result
	    $barredDB = new App_Model_Record_DbTable_Barringrelease();
	      
		$barred = $barredDB->checkBarred($registration_id,$semid,$type);
		return $barred;	   	
	}
}
?>