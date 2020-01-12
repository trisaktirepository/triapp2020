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
		$act=$dbActivity->getActiveData($std['IdProgram']);
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
	
			
			//get current payment setup
			$dbBundle=new Studentfinance_Model_DbTable_BundleFee();
			$bundle=$dbBundle->getCurrentSetup(1, $program['IdCollege'], $std['IdProgram'], $std['IdBranch'], $idsemester, $act['idActivity']);
			$this->view->bundle=$bundle;
		//if (!$bundle)	{
			//echo var_dump($bundle);exit;
		//}
			//get item detail
			$dbBudleDetail=new Studentfinance_Model_DbTable_BundleFeeDetail();
			$bundleDetail=$dbBudleDetail->getDataByBudle($bundle['idfeebundle']);
			
			$dbregsub=new App_Model_Record_DbTable_StudentRegSubjects();
			 
			$invoiceDb = new Studentfinance_Model_DbTable_InvoiceMain();
			$invoiceDetailDb = new Studentfinance_Model_DbTable_InvoiceDetail(); 
			 
			$db = Zend_Db_Table::getDefaultAdapter();
			if ($this->getRequest()->isPost()) {
				$formData=$this->getRequest()->getPost();
				if (isset($formData['agree'])) {
					//get invoice no from sequence
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
					if ($formData["idinvoice"]!='') {
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
					}
					
					//push to BNI
					$dbActCalendar=new App_Model_General_DbTable_ActivityCalendar();
					$calendar=$dbActCalendar->getData($formData['id']);
					if ($calendar) {
						$dateexprired=date('Y-m-d H:s:i',strtotime($calendar['EndDate'].' '.$calendar['EndTime']));
						$invoiceDb->pushToEColl($invoice_id, $dateexprired,'createbilling');
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
				$this->view->invoice=$invoice;
			} else {
				$this->view->invoice=array();
				$note=$bundle['bundlename'].' '.$semester['SemesterMainName'];
				
				//foreach ($ses_batch_invoice->student_list as $student):
				$feeStructure = new Studentfinance_Model_DbTable_FeeStructure();
				$dbBranch=new App_Model_General_DbTable_Branchofficevenue();
				 
				//get current semester level
				$sql = $db->select()
					->from(array('sss' => 'tbl_studentsemesterstatus'), array(new Zend_Db_Expr('max(Level) as Level')))
					->where('sss.IdStudentRegistration  = ?',$std['IdStudentRegistration']);
				
				$result = $db->fetchRow($sql);
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
						//echo var_dump($row);exit;
						$this->view->fee_structure=$fee_structure;
						$amount=0;
						foreach ($bundleDetail as $key=>$value) {
							 
							$invoice = $invoiceDb->getInvoiceFee($idsemester,$std['IdStudentRegistration'], $fee_structure['fs_id'], $value['fee_item'], $value['percentage'],"1");
							//echo var_dump($invoice);exit;
							if ($invoice['amount']>0) $bundleDetail[$key]['fee']=$invoice;
							else unset($bundleDetail[$key]);
							$amount=$amount+$invoice['amount'];
						}
				
						 
					}
			 
				}
				$this->view->bundleDetail= $bundleDetail;
			} else {
				echo "Jadwal Pembuatan Invoice Belum dibuka, Hubungi admin Fakultas masing-masing";
				exit;
			}
		 
		
	}
	
	  
}