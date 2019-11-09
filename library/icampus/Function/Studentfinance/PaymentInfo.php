<?php 
class icampus_Function_Studentfinance_PaymentInfo{
 
  /*
   * Get payment status and invoice detail
   */
	
  public function getStudentPaymentInfo($idRegistration,$semesterMainId=null, $academicYearId=null){
    
    //get student profile info
    $studentRegistrationDb = new App_Model_Record_DbTable_StudentRegistration();
    $profile = $studentRegistrationDb->getStudentInfo($idRegistration);
    
    //get invoice 
    $invoiceMainDb = new Studentfinance_Model_DbTable_InvoiceMain();
    $program=$profile['IdProgram'];
    
    
    //specific semester
    //if($semesterMainId){
    //  $condition['semester = ?'] = $semesterMainId;
   // }
    
    //specific academic year
   // if($academicYearId){
    //  $condition['academic_year = ?'] = $academicYearId;
   // }
   
    
    $condition['status != ?'] = 'X';
    $select=$invoiceMainDb->select()
    	->from(array('invoice_main'))
    	->where("status='A'");
    
   
    /*
    if ($program==11) {
    	$select->where('IdStudentRegistration =?',$profile['IdStudentRegistration'])
    			->where('semester=?',$semesterMainId);
    } else  
    	*/
    if ($program==5 || $program==6 || $program==7) {
    	$select->where('semester <>?',$semesterMainId);
    }
    $select->where('appl_id =?', $profile['IdApplication']);
    //$invoices = $invoiceMainDb->fetchAll($condition)->toArray();
    $invoices = $invoiceMainDb->fetchAll($select);
    
    //pack invoice into array
    $result['idStudentRegistration'] = $idRegistration;
    $result['nim'] = $profile['registrationId'];
    
    $result['fullname'] = "";
    $profile['appl_fname']!=""?$result['fullname'] .= $profile['appl_fname']." ":"";
    $profile['appl_mname']!=""?$result['fullname'] .= $profile['appl_mname']." ":"";
    $profile['appl_lname']!=""?$result['fullname'] .= $profile['appl_lname']." ":"";
    
    if($semesterMainId){
      $result['semester_id'] = $semesterMainId;
    }
    if($academicYearId){
      $result['academic_year_id'] = $academicYearId;
    }
    
    $result['total_invoice_amount'] = 0;
    $result['total_invoice_paid'] = 0;
    $result['total_invoice_balance'] = 0;
    
    if($invoices){
      $result['invoices'] = $invoices;
    }else{
      $result['invoices'] = '';
    }
    
    
   
    foreach ($invoices as $invoice){
      $result['total_invoice_amount'] += $invoice['bill_amount'];
      $result['total_invoice_paid'] += $invoice['bill_paid'];
      $result['total_invoice_balance'] += $invoice['bill_balance'];
    }
    
    //echo var_dump($result);
    //get exception
    if($semesterMainId){
      
      $db = Zend_Db_Table::getDefaultAdapter();
      $selectData = $db->select()
                  ->from(array('pe'=>'payment_exception'))
                  ->where("pe.idsemester = ?", (int)$semesterMainId)
                  ->where("pe.idreg = ?", $profile['registrationId'])
      			  ->where('pe.date_to >= ?',date('Y-m-d'));
      
      $row_ex = $db->fetchAll($selectData);
      $result['exception'] = array();
      foreach ($row_ex as $exp){
        $result['exception'][$exp['extype']] = true;
      }
    }
    //echo var_dump($result);exit;
    return $result;
    
  } 
  
  public function getStudentOustandingInfo($idRegistration,$semesterMainId=null, $academicYearId=null){
  
  	//get student profile info
  	$studentRegistrationDb = new App_Model_Record_DbTable_StudentRegistration();
  	$profile = $studentRegistrationDb->getStudentInfo($idRegistration);
  
  	//get invoice
  	$invoiceMainDb = new Studentfinance_Model_DbTable_InvoiceMain();
  
   
  	//pack invoice into array
  	$result['idStudentRegistration'] = $idRegistration;
  	$result['nim'] = $profile['registrationId'];
  
  	$result['fullname'] = "";
  	$profile['appl_fname']!=""?$result['fullname'] .= $profile['appl_fname']." ":"";
  	$profile['appl_mname']!=""?$result['fullname'] .= $profile['appl_mname']." ":"";
  	$profile['appl_lname']!=""?$result['fullname'] .= $profile['appl_lname']." ":"";
  
  	if($semesterMainId){
  		$result['semester_id'] = $semesterMainId;
  	}
  	if($academicYearId){
  		$result['academic_year_id'] = $academicYearId;
  	}
    
  	$outstandingpayment=$invoiceMainDb->anyOustandingTillSemester($idRegistration, $semesterMainId);
  	 
  	$result['invoices'] = $outstandingpayment;
  	 
   
  
  	//get exception
  	if($semesterMainId){
  
  		$db = Zend_Db_Table::getDefaultAdapter();
  		$selectData = $db->select()
  		->from(array('pe'=>'payment_exception'))
  		->where("pe.idsemester = ?", (int)$semesterMainId)
  		->where("pe.idreg = ?", $profile['registrationId'])
  		->where('pe.date_to >= ?',date('Y-m-d'));
  
  		$row_ex = $db->fetchAll($selectData);
  		$result['exception'] = array();
  		foreach ($row_ex as $exp){
  			$result['exception'][$exp['extype']] = true;
  		}
  	}
  	// echo var_dump($result);exit;
  	return $result;
  
  }
}
?>