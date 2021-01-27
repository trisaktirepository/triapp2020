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
		 
		$dbtxt=new App_Model_General_DbTable_TmpTxt();
		$IdStudentRegistration = $this->_getParam('id', null);
		$idinvoice=$this->_getParam('idinvoice','');
		$this->view->idinvoice=$idinvoice;
		$idactivity = $this->_getParam('idactivity', null);
		$this->view->student_registration_id = $IdStudentRegistration;
		$auth = Zend_Auth::getInstance();
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
		//echo var_dump($bundleDetail);exit;
		$dbregsub=new App_Model_Record_DbTable_StudentRegSubjects();
		$invoiceDb = new Studentfinance_Model_DbTable_InvoiceMain();
		$dbStd=new App_Model_Record_DbTable_StudentRegistration();
		$std=$dbStd->getStudentInfo($IdStudentRegistration);
		$dbBudleDetail=new Studentfinance_Model_DbTable_BundleFeeDetail();
		$invoiceDetailDb = new Studentfinance_Model_DbTable_InvoiceDetail();
		$semesterDb = new App_Model_General_DbTable_Semestermaster();
		$academicYearDb = new App_Model_Record_DbTable_AcademicYear();
		$dbCreditNote=new Studentfinance_Model_DbTable_CreditNote();
		$dbCnDetail=new Studentfinance_Model_DbTable_CreditNoteDetail();
		$dbInvoice=new Studentfinance_Model_DbTable_InvoiceMain();
		$dbFeeitem=new Studentfinance_Model_DbTable_FeeItem();
			
		$dbBundle=new Studentfinance_Model_DbTable_BundleFee();
		//get type of active invoice from active activity
		$dbActivity=new App_Model_General_DbTable_Activity();
		$dbIntake=new App_Model_Record_DbTable_Intake();
		$intake=$dbIntake->getData($std['IdIntake']);
		$startclass=$intake['class_start'];
		$act=$dbActivity->getActiveDataActivity($std['IdProgram'],$idactivity,$startclass);
		 
		$this->view->activity=$act;
		
		//program
		$programDb = new App_Model_General_DbTable_Program();
		$program = $programDb->fngetProgramData($std['IdProgram']);
		$this->view->program=$program;
		$dbDiscountSetup=new Studentfinance_Model_DbTable_DiscountMain();
		$db = Zend_Db_Table::getDefaultAdapter();
			if ($this->getRequest()->isPost()) {
				$formData=$this->getRequest()->getPost();
				if (isset($formData['agree'])) {
					if (isset($formData['payment'])) {
						//get invoice no from sequence
						$idsemester=$formData['idsemester'];
						$semester = $semesterDb->fnGetSemestermaster($idsemester);
						$academicYear = $academicYearDb->getData($semester['idacadyear']);
						
						$IdStudentRegistration=$formData['IdStudentRegistration'];
						$idinvoice=$formData['idinvoice'];
			
						$seq_data = array(
								date('y',strtotime($academicYear['ay_start_date'])),
								substr($intake['IntakeId'],2,2),
								$program['ProgramCode'], 0
						);
			
						 
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
								'cn_amount' => $formData['discountamount'],
								'bill_balance' => $formData['totalamount']-$formData['discountamount'],
								'bill_description' => $formData['description'],
								'college_id' => $formData['IdCollege'],
								'program_code' => $formData['ProgramCode'],
								'creator' => '1',
								'fs_id' => $formData['fs_id'],
								'status' => 'A',
								'date_create' => date('Y-m-d H:i:s'),
								'idactivity'=>$formData['idactivity'],
								'trx_id' => $invoice_no['invoice_no']
						);
						if ($formData["idinvoice"]=='') {
							$invoice_id = $invoiceDb->insert($inv_data);
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
						
						//discount here ----------------
						if (isset($formData['discount'])) {
							$dbDiscount=new Studentfinance_Model_DbTable_Discount();
							$dbDisDetail=new Studentfinance_Model_DbTable_DiscountDetail();
							$dbFee=new Studentfinance_Model_DbTable_FeeItem();
							$discount=$formData['discount'];
							foreach ($discount as $iddm=>$value) {
								$dm=$dbDiscountSetup->getDataById($iddm);
								$data=array('dcnt_appl_id'=>$formData['IdApplication'],
										'dcnt_txn_id'=>$formData['transaction_id'],
										'dcnt_type_id'=>$dm['idDiscount'],
										'dcnt_letter_number'=>$dm['no_skr'],
										'dcnt_invoice_id'=>$invoice_id
								);
								$row=$dbDiscount->isIn($formData['transaction_id'], $invoice_id, $dm['no_skr']);
								if ($row) $iddisc=$row['dcnt_id'];
								else $iddisc=$dbDiscount->insert($data);
								$tamount=0;
								foreach ($value as $fiid=>$amount) {
									$fee=$dbFee->getData($fiid);
									$discdetail=array('dcnt_id'=>$iddisc,
														'dcntdtl_fi_id'=>$fiid,
														'dcntdtl_fi_name'=>$fee['fi_name_bahasa'],
														'dcntdtl_amount'=>$amount
													);
									$row=$dbDisDetail->isIn($iddisc, $fiid);
									if ($row) $dbDisDetail->update($discdetail, 'dcntdtl_id='.$row['dcntdtl_id']);
									else $dbDisDetail->insert($discdetail);
									 
									$tamount=$tamount+$amount;
								}
								$dbDiscount->update(array('dcnt_amount'=>$tamount), 'dcnt_id='.$iddisc);
								//put to credirt note and invoice
							}
								$disc=$dbDiscount->getDataFeeItemByInvoice($invoice_id);
								if ($disc) {
									echo var_dump($disc);exit;
									$tamount=array();
									foreach ($disc as $discvalue) {
										if (isset($tamount[$discvalue['fi_id']])) $tamount[$discvalue['fi_id']]=$tamount[$discvalue['fi_id']]+$discvalue['amount'];
										else $tamount[$discvalue['fi_id']]=$discvalue['amount'];
										//check for fi invoice
										$invdet=$invoiceDetailDb->isIn($invoice_id, $discvalue['fi_id']);
										if ($tamount[$discvalue['fi_id']]>$invdet['amount']) {
											$discvalue['amount']=$discvalue['amount']-($tamount[$discvalue['fi_id']]-$invdet['amount']);
											$tamount[$discvalue['fi_id']]=$invdet['amount'];
											
										}
										if ($discvalue['amount']>0) {
											$cn=array('cn_billing_no'=>$invoice_no['invoice_no'],
													'appl_id'=>$discvalue['dcnt_appl_id'],
													'IdStudentRegistration'=>$formData['IdStudentRegistration'],
													'cn_amount'=>$tamount[$discvalue['fi_id']],
													'cn_description'=>$discvalue['dt_discount'].' '.$discvalue['dcnt_letter_number'],
													'cn_approver'=>1,
													'cn_approve_date'=>date('Y-m-d H:i:s')
											);
											$row=$dbCreditNote->isIn($invoice_no['invoice_no'], $formData['IdStudentRegistration'], $discvalue['dt_discount'].' '.$discvalue['dcnt_letter_number']);
											if ($row) {
												$cnid=$row['cn_id'];
												$dbCreditNote->update($cn, 'cn_id='.$row['cn_id']);
											}
											else $cnid=$dbCreditNote->insert($cn);
											//detail
											/* $discdet=$dbDisDetail->getDataByMain($discvalue['dcnt_id']);
											foreach ($discdet as $det) {
												$cndet=array('cnd_cn_id'=>$cnid,
														'cnd_fi_id'=>$det['dcntdtl_fi_id'],
														'cnd_fi_name'=>$det['dcntdtl_fi_name'],
														'cnd_amount'=>$det['dcntdtl_amount']
											);
											$row=$dbCnDetail->isIn($cnid, $det['dcntdtl_fi_id']);
											if ($row) $dbCnDetail->updateData($cndet, 'cnd_id='.$row['cnd_id']);
											else $dbCnDetail->addData($cndet);
										} */
										
											$cndet=array('cnd_cn_id'=>$cnid,
													'cnd_fi_id'=>$discvalue['fi_id'],
													'cnd_fi_name'=>$discvalue['fi_name'],
													'cnd_amount'=>$discvalue['amount']
											);
											$row=$dbCnDetail->isIn($cnid, $discvalue['fi_id']);
											if ($row) $dbCnDetail->updateData($cndet, 'cnd_id='.$row['cnd_id']);
											else $dbCnDetail->addData($cndet);
										}
										
										
									}
									$total=0;
									foreach ($tamount as $amount) {
										$total=$total+$amount;
									}
									if (($formData['totalamount']-$total)>=0) $dbInvoice->update(array('cn_amount'=>$total,'bill_balance'=>$formData['totalamount']-$total), 'id='.$invoice_id);
									   
								}
							 
						}
						
						//------------------------------
						$this->view->idinvoice=$invoice_id;
						//push to BNI
						$dbActCalendar=new App_Model_General_DbTable_ActivityCalendar();
						$calendar=$dbActCalendar->getData($formData['id']);
						//echo var_dump($calendar);
						if ($calendar) {
							$dateexprired=date('Y-m-d H:s:i',strtotime($calendar['EndDate'].' '.$calendar['EndTime']));
							$invoiceDb->pushToEColl($invoice_id, $dateexprired,'createbilling');
						}
						
						
						
					}
					if (isset($formData['restpayment'])) {
					 
				 			//get invoice no from sequence
							$idsemester=$formData['idsemester'];
							$IdStudentRegistration=$formData['IdStudentRegistration'];
								
							if ($formData['totalamountpos']>0) {
								//kekurangan pemmbayaran
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
										'bill_amount' =>$formData['totalamountpos'],
										'bill_paid' => 0.00,
										'bill_balance' => $formData['totalamountpos'],
										'bill_description' => $formData['description'],
										'college_id' => $formData['IdCollege'],
										'program_code' => $formData['ProgramCode'],
										'creator' => '1',
										'fs_id' => $formData['fs_id'],
										'status' => 'A',
										'date_create' => date('Y-m-d H:i:s'),
										'idactivity'=>$formData['idactivity'],
										'trx_id' => $invoice_no['invoice_no']
								);
									
								$invoice_id = $invoiceDb->insert($inv_data);
								$dbFeeitem=new Studentfinance_Model_DbTable_FeeItem();
								//insert invoice detail
								foreach ($formData['itemrestpos'] as $itemid=>$amount){
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
							if ($formData['id']!='') $calendar=$dbActCalendar->getData($formData['id']);
							
							else $calendar=$dbActCalendar->getDataBySem($formData['idsemester'], $program['IdProgram'], $formData['idactivity']);
							
							if ($calendar) {
								$dateexprired=date('Y-m-d H:s:i',strtotime($calendar['EndDate'].' '.$calendar['EndTime']));
								$invoiceDb->pushToEColl($idinvoice, $dateexprired,'createbilling');
							}
						}
						
						if ($formData['totalamountneg']!=0) {
							//move to advance payment
							$idinvoice=$formData['idinvoice'];
							$invoice=$invoiceDb->getData($idinvoice);
							$formname=$formData['itemrestnegname'];
							 
							//foreach ($formitem as $fii=>$amount) {
								$data = array(
									'cn_billing_no' => $invoice['bill_number'],
									'appl_id' => $invoice['appl_id'],
									'IdStudentRegistration' => $invoice['IdStudentRegistration'],
									'cn_amount' => abs($formData['totalamountneg']),
									'cn_description' =>'Kelebihan Tagihan',
									'cn_creator'=>1,
									'cn_create_date'=>date('Y-m-d H:i:s'),
									'cn_approver'=>1,
									'cn_approve_date'=>date('Y-m-d H:i:s')
								);
								$idcn=$dbCreditNote->insert($data);
								foreach ($formData['itemrestneg'] as $fii=>$amount) {
									//$item=$dbFeeitem->getData($fii);
									$dbCnDetail->insert(array('cnd_cn_id'=>$idcn,'cnd_fi_id'=>$fii,'cnd_fi_name'=>$formname[$fii],'cnd_amount'=>abs($amount)));
								}
							//}
							if (($invoice['bill_amount']-$invoice['bill_paid'])>abs($formData['totalamountneg'])) {
								$dbInvoice->update(array('cn_amount'=>abs($formData['totalamountneg']),'bill_balance'=>$invoice['bill_balance']-abs($formData['totalamountneg'])), 'id='.$invoice['id']);
								//push to BNI
								$dbActCalendar=new App_Model_General_DbTable_ActivityCalendar();
								if ($formData['id']!='') $calendar=$dbActCalendar->getData($formData['id']);
									
								else $calendar=$dbActCalendar->getDataBySem($formData['idsemester'], $program['IdProgram'], $formData['idactivity']);
									
								if ($calendar) {
									$dateexprired=date('Y-m-d H:s:i',strtotime($calendar['EndDate'].' '.$calendar['EndTime']));
									$invoiceDb->pushToEColl($idinvoice, $dateexprired,'createbilling');
								}
							}  
							if ($invoice['bill_paid']>0) {
								
								//add advance payment
								 
								$adv_amount = abs($invoice['bill_amount'] - $invoice['bill_paid']-abs($formData['totalamountneg']));// - $invoice['cn_amount']+$invoice['dn_amount']);
								$data = array(
										'advpy_appl_id' => $invoice['appl_id'],
										'advpy_acad_year_id' => $invoice['academic_year'],
										'advpy_sem_id' => $invoice['semester'],
										'advpy_prog_code' => $invoice['program_code'],
										'advpy_fomulir' => $invoice['no_fomulir'],
										'advpy_invoice_no' => $invoice['bill_number'],
										'advpy_invoice_id' => $invoice['id'], 
										'advpy_description' => 'Excess Payment for invoice no:'.$invoice['bill_number'],
										'advpy_amount' => $adv_amount,
										'advpy_total_paid' => 0,
										'advpy_total_balance' => $adv_amount,
										'advpy_status' => 'A',
										'advpy_creator' => $auth->getIdentity()->appl_id
											
								
								);
								$dbAdnc=new Studentfinance_Model_DbTable_AdvancePayment();
								$dbAdnc->insert($data);
								$dbInvoice->update(array('cn_amount'=>abs($formData['totalamountneg']),'bill_balance'=>0), 'id='.$invoice['id']);
								
							} 
							
							
						}
						 
					}
					
					$this->_redirect('/applicant-portal/account');
					
				}
			}
			

		$bundleDetail=array();
		//echo var_dump($act); echo 'd='.$idinvoice;exit;
		if ($act && $idinvoice=="") {
			foreach ($act as $key=>$value) {
				 
				$idsemester=$value['IdSemesterMain'];
				$act[$key]['bundledetail']=array();
				$act[$key]['bundle']=array();
				$act[$key]['idinvoice']='';
				//semester
				$semester = $semesterDb->fnGetSemestermaster($idsemester);
				//echo $semester;echo "semesterif=".$idsemester;
				$this->view->semester=$semester;
				 
				//academic year
				$academicYear = $academicYearDb->getData($semester['idacadyear']);
				 
				//echo var_dump($std);exit;
				//get current payment setup
				$bundle=$dbBundle->getCurrentSetup(1, $program['IdCollege'], $std['IdProgram'], $std['IdBranch'], $idsemester,$idactivity,$std['IdProgramMajoring']);
				$act[$key]['bundle']=$bundle;
			//	echo var_dump($bundle);exit;
				if ($bundle) {
				 	//get item detail
					$bundleDetail=$dbBudleDetail->getDataByBudle($bundle['idfeebundle']);
					$invoice=$invoiceDb->isInByActivity($idsemester, $IdStudentRegistration, $idactivity);
					$restamount=array();
					$act[$key]['invoicerest']=array();
					$act[$key]['invoice']=$invoice;
					
					if ($invoice) {
						//echo var_dump($invoice);
						//echo var_dump($bundleDetail);
						$current_level=$this->getLevel($IdStudentRegistration, $idsemester, $intake);
						
						//$this->view->invoice=$invoice;
						//chek for incompatibility
						if ($invoice['mhsbaru']=="0") {
							//$dbtxt->add(array('txt'=>'no msh baru'.$IdStudentRegistration));
							$act[$key]['idinvoice']=$invoice['id'];
							$restamount=$invoiceDb->inCompatibilityInvoice($IdStudentRegistration, $idsemester, $idactivity);
							//echo var_dump($restamount);exit;
							$act[$key]['invoicerest']=$restamount;
							//$dbtxt->add(array('txt'=>'rest'.var_dump($restamount).'-'.$IdStudentRegistration));
							$bundleDetail=array();
							
						} else {
							$act[$key]['invoicerest']=array();
							$bundleDetail=array();
						}
						
						//if ($invoice['va']!='' && $restamount!=array()) $this->_redirect('/applicant-portal/account');
					} else {
						 
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
						  
						  		$semselect=$db->select()
						  		->from('tbl_semestermaster')
						  		->where('IdSemesterMaster=?',$idsemester);
						  		$sem=$db->fetchRow($semselect);
						  		
							  $sql = $db->select()
							  ->from(array('sss' => 'tbl_studentsemesterstatus'), array(new Zend_Db_Expr('max(Level) as Level')))
							  ->join(array('b'=>'tbl_semestermaster'),'b.IdSemesterMaster=sss.IdSemesterMain')
							  ->where('sss.IdStudentRegistration  = ?', $IdStudentRegistration)
							  ->where('b.SemesterMainStartDate<= ?',$sem['SemesterMainStartDate']);
							  
							  $result = $db->fetchRow($sql);
							  if (!$result) $result['Level']=1;
							  else $result['Level']=$result['Level']+1;
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
							if (!$row) {
								$sql = $db->select()
								->from(array('sss' => 'tbl_studentregistration'), array('IdProgram','IdIntake','IdBranch','IdProgramMajoring'))
								->where('sss.registrationId  = ?', $registration['registrationId'])
								->where('sss.IdProgram<>?',$registration['IdProgram']);
								//echo $sql;
								$std = $db->fetchRow($sql);
								 
								if (!$std) {
									//$row =$feeStructure->getApplicantFeeStructure($std['IdIntake'],$std['IdProgram'],$student_category,$std['IdBranch'],$std['IdProgramMajoring']);
									//echo var_dump($registration);exit;
									if (!$row && $registration['IdProgram']==60) {
										//get from oldnim'
										$sql = $db->select()
										->from(array('sss' => 'tbl_studentregistration'), array('IdProgram','IdIntake','IdBranch','IdProgramMajoring','oldnim'))
										->where('sss.registrationId  = ?', $registration['registrationId']);
										
										$oldnim=$std = $db->fetchRow($sql);
										
										$sql = $db->select()
										->from(array('sss' => 'tbl_studentregistration'), array('IdProgram','IdIntake','IdBranch','IdProgramMajoring'))
										->where('sss.registrationId  = ?',$oldnim['oldnim']);
										//->where('sss.IdProgram  = ?',$oldnim['IdProgram']);
										//echo $sql;
										$std = $db->fetchRow($sql);
										//echo var_dump($std);
										if ($std) {
											$row =$feeStructure->getApplicantFeeStructure($std['IdIntake'],$std['IdProgram'],$student_category,$std['IdBranch'],$std['IdProgramMajoring']);
											//echo var_dump($std); echo var_dump($row);
												
										}
										//exit;
									}
								}
								//exit;
							}
							//echo var_dump($row);exit;
							if ($row) {
								$fee_structure = $row;
								//echo var_dump($row);
								$this->view->fee_structure=$fee_structure;
								$amount=0;
								//echo var_dump($bundleDetail); 
								//echo var_dump($std);exit;
								foreach ($bundleDetail as $key1=>$value) {
									 
									$invoicedet = $invoiceDb->getInvoiceFee($idsemester,$registration['IdStudentRegistration'], $fee_structure['fs_id'], $value['fee_item'], $value['percentage'],"1",$idactivity);
								 	//echo var_dump($invoicedet);
									if ($invoicedet['amount']>0) $bundleDetail[$key1]['fee']=$invoicedet;
									else unset($bundleDetail[$key1]);
									$amount=$amount+$invoicedet['amount'];
								}
						
								//exit;
							}
					 
						} 
						//echo var_dump($bundleDetail);exit;
						if ($bundleDetail!=array() || $restamount!=array()) {
							$act[$key]['bundledetail']=$bundleDetail;
							$act[$key]['level']=$current_level;
						} else unset($act[$key]);
			 		 }  else unset($act[$key]);
				}
				//echo var_dump($act);
				//$this->view->activity= $act;
			} else if ($idinvoice!='' && $act) {
					//invoice
					
					$invoice=$invoiceDb->getData($idinvoice);
					$idactivity=$invoice['idactivity'];
					$idsemester=$invoice['semester'];
					$this->view->fee_structure=array('fs_id'=>$invoice['fs_id']);
					foreach ($act as $key=>$value) {
						$act[$key]['idinvoice']=$idinvoice;
						$act[$key]['level']='';
						if ($value['idActivity']==$invoice['idactivity'] && $value['IdSemesterMain']==$invoice['semester']) {
						if ($invoice)  
							$bundleDetail=$invoiceDetailDb->getInvoiceDetail($idinvoice);
							foreach ($bundleDetail as $idx=>$item) {
								$bundleDetail[$idx]['fee']=array('amount'=>$item['amount'],
																'fi_id'=>$item['fi_id']
								);
							}
							$act[$key]['bundledetail']=$bundleDetail;
							$act[$key]['invoice']=$invoice;
							$act[$key]['invoicerest']=array();
							$bundle=$dbBundle->getCurrentSetup(1, $program['IdCollege'], $std['IdProgram'], $std['IdBranch'], $idsemester,$idactivity,$std['IdProgramMajoring']);
							$act[$key]['bundle']=$bundle;
						} else unset($act[$key]);
						
					}
					//$this->view->activity= $act;
					
				} else  if ($idinvoice!='') {
					$invoice=$invoiceDb->getData($idinvoice);
					$idactivity=$invoice['idactivity'];
					$idsemester=$invoice['semester'];
					$this->view->fee_structure=array('fs_id'=>$invoice['fs_id']);
					$semester = $semesterDb->fnGetSemestermaster($idsemester);
					$act[0]['idacadyear']=$semester['idacadyear'];
					$act[0]['idActivity']=$idactivity;
					$act[0]['id']='';
					$act[0]['idinvoice']=$idinvoice;
					$act[0]['level']='';
					if ($invoice) {
						$bundleDetail=$invoiceDetailDb->getInvoiceDetail($idinvoice);
							foreach ($bundleDetail as $idx=>$item) {
								$bundleDetail[$idx]['fee']=array('amount'=>$item['amount'],
										'fi_id'=>$item['fi_id']
								);
							}
							$act[0]['bundledetail']=$bundleDetail;
							$act[0]['invoice']=$invoice;
							$act[0]['invoicerest']=array();
							$bundle=$dbBundle->getCurrentSetup(1, $program['IdCollege'], $std['IdProgram'], $std['IdBranch'], $idsemester,$idactivity,$std['IdProgramMajoring']);
							$act[0]['bundle']=$bundle;
							$act[0]['IdSemesterMaster']=$semester['IdSemesterMaster'];
							$act[0]['SemesterMainName']=$semester['SemesterMainName'];
					} 
					 
					
					
					
				} else {
				 
					echo "Jadwal Pembuatan Invoice Belum dibuka, Hubungi admin Fakultas masing-masing";
					exit;
				}
				
			//discount calculation
		 	foreach ($act as $key=>$actitem) { 
				if (isset($actitem['bundledetail'])) {
					foreach ($actitem['bundledetail'] as $idxitem=>$item) {
						//echo $std['Strata_code_EPSBED'];	
						//discount processing
						$dbDiscountSetup=new Studentfinance_Model_DbTable_DiscountMain();
						$discounttype=$dbDiscountSetup->getDiscountType();
						foreach ($discounttype as $idx=>$value) {
							$iddiscount=$value['dt_id'];
							$discountSetup=$dbDiscountSetup->getCurrentSetup(1, $registration['Strata_code_EPSBED'],$registration['IdCollege'], $registration['IdProgram'], $registration['IdBranch'], $idsemester, $registration['IdProgramMajoring'],$iddiscount);
							//echo var_dump($discountSetup);
							//echo $iddiscount;
							//echo '<br>';
							if  ($discountSetup) {
								$discounttype[$idx]['discount']=$discountSetup;
							} else unset($discounttype[$idx]);
						}
					//echo var_dump($discounttype);
					//exit;
						if ($discounttype) {
							foreach ($discounttype as $idx=>$value) {
								$setup=$value['discount'];
								$maind=$setup['id_dm'];
								//echo '<br>'.$maind;
								$valid="0"; $validsem="0"; $validintake="1"; $validlevel="1"; $validstd="1";
								if ($dbDiscountSetup->isSemesterApplied($maind)) {
									if ($dbDiscountSetup->isSemesterApplied($maind,$idsemester)) $validsem="1";
								} 
								 
									if ($dbDiscountSetup->isLevelApplied($maind)) {
										$level=$actitem['level'];//$this->getLevel($std['IdStudentRegistration'], $idsemester, $std['IdIntake']);
										if ($dbDiscountSetup->isLevelApplied($maind,$level)) $validlevel="1";
										else $validlevel="0";
										 
									} 
										
									if ($dbDiscountSetup->isIntakeApplied($maind)) {
										if ($dbDiscountSetup->isIntakeApplied($maind,$registration['IdIntake'])) $validintake="1";
										else $validintake="0";
									}
									//echo '<br>discound';
									if ($dbDiscountSetup->isStudentApplied($maind)) {
									//	echo $registration['IdStudentRegistration'];
										if (!$dbDiscountSetup->isStudentApplied($maind,$registration['IdStudentRegistration'])) $validstd="0";
										 
									}
								// echo $maind.'=';echo $validsem;echo $validlevel;echo $validintake;echo $validstd; echo '<br>';
								if (!($validsem=="1" && $validlevel=="1" && $validintake=="1" && $validstd=="1")) unset($discounttype[$idx]);
							}
							//echo var_dump($discounttype);exit;
							if ($discounttype) {
								foreach ($discounttype as $idx=>$value) {
									$setup=$value['discount'];
									$maind=$setup['id_dm'];
									$discount=$dbDiscountSetup->getDiscount($maind,$item['fi_id']);
									if ($discount) {
										$discount["type"]=$value['dt_discount'];
										$discount["id_dm"]=$setup['id_dm'];
										$act[$key]['bundledetail'][$idxitem]['discount'][]=$discount;
									}
								}
								//echo var_dump($discounttype);
							} 
						}
						
					}
				}
		 	}
		 	$this->view->activity= $act;
	}
	 
	public function  getLevel($IdStudentRegistration,$idsemester,$intake) {
		
		$db = Zend_Db_Table::getDefaultAdapter();
		$semselect=$db->select()
		->from('tbl_semestermaster')
		->where('IdSemesterMaster=?',$idsemester);
		$semester=$db->fetchRow($semselect);
		
		$sql = $db->select()
		->from(array('sss' => 'tbl_studentsemesterstatus'), array(new Zend_Db_Expr('max(Level) as Level')))
		->join(array('b'=>'tbl_semestermaster'),'b.IdSemesterMaster=sss.IdSemesterMain')
		->where('sss.IdStudentRegistration  = ?', $IdStudentRegistration)
		->where('b.IdSemesterMaster=?',$idsemester);
			
		$result = $db->fetchRow($sql);
		if (!$result) {
		
			
		
			$sql = $db->select()
			->from(array('sss' => 'tbl_studentsemesterstatus'), array(new Zend_Db_Expr('max(Level) as Level')))
			->join(array('b'=>'tbl_semestermaster'),'b.IdSemesterMaster=sss.IdSemesterMain')
			->where('sss.IdStudentRegistration  = ?', $IdStudentRegistration)
			->where('b.SemesterMainStartDate<= ?',$semester['SemesterMainStartDate']);
				
			$result = $db->fetchRow($sql);
			if (!$result) $result['Level']=1;
			else $result['Level']=$result['Level'];
			//echo $sql;
		}
		else if( $result['Level'] ){
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
		return $current_level;
	}
	  
}
