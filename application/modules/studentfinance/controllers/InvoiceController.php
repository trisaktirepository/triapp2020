<?php
/**
 * @author Muhamad Alif
 * @version 1.0
 */

class Studentfinance_InvoiceController extends Zend_Controller_Action {
	
	private $_DbObj;
	
	public function init(){
		$db = new Studentfinance_Model_DbTable_InvoiceMain();
		$this->_DbObj = $db;
	}
	
	 
	public function generateStdInvoiceAction(){
		 
		
		$IdStudentRegistration = $this->_getParam('id', null);
		$idinvoice=$this->_getParam('idinvoice');
		$this->view->idinvoice=$idinvoice;
		$idactivity = $this->_getParam('idactivity', null);
		$this->view->student_registration_id = $IdStudentRegistration;
		
		//title
		$this->view->title= $this->view->translate("Invoice : Student Invoice ");
			
		//registration info
		$studentRegistrationDb = new Registration_Model_DbTable_Studentregistration();
		$registration = $studentRegistrationDb->getData($IdStudentRegistration);
		$this->view->registration = $registration;
		
		//student profile
		$studentProfileDb = new App_Model_Student_DbTable_StudentProfile();
		$profile = $studentProfileDb->getData($registration['IdApplication']);
		$this->view->profile = $profile;
		//-------
		$dbStd=new App_Model_Record_DbTable_StudentRegistration();
		$std=$dbStd->getStudentInfo($IdStudentRegistration);
		//get type of active invoice from active activity
		$dbActivity=new App_Model_General_DbTable_Activity();
		$act=$dbActivity->getActiveDataActivity($std['IdProgram'],$idactivity);
		$this->view->activity=$act;
		$bundleDetail=array();
		if ($act) {
				
			$idsemester=$act['IdSemesterMain'];
			//semester
			$semesterDb = new App_Model_General_DbTable_Semestermaster();
			$semester = $semesterDb->fnGetSemestermaster($idsemester);
			//echo $semester;echo "semesterif=".$idsemester;
			$this->view->semester=$semester;
			
			//Intake
			$dbIntake=new App_Model_Record_DbTable_Intake();
			$intake=$dbIntake->getData($std['IdIntake']);
			//academic year
			$academicYearDb = new App_Model_Record_DbTable_AcademicYear();
			$academicYear = $academicYearDb->getData($semester['idacadyear']);
			 
			//program
			$programDb = new App_Model_General_DbTable_Program();
			$program = $programDb->fngetProgramData($std['IdProgram']);
			$this->view->program=$program;
	
			//echo var_dump($std);exit;
			//get current payment setup
			$dbBundle=new Studentfinance_Model_DbTable_BundleFee();
			$bundle=$dbBundle->getCurrentSetup(1, $program['IdCollege'], $std['IdProgram'], $std['IdBranch'], $idsemester, $act['idActivity'],$std['IdProgramMajoring']);
			if ($bundle) {
			$this->view->bundle=$bundle;
		//if (!$bundle)	{
			//echo var_dump($bundle);exit;
		//}
			//get item detail
			$dbBudleDetail=new Studentfinance_Model_DbTable_BundleFeeDetail();
			$bundleDetail=$dbBudleDetail->getDataByBudle($bundle['idfeebundle']);
			//echo var_dump($bundleDetail);exit;
			$dbregsub=new App_Model_Record_DbTable_StudentRegSubjects();
			 
			$invoiceDb = new Studentfinance_Model_DbTable_InvoiceMain();
			$invoiceDetailDb = new Studentfinance_Model_DbTable_InvoiceDetail(); 
			 
			$db = Zend_Db_Table::getDefaultAdapter();
			if ($this->getRequest()->isPost()) {
				$formData=$this->getRequest()->getPost();
				if (isset($formData['agree'])) {
					if (!isset($formData['restpayment'])) {
						//get invoice no from sequence
						$idsemester=$formData['idsemester'];
						$IdStudentRegistration=$formData['IdStudentRegistration'];
						$idinvoice=$formData['idinvoice'];
						
						$seq_data = array(
								date('y',strtotime($academicYear['ay_start_date'])),
								substr($intake['IntakeId'],2,2),
								$program['ProgramCode'], 0
						);
						
						$db = Zend_Db_Table::getDefaultAdapter();
						if ($idinvoice=='') {
								$stmt = $db->prepare("SELECT invoice_seq(?,?,?,?) AS invoice_no");
								$stmt->execute($seq_data);
								$invoice_no = $stmt->fetch();
						}
						else {
	 						$smt=$db->select()
							->from('invoice_main',array('invoice_no'=>'bill_number'))
							->where('id=?',$idinvoice);
							$invoice_no=$db->fetchRow($smt);
	
						}		
						$inv_data = array(
								'bill_number' => $invoice_no['invoice_no'],
								'appl_id' => $formData['IdApplication'],
								'IdStudentRegistration' => $formData['IdStudentRegistration'],
								'academic_year' => $formData['idacadyear'],
								'semester' => $formData['idsemester'],
								'bill_amount' =>$formData['totalamount'],
								'bill_paid' => 0.00,
								'bill_balance' => $formData['totalamount'],
								'bill_description' => $formData['description'],
								'college_id' => $formData['IdCollege'],
								'program_code' => $formData['ProgramCode'],
								'creator' => '1',
								'fs_id' => $formData['fs_id'],
								'status' => 'A',
								'date_create' => date('Y-m-d H:i:s'),
								'idactivity'=>$formData['idactivity']
						);
						if ($formData["idinvoice"]=='') {
							$invoice_id = $invoiceDb->insert($inv_data);
							$dbFeeitem=new Studentfinance_Model_DbTable_FeeItem();
							//insert invoice detail
							foreach ($formData['item'] as $itemid=>$amount){
								$item=$dbFeeitem->getData($itemid);
								$inv_detail_data = array(
										'invoice_main_id' => $invoice_id,
										'fi_id' => $itemid,
										'fee_item_description' => $item['fi_name_bahasa'],
										'amount' => $amount
								);
							
								$invoiceDetailDb->insert($inv_detail_data);
							}
						} else $invoice_id=$formData['idinvoice'];
						$this->view->idinvoice=$invoice_id;
						//push to BNI
						$dbActCalendar=new App_Model_General_DbTable_ActivityCalendar();
						$calendar=$dbActCalendar->getData($formData['id']);
						if ($calendar) {
							$dateexprired=date('Y-m-d H:s:i',strtotime($calendar['EndDate'].' '.$calendar['EndTime']));
							$invoiceDb->pushToEColl($invoice_id, $dateexprired,'createbilling');
						}
					}
					 else {
						//get invoice no from sequence
						$idsemester=$formData['idsemester'];
						$IdStudentRegistration=$formData['IdStudentRegistration'];
						 
						
						$seq_data = array(
								date('y',strtotime($academicYear['ay_start_date'])),
								substr($intake['IntakeId'],2,2),
								$program['ProgramCode'], 0
						);
						
						$db = Zend_Db_Table::getDefaultAdapter();
						$stmt = $db->prepare("SELECT invoice_seq(?,?,?,?) AS invoice_no");
						$stmt->execute($seq_data);
						$invoice_no = $stmt->fetch();
						 
						$inv_data = array(
								'bill_number' => $invoice_no['invoice_no'],
								'appl_id' => $formData['IdApplication'],
								'IdStudentRegistration' => $formData['IdStudentRegistration'],
								'academic_year' => $formData['idacadyear'],
								'semester' => $formData['idsemester'],
								'bill_amount' =>$formData['totalamountrest'],
								'bill_paid' => 0.00,
								'bill_balance' => $formData['totalamountrest'],
								'bill_description' => $formData['description'],
								'college_id' => $formData['IdCollege'],
								'program_code' => $formData['ProgramCode'],
								'creator' => '1',
								'fs_id' => $formData['fs_id'],
								'status' => 'A',
								'date_create' => date('Y-m-d H:i:s'),
								'idactivity'=>$formData['idactivity']
						);
						 
							$invoice_id = $invoiceDb->insert($inv_data);
							$dbFeeitem=new Studentfinance_Model_DbTable_FeeItem();
							//insert invoice detail
							foreach ($formData['itemrest'] as $itemid=>$amount){
								$item=$dbFeeitem->getData($itemid);
								$inv_detail_data = array(
										'invoice_main_id' => $invoice_id,
										'fi_id' => $itemid,
										'fee_item_description' => $item['fi_name_bahasa'],
										'amount' => $amount
								);
									
								$invoiceDetailDb->insert($inv_detail_data);
							}
						  
						//push to BNI
						$dbActCalendar=new App_Model_General_DbTable_ActivityCalendar();
						$calendar=$dbActCalendar->getData($formData['id']);
						if ($calendar) {
							$dateexprired=date('Y-m-d H:s:i',strtotime($calendar['EndDate'].' '.$calendar['EndTime']));
							$invoiceDb->pushToEColl($invoice_id, $dateexprired,'createbilling');
						}
						$this->_redirect('/applicant-portal/account');
					}
				}
			}
			$invoice=$invoiceDb->isInByActivity($idsemester, $IdStudentRegistration, $act['idActivity']);
			if ($invoice) {
				$bundleDetail=$invoiceDetailDb->getInvoiceDetail($invoice['id']);
				foreach ($bundleDetail as $key=>$value) {
					$bundleDetail[$key]['fee']=array('amount'=>$value['amount']);
					//$amount=$amount+$invoice['amount'];
				}
				$this->view->idinvoice=$invoice['id'];
				$this->view->invoice=$invoice;
				//chek for incompatibility
				$restamount=$invoiceDb->inCompatibilityInvoice($IdStudentRegistration, $idsemester, $act['idActivity']);
				$this->view->invoicerest=$restamount;
				//if ($invoice['va']!='' && $restamount!=array()) $this->_redirect('/applicant-portal/account');
			} else {
				$this->view->invoice=array();
				$note=$bundle['bundlename'].' '.$semester['SemesterMainName'];
				
				//foreach ($ses_batch_invoice->student_list as $student):
				$feeStructure = new Studentfinance_Model_DbTable_FeeStructure();
				$dbBranch=new App_Model_General_DbTable_Branchofficevenue();
				 
				//get current semester level
			$sql = $db->select()
				  ->from(array('sss' => 'tbl_studentsemesterstatus'), array('Level'))
					->join(array('b'=>'tbl_semestermaster'),'b.IdSemesterMaster=sss.IdSemesterMain')
				  ->where('sss.IdStudentRegistration  = ?', $IdStudentRegistration)
				  ->where('b.IdSemesterMaster=?',$idsemester);
				   
				  $result = $db->fetchRow($sql);
				  if (!$result) {
				  
					  $sql = $db->select()
					  ->from(array('sss' => 'tbl_studentsemesterstatus'), array(new Zend_Db_Expr('max(Level) as Level')))
					  ->join(array('b'=>'tbl_semestermaster'),'b.IdSemesterMaster=sss.IdSemesterMain')
					  ->where('sss.IdStudentRegistration  = ?', $IdStudentRegistration)
					  ->where('b.SemesterMainStartDate<=?',$idsemester);
					  
					  $result = $db->fetchRow($sql);
					  $result['Level']=$result['Level']+1;
					  //echo $sql;
				  } 
				if( $result['Level'] ){
						$current_level = $result['Level'];
				}else{
						//check if senior student then hardcode level
						$intake_year = substr($intake['IntakeId'], 0,4);
						$cur_sem_year = substr($semester['SemesterMainCode'], 0,4);
				
						if($intake_year<2013){
							$current_level=0;
				
							while($intake_year<=$cur_sem_year){
								//check current gasal or genap for currencty
								if($intake_year == $cur_sem_year){
				
									if($semester['SemesterCountType']==1){
										$current_level += 1;
									}else{
										$current_level += 2;
									}
				
								}else{
									$current_level+= 2;
								}
				
								$intake_year++;
							}
				
							//remove 1 because we will add 1 in view
							$current_level -= 1;
				
						}else{
							$current_level = 0;
				
							//unset($student_list[$i]);
						}
					}
				
					//get fee structure
					if($std['appl_nationality']!=96){
						$student_category = 315;
					}else{
						$student_category = 314;
					}
					
					$row =$feeStructure->getApplicantFeeStructure($intake['IdIntake'],$std['IdProgram'],$student_category,$std['IdBranch'],$std['IdProgramMajoring']);
					 
					if ($row) {
						$fee_structure = $row;
				//		echo var_dump($row);exit;
						$this->view->fee_structure=$fee_structure;
						$amount=0;
						//echo var_dump($bundleDetail);exit;
						foreach ($bundleDetail as $key=>$value) {
							 
							$invoicedet = $invoiceDb->getInvoiceFee($idsemester,$std['IdStudentRegistration'], $fee_structure['fs_id'], $value['fee_item'], $value['percentage'],"1",$idactivity);
							//echo var_dump($invoicedet);exit;
							if ($invoicedet['amount']>0) $bundleDetail[$key]['fee']=$invoicedet;
							else unset($bundleDetail[$key]);
							$amount=$amount+$invoicedet['amount'];
						}
				
						 
					}
			 
				} 
				
				 
				$this->view->bundleDetail= $bundleDetail;
				 } else {
					echo 'Skema pembayaran '.$idactivity.' dan semester '.$idsemester. 'tidak ditermukan';
					exit;
				}
			} else {
				echo "Jadwal Pembuatan Invoice Belum dibuka, Hubungi admin Fakultas masing-masing";
				exit;
			}
		 
		
	}
	
	  
}
