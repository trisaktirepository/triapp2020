<?php 
class icampus_Function_Application_BankSlip extends Zend_View_Helper_Abstract{
	
	public function generateBankSlip($payment_plan_id, $installment){
		
		$paymentPlanDb = new Studentfinance_Model_DbTable_PaymentPlan();
		$data = $paymentPlanDb->getData($payment_plan_id);
		
		$paymentPlanDetailDb = new Studentfinance_Model_DbTable_PaymentPlanDetail();
		$data['detail'] = $paymentPlanDetailDb->getPaymentPlanDetail($data['pp_id']);
		
		//inject no rekoning
		$feeItemAccountDb = new Studentfinance_Model_DbTable_FeeItemAccount();
		$programDb = new App_Model_Record_DbTable_Program();
		
		foreach ($data['detail'] as $index => $plan){
			$faculty_id = $plan['invoice']['college_id'];
			$program_code = $plan['invoice']['program_code'];
			$program_data = $programDb->getProgrambyCode($program_code);
			
			foreach($plan['invoice']['detail'] as $fee_item_index => $fee_item){
				$account = $feeItemAccountDb->getFeeItem($faculty_id,$program_data['IdProgram'],$fee_item['fi_id']);
				
				if($account){
					$data['detail'][$index]['invoice']['detail'][$fee_item_index]['account'] = $account;
				}else{
					$data['detail'][$index]['invoice']['detail'][$fee_item_index]['account'] = '--- Not Setup ---';
				}
			}
			
		}
		
		//invoice
		$invoice_data = $data['detail'][$installment-1]['invoice'];
		global $invoice;
		$invoice = $invoice_data;
		
		//faculty info
		$faculty_id = $data['detail'][$installment-1]['invoice']['college_id'];
		
		$collegeMasterDb = new App_Model_General_DbTable_Collegemaster();
		$faculty = $collegeMasterDb->getData($faculty_id);
		
		global $faculty_data;
		$faculty_data = $faculty;

		//applicant biodata
		$applicantProfileDb = new App_Model_Application_DbTable_ApplicantProfile();
		$profile_data = $applicantProfileDb->getData($data['pp_appl_id']);
		
		global $profile;
		$profile = $profile_data;
		
		//academic year
		$academicYearDb = new App_Model_Record_DbTable_AcademicYear();
		$academic_year = $academicYearDb->getData($invoice['academic_year']);
		
		global $academicYear;
		$academicYear = $academic_year;
		

		global $fee_item;
		$fee_item = $data;
		
		global $cicilan;
		$cicilan = $installment;
		
		require_once 'dompdf_config.inc.php';
		
		$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
		$autoloader->pushAutoloader('DOMPDF_autoload');
		
		
		if ($bank_id==null || $bank_id==1 )
				$html_template_path = DOCUMENT_PATH."/template/BankSlip.html";
		else  if ($bank_id==3 )
				$html_template_path = DOCUMENT_PATH."/template/BankSlipMandiri.html";
		else  if ($bank_id==5 )
			$html_template_path = DOCUMENT_PATH."/template/BankSlipDKI.html";
		else  if ($bank_id==7 )
			$html_template_path = DOCUMENT_PATH."/template/BankSlipMandiriSyariah.html";
		 
		
		$html = file_get_contents($html_template_path);
				
		//echo $html;
		//exit;
		$dompdf = new DOMPDF();
		$dompdf->load_html($html);
		$dompdf->set_paper('a4', 'potrait');
		$dompdf->render();
		
		
		$dompdf->stream("BankSlip.pdf");
		//$pdf = $dompdf->output();


	}
	
	public function printBankSlip($invoice_id,$bank_id=null){
		
		//invoice
		$invoiceMainDb = new Studentfinance_Model_DbTable_InvoiceMain();
		$invoice_data = $invoiceMainDb->getData($invoice_id);
		
		$invoiceDetailDb  = new Studentfinance_Model_DbTable_InvoiceDetail();
		$invoice_data['detail'] =  $invoiceDetailDb->getInvoiceDetail($invoice_data['id']);
		
		//knockoff paid amount for fee_item amount and knockoff CN amount
		$invoice_paid_Amount = $invoice_data['bill_paid'] + $invoice_data['cn_amount'];
		
		foreach ($invoice_data['detail'] as $index=>$fee_item){
			$invoice_data['detail'][$index]['fee_item_amount'] = $fee_item['amount'];
			
			
			if( $invoice_paid_Amount >=  $fee_item['amount']){
				
				$invoice_data['detail'][$index]['paid'] = $fee_item['amount'];
				$invoice_data['detail'][$index]['balance'] = 0;
				$invoice_data['detail'][$index]['amount'] = $invoice_data['detail'][$index]['balance'];
				
				$invoice_paid_Amount = $invoice_paid_Amount - $fee_item['amount'];
				
			}else{
				
				$invoice_data['detail'][$index]['paid'] = $invoice_paid_Amount;
				$invoice_data['detail'][$index]['balance'] = $invoice_data['detail'][$index]['fee_item_amount'] - $invoice_paid_Amount;
				$invoice_data['detail'][$index]['amount'] = $invoice_data['detail'][$index]['balance'];
				
				$invoice_paid_Amount = 0;
				
			}
			
		}
		
				
		global $invoice;
		$invoice = $invoice_data;
		
		//get nim for student
		if($invoice['no_fomulir']=="" && $invoice['IdStudentRegistration']!=""){
			$studentRegistrationDb = new App_Model_Registration_DbTable_Studentregistration();
			$invoice['registration_info'] = $studentRegistrationDb->fetchRow('IdStudentRegistration = '.$invoice['IdStudentRegistration']);
		}
		
		//get sem name for invoice
		if($invoice['semester']!=""){
			$semesterDb = new App_Model_General_DbTable_Semestermaster();
			$semester = $semesterDb->fetchRow('IdSemesterMaster = '.$invoice['semester']);
			$invoice['semester_data'] = $semester;
		}
		
		//faculty info
		$faculty_id = $invoice['college_id'];
		
		$collegeMasterDb = new App_Model_General_DbTable_Collegemaster();
		$faculty = $collegeMasterDb->getData($faculty_id);
		
		global $faculty_data;
		$faculty_data = $faculty[0];
		
		//program info
		$programDb = new GeneralSetup_Model_DbTable_Program();
		$program = $programDb->getProgramDataByCode($invoice['program_code']);
		
		global $program_data;
		$program_data = $program;
		

		//applicant biodata
		$applicantProfileDb = new App_Model_Application_DbTable_ApplicantProfile();
		$profile_data = $applicantProfileDb->getData($invoice['appl_id']);
		
		global $profile;
		$profile = $profile_data;
		
		//academic year
		$academicYearDb = new App_Model_Record_DbTable_AcademicYear();
		$academic_year = $academicYearDb->getData($invoice['academic_year']);
		
		global $academicYear;
		$academicYear = $academic_year;
		
		$data['detail'][0]['invoice'] = $invoice;
		
		//inject no rekoning & fee item short name
		
		$feeItemAccountDb = new Studentfinance_Model_DbTable_FeeItemAccount();
		
		$programDb = new App_Model_Record_DbTable_Program();
		
		foreach ($data['detail'] as $index => $plan){
			$faculty_id = $plan['invoice']['college_id'];
			$program_code = $plan['invoice']['program_code'];
			$program_data = $programDb->getProgrambyCode($program_code);
			
			foreach($plan['invoice']['detail'] as $fee_item_index => $fee_item){
				$account = $feeItemAccountDb->getFeeItem($faculty_id, $program_data['IdProgram'], $fee_item['fi_id']);
				
				
				
				//account
				if($account){
					$data['detail'][$index]['invoice']['detail'][$fee_item_index]['account'] = $account;
				}else{
					$data['detail'][$index]['invoice']['detail'][$fee_item_index]['account'] = '---Not setup---';
				}
			}
			
		}
				
		global $fee_item;
		$fee_item = $data;
		
		global $cicilan;
		$cicilan = 1;
		
		require_once 'dompdf_config.inc.php';
		
		$autoloader = Zend_Loader_Autoloader::getInstance(); // assuming we're in a controller
		$autoloader->pushAutoloader('DOMPDF_autoload');
		
		if ($bank_id==null || $bank_id==1 )
				$html_template_path = DOCUMENT_PATH."/template/BankSlip.html";
		else  if ($bank_id==3 )
				$html_template_path = DOCUMENT_PATH."/template/BankSlipMandiri.html";
		else  if ($bank_id==5 )
			$html_template_path = DOCUMENT_PATH."/template/BankSlipDKI.html";
		else  if ($bank_id==7 )
			$html_template_path = DOCUMENT_PATH."/template/BankSlipMandiriSyariah.html";
		
		$html = file_get_contents($html_template_path);
				
		//echo $html;
		//exit;
		$dompdf = new DOMPDF();
		$dompdf->load_html($html);
		$dompdf->set_paper('a4', 'potrait');
		$dompdf->render();
		
		
		$dompdf->stream("BankSlip.pdf");
		//$pdf = $dompdf->output();
	}
}
?>