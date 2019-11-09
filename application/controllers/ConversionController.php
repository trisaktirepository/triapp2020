<?php
/**
 *  @author alif 
 *  @date Oct 09, 2013
 */
 
class ConversionController  extends Zend_Controller_Action {

	private $_DbObj;
	private $_sis_session;
	private $dbLandscape;
	private $dbProgram;
	
	public function init(){
		
		$this->_sis_session = new Zend_Session_Namespace('sis');
		$this->dbLandscape=new App_Model_General_DbTable_Landscape();
		$this->dbProgram=new App_Model_General_DbTable_Program();
	}
	
	
	public function studentListAction() {
		$this->view->title = $this->view->translate("Choose Conversion Plan");
		$auth = Zend_Auth::getInstance();
		$userId = $auth->getIdentity()->registration_id; 
		$dbStudent=new App_Model_Record_DbTable_ConversionResult();
		$dbReg=new App_Model_Registration_DbTable_Studentregistration();
		$student=$dbReg->getStudentRegistrationHistory($userId);
		$dbConv=new App_Model_Record_DbTable_Conversion();
		$this->view->conversionmain=$dbConv->getAllSetupMain($student['IdProgram'], $student['IdProgramMajoring'], $student['IdBranch']);
	}
	
	public function viewConversionResultAction(){
		$this->view->title = $this->view->translate("Student Conversion Result");
		$auth = Zend_Auth::getInstance();
		$IdStudentRegistration = $auth->getIdentity()->registration_id;
		$userId = $auth->getIdentity()->registration_id; 
		$dbConv=new App_Model_Record_DbTable_Conversion();
		$dbConvResult=new App_Model_Record_DbTable_ConversionResult();
		$studentRegDB = new App_Model_Exam_DbTable_StudentRegistration();
		$studentGradeDB = new App_Model_Exam_DbTable_StudentGrade();
		if ($this->getRequest()->isPost()){
			$formData = $this->getRequest()->getPost();
			$idmain=$formData['IdConversionMain'];
			$this->view->idmain=$idmain;
			$main=$dbConv->getConversionMain($idmain);
			$this->view->landscape=$main['IdLandscape'];
			$this->view->landscapenew=$main['IdLandscapeNew'];
			 
			$result=array();
			$key=0;
		//	foreach ($formData['chk'] as $key=>$IdStudentRegistration) {
			$result[$key]['courses']=$dbConvResult->getData($IdStudentRegistration, $idmain);
			$student=$studentRegDB->getStudentInfo($IdStudentRegistration);
			$result[$key]['student']= $student;
		    
			 $student_grade = $studentGradeDB->getStudentGradeInfo($IdStudentRegistration);
		    //echo var_dump($student_grade);exit;
			$result[$key]['grade'] = $student_grade;
			//calculate new cgpa
			$convresult=$dbConvResult->getConversionResult($IdStudentRegistration, $idmain);
			$totalch=0;
			$totalpoint=0;
			foreach ($convresult as $value) {
				if ($value['grade_point']>=2) {
					$totalch=$totalch+$value['CreditHoursNew'];
					$totalpoint=$totalpoint+$value['CreditHoursNew']*$value['grade_point'];
				}
			}
			$newcgpa=$totalpoint/$totalch;
			$result[$key]['newgrade']=array('newcgpa'=>$newcgpa,'newcredithour'=>$totalch);
				
			//}
			
			$this->view->conversion_list=$result;
		}
	}
	public function conversionApprovalAction(){
		$auth = Zend_Auth::getInstance();
		$userid = $auth->getIdentity()->registration_id; 
		if ($this->getRequest()->isPost()){
			$formData = $this->getRequest()->getPost();
			$dbConvResult=new App_Model_Record_DbTable_ConversionResult();
			$dbConv=new App_Model_Record_DbTable_Conversion();
			$idmain=$formData['IdConversionMain'];
			
			if (isset($formData['IdSubjectNew'])) {
				//save optional
					
				foreach ($formData['IdSubjectNew'] as $key=>$value) {
					$data=$dbConvResult->getDataById($key);
					//echo var_dump($data);exit;
					if ($value!='') {
					if ($data['Status']=='1-1(O)') {
						$data['IdSubjectNew']=$value;
						$data['remark']='Ok';
						$data['Grade_name_new']=$data['Grade_name'];
						$data['Grade_point_new']=$data['Grade_point'];
						$data['Final_mark_new']=$data['Final_mark'];
						$dbConvResult->updateConversionResult($data, $key);
						} else {
							$idstd=$data['idStudentRegistration'];
							$idmain=$formData['IdConversionMain'];
							$idsubjectnew=$data['IdSubjectNew'];
							$landscape=$formData['IdLandscape'];
							$landscapenew=$formData["IdLandscapeNew"];
							$subjectSource=$dbConv->getConversionBySubjectNew($idmain,$landscape, $landscapenew, $idsubjectnew);
							//echo var_dump($subjectSource);exit;
							foreach ($subjectSource as $item) {
								$idsubject=$item['IdSubject'];
								$data=$dbConvResult->getDataByIdSubject($idstd, $idsubject, $idmain);
								$idconversionresult=$data['IdConversionResult'];
								$data['IdSubjectNew']=$value;
								$data['remark']='Ok';
								unset($data['IdConversionResult']);
								//echo $idconversionresult;
								$dbConvResult->updateConversionResult($data, $idconversionresult);
							}
							//exit;
						}
					}
			
				}
			}
			
			 
			$dbGrade=new App_Model_Exam_DbTable_Grade();
			$dbStudentRegSubject=new App_Model_Exam_DbTable_StudentRegistrationSubject();
			foreach ($formData['chk'] as $value) {
				$idconversionresult=$value;
				$idprogram=$formData['IdProgram'][$idconversionresult];
				$result=$dbConvResult->getRecord($idconversionresult);
				if ($result) {
					$dbConvResult->updateConversionResult(array('ApprovalByStd'=>$userid,'ApprovalByStd_dt'=>date('Y-m-d H:i:sa')), $idconversionresult);
				}
				
			}
			 
			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'conversion', 'action'=>'student-list'),'default',true));
		}
	}
	
}
 ?>
