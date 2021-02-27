<?php

class CourseRegistrationController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }
    
    public function ajaxSendSmsAction(){
    
    	$this->_helper->layout->disableLayout();
    	$idstd = $this->_getParam('idstudent',null);
    	$iduser = $this->_getParam('iduser',null);
    	$hp = $this->_getParam('hp',null);
    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
    	$ajaxContext->addActionContext('view', 'html');
    	$ajaxContext->initContext();
    
    	$dbconf=new App_Model_Record_DbTable_Confirmation();
    	    
    	$rand=$dbconf->genRandomNumber();
    	$dbconf->addData(array('confirmation'=>$rand,'id_user'=>$iduser,'idStudentRegistration'=>$idstd,'dt_entry'=>date("Y-m-d H:m:s")));
    		
    	$dbsms=new App_Model_Smsgateway_DbTable_SmsGateways();
    	$message="Masukan nomor konfirmasi ".$rand." untuk persetujuan proses";
    	$status="";
    	$status=$dbsms->sendMessage($message, $hp,0 ,$iduser,$idstd);
    
    	$ajaxContext->addActionContext('view', 'html')
    	->addActionContext('form', 'html')
    	->addActionContext('process', 'json')
    	->initContext();
    
    	$json = Zend_Json::encode($status);
    
    	echo $json;
    	exit();
    }
    
    public function ajaxCheckConfirmAction(){
    
    
    	$this->_helper->layout->disableLayout();
    	$idstd = $this->_getParam('idstudent',null);
    	$confirm = $this->_getParam('confirm',null);
    		
    	$ajaxContext = $this->_helper->getHelper('AjaxContext');
    	$ajaxContext->addActionContext('view', 'html');
    	$ajaxContext->initContext();
    
    	$dbconf=new App_Model_Record_DbTable_Confirmation();	
    	$ntry=$dbconf->isIn($confirm, $idstd);
    	if ($ntry==0) $status='Valid'; else $status=$ntry;
    
    	$ajaxContext->addActionContext('view', 'html')
    	->addActionContext('form', 'html')
    	->addActionContext('process', 'json')
    	->initContext();
    
    	$json = Zend_Json::encode($status);
    
    	echo $json;
    	exit();
    }
    
    
    public function courseRegisterAction(){
    	
    	$auth = Zend_Auth::getInstance();    	
    	
    	$dbCourseMinor=new App_Model_Registration_DbTable_CourseGroupMinor();
    	$dbCourseMinorStd=new App_Model_General_DbTable_CourseGroupStudentMinor();
    	$dbCourseMinorSch=new App_Model_Registration_DbTable_CourseGroupScheduleMinor();
    	$dbStdRegsubject=new App_Model_Record_DbTable_StudentRegSubjects();
    	 
    	$appl_id = $auth->getIdentity()->appl_id; 
    	$registration_id = $auth->getIdentity()->registration_id;    
        //print_r($auth->getIdentity());
    	$this->view->appl_id = $appl_id;
    	$this->view->IdStudentRegistration = $registration_id;
    	$studentRegDB = new App_Model_Record_DbTable_StudentRegistration();
    	$student = $studentRegDB->getStudentInfo($registration_id);
    	
    	
    	$idSemester = $this->_getParam('idSemester', 0);
    	$this->view->idSemester = $idSemester;
        if ($idSemester=='') $idSemester=0;
        $errMsg = $this->_getParam('errmsg', 0);
    	$this->view->errMsg = $errMsg;
    	
    	$this->view->title = $this->view->translate("Pre Course Registration");
    	
        $Dbinvoice=new Studentfinance_Model_DbTable_InvoiceMain();
		$activity=$Dbinvoice->isAnyOpenInvoice($registration_id);
		if ($activity!=0 && $student['IdProgram']!=60) $Dbinvoice->dispatcher($registration_id,$activity);    	 
    	// check barring Registration
    	$dbRelease=new App_Model_Record_DbTable_Barringrelease();
    	$GroupList = new App_Model_Registration_DbTable_CourseGroup();
    	
    	
    	$paket='';
    	if ($dbRelease->isIn($registration_id, $idSemester))
    		$this->_redirect($this->view->url(array('module'=>'default','controller'=>'course-registration', 'action'=>'block-view-student','idSemester'=>$idSemester,'errMsg' => 1),'default',true));
    	//=============================================
    	
    	//1) Get Student Academic Info
    	
    	//get pprogram
    	$dbProgram= new App_Model_General_DbTable_Program();
    	$program=$dbProgram->getData($student['IdProgram']);
    	$chmax=$program['CreditHoursMax'];
    	$this->view->hidelecturer=$program['hide_lecturer'];
    	$gradeLateCheck=$program['grade_late_check'];
    	if ($program['ProgramCode']=="0300" ||$program['ProgramCode']=="0310" ||$program['ProgramCode']=="0400" ||$program['ProgramCode']=="0410" ) $this->view->modul="1"; else $this->view->modul="0";
    	//----
    	//get semester info
    	$dbsem=new App_Model_General_DbTable_Semestermaster();
    	$sem=$dbsem->getSemester($idSemester);
    	//student should ask for dosen wali permission in MeetAdvisor equal to 1
    	
    	if ($student['MeetAdvisor']=="1" && $idSemester>0 && $sem['IsCountable']=='1') {
    		$dbConseling=new Counseling_Model_IssuesDetail();
    		if (!$dbConseling->isPermit($registration_id, $idSemester))
    			$this->_redirect($this->view->url(array('module'=>'default','controller'=>'course-registration', 'action'=>'block-view-student','idSemester'=>$idSemester,'errmsg' => 2),'default',true));
    			 
    	}
    	//=============================================== 	  
		    	       
		        
		    	$programid=$student['IdProgram'];
		    	$idLandscape=$student['IdLandscape'];
		        $this->view->student = $student;    
		    	$gradepointmin=$student['Gradepoint_rmd'];
		    	if ($GroupList->isPackage($programid, $student['IdBranch'])) $paket="1"; else $paket="0";
               //print_r($student);
		    	//2) Get available semester for registration		    	
		    	$semesterDB = new App_Model_General_DbTable_Semestermaster();
		    	if(isset($auth->getIdentity()->adminlogin)){
		    		$this->view->semester = $semesterDB->getSemestermasterList();	
		    	}else{
		    		$dbExecption=new App_Model_Registration_DbTable_RegistrationException();
                    if($idSemester != 0)
                    {
                        /**
                         **Check if the semester still open, if not redirect it back to 0 semester id
                         **/
                        $semesterValidate = $semesterDB->getSemesterCourseRegistrationValidate($student["IdProgram"],$student['scheme'],$idSemester,$student['IdIntake']);
                        //echo 'semester='.var_dump($semesterValidate);exit;
                        if(empty($semesterValidate))
                        {
                        	//check for exception
                        	
                        	$exception=$dbExecption->getDataByStd($student['registrationId'], $idSemester);
                        	if (!$exception)
                            	$this->_redirect($this->view->url(array('module'=>'default','controller'=>'course-registration', 'action'=>'course-register','idSemester' => 0)));
							//else $this->view->semester=$exception;
                        }
                        
                    }
                    else
                    {	
                    	$semestercalendar=$semesterDB->getSemesterCourseRegistration($student["IdProgram"],$student['scheme'],$student['IdIntake']);
                        if ($semestercalendar) $this->view->semester = $semestercalendar;
                        else {
                        	$exception=$dbExecption->getDataByStd($student['registrationId'], null);
                        	
                        	if ($exception) $this->view->semester = $exception;
                        	else echo "Pengisian KRS belum dibuka/telah ditutup";//$this->_redirect($this->view->url(array('module'=>'default','controller'=>'course-registration', 'action'=>'course-register','idSemester' => 0)));
                        }
                    }
		    	}
		    	//print_r($this->view->semester);
		    	//3) Get Student Landscape
		    	$landscapeDb = new App_Model_Record_DbTable_Landscape();
		        $landscape = $landscapeDb->getData($idLandscape);
		        $dbSemesterStatus=new App_Model_Record_DbTable_Studentsemesterstatus();
		        $dbSemester=new App_Model_General_DbTable_Semestermaster();
		        $landscapeSubjectDB = new App_Model_Record_DbTable_LandscapeSubject();
		         
		        $this->view->landscape = $landscape;
		        //ceh for package
		        
		        //cek for package course register
		        if ($paket=='1') {
		        	//-- if Level > max semester curriculum then not package
		        	//get maks level
		        	$level=$dbSemesterStatus->getMaxLevelPrevious($registration_id,$idSemester);
		        	$semester=$dbSemester->fnGetSemestermaster($idSemester);
		        	$semcounttype=$semester['SemesterCountType'];
		        	//if (($level+1)%2 == $semcounttype%2) {
		        	$landscapeSem=$level+1;
		        //	} else $landscapeSem=$level+2;
		        	//echo $landscapeSem;exit;
		        	//get from landscape subject offered from current level
		        	if ($landscapeSem>$landscape['SemsterCount']) $paket="0";
		        }
		        $prerequisiteDb = new App_Model_Record_DbTable_LandscapeSubject();
                $Schedule = new App_Model_Record_DbTable_StudentRegistration();
                
                if(isset($idSemester) && $idSemester!=0){
                	if($this->_getparam("dp",0)!=0){
		        		$sregdb = $subjectDB = new App_Model_Record_DbTable_StudentRegSubjects();
		        		$sregdb->drop($this->_getparam("dp",0));
		        		//echo "drop ".$this->_getparam("dp",0);
		        	}
		        	//4) Get Selected Semester Info
		        	$semesterDB = new App_Model_General_DbTable_Semestermaster();
		    		$sel_semester = $semesterDB->fnGetSemestermaster($idSemester);
		    		$this->view->sel_semester = $sel_semester;
		    		$semesterFunctionType=$sel_semester['SemesterFunctionType'];
		    		//5) Get total registered semester 
		         	$studentSemesterDB = new App_Model_Record_DbTable_Studentsemesterstatus();
		         	if($sel_semester["IsCountable"]==1){
		         		$semesterStudi = $studentSemesterDB->getCountableRegisteredSemester($registration_id,$idSemester);         	
		         		$total_countable_registered_semester = count($semesterStudi);
		         		$levelsem=$studentSemesterDB->getRegisteredSemesterByLevel($registration_id, $total_countable_registered_semester);
		         		$idsemesterstatus=$levelsem['idstudentsemsterstatus'];
		         		
		         		//get sg_cum_allcredits
		         		$gradeDb=new App_Model_Exam_DbTable_StudentGrade();
		         		$grades=$gradeDb->getStudentGradeInfo($registration_id);
		         		if ($grades) {
		         			$all_creditthours=$grades['sg_all_cum_credithour']; 
		         			$all_creditthours_pass=$grades['sg_cum_credithour'];
		         			$stdcgpa=$grades['sg_cgpa'];
		         		} else {
		         			$all_creditthours=0;
		         			$all_creditthours_pass=0;
		         			$stdcgpa=0;
		         		}
		         	//check for financial report
		         		
						$sfhelper= new icampus_Function_Studentfinance_PaymentInfo();
                		$pymtinfo=$sfhelper->getStudentPaymentInfo($registration_id,$idSemester,null,'1');
                		if($pymtinfo["invoices"]==''){
                			echo "belum ada invoice";
                			//$this->view->paymentstatus=0;
                			$this->view->paymentstatus=0;
                			
                		}else{
                			//check dah x dak balance 
                			if($pymtinfo["total_invoice_balance"]<= 0 ){
                				$this->view->paymentstatus=1;
                				//echo "lunas";
                			}else{
                				$this->view->paymentstatus=0;
                				//echo "hutang";
                			}
                		}
                		
                		//kalau ada payment exception utk course registration
                		if(isset($pymtinfo["exception"][1])){
                			$this->view->paymentstatus=1;
                		}

                		if($this->view->paymentstatus==0){
                			
                			return 0;
                		}
		         	}else{
		         		$this->view->paymentstatus=1;
                		$total_countable_registered_semester="-";
                	}
                    //get registered subject for selected semester
                    
                	//cek if any last block module has registered
                	$lastblock=null;
                	if ($programid==11) {
                		
                		if ($dbStdRegsubject->isBlock7Registered($registration_id, $idLandscape)) $lastblock=7; else $lastblock=0;
                		 
                	}
                	
					$courseRegisterDb = new App_Model_Record_DbTable_StudentRegistration();
					//get Registed Owrneship
					$courses = $courseRegisterDb->getCourseRegisteredBySemester($registration_id,$idSemester);		         	
					
					//get rule==================for cross Branch coursetaking currently for FEB
						$rules=$GroupList->getRule($student['IdProgram'], $student['IdBranch']);
						if ($rules) {
							$groupsclass=array();
							$this->view->rule=$rules;
							foreach ($courses as $value) {
								$groupsclass[]=$value['IdCourseTaggingGroup'];
							}
							if ($groupsclass!=array()) {
								$groupslist=implode(',', $groupsclass);
								$regOwner =$courseRegisterDb->getRegisterMngCourse($groupslist);
								$this->view->registedclass=$regOwner;
							} else $this->view->registedclass=array();
						} else {
							$this->view->rule=array();
							$this->view->registedclass=array();
						}
				   //========end rule===============
					
		         	$normalSchedule = array(
                            'Monday' => array(),
                            'Tuesday' => array(),
                            'Wednesday' => array(),
                            'Thursday' => array(),
                            'Friday' => array(),
		         			'Saturday' => array()
                        );
		         	$dateSchedule   = array();
                     $dateExam = array();
                    if($courses){
  						$this->view->registeredcourses = $courses;
                        
                        /**
                         *START Generate Schedule
                         */
                          $DetailSubject = new App_Model_Record_DbTable_SubjectMaster();
                                    
                        foreach($courses as $y => $course)
                        {
                        	$idSubject = $course['IdSubject'];
                            $idGroup   = $course['IdCourseTaggingGroup'];
                            
                            if($idGroup != 0)
                            {
                                
                                $schedules = $Schedule->getSubjectSchedule($idSubject,$idGroup);
                            
                                if(!empty($schedules))
                                {
                                    $x = 0;
                                    $y = 0;
                                    
                                   
                                    foreach ($schedules as $schedule)
                                    {
                                        if($schedule['sc_date']!= '')
                                        {
                                            /**
                                             *Arrange array by date
                                             */
                                            
                                            $start_time = explode(':',$schedule['sc_start_time']);
                                            $end_time   = explode(':',$schedule['sc_end_time']);
                                            $start      = $start_time[0];
                                            $end        = $end_time[0];
                                            
                                            $detailSubject = $DetailSubject->getData($schedule['IdSubject']);
                                            
                                            $dateSchedule[$schedule['sc_date']]['sc_day'] = $schedule['sc_day'];
                                            
                                            
                                            for($x=(int)$start;$x<$end;$x++)
                                            {
                                                
                                                $xKey = sprintf("%02d",$x);
                                                
                                                if(isset($dateSchedule[$schedule['sc_date']][$xKey]))
                                                {
                                                    /**
                                                     *If array has value, append new subject
                                                     */
                                                    @$dateSchedule[$schedule['sc_date']][$xKey] = $dateSchedule[$schedule['sc_date']][$xKey].'<br />'. $detailSubject['ShortName'];  
                                                }
                                                else
                                                {
                                                    /**
                                                     * Assign value to array for the first time
                                                     */
                                                     @$dateSchedule[$schedule['sc_date']][$xKey] = $detailSubject['ShortName'];
                                                }
                                                                                             
                                            }
                                            
                                           
                                        }
                                        else
                                        {
                                            /**
                                             *Arrange array for normal schedule (Monday,Tuesday ....)
                                             */
                                            $start_time = explode(':',$schedule['sc_start_time']);
                                            $end_time   = explode(':',$schedule['sc_end_time']);
                                            $start      = $start_time[0];
                                            $end        = $end_time[0];
                                            
                                            $detailSubject = $DetailSubject->getData($schedule['IdSubject']);
                                            
                                            //$dateSchedule['sc_day'] = $schedule['sc_day'];
                                            
                                            if(isset($normalSchedule[$schedule['sc_day']][$start]))
                                            {
                                                /**
                                                 *If array has value, append new subject
                                                 */
                                                for($x=(int)$start;$x<$end;$x++)
                                                {
                                                    $xKey = sprintf("%02d",$x);
                                                    
                                                    @$normalSchedule[$schedule['sc_day']][$xKey] = $normalSchedule[$schedule['sc_day']][$xKey].'<br />'. $detailSubject['ShortName'];
                                                }
                                            }
                                            else
                                            {
                                                /**
                                                 * Assign value to array for the first time
                                                 */
                                                for($x=(int)$start;$x<$end;$x++)
                                                {
                                                    $xKey = sprintf("%02d",$x);
                                                    
                                                    @$normalSchedule[$schedule['sc_day']][$xKey] = $detailSubject['ShortName'];
                                                }
                                            }
                                            
                                        }
                                    }//end foreach $schedule
                                    
                                }//end empty $schedule
                                
                                //GENERATE EXAM SCHEDULE
                                $Examschedules = new App_Model_General_DbTable_ExamGroup();
                                $exams = $Examschedules->getExamSchedule($idSemester,$idSubject,$student["IdProgram"]);
                               
                                if(!empty($exams))
                                {
                                    $x = 0;
                                    $y = 0;
                                    
                                   foreach($exams as $key => $exam)
								   {
										/**
										 *Arrange array by date
										 */
												
										$start_time = explode(':',$exam['eg_start_time']);
										$end_time   = explode(':',$exam['eg_end_time']);
										$start      = $start_time[0];
										$end        = $end_time[0];
											
										//$dateExam[$exam['eg_date']][$exam['eg_date']] = $schedule['sc_day'];
										$startS = strtotime($exam['eg_start_time']);        
										$endS   = strtotime($exam['eg_end_time']);        
										
										$ten    = 10 * 60;
										while ($startS <= $endS)
										{
											$keyTime = date("H:i:s",$startS);
											
											$detailSubject = $DetailSubject->getData($exam['eg_sub_id']);
											
											if(isset($dateExam[$exam['eg_date']][$keyTime]))
											{
												$dateExam[$exam['eg_date']][$keyTime] = $dateExam[$exam['eg_date']][$keyTime].' <br />'.$detailSubject['ShortName'];
											}
											else
											{
												$dateExam[$exam['eg_date']][$keyTime] = $detailSubject['ShortName'];
											}
											
											$startS = $startS + $ten;
										}
                                    } 
                                }
                                
                            }//end if idgroup != 0
                            
                        }// end foreach $courses
                        /**
                         *END Generate Schedule
                         */
		         	}
                   
                    ksort($dateSchedule);
                    
                    $this->view->normalSchedule = $normalSchedule;
                    $this->view->dateSchedule   = $dateSchedule;
                    $this->view->dateExam       = $dateExam;
		         	// echo '<pre>';
                    // print_r($normalSchedule);
                    // echo '</pre>';
                    /*
                     * Dapatkan id semester
                     */
                    $semesterId = $semesterDB->getAllSemesterMasterId($sel_semester['idacadyear'],$sel_semester['SemesterCountType']);
                    //print_r($semesterId);
                    
                    $i = 0;
                    foreach($semesterId as $key => $value)
                    {
                        $semesterIds[$i] = $value['IdSemesterMaster'];
                        $i++;
                    }
                    
                    $stringSemesterIds = implode($semesterIds,',');
                    
                    
                    
                    /*
                     * Get All Subject Based On semesterId $stringSemesterIds
                     */
                     $courses_credit = $courseRegisterDb->getCourseRegisteredBySemesterId($registration_id,$stringSemesterIds);
  				
                    //totalkan kredit
                    $total_credit = NULL;
                    foreach($courses_credit as $a => $b)
                    {
                        $total_credit = $total_credit + $b['CreditHours'];
                    }
                    
                    if($total_credit != NULL)
                    {
                        $this->view->total_credit = $total_credit;
                    }
                    else
                    {
                        $this->view->total_credit = 0;
                        $total_credit = 0;
                    }
              
                    
                    if($sel_semester["IsCountable"]==1){		
		         		
		         		//cek if selected semester is running semester 
 		         		if(strtotime($sel_semester["SemesterMainStartDate"]) <= strtotime('today') && strtotime($sel_semester["SemesterMainEndDate"]) >= strtotime('today')){
 		         			 //$current_level_semester_to_register =    $total_countable_registered_semester //ori explain why?	
 		         			 $current_level_semester_to_register =    $total_countable_registered_semester+1;
 		         			 $this->view->current_level = $current_level_semester_to_register;		         		  
		         			 $this->view->semester_level = $current_level_semester_to_register;		
 		         		}else{
 		         			 $current_level_semester_to_register =    $total_countable_registered_semester+1;	
 		         			 $this->view->current_level = $current_level_semester_to_register;		         		  
		         			 $this->view->semester_level = $total_countable_registered_semester+1;	
 		         		}
 		         		//echo $current_level_semester_to_register;exit;
 		         		//Utk check CH Limitation
 		         		$chlimitDB = new App_Model_Registration_DbTable_Chlimit();
 		         		$chlimit = $chlimitDB->isLimitSet($student["IdProgram"],$student["IdIntake"]);
 		         		//print_r($chlimit);exit;
 		         		if($chlimit){
	 		         		$previousSemLevel = $current_level_semester_to_register-1;
	 		         		$prvsem = $studentSemesterDB->getRegisteredSemesterByLevel($registration_id,$previousSemLevel);
	 		         		//print_r($prvsem);
	 		         		$studentGradeDb = new App_Model_Exam_DbTable_StudentGrade();
			  				$sgrade = $studentGradeDb->getGradebySemester($registration_id,$prvsem["idstudentsemsterstatus"],1);
	 		         		//print_r($sgrade);
	 		         		if(!$sgrade["sg_univ_gpa"]){ 
                                $sgrade["sg_univ_gpa"] = 0.00;
                            }
                            
                            $crlimit = $chlimitDB->checklimit($chlimit["clid"],$sgrade["sg_univ_gpa"]);
 		         			
                            if ($paket=="1") $this->view->crlimit = $chmax;
 		         			else $this->view->crlimit = $crlimit;
 		         		}else{
 		         			
 		         			$this->view->crlimit=$chmax;//government policy
 		         			//this->view->crlimit=14;//temp
 		         		}
 		         		
 		         		//get offered subjects====================
 		         		
		  				$subjectDB = new App_Model_Record_DbTable_StudentRegSubjects();
		  				 
		  				if ($paket=='1') {
		  					
		  					$landscape_subject = $subjectDB->getSubjectOfferedPackage($registration_id,$student["IdLandscape"],$idSemester,$landscape["LandscapeType"],null,$landscapeSem,$lastblock,$programid,$student['IdBranch'],$student['IdIntake']);
		  					
		  				} else {
		  					$landscape_subject = $subjectDB->getSubjectOfferedReg($registration_id,$student["IdLandscape"],$idSemester,$landscape["LandscapeType"],null,null,$lastblock,$programid);		
		  				}
		  				
		  				$subjectOffered = $landscape_subject; 
                        
		  				//check for medicine profession
		  				if ($student['IdProgram']==60) {
		  				
		  					$studentRegSubjectDB = new App_Model_Record_DbTable_StudentRegSubjects();
		  					//student can not retaken of passed module
		  					 
		  					$outerlastblock=false;
		  					foreach ($subjectOffered as $key=>$subjects){
		  						if ($studentRegSubjectDB->isPass($registration_id, $subjects['IdSubject'])) {
		  							unset($subjectOffered[$key]);
		  						}
		  					}
		  					
		  					///cek daring luring prerequisite
		  					
		  					foreach ($subjectOffered as $key=>$subjects){
		  						if ($studentRegSubjectDB->isLuring($idSemester,$registration_id, $subjects['IdSubject']) && !$studentRegSubjectDB->tookOnline($idSemester,$registration_id, $subjects['IdSubject'])) {
		  							unset($subjectOffered[$key]);
		  						}
		  					}
		  					
		  					
		  				
		  				}
                       	$dbGroupSchedule=new App_Model_Registration_DbTable_CourseGroupSchedule();
                        
                        foreach ($subjectOffered as $key => $val)
                        {
                            $groupLists = $GroupList->getGroupList($val["IdSubject"],$idSemester,$student['IdProgram'],null,$student['IdBranch']);
                           // echo var_dump($groupLists);echo "<br>";
                          
                            if(!empty($groupLists))
                            {
                            	//$hasminor="0";
                            	//get micro class
                            	foreach ($groupLists as $keyb=>$grpbesar) {
                            		//$groupLists[$keyb]["hasminor"]="0";
                            		$grpminors=$dbCourseMinor->getGroupList($grpbesar['IdCourseTaggingGroup']);
                            		if ($grpminors) {
                            			//$groupLists[$keyb]["hasminor"]="1";
                            			$groupLists[$keyb]['minor']['show'] = 'no';
                            			$groupLists[$keyb]['groupTotal']=0;
                            			$totalminor=0;
                            			foreach ($grpminors as $keymin=>$grpminor) {
	                            			$sch=$dbCourseMinorSch->getSchedule($grpminor['IdCourseTaggingGroupMinor']);
	                            			$grpminors[$keymin]['sch']=$sch;
	                            			
	                            			$groupLists[$keyb]['groupTotal'] = $groupLists[$keyb]['groupTotal']+ $grpminor['maxstud'];
	                            		
	                            			$totalminor = $dbCourseMinorStd->getTotalStudent($grpminor['IdCourseTaggingGroupMinor']);
	                            		
		                            		//$totalminor = ($totalminorm['Total'] == '') ? 0:$totalminorm['Total'];
		                            		
		                            		$grpminors[$keymin]['currentStud'] = $totalminor;
		                            		
		                            		//$landscape_subject[$key]['currentStud'] = $landscape_subject[$key]['currentStud'] + $total;
		                            		//echo $grpminor['GroupName'].'-'.$totalminor.'<'. $grpminor['maxstud'].'<br>';
		                            		if($totalminor < $grpminor['maxstud'])
		                            		{
		                            			
		                            			$groupLists[$keyb]['minor']['show'] = 'yes';
		                            		}  //else $groupLists[$keyb]['minor']['show'] = 'yes';
		                            		//else {
		                            			//unset($grpminors[$keymin]);
		                            		//}
                            			}
                            			$groupLists[$keyb]['minor']=$grpminors;
                            			//echo var_dump($landscape_subject);exit;
                            		} else $groupLists[$keyb]['minor']=array();
                            		
                            		
                            	}
                                $landscape_subject[$key]['group'] = $groupLists;
                                $landscape_subject[$key]['groupTotal'] = 0;
                                $landscape_subject[$key]['currentStud'] = 0;
                                $landscape_subject[$key]['show'] = 'no';
                                $total=0;
                                //echo "<br>";
                                foreach($groupLists as $x => $y)
                                {
                                	$sch=$dbGroupSchedule->getSchedule($y['IdCourseTaggingGroup']);
                                	$landscape_subject[$key]['group'][$x]['sch']=$sch;
                                    $landscape_subject[$key]['groupTotal'] = $landscape_subject[$key]['groupTotal'] + $y['maxstud'];
                                    
                                    $totalm = $Schedule->getTotalStudentRegistered($y['IdSubject'],$y['IdSemester'],$y['IdCourseTaggingGroup']);
                                    
                                    $total = ($totalm['Total'] == '') ? 0:$totalm['Total'];
                                    
                                    $landscape_subject[$key]['group'][$x]['currentStud'] = $total;
                                
                                    $landscape_subject[$key]['currentStud'] = $landscape_subject[$key]['currentStud'] + $total;

                                  //  echo $total.'='.$y['maxstud'].'<br>';
                                    
                                    if($total < $y['maxstud'])
                                    {
                                        $landscape_subject[$key]['show'] = 'yes';
                                    }
                                }
                            }
                            
                            
                          if ($paket!="1") {
                          	$dbRomm=new App_Model_General_DbTable_Room();
                          	$prerequisiteBlockDb=new App_Model_Record_DbTable_LandscapeBlockSubject();
                          	
                          	//check prerequisite
                          	//===================================
                          	//-------------------------------
                         	if($landscape["LandscapeType"]==43){
	                            $subject_prerequisite = $prerequisiteDb->getCoursePrerequisite($student["IdLandscape"],$val["IdLandscapeSub"]);
                         	 }  else {
                         	 	$subject_prerequisite = $prerequisiteBlockDb->getCoursePrerequisite($student["IdLandscape"],$val["IdLandscapeblocksubject"]);
                         	 	
                         	 }
	                         if(!empty($subject_prerequisite)){
	                            	foreach ($subject_prerequisite as $preq){
	                            		//dah pernah complete blom;
	                            		if ($preq['PrerequisiteType']!='3') {
	                            			if ($preq["PrerequisiteType"]=='8') {
	                            				//TOEFL code 453
	                            				$dbToefl=new App_Model_General_DbTable_Toefl();
	                            				
	                            				$regcompleted=$dbToefl->getPrerequisite($student['IdStudentRegistration'], 453, $preq["MinCreditHours"]);
	                            			}else 
	                            			if ($preq["PrerequisiteType"]=='9') {
	                            				//max credit hours in semester
	                            				$chlimit=$preq["MinCreditHours"];
	                            				$this->view->crlimit=$chlimit;
	                            			}else if ($preq["PrerequisiteType"]=='6') {
	                            				//max credit hours in semester
	                            				$minsks=$preq["PrerequisiteGrade"];
	                            				if ($minsks<=$all_creditthours_pass) $regcompleted=true;
	                            			}else
	                            			if($preq["PrerequisiteType"]=='1' && $preq["MinCreditHours"]>0) {
	                            				//echo "Syaraf <br>";
	                            				$regcompleted = true;
	                            				$avid=(int)$preq["MinCreditHours"];
	                            				$room=$dbRomm->fnGetRoom($avid);
	                            				//echo var_dump($room);
	                            				//check for minor
	                            				$grouplist=$landscape_subject[$key]['group'];
	                            				
	                            				foreach ($grouplist as $keygrp=>$grp) {
	                            					 $grpminors=$grp['minor'];
	                            					 foreach ($grpminors as $keymin=>$minor) {
	                            					 	$scslist=$minor['sch'];
	                            					 	foreach ($scslist as $sc) {
		                            					 	$avcode=$sc['sc_venue'];
		                            					 	$landscape_subject[$key]['group'][$keygrp]['minor'][$keymin]['prereqmin']="1";
		                            					 	if ($room['av_room_code']==$avcode) {
		                            					 		//cek prerequisite
		                            					 		//echo $room['av_room_code']."<br>";
		                            					 		$regcompletedminor = $subjectDB->isCompleted($registration_id,$preq["IdRequiredSubject"]);
		                            					 		if (!$regcompletedminor) $landscape_subject[$key]['group'][$keygrp]['minor'][$keymin]['prereqmin']="0";
		                            					 	
		                            					 	}
	                            					 	}
	                            					 }
	                            					 
	                            				}
	                            			} else
	                            			if($preq["PrerequisiteGrade"]=="" ){
			                            		$regcompleted = $subjectDB->isCompleted($registration_id,$preq["IdRequiredSubject"]);
			                            	}else{
			                            		$regcompleted = $subjectDB->isCompleted($registration_id,$preq["IdRequiredSubject"],$preq["PrerequisiteGrade"],$preq["PrerequisiteType"],$all_creditthours,$all_creditthours_pass,$stdcgpa,$idSemester,$gradeLateCheck);
			                            	}
	                            		}   else {
	                            			$regcompleted = $subjectDB->isCoRequisite($registration_id,$preq["IdRequiredSubject"]);
	                            		}
	                            		
	                            		if($regcompleted){
	                            			$completed = 1;
	                            		}else{
	                            			//check for equivalent
	                            			$completedOr=0;
	                            			$subject_prerequisite_OR = $prerequisiteDb->getCoursePrerequisiteOr($preq['IdSubjectPrerequisites']);
	                            			if ($subject_prerequisite_OR) {
	                            				foreach ($subject_prerequisite_OR as $preq){
	                            					//dah pernah complete blom;
	                            					if ($preq['PrerequisiteType']!='3') {
	                            						if($preq["PrerequisiteGrade"]==""){
	                            							$regcompletedOr = $subjectDB->isCompleted($registration_id,$preq["IdRequiredSubject"]);
	                            						}else{
	                            							$regcompletedOr = $subjectDB->isCompleted($registration_id,$preq["IdRequiredSubject"],$preq["PrerequisiteGrade"],$preq["PrerequisiteType"],$all_creditthours);
	                            						}
	                            					} else {
	                            						$regcompletedOr = $subjectDB->isCoRequisite($registration_id,$preq["IdRequiredSubject"]);
	                            					}
	                            					 
	                            					if($regcompletedOr){
	                            						$completedOr = 1;
	                            						break;
	                            					}else $completedOr = 0;  
	                            				}
	                            			}  
	                            			if ($completedOr==0) {
	                            				$completed = 0; break;
	                            			} else $completed = 1;
	                            		}
	                            	}
	                           		$landscape_subject[$key]['prereq'] =$subject_prerequisite;
	                           		$landscape_subject[$key]['preqstatus'] = $completed;
	                            } 
                            //}
                            //=================prerequisite 
                           }                      
                       }    
                      //  echo var_dump($landscape_subject);
                        $this->view->offeredsubject = $landscape_subject;	         		
		         	}else{
		         		//semester non reguler
		         		$current_level_semester_to_register =0;
		         		$this->view->current_level = $total_countable_registered_semester;
		         		$this->view->semester_level = 0; //semester pembaikan tak naik level,so setkan level=0			         		
						$dbProgram=new App_Model_General_DbTable_Program();
		         		$subjectDB = new App_Model_Record_DbTable_StudentRegSubjects();
    					//get course based on semesterFuctionType if '6' only current course completed (Remedial)
		         		if ($semesterFunctionType=='6') 
		         			
		         			$landscape_subject = $subjectDB->getSubjectRegisterCurrentSemester($registration_id,$student["IdLandscape"],$idSemester,$landscape["LandscapeType"],null,$gradepointmin);
		         		
		         		 else
		         			$landscape_subject = $subjectDB->getSubjectRegisterAllPreviousSemester($registration_id,$student["IdLandscape"],$idSemester,$landscape["LandscapeType"],null);		         	
		         		
                        $subjectOffered = $landscape_subject; 
                        
                        foreach ($subjectOffered as $key => $val)
                        {
                            $GroupList = new App_Model_Registration_DbTable_CourseGroup();
                            if ($semesterFunctionType=='6') {
                            	//check remedial mode 1: stick to previous class 0: free to choose class
                            	$prg=$dbProgram->fngetProgramData($programid);
                            	if ($prg['Rmd_mode']=='1') 
                            		$groupLists = $GroupList->getGroupList($val["IdSubject"],$idSemester,$student['IdProgram'],$val['GroupCode']);
                            	else 
                            		$groupLists = $GroupList->getGroupList($val["IdSubject"],$idSemester,$student['IdProgram'],'');
                            }
                            else
                           		$groupLists = $GroupList->getGroupList($val["IdSubject"],$idSemester,$student['IdProgram'],'');
                            
                            
                            if(!empty($groupLists))
                            {
                            	
                                $landscape_subject[$key]['group'] = $groupLists;
                                $landscape_subject[$key]['groupTotal'] = 0;
                                $landscape_subject[$key]['currentStud'] = 0;
                                $landscape_subject[$key]['show'] = 'no';
                                
                                foreach($groupLists as $x => $y)
                                {
                                    $landscape_subject[$key]['groupTotal'] = $landscape_subject[$key]['groupTotal'] + $y['maxstud'];
                                    
                                    $total = $Schedule->getTotalStudentRegistered($y['IdSubject'],$y['IdSemester'],$y['IdCourseTaggingGroup']);
                                    
                                    $total = ($total['Total'] == '') ? 0:$total['Total'];
                                    
                                    $landscape_subject[$key]['group'][$x]['currentStud'] = $total;
                                
                                    $landscape_subject[$key]['currentStud'] = $landscape_subject[$key]['currentStud'] + $total;
                                    
                                    if($total < $y['maxstud'])
                                    {
                                        $landscape_subject[$key]['show'] = 'yes';
                                    }
                                }
                            }
                        }    
                        
                        $this->view->offeredsubject = $landscape_subject;
		         	
                    }	         	
		         	
		     
			        $common_compulsory_subject = array();
			        $common_notcompulsory_subject = array();
			        $major_compulsory_subject = array();
			        $major_notcompulsory_subject = array();		        
			       //nak dapatkan total register semester yg countable 
			    	if($landscape["LandscapeType"]==43) {//Semester Based  			    		
			    		
			    		$landscape_subject = $landscapeSubjectDB->getLandscapeCourseByLevel($student["IdLandscape"],$current_level_semester_to_register);
			         	
			         	foreach($landscape_subject as $index=>$subject){
			         		
				         	//get status subject offer or not?
				         	$subjectOfferDb = new App_Model_Record_DbTable_SubjectsOffered();
				         	$subject_offer = $subjectOfferDb->getSubjectsOfferBySemester($idSemester,$subject["IdSubject"]);
				         	
                           
				         	if(is_array($subject_offer)){
				         		$landscape_subject[$index]['status_name']=$this->view->translate("Offered");
				         		$landscape_subject[$index]['status']=1;
                                
				         	}else{
				         		unset($landscape_subject[$index]);
				         		//$landscape_subject[$index]['status_name']=$this->view->translate("Not Offered");
				         		//$landscape_subject[$index]['status']=2;
				         	}
				         					         	
				         	
				         	//get subject status already taken/registered or not
				         	$subjectRegDB = new App_Model_Record_DbTable_StudentRegSubjects();
				         	$subject_registered = $subjectRegDB->isRegister($registration_id,$subject["IdSubject"],$idSemester);	
				         	
				         	if(is_array($subject_registered)){
				         		$landscape_subject[$index]['register_status']="Registered";
				         		$landscape_subject[$index]['register']=1;
				         	} else{
				         		$landscape_subject[$index]['register_status']="Not Registered";
				         		$landscape_subject[$index]['register']=2;
				         	}     
				         				         	
				         	
				         	//nak groupkan subject ni dalam common course or majoring course
				         	if($subject["IDProgramMajoring"]==0){	        		
				         		//common subject				         		
				         		if($subject["Compulsory"]==1){	         			
				         			//common compulsory subject
				         			array_push($common_compulsory_subject,$landscape_subject[$index]);
				         		}else{
				         			array_push($common_notcompulsory_subject,$landscape_subject[$index]);
				         		}
				         		
				         	}else{
				         		//major subject
				         		
				         		if($subject["Compulsory"]==1){	         			
				         			//major compulsory subject
				         			array_push($major_compulsory_subject,$landscape_subject[$index]);
				         		}else{
				         			array_push($major_notcompulsory_subject,$landscape_subject[$index]);
				         		}				         		
				         	}
				         	
				         	
			         	}//end foreach
			         	
			         	
			  		
			         }elseif($landscape["LandscapeType"]==44){		         	
			         	
			         	//get from landscape subject offered from current level
			         	$landscapeBlockSubjectDB = new App_Model_Record_DbTable_LandscapeBlockSubject();
			            $landscape_subject = $landscapeBlockSubjectDB->getLandscapeCourseByLevel($student["IdLandscape"],$current_level_semester_to_register,$registration_id,$idSemester);
			         
		            	foreach($landscape_subject as $index=>$subject){
		         		
					         	//get status subject offer or not?
					         	/*$subjectOfferDb = new App_Model_Record_DbTable_SubjectsOffered();
					         	$subject_offer = $subjectOfferDb->getSubjectsOfferBySemester($idSemester,$subject["IdSubject"]);
					         	
					         	if(is_array($subject_offer)){
					         		$landscape_subject[$index]['status_name']=$this->view->translate("Offered");
					         		$landscape_subject[$index]['status']=1;
					         	}else{
					         		unset($landscape_subject[$index]);
					         		$landscape_subject[$index]['status_name']=$this->view->translate("Not Offered");
					         		$landscape_subject[$index]['status']=2;
					         	}*/
					         	
					         	//get subject status already taken/registered or not
					         	$subjectRegDB = new App_Model_Record_DbTable_StudentRegSubjects();
					         	$subject_registered = $subjectRegDB->isRegister($registration_id,$subject["IdSubject"],$idSemester);	
					         	
					         	if(is_array($subject_registered)){				         	
					         		$landscape_subject[$index]['register_status']="Registered";
					         		$landscape_subject[$index]['register']=1;
					         	} else{
					         		$landscape_subject[$index]['register_status']="Not Registered";
					         		$landscape_subject[$index]['register']=2;
					         	}   
					         					         		
				         		if($subject["Compulsory"]==1){ 
				         			array_push($common_compulsory_subject,$landscape_subject[$index]);
				         		}else{
				         			array_push($common_notcompulsory_subject,$landscape_subject[$index]);
				         		}
					         		
					         		        	
			         	
		         	    }//end foreach
		         	
			         	//echo '<pre>';
			         	//print_r($subject_registered);
			         	//print_r($landscape_subject);
			         	//echo '</pre>';
		         		
		         }
		        
		    	 
		    	$this->view->common_compulsory_subject = $common_compulsory_subject;
		    	$this->view->common_notcompulsory_subject = $common_notcompulsory_subject;
		    	$this->view->major_compulsory_subject = $major_compulsory_subject;
		    	$this->view->major_notcompulsory_subject = $major_notcompulsory_subject;
		    	
		    	 
		    	//echo '<pre>';
		    	//print_r($landscape_subject);
		    	//print_r($common_compulsory_subject);
		    	//print_r($common_notcompulsory_subject);
		    	//print_r($major_compulsory_subject);
		    	//print_r($major_compulsory_subject);
		    	//echo '</pre>';
    	
		    	
		        }//end if
		        
		    			        
	        	
    	
    }
    
    public function ajaxSearchCourseAction(){
    	
    	$this->_helper->layout()->disableLayout();
    	
    	$landscape_id = $this->_getParam('landscape_id', 0);
    	$landscape_type = $this->_getParam('landscape_type', 0);
    	$course_code  = $this->_getParam('course_code', null);    	
    	$level = $this->_getParam('level', 0); //semester level
    	$current_level = $this->_getParam('current_level', 0);
    	$semester_id = $this->_getParam('idSemester', 0);
    	$student_id = $this->_getParam('idStudent', 0);
    	
    	
    	
    	if ($this->getRequest()->isPost()) {
		
			$ajaxContext = $this->_helper->getHelper('AjaxContext');
			$ajaxContext->addActionContext('view', 'html');
			$ajaxContext->initContext();
									
			if(isset($level) && $level!=0){			
				
				//for regular semester
				
				if($landscape_type==43){					
					//allow student to search course from landscape where semester level is < from current/selected semester level and subject offered.
		         	$landscapeSubjectDB = new App_Model_Record_DbTable_LandscapeSubject();
		            $landscape_subject = $landscapeSubjectDB->getPreviousLevelCourseList($landscape_id,$course_code,$level,$semester_id,$student_id);
				}
				
				if($landscape_type==44){					
					//allow student to search course from block landscape where semester level is < from current/selected semester level and subject offered.
					$landscapeBlockSubjectDB = new  App_Model_Record_DbTable_LandscapeBlockSubject();
					$landscape_subject = $landscapeBlockSubjectDB->getPreviousLevelCourseList($landscape_id,$course_code,$level,$semester_id,$student_id);
				}
				
    		}else{
    			
    			//subject to register for semester pembaikan    		
    			//open for semester based landscape & block based landscape
    			//allow student to re-register (repeat) subject that they already taken from previous semester			
    			$subjectDB = new App_Model_Record_DbTable_StudentRegSubjects();
    			$landscape_subject = $subjectDB->getSubjectRegisterPreviousSemester($student_id,$landscape_id,$semester_id,$landscape_type,$course_code);
    			;   			
    		}
					
			$ajaxContext->addActionContext('view', 'html')
						->addActionContext('form', 'html')
						->addActionContext('process', 'json')
						->initContext();
		
			$json = Zend_Json::encode($landscape_subject);		
			echo $json;
			exit();
    	}
    }
    
public function registerAction(){
    	
    	 $auth = Zend_Auth::getInstance();    
    	   	
    	 $semdb=new App_Model_Record_DbTable_Studentsemesterstatus();
         
         if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();	
					  	 
           // echo var_dump($formData);exit;
            
            /*
             * Dapatkan id semester untuk semua semester yang berkaitan
             */
			$examDb = new App_Model_Exam_DbTable_ExamGroup();
            $semesterDB = new App_Model_General_DbTable_Semestermaster();
            $sel_semester = $semesterDB->fnGetSemestermaster($formData['idSemester']);
            $idsemester=$formData['idSemester'];
            $semesterId = $semesterDB->getAllSemesterMasterId($sel_semester['idacadyear'],$sel_semester['SemesterCountType']);
            //print_r($semesterId);
            $dbCourseMinor=new App_Model_Registration_DbTable_CourseGroupMinor();
            $dbMinorStd=new App_Model_General_DbTable_CourseGroupStudentMinor();
            $registration_id = $auth->getIdentity()->registration_id;    
            
            $studentRegDB = new App_Model_Record_DbTable_StudentRegistration();
            $student = $studentRegDB->getStudentInfo($registration_id);
            $idprogram=$student['IdProgram'];
            $idbranch=$student['IdBranch'];
            $idlandscape=$student['IdLandscape'];
            //get ch max
            //get pprogram
            $dbProgram= new App_Model_General_DbTable_Program();
            $program=$dbProgram->getData($idprogram);
            $chmax=$program['CreditHoursMax'];
           	$GroupList = new App_Model_Registration_DbTable_CourseGroup();
    	
            if ($GroupList->isPackage($idprogram, $idbranch)) $paket="1"; else $paket="0";
             
            
            $approved=$student['AdvisorDefaultApprove'];
           // echo $approved;exit;
            $studentSemesterDB = new App_Model_Record_DbTable_Studentsemesterstatus();
             
            if($sel_semester["IsCountable"]==1){//regular		
		        $semesterStudi = $studentSemesterDB->getCountableRegisteredSemester($registration_id,$formData['idSemester']);         	
		         $total_countable_registered_semester = count($semesterStudi); 		
                //cek if selected semester is running semester 
                if(strtotime($sel_semester["SemesterMainStartDate"]) <= strtotime('today') && strtotime($sel_semester["SemesterMainEndDate"]) >= strtotime('today')){
                     //$current_level_semester_to_register =    $total_countable_registered_semester //ori explain why?	
                     $current_level_semester_to_register =    $total_countable_registered_semester+1;
                     $this->view->current_level = $current_level_semester_to_register;		         		  
                     $this->view->semester_level = $current_level_semester_to_register;		
                }else{
                     $current_level_semester_to_register =    $total_countable_registered_semester+1;	
                     $this->view->current_level = $current_level_semester_to_register;		         		  
                     $this->view->semester_level = $total_countable_registered_semester+1;	
                }
                //echo $current_level_semester_to_register;exit;
                //Utk check CH Limitation
                $chlimitDB = new App_Model_Registration_DbTable_Chlimit();
                $chlimit = $chlimitDB->isLimitSet($student["IdProgram"],$student["IdIntake"]);
                //print_r($chlimit);exit;
                if($chlimit &&  $paket=='0'){
                    $previousSemLevel = $current_level_semester_to_register-1;
                    $prvsem = $studentSemesterDB->getRegisteredSemesterByLevel($registration_id,$previousSemLevel);
                    
                    $studentGradeDb = new App_Model_Exam_DbTable_StudentGrade();
                    $sgrade = $studentGradeDb->getGradebySemester($registration_id,$prvsem["idstudentsemsterstatus"],1);
                    //print_r($sgrade);
                    if(!$sgrade["sg_univ_gpa"]){ 
                        $sgrade["sg_univ_gpa"] = 0.00;
                    }
                    
                    $crlimit = $chlimitDB->getLimit($chlimit['progid'],$chlimit['intakeid'],$sgrade["sg_univ_gpa"]);
                   // $crlimit=24;
                    $this->view->crlimit = $crlimit;
                }else{
                    $crlimit = $this->view->crlimit=$chmax;//government policy
                    //this->view->crlimit=14;//temp
                }
            //print_r($chlimit);exit;
            
            /* if($chlimit){
                $previousSemLevel = $current_level_semester_to_register-1;
                
               
                $prvsem = $studentSemesterDB->getRegisteredSemesterByLevel($registration_id,$previousSemLevel);
                
                $studentGradeDb = new App_Model_Exam_DbTable_StudentGrade();
                $sgrade = $studentGradeDb->getGradebySemester($registration_id,$prvsem["idstudentsemsterstatus"],1);
                //print_r($sgrade);
                if(!$sgrade["sg_univ_gpa"]){ 
                    $sgrade["sg_univ_gpa"] = 0.00;
                }
                
                $crlimit = $chlimitDB->checklimit($chlimit["clid"],$sgrade["sg_univ_gpa"]);
                
                $this->view->crlimit = $crlimit;
            }else{
                $this->view->crlimit=24;//government policy
                //this->view->crlimit=14;//temp
            } */
                
               // if($new_credit > $crlimit){
                //	$this->_redirect($this->view->url(array('module'=>'default','controller'=>'course-registration', 'action'=>'course-register','idSemester'=>$formData["idSemester"],'errmsg' => 1),'default',true));
               // }
        }//end regular
        
            //$crlimit = $formData["crlimit"];
            //$crlimit = 14;
            //if($crlimit==""){
            	//$crlimit=24; //governmentpolicy
            //}
            
            $i = 0;
            foreach($semesterId as $key => $value)
            {
                $semesterIds[$i] = $value['IdSemesterMaster'];
                $i++;
            }
            
            $stringSemesterIds = implode($semesterIds,',');
            
            
            /*
             * Dapatkan detail current semester
             */
            $semesterDB = new App_Model_General_DbTable_Semestermaster();
            $sel_semester = $semesterDB->fnGetSemestermaster($formData['idSemester']);
        
            
            /*
             * Get All Subject Based On semesterId $stringSemesterIds
             */
             $registration_id = $auth->getIdentity()->registration_id;
             $courseRegisterDb = new App_Model_Record_DbTable_StudentRegistration();
             $courses_credit = $courseRegisterDb->getCourseRegisteredBySemesterId($registration_id,$stringSemesterIds);
        

           // $current_courses_credit = $courseRegisterDb->getCourseRegisteredBySemester($registration_id,$formData['idSemester']);
			
            if($sel_semester['SemesterFunctionType'] == 1 || $sel_semester['SemesterFunctionType'] == 6){//pembaikan   or remedial             
            	
            	//if ($idprogram==4||$idprogram==1||$idprogram==2||$idprogram==15||$idprogram==16||$idprogram==17)  $crlimit = 36;
            	//else $crlimit = 24;
            	if ($sel_semester['SemesterFunctionType'] == 6) $crlimit = 48; else $crlimit = 24;
            	//get semester reguler for this semester pembaikan
            	$regularSem = $courseRegisterDb->getSemesterRegular($formData['idSemester'],$registration_id);
            	if ($$regularSem)  
            		//amik course registered dari regular
            		$regular_courses_registered = $courseRegisterDb->getCourseRegisteredBySemester($registration_id,$regularSem['IdSemesterMaster']);
            	 else $regular_courses_registered=array();	
            	
            	//kira CH untuk pembaikan (CH dari regular + CH dah register + CH to-be register)
                $regular_pembaikan_credit = 0;
                //all course in regular
                for($i=0; $i<sizeof($regular_courses_registered); $i++){
                	$regular_pembaikan_credit = $regular_pembaikan_credit + $regular_courses_registered[$i]['CreditHours'];
                }
                
                //get course already registered in pembaikan
                $pembaikan_courses_registered = $courseRegisterDb->getCourseRegisteredBySemester($registration_id,$formData['idSemester']);
                //all course in pembaikan + to-be register
				//echo var_dump($pembaikan_courses_registered);exit;
                $arr_course_id_pembaikan = array();
                for($i=0; $i<sizeof($pembaikan_courses_registered); $i++){
                	$arr_course_id_pembaikan[] = $pembaikan_courses_registered[$i]['IdSubject'];
                }
                
                
                //mearge with to-be register
                $arr_course_id_pembaikan = array_merge($arr_course_id_pembaikan,$formData['idSubject']);
                
                
                
                //count CH for pembaikan + regular according to policy ABD
                $landscapeSubjectDB = new App_Model_Record_DbTable_LandscapeSubject();
                for($i=0; $i<count($arr_course_id_pembaikan); $i++){
                	if( !$this->multidimensional_search($regular_courses_registered,'IdSubject',$arr_course_id_pembaikan[$i]) ){
                		
                		$regular_pembaikan_credit = $regular_pembaikan_credit + $landscapeSubjectDB->getSubjectCreditHours($arr_course_id_pembaikan[$i]);
                	}
                }
                
                //assign total_cr to pembaikan policy ab
                $total_credit = $regular_pembaikan_credit;
                
                
                
                //bill subject & CH for pembaikan
                $pembaikan_credit = 0;
                $pembaikan_subject = 0;
                foreach($arr_course_id_pembaikan as $c => $d)
                {
                	$pembaikan_credit = $pembaikan_credit+$landscapeSubjectDB->getSubjectCreditHours($d);
                    $pembaikan_subject++;
                }
                
                
                
                //exception CH
                $crhelper= new icampus_Function_Courseregistration_Credithourex();
                $crexception = $crhelper->getPembaikanExceptionMaxCh($registration_id,$formData['idSemester']);
                
                if( ($total_credit  > $crlimit))
                {
                	
                	$this->_redirect($this->view->url(array('module'=>'default','controller'=>'course-registration', 'action'=>'course-register','idSemester'=>$formData["idSemester"],'errmsg' => 1),'default',true));
                }
                /*
                if ($student['IdProgram']!=3) {
	                if($pembaikan_credit > 9 && !is_array($crexception))
	                {
	                	
	                    $this->_redirect($this->view->url(array('module'=>'default','controller'=>'course-registration', 'action'=>'course-register','idSemester'=>$formData["idSemester"],'errmsg' => 2),'default',true));
	                }
	                
	                //if($pembaikan_subject > 3 && !is_array($crexception))
	                if($pembaikan_subject > 3)
	                {
	                    $this->_redirect($this->view->url(array('module'=>'default','controller'=>'course-registration', 'action'=>'course-register','idSemester'=>$formData["idSemester"],'errmsg' => 3),'default',true));
	                }
                }
                */
            }//end of pembaikan sem
            
            //totalkan kredit
            $total_credit = 0;
            
            foreach($courses_credit as $a => $b)
            {
                $total_credit = $total_credit + $b['CreditHours'];
                
            }
            
            if($total_credit != 0)
            {
                $this->view->total_credit = $total_credit;
            }
            else
            {
                $this->view->total_credit = 0;
            }		
           // echo $crlimit;
            if ($total_credit  > $crlimit)
            {
            	 
                $this->_redirect($this->view->url(array('module'=>'default','controller'=>'course-registration', 'action'=>'course-register','idSemester'=>$formData["idSemester"],'errmsg' => 1),'default',true));
            }
            
            
            
            $new_credit = $total_credit;
            $new_pembaikan_credit = 0;        
            
            $landscapeSubjectDB = new App_Model_Record_DbTable_LandscapeSubject();
            $studentRegSubjectDB = new App_Model_Record_DbTable_StudentRegSubjects();
            $studentRegDB= new App_Model_Registration_DbTable_Studentregistration();
          	 
            //save current credithours
            $studentRegSubjectDB->saveCurrentCH($registration_id, $new_credit);
            //----------------------------
            $msg='';
            //echo var_dump($formData); echo "===<br>";
            $formData=$this->clashCleaningPreSaving($idsemester,$registration_id,$formData,$msg);
           //	echo var_dump($formData); exit;
          	if ($msg!='') $msg='Perkuliahan '.$msg.' bentrok kuliah <br>';
         
          	
          	if ($formData!=array() && $examDb->isExamAvailable($idsemester,$idprogram )) {
          		$msgexam='';
          		$formData=$this->clashExamCleaningPreSaving($idsemester,$registration_id,$formData,$msgexam);
          	 	if ($msgexam!='') $msg=$msg.'>==> Ujian '.$msgexam.' bentrok Ujan <br>';
          	
          	}
          	//
         	//cek rule ===================
         	//this rule for taking course from different location
         	//for example student campus F take course in campus F and campus A
          	$rules=$GroupList->getRule($student['IdProgram'], $student['IdBranch']);
          	if ($rules) $formData=$this->ruleCleaningPreSaving($idsemester, $registration_id, $formData, $msg, $rules);
          	
         	//check for mix subject to last block subject if so release last block subject
         	//this for medicine
         	if ($program==11) {
         		$blocklandsDb=new App_Model_Record_DbTable_LandscapeBlockSubject();
         		//medice only to release last block if student take lower block
         		$outerlastblock=false;
	          	foreach ($formData['idSubject'] as $key=>$idSubject){
	          	   if ($blocklandsDb->getBlock($idlandscape, $idSubject)!=7) {
	          	   		$outerlastblock=true;
	          	   		break;
	          	   }
	          	}
	          	if ($outerlastblock) {
	          		//release last block
	          		foreach ($formData['idSubject'] as $key=>$idSubject){
	          			if ($blocklandsDb->getBlock($idlandscape, $idSubject)==7)  
	          				unset($formData['idSubject'][$key]);
	          			 
	          		}
	          	}
         	}
         	
         // echo var_dump($formData);exit;
         //--------------save all selected subject
            foreach ($formData['idSubject'] as $key=>$idSubject){ //loop add subject
				//echo var_dump($formData['idSubject']); exit;
                    
                //check dulu dia dah ada semesterstatus, kalau takdak add
                $semreg = $semdb->isSemRegistered($registration_id,$idsemester);
                if(!$semreg){
                    $semstatus = array( 'IdStudentRegistration' => $formData["idStudentRegistration"],									           
                    'idSemester' => 0,
                    'IdSemesterMain' => $formData["idSemester"],		
                    'studentsemesterstatus' => 130, //Register idDefType = 32 (student semester status)
                    'Level'=>$formData["semester_level"],
                    'UpdDate' => date ( 'Y-m-d H:i:s'),
                    'UpdUser' => $auth->getIdentity()->appl_id
                    );	
                    $semdb->addData($semstatus);
                }
                
                //$idSubject   = $formData['idSubject'][$i];
                
                $creditHours = $landscapeSubjectDB->getSubjectCreditHours($idSubject);
                
                //get current credithours
               	$new_credit= $studentRegSubjectDB->getCurrentCH($registration_id);
                $new_credit = $new_credit + $creditHours;
                
                                   
                if($formData["landscape_type"]==43) { //Semester Based Landscape
                    
                    $subject["IdLandscapeSub"] = $formData["idLandscapeSub"][$idSubject];
                    								
                                                        
                }else if($formData["landscape_type"]==44) { //Block Based Landscape
                    
                    $subject["IdBlock"]   = $formData["idLandscapeSub"][$idSubject];
                    $subject["BlockLevel"]= $formData["block_level"][$idSubject];
                }
                
                $radioGroup = "radiominor_".$idSubject;
                //echo 'radio='.$radioGroup;
                $subject["IdSubject"] = $idSubject;
                $subject["IdStudentRegistration"] = $formData["idStudentRegistration"];
                $subject["IdSemesterMain"] = $formData["idSemester"];
                $subject["SemesterLevel"]= $formData["semester_level"];
                $subject["Active"]    = 1; 
                $subject["UpdDate"]   = date ( 'Y-m-d H:i:s');
                $subject["UpdUser"]   = $auth->getIdentity()->appl_id;						
                if ($approved==1) {
                	$subject['Approvedby']=1;
          		} else $subject['Approvedby']=0;
                if(!isset($formData[$radioGroup]))
                {
                    $subject["IdCourseTaggingGroup"]   = $formData["radio_".$idSubject];
                } else {
                	$radioGroup = "radiominor_".$idSubject;
                	if (isset($formData[$radioGroup])) {
                		$subject["IdCourseTaggingGroupMinor"] =$formData[$radioGroup];
                		$subject["IdCourseTaggingGroup"]=$formData["radio_".$idSubject];
                	}
                }
                
           
               $idGroup= $subject["IdCourseTaggingGroup"];
              //  echo var_dump($subject);exit;
                //echo 	$studentRegDB->getCountGroupStudents($idGroup)."<". $studentRegDB->getCapacityGroup($idGroup) ; exit;
               If ($new_credit <= $crlimit) {
	               if ($studentRegDB->getCountGroupStudents($idGroup) < $studentRegDB->getCapacityGroup($idGroup)) {
	               // echo 	$studentRegDB->getCountGroupStudents($idGroup)."<". $studentRegDB->getCapacityGroup($idGroup) ; exit;	
	               		
	               		if (isset($subject['IdCourseTaggingGroupMinor'])) {
	               			//check minor capacity
	               		
		               		if ($dbCourseMinor->getCountStudentByGroup($subject['IdCourseTaggingGroupMinor']) < $dbCourseMinor->getCapacity($subject['IdCourseTaggingGroupMinor'])) {
		               				               			
		               			$dataminor=array('IdStudentRegistration'=>$formData["idStudentRegistration"],'IdCourseTaggingGroupMinor'=>$subject['IdCourseTaggingGroupMinor'],'dt_entry'=>date('Y-m-d h:i:s'),'entry_by'=>$auth->getIdentity()->appl_id);
		               			
		               			$dbMinorStd->addData($dataminor);
		               			unset($subject['IdCourseTaggingGroupMinor']);
		               			$studentRegSubjectDB->addData($subject);
		               			$studentRegSubjectDB->saveCurrentCH($registration_id, $new_credit);
		               		}
	               			
	               		} else {
	               			 
	               			$studentRegSubjectDB->addData($subject);
	               			$studentRegSubjectDB->saveCurrentCH($registration_id, $new_credit);
	               		}
	               } 
               } else $msg=$msg.$new_credit.' Credits Hours Over Limit = '.$crlimit;
               
               /* }
                else
                {
                    //exit;
                    $this->_redirect($this->view->url(array('module'=>'default','controller'=>'course-registration', 'action'=>'course-register','idSemester'=>$formData["idSemester"],'errmsg' => 1),'default',true));
                }*/
                    
                       
            }//end for		
			
            //get for idAllgroup
           // $regsubject=$courseRegisterDb->getCourseRegisteredBySemester($registration_id, $idsemester);
           // $this->clashCleaning($regsubject);
           // $regsubject=$courseRegisterDb->getCourseRegisteredBySemester($registration_id, $idsemester);
           // $this->clashExamCleaning($regsubject);
        //   exit;
            $this->_redirect($this->view->url(array('module'=>'default','controller'=>'course-registration', 'action'=>'course-register','idSemester'=>$idsemester,'errmsg'=>$msg),'default',true));
						
         }
      
      
    }
    
    
 	public function ajaxSearchChildAction(){
    	
    	$this->_helper->layout()->disableLayout();
    	
    	$idLandscapeBlockSubject = $this->_getParam('idLandscapeBlockSubject', 0);
    				
    	if ($this->getRequest()->isPost()) {
		
			$ajaxContext = $this->_helper->getHelper('AjaxContext');
			$ajaxContext->addActionContext('view', 'html');
			$ajaxContext->initContext();

			
			$landscapeBlockSubjectDB = new  App_Model_Record_DbTable_LandscapeBlockSubject();
			$landscape_subject = $landscapeBlockSubjectDB->getChildbyParentId($idLandscapeBlockSubject);
			
			$ajaxContext->addActionContext('view', 'html')
						->addActionContext('form', 'html')
						->addActionContext('process', 'json')
						->initContext();
		
			$json = Zend_Json::encode($landscape_subject);		
			echo $json;
			exit();
    	}
 	}
    
    public function ajaxGetScheduleAction()
    {
        
        $this->_helper->layout->disableLayout();//'ajaxwithcss'
        //$registration_id = $auth->getIdentity()->registration_id;
        $idSubject   = $this->_getParam('idSubject', 0);
        $idGroup     = $this->_getParam('idgroup', 0);
        $idGroupMinor= $this->_getParam('idgroupminor', 0);
        $idLandscape = $this->_getParam('idLandscape', 0);
        $idLandscapeSub = $this->_getParam('idLandscapeSub', 0);
        
        // $studentRegDB = new App_Model_Record_DbTable_StudentRegistration();
		// $student = $studentRegDB->getStudentInfo($registration_id);
        $Schedule = new App_Model_Record_DbTable_StudentRegistration();
        $dbGroupMinor=new App_Model_Registration_DbTable_CourseGroupMinor();
        $dbSchMinor=new App_Model_Registration_DbTable_CourseGroupScheduleMinor();
        if ($idGroup!=0) {
        	
        	$schedules = $Schedule->getSubjectSchedule($idSubject,$idGroup);//308,2438
        	$groupSubjectDetail = $Schedule->getInfo($idGroup);
        } else {
        	//get group mayor
        	
        	$groupminor=$dbGroupMinor->getInfo($idGroupMinor);
        	$schedules = $dbSchMinor->getSchedule($idGroupMinor);
        	$groupSubjectDetail=$dbGroupMinor->getInfo($idGroupMinor);
        	$idGroup=$groupminor['group_id'];
        }
        
        //print_r($groupSubjectDetail);
        //$idLandscapeSub = $groupSubjectDetail['IdLandscapeSub'];
        
        $prerequisiteDb = new App_Model_Record_DbTable_LandscapeSubject();
		$subject_prerequisite = $prerequisiteDb->getCoursePrerequisite($idLandscape,$idLandscapeSub);
        
        $this->view->schedules = $schedules;
        $this->view->idSubject = $idSubject;
        $this->view->groupSubject = $groupSubjectDetail;
        $this->view->subject_prerequisite = $subject_prerequisite;
        
        
    }
    
    function multidimensional_search($array, $key, $value) {
    
    	$results = array();
    
    	if (is_array($array))
    	{
    		if (isset($array[$key]) && $array[$key] == $value)
    			$results[] = $array;
    
    		foreach ($array as $subarray)
    			$results = array_merge($results, $this->multidimensional_search($subarray, $key, $value));
    	}
    
    	return $results;
    }
    
    public function clashCleaning($formdata) {
    	//$formdatatmp=$formdata;
    	$dbschedule=new App_Model_Registration_DbTable_CourseGroupSchedule();
    	foreach ($formdata as $i=>$data ) {
    		//get schedule
    		//$idSubject   = $formdata['idSubject'][$i];
    		//$radioGroup= "radio_".$idSubject;
    		$idGroup=$data['IdCourseTaggingGroup'];
    		$schtmps[$i]=$dbschedule->getScheduleRow($idGroup);
    		$schtmps[$i]=array_merge($schtmps[$i],array('IdReg'=>$data['IdStudentRegSubjects']));
    		
    	}
    	//echo var_dump($schtmps);exit;
    	//check clash courser
    	//$schs=$formdata;
    	$dbReg = new App_Model_Record_DbTable_StudentRegSubjects();
    	$schs=$schtmps;
    	$nodrop=array();
    	foreach ($schtmps as $key=>$schtmp) {
    		foreach ($schs as $index=>$sch) {
    			//echo var_dump($schtmp)."=".$sch['idGroup'];exit;
    			if ($schtmp['idGroup']!=$sch['idGroup']) {
    				
    				if ($schtmp['sc_date']==$sch['sc_date'] && $schtmp['sc_day']==$sch['sc_day'] ) {
    					//check time clash
    					//echo var_dump($schtmp);echo '=';var_dump($sch);exit;
    					if (($schtmp['sc_start_time']<=$sch['sc_start_time'] && $sch['sc_start_time'] <$schtmp['sc_end_time']) || ($schtmp['sc_start_time']<$sch['sc_end_time'] && $sch['sc_end_time'] <=$schtmp['sc_end_time'])) {
    						//drop subject
    						unset($schs[$index]);
    						unset($schtmps[$index]);
    						//echo $sch['IdReg'];exit;
    						$sts='';
    						for ($i=0;$i<count($nodrop);$i++) {
    							if ($nodrop[$i]==$sch['IdReg']) $sts='1';
    						}
    						if ($sts=='') {
	    						$dbReg->drop($sch['IdReg']);
	    						$nodrop=array_merge($nodrop,array("'".$schtmp['IdReg']."'"));
    						}
    						
    					} 
    				} 
    			}
    		}
    	}
    }
    
    
    
    public function clashExamCleaning($formdata) {
    	//$formdatatmp=$formdata;
    	$dbschedule=new App_Model_Exam_DbTable_ExamGroup();
    	foreach ($formdata as $i=>$data ) {
    		//get schedule
    		//$idSubject   = $formdata['idSubject'][$i];
    		//$radioGroup= "radio_".$idSubject;
    		
    		$schtmps[$i]=$dbschedule->getDataBySubject($data['IdSemesterMain'], $data['IdSubject'], $data['IdProgram']);
    		$schtmps[$i]=array_merge($schtmps[$i],array('IdReg'=>$data['IdStudentRegSubjects']));
    
    	}
    	//echo var_dump($schtmps);exit;
    	//check clash courser
    	//$schs=$formdata;
    	$dbReg = new App_Model_Record_DbTable_StudentRegSubjects();
    	$schs=$schtmps;
    	$nodrop=array();
    	foreach ($schtmps as $key=>$schtmp) {
    		foreach ($schs as $index=>$sch) {
    			//echo var_dump($schtmps);exit;
    			if ($schtmp['eg_id']!=$sch['eg_id']) {
    
    				if ($schtmp['eg_date']==$sch['eg_date']) {
    					//check time clash
    					//if ($schtmp['subject_code']=='MKI302' && $sch['subject_code']=='MGS320') echo var_dump($schtmp);echo '=';var_dump($sch);exit;
    					if (($schtmp['eg_start_time']<=$sch['eg_start_time'] && $sch['eg_start_time'] <$schtmp['eg_end_time']) || ($schtmp['eg_start_time']<$sch['eg_end_time'] && $sch['eg_end_time'] <=$schtmp['eg_end_time'])) {
    						//drop subject
    						//echo var_dump($schtmps[$index]).'='.var_dump($schs[$index]);exit;
    						unset($schs[$index]);
    						unset($schtmps[$index]);
    						//echo $sch['IdReg'];exit;
    						$sts='';
    						for ($i=0;$i<count($nodrop);$i++) {
    							if ($nodrop[$i]==$sch['IdReg']) $sts='1';
    						}
    						if ($sts=='') {
    							$dbReg->drop($sch['IdReg']);
    							$nodrop=array_merge($nodrop,array("'".$schtmp['IdReg']."'"));
    						}
    
    
    					}
    				}
    			}
    		}
    	}
    }
    public function clashExamCleaningPreSaving($idsemester,$IdStudentRegistration,$formdata,&$msg) {
    	//$formdatatmp=$formdata;
    	//get program i
    
    	$stdDb=new  App_Model_Record_DbTable_StudentRegistration();//DbTable_Studentregistration();
    	$std=$stdDb->getStudentInfo($IdStudentRegistration);
    	$idprogram=$std['IdProgram'];
    	$dbschedule=new App_Model_Exam_DbTable_ExamGroup();
    	$newExams=array();
    	foreach ($formdata['idSubject'] as $i=>$data ) {
    		  
    		$newexam=$dbschedule->getDataBySubjectAll($idsemester, $data, $idprogram);
    		foreach ($newexam as $exam) {
    			$newExams[]=array(//'eg_id'=>$exam['eg_id'],
    					'IdSubject'=>$data,
    					'SubCode'=>$exam['subject_code'],
    					'SubjectName'=>$exam['subject_name'],
    					'eg_date'=>$exam['eg_date'],
    					'eg_start_time'=>$exam['eg_start_time'],
    					'eg_end_time'=>$exam['eg_end_time']);
    		}
    	}
    	//
    	//echo '--before remove';echo var_dump($newExams);echo "===new examsch after=======";
    	if ($newExams!=array()) $newExams=$this->removeclashExam($newExams,$msg);
    	//echo var_dump($newExams);
    
    	if ($newExams!=array()) {
    		//get registered exam...
    		$SubjectDb=new App_Model_Record_DbTable_StudentRegSubjects();
    		$registerSubject=$SubjectDb->getSemesterSubjectRegistered($idsemester, $IdStudentRegistration);
    		$registeredExams=array();
    		if ($registerSubject) {
    			foreach ($registerSubject as $subject) {
    				$idsubject=$subject['IdSubject'];
    				$newexam=$dbschedule->getDataBySubjectAll($idsemester, $idsubject, $idprogram);
    				foreach ($newexam as $exam) {
    					$registeredExams[]=array(//'eg_id'=>$exam['eg_id'],
    							'IdSubject'=>$idsubject,
    							'SubCode'=>$exam['subject_code'],
    							'SubjectName'=>$exam['subject_name'],
    							'eg_date'=>$exam['eg_date'],
    							'eg_start_time'=>$exam['eg_start_time'],
    							'eg_end_time'=>$exam['eg_end_time']);
    				}
    			}
    		}
    		
    		if ($registeredExams!=array()) {
    			foreach ($newExams as $key=>$new){
    				foreach ($registeredExams as $old) {
    					//echo "new=";echo var_dump($new); echo "old";echo var_dump($old);
    					if ($this->isExamClash($new, $old)) {
    						unset($newExams[$key]);
    						//echo '<====exam clash==>';
    						$msg=$msg." ".$new['SubCode'].'-'.$new['SubjectName']." dengan ".$old['SubCode'].'-'.$old['SubjectName'].' bentrok ujian <br>';
    					} 
    					//else echo '<====exam No clash==>'.$new['SubjectName'];
    				}
    			}
    		}
    		
    	}
    	
    	//remove subject that exam is clash
    	
    	if ($newExams!=array()) {
	    	foreach ($formdata['idSubject'] as $key=>$subject)  {
	    		$sts='';
	    		foreach ($newExams as $sch) {
	    			//	echo var_dump($subject);echo '==';echo $sch['IdSubject'];
	    		 		if ($subject==$sch['IdSubject']) $sts='1';
	    		   		
	    		}
	    		if ($sts=='') unset($formdata['idSubject'][$key]);
	    	} 
    	}
    	//echo "==========formdata";
    	//echo var_dump($formdata['idSubject']);
    	//echo '========================================';
    	if (!isset($formdata['idSubject'])) $formdata=array();
    	return $formdata;
    }
    
    public function clashCleaningPreSaving($idsemester,$IdStudentRegistration,$formData,&$msg) {
    	
    	//get register subject
    	$dbGrpMinor=new App_Model_Registration_DbTable_CourseGroupMinor();
    	$dbSchMinor=new App_Model_Registration_DbTable_CourseGroupScheduleMinor();
    	$dbschedule=new App_Model_Registration_DbTable_CourseGroupSchedule();
    	
    	$SubjectDb=new App_Model_Record_DbTable_StudentRegSubjects();
    	//$idsemester=$formdata['IdSemester'];
    	//$IdStudentRegistration=$formdata['IdStudentRegistration'];
    	//get new schedule
    	//echo "----";
    	//echo var_dump($formData);
    	
    	$newschedules=array();
    	$newschedulesMinor=array();
    	for($i=0; $i<count($formData['idSubject']); $i++) {
    		$idSubject=$formData['idSubject'][$i];
    		$subjs=$SubjectDb->getSubjectmaster($idSubject);
    		$radioGroup = "radio_".$idSubject;
    		if (!isset($formData[$radioGroup])) {
    			$radioGroup = "radiominor_".$idSubject;
    			$idGroupMinor=$formData[$radioGroup];
	    		if($idGroupMinor!='') {
	    			$grpminor=$dbGrpMinor->getInfo($idGroupMinor);
	    			//echo var_dump($grpminor);exit;
	    			$idGroup=$grpminor['group_id'];
	    			$formData["radio_".$idSubject]=$idGroup;
	    			//echo 'ik';
	    			//echo var_dump($formData);exit;
	    			$schedule=$dbSchMinor->getSchedule($idGroupMinor);
	    			foreach ($schedule as $sch) {
	    				$newschedulesMinor[]=array('IdCourseTaggingGroupMinor'=>$idGroupMinor,
	    						'IdSubject'=>$idSubject,
	    						'group_id'=>$idGroup,
	    						'SubCode'=>$subjs['SubCode'],
	    						'SubjectName'=>$subjs['BahasaIndonesia'],
	    						'sc_date'=>$sch['sc_date'],
	    						'sc_date_end'=>$sch['sc_date_end'],
	    						'sc_day'=>$sch['sc_day'],
	    						'sc_start_time'=>$sch['sc_start_time'],
	    						'sc_end_time'=>$sch['sc_end_time']);
	    			}
	    			//echo var_dump($newschedulesMinor);
	    			$newschedulesMinor=$this->removeclashMinor($newschedulesMinor,$msg);
	    			//echo 'after';
	    		}
    			//echo var_dump($newschedulesMinor);echo "---<br>";
    		} else 
    			$idGroup=$formData[$radioGroup];
    		//echo var_dump($formData);exit;
    		if ($idGroup!='') {
    		$schedule=$dbschedule->getSchedule($idGroup);
	    		foreach ($schedule as $sch) {
	    			$newschedules[]=array('IdCourseTaggingGroup'=>$idGroup,
	    					'IdSubject'=>$idSubject,
	    					'SubCode'=>$subjs['SubCode'],
	    					'SubjectName'=>$subjs['BahasaIndonesia'],
	    					'sc_date'=>$sch['sc_date'],
	    					'sc_date_end'=>$sch['sc_date_end'],
	    					'sc_day'=>$sch['sc_day'],
	    					'sc_start_time'=>$sch['sc_start_time'],
	    					'sc_end_time'=>$sch['sc_end_time']);
	    		}
    		}
    	
    	}
    	//check forclash
    	 
    	if ($newschedules) $newschedules=$this->removeclash($newschedules,$msg);
    	 
    	//exit;
    	if ($newschedulesMinor!=array()) {
    		//cleansing for Minor Class
    		$dbMinorStd=new App_Model_General_DbTable_CourseGroupStudentMinor();
    		$newschedulesMinor=$this->removeclashMinor($newschedulesMinor,$msg);
    		$schedules=array();
    		if ($newschedulesMinor!=array()) {
    			//get registersed minor class
    			$registerSubjectMinor=$dbMinorStd->getSubjectRegisterMinor( $IdStudentRegistration,$idsemester);
    			//echo var_dump($registerSubjectMinor);echo "-subject registered--<br>";
    			if ($registerSubjectMinor) {
    				foreach ($registerSubjectMinor as $subject) {
    					$schedule=$dbSchMinor->getSchedule($subject['IdCourseTaggingGroupMinor']);
    					foreach ($schedule as $sch) {
    						$schedules[]=array('IdCourseTaggingGroupMinor'=>$subject['IdCourseTaggingGroupMinor'],
    								'group_id'=>$subject['group_id'],
    								'IdSubject'=>$subject['IdSubject'],
    								'SubCode'=>$subject['SubCode'],
    								'SubjectName'=>$subject['SubjectName'],
    								'sc_date'=>$sch['sc_date'],
    								'sc_date_end'=>$sch['sc_date_end'],
    								'sc_day'=>$sch['sc_day'],
    								'sc_start_time'=>$sch['sc_start_time'],
    								'sc_end_time'=>$sch['sc_end_time']);
    					}
    				}
    			}
    		}
    		
    		//remove clash to registered subjects;
    		if ($schedules!=array()) {
    			//echo var_dump($schedules);
    			//echo "-------------";
    			//echo "<br>";
    			//echo  var_dump($newschedules);
    			//exit;
    			//echo ">>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>register>>>>>>";
    			foreach ($newschedulesMinor as $key=>$new){
    				foreach ($schedules as $old) {
    					//echo "new=";echo var_dump($new); echo "old";echo var_dump($old);
    					if ($this->isClashMinor($new, $old)) {
    						unset($newschedulesMinor[$key]);
    						$msg=$msg.' '.$new['SubCode'].'-'.$new['SubjectName'].' dengan '.$old['SubCode'].'-'.$old['SubjectName'].'<br>';
    						//echo "clashXXxxxxxxx===";
    					}
    				}
    			}
    		}
    		//echo "======after classs Minor";
    		//echo var_dump($newschedulesMinor);
    		//echo'----->>>>>--------'.$msg;
    		//exit;
    		//if ($newschedulesMinor==array()) $formData=array();
    		 
    		foreach ($formData['idSubject'] as $key=>$subject)  {
    			$sts='';
    			foreach ($newschedulesMinor as $sch) {
    				//	echo var_dump($subject);echo '==';echo $sch['IdSubject'];
    				if ($subject==$sch['IdSubject']) $sts='1';
    					
    			}
    			if ($sts=='') unset($formData['idSubject'][$key]);
    		}
    	}
    	//echo var_dump($newschedulesMinor);echo '<br>';
    	//echo "======after remove=====";
    	//echo var_dump($formData);
    	//exit;
    	//echo "========>>>>>>>==";
    	//============== get registered subject
    	$schedules=array();
    	if ($newschedules!=array()) {
    		
    		$registerSubject=$SubjectDb->getSemesterSubjectRegistered($idsemester, $IdStudentRegistration);
	    	if ($registerSubject) {
	    		foreach ($registerSubject as $subject) {
	    			$schedule=$dbschedule->getSchedule($subject['IdCourseTaggingGroup']);
	    			foreach ($schedule as $sch) {
	    				$schedules[]=array('IdCourseTaggingGroup'=>$subject['IdCourseTaggingGroup'],
	    									'IdSubject'=>$subject['IdSubject'],
	    									'SubCode'=>$subject['SubCode'],
	    									'SubjectName'=>$subject['BahasaIndonesia'],
	    									'sc_date'=>$sch['sc_date'],
	    									'sc_date_end'=>$sch['sc_date_end'],
	    									'sc_day'=>$sch['sc_day'],
	    									'sc_start_time'=>$sch['sc_start_time'],
	    									'sc_end_time'=>$sch['sc_end_time']);
	    			}
	    		}
	    	}
    	}
    	//remove clash to registered subjects;
    	//echo var_dump($formData);
    	if ($schedules!=array()) {
    		//echo "remove registered class <br>";
    		//echo var_dump($schedules);
    		//echo "-------------";
    		//echo "<br>";
    		
    		//echo  var_dump($newschedules);
    		//exit;
    		//echo ">>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>register>>>>>>";
    		foreach ($newschedules as $key=>$new){
    			foreach ($schedules as $old) {
    				//echo "new=";echo var_dump($new); echo "old";echo var_dump($old);
    				if ($this->isClash($new, $old)) {
    					unset($newschedules[$key]);
    					$msg=$msg.' '.$new['SubCode'].'-'.$new['SubjectName'].' dengan '.$old['SubCode'].'-'.$old['SubjectName'].'<br>';
    					//echo "clashXXxxxxxxx===".$msg;
    				}
    			}
    		}
    	}
    	//exit;
    	// "======after classs";
    	//echo var_dump($newschedules);//exit;
    	//echo'----->>>>>--------';
    	//echo var_dump($newschedulesMinor);
    	
    	if ($newschedules==array() && $newschedulesMinor=array()) $formData=array();
    	if ($newschedules!=array()) {
	    	foreach ($formData['idSubject'] as $key=>$subject)  {
	    		$sts='';
	    		foreach ($newschedules as $sch) {
	    			//	echo var_dump($subject);echo '==';echo $sch['IdSubject'];
	    		 		if ($subject==$sch['IdSubject']) $sts='1';
	    		   		
	    		}
	    		if ($sts=='') unset($formData['idSubject'][$key]);
	    	} 
    	}
    	 
    	if (!isset($formData['idSubject'])) $formData=array();
    	
    	return $formData;
    }
    
    public function clashCleaningPreSavingMinorOnly($idsemester,$IdStudentRegistration,$formData,&$msg) {
    	 
    	//get register subject
    	$dbGrpMinor=new App_Model_Registration_DbTable_CourseGroupMinor();
    	$dbSchMinor=new App_Model_Registration_DbTable_CourseGroupScheduleMinor();
    	$dbschedule=new App_Model_Registration_DbTable_CourseGroupSchedule();
    	 
    	$SubjectDb=new App_Model_Record_DbTable_StudentRegSubjects();
    	//$idsemester=$formdata['IdSemester'];
    	//$IdStudentRegistration=$formdata['IdStudentRegistration'];
    	//get new schedule
    	$newschedules=array();
    	$newschedulesMinor=array();
    	for($i=0; $i<count($formData['idSubject']); $i++) {
    		$idSubject=$formData['idSubject'][$i];
    		$subjs=$SubjectDb->getSubjectmaster($idSubject);
    		$radioGroup = "radio_".$idSubject;
    		if (!isset($formData[$radioGroup])) {
    			$radioGroup = "radiominor_".$idSubject;
    			$idGroupMinor=$formData[$radioGroup];
	    		if ($idGroupMinor!='') {
	    			$grpminor=$dbGrpMinor->getInfo($idGroupMinor);
	    			//echo var_dump($grpminor);exit;
	    			$idGroup=$grpminor['group_id'];
	    			$formData["radio_".$idSubject]=$idGroup;
	    			//echo 'ik';
	    			//echo var_dump($formData);exit;
	    			$schedule=$dbSchMinor->getSchedule($idGroupMinor);
	    			foreach ($schedule as $sch) {
	    				$newschedulesMinor[]=array('IdCourseTaggingGroupMinor'=>$idGroupMinor,
	    						'IdSubject'=>$idSubject,
	    						'group_id'=>$idGroup,
	    						'SubCode'=>$subjs['SubCode'],
	    						'SubjectName'=>$subjs['BahasaIndonesia'],
	    						'sc_date'=>$sch['sc_date'],
	    						'sc_date_end'=>$sch['sc_date_end'],
	    						'sc_day'=>$sch['sc_day'],
	    						'sc_start_time'=>$sch['sc_start_time'],
	    						'sc_end_time'=>$sch['sc_end_time']);
	    			}
	    			//echo var_dump($newschedulesMinor);
	    			$newschedulesMinor=$this->removeclashMinor($newschedulesMinor,$msg);
	    		}
    			//echo 'after';
    			//echo var_dump($newschedulesMinor);echo "---<br>";
    		} else
    			$idGroup=$formData[$radioGroup];
    		//echo var_dump($formData);exit;
    		$schedule=$dbschedule->getSchedule($idGroup);
    		foreach ($schedule as $sch) {
    			$newschedules[]=array('IdCourseTaggingGroup'=>$idGroup,
    					'IdSubject'=>$idSubject,
    					'SubCode'=>$subjs['SubCode'],
    					'SubjectName'=>$subjs['BahasaIndonesia'],
    					'sc_date'=>$sch['sc_date'],
    					'sc_date_end'=>$sch['sc_date_end'],
    					'sc_day'=>$sch['sc_day'],
    					'sc_start_time'=>$sch['sc_start_time'],
    					'sc_end_time'=>$sch['sc_end_time']);
    		}
    		 
    	}
    	//check forclash
    	//echo var_dump($newschedules);echo "-====before<br>";
    	$newschedules=$this->removeclash($newschedules,$msg);
    	//echo var_dump($newschedules);echo "-====after<br>";
    	//exit;
    	if ($newschedulesMinor!=array()) {
    		//cleansing for Minor Class
    		$dbMinorStd=new App_Model_General_DbTable_CourseGroupStudentMinor();
    		$newschedulesMinor=$this->removeclashMinor($newschedulesMinor,$msg);
    		$schedules=array();
    		if ($newschedulesMinor!=array()) {
    			//get registersed minor class
    			$registerSubjectMinor=$dbMinorStd->getSubjectRegisterMinor( $IdStudentRegistration,$idsemester);
    			//echo var_dump($registerSubjectMinor);echo "-subject registered--<br>";
    			if ($registerSubjectMinor) {
    				foreach ($registerSubjectMinor as $subject) {
    					$schedule=$dbSchMinor->getSchedule($subject['IdCourseTaggingGroupMinor']);
    					foreach ($schedule as $sch) {
    						$schedules[]=array('IdCourseTaggingGroupMinor'=>$subject['IdCourseTaggingGroupMinor'],
    								'group_id'=>$subject['group_id'],
    								'IdSubject'=>$subject['IdSubject'],
    								'SubCode'=>$subject['SubCode'],
    								'SubjectName'=>$subject['SubjectName'],
    								'sc_date'=>$sch['sc_date'],
    								'sc_date_end'=>$sch['sc_date_end'],
    								'sc_day'=>$sch['sc_day'],
    								'sc_start_time'=>$sch['sc_start_time'],
    								'sc_end_time'=>$sch['sc_end_time']);
    					}
    				}
    			}
    		}
    
    		//remove clash to registered subjects;
    		if ($schedules!=array()) {
    			//echo var_dump($schedules);
    			//echo "-------------";
    			//echo "<br>";
    			//echo  var_dump($newschedules);
    			//exit;
    			//echo ">>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>register>>>>>>";
    			foreach ($newschedulesMinor as $key=>$new){
    				foreach ($schedules as $old) {
    					//echo "new=";echo var_dump($new); echo "old";echo var_dump($old);
    					if ($this->isClashMinor($new, $old)) {
    						unset($newschedulesMinor[$key]);
    						$msg=$msg.' '.$new['SubCode'].'-'.$new['SubjectName'].' dengan '.$old['SubCode'].'-'.$old['SubjectName'].'<br>';
    						//echo "clashXXxxxxxxx===";
    					}
    				}
    			}
    		}
    		//echo "======after classs Minor";
    		//echo var_dump($newschedulesMinor);
    		//echo'----->>>>>--------'.$msg;
    		//exit;
    		//if ($newschedulesMinor==array()) $formData=array();
    		 
    		foreach ($formData['idSubject'] as $key=>$subject)  {
    			$sts='';
    			foreach ($newschedulesMinor as $sch) {
    				//	echo var_dump($subject);echo '==';echo $sch['IdSubject'];
    				if ($subject==$sch['IdSubject']) $sts='1';
    					
    			}
    			if ($sts=='') unset($formData['idSubject'][$key]);
    		}
    	}
    	//echo var_dump($newschedulesMinor);echo '<br>';
    	//echo "======after remove=====";
    	//echo var_dump($formData);
    	//exit;
    	//echo "========>>>>>>>==";
    	//============== get registered subject
    	$schedules=array();
    	if ($newschedules!=array()) {
    
    		$registerSubject=$SubjectDb->getSemesterSubjectRegistered($idsemester, $IdStudentRegistration);
    		if ($registerSubject) {
    			foreach ($registerSubject as $subject) {
    				$schedule=$dbschedule->getSchedule($subject['IdCourseTaggingGroup']);
    				foreach ($schedule as $sch) {
    					$schedules[]=array('IdCourseTaggingGroup'=>$subject['IdCourseTaggingGroup'],
    							'IdSubject'=>$subject['IdSubject'],
    							'SubCode'=>$subject['SubCode'],
    							'SubjectName'=>$subject['BahasaIndonesia'],
    							'sc_date'=>$sch['sc_date'],
    							'sc_date_end'=>$sch['sc_date_end'],
    							'sc_day'=>$sch['sc_day'],
    							'sc_start_time'=>$sch['sc_start_time'],
    							'sc_end_time'=>$sch['sc_end_time']);
    				}
    			}
    		}
    	}
    	//remove clash to registered subjects;
    	//echo var_dump($formData);
    	if ($schedules!=array()) {
    		//echo "remove registered class <br>";
    		//echo var_dump($schedules);
    		//echo "-------------";
    		//echo "<br>";
    
    		//echo  var_dump($newschedules);
    		//exit;
    		//echo ">>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>register>>>>>>";
    		foreach ($newschedules as $key=>$new){
    			foreach ($schedules as $old) {
    				//echo "new=";echo var_dump($new); echo "old";echo var_dump($old);
    				if ($this->isClash($new, $old)) {
    					unset($newschedules[$key]);
    					$msg=$msg.' '.$new['SubCode'].'-'.$new['SubjectName'].' dengan '.$old['SubCode'].'-'.$old['SubjectName'].'<br>';
    					//echo "clashXXxxxxxxx===".$msg;
    				}
    			}
    		}
    	}
    	//exit;
    	// "======after classs";
    	//echo var_dump($newschedules);//exit;
    	//echo'----->>>>>--------';
    	if ($newschedules==array()) $formData=array();
    	 
    	foreach ($formData['idSubject'] as $key=>$subject)  {
    		$sts='';
    		foreach ($newschedules as $sch) {
    			//	echo var_dump($subject);echo '==';echo $sch['IdSubject'];
    			if ($subject==$sch['IdSubject']) $sts='1';
    				
    		}
    		if ($sts=='') unset($formData['idSubject'][$key]);
    	}
    	//echo "==========formdata";
    	//echo var_dump($formData['idSubject']);exit;
    	if (!isset($formData['idSubject'])) $formData=array();
    	 
    	return $formData;
    }
    
    public function ruleCleaningPreSaving($idsemester,$IdStudentRegistration,$formData,&$msg,$rules) {
    	 
     	//$dbCourseGroup=new App_Model_Registration_DbTable_CourseGroup();
     	$courseRegisterDb = new App_Model_Record_DbTable_StudentRegistration();
     	//echo var_dump($formData);echo '<br>';
     	$idGroup=null;
     	foreach ($formData['idSubject'] as $idSubject) {
    			$radioGroup = "radio_".$idSubject;
    			$idGroup[]=$formData[$radioGroup];	 
    	}
    	$groupslistNew=null;
    	if ($idGroup!=null) {
	     	$groupslistNew=implode(',', $idGroup);
	     	$newOwner =$courseRegisterDb->getRegisterMngCourse($groupslistNew);
    	} else $newOwner=null;
    	//========================
     	$idGroup=null;
     	$registerSubject=$courseRegisterDb->getCourseRegisteredBySemester($IdStudentRegistration, $idsemester);
     	foreach ($registerSubject as $subject) {
     		$idGroup[]=$subject['IdCourseTaggingGroup'];
     	}
     	if ($idGroup!=null) {
     		$groupslist=implode(',', $idGroup);
     		$regOwner =$courseRegisterDb->getRegisterMngCourse($groupslist);
    	} else $regOwner=null;
    	 
     	if (!$regOwner) $regOwner=$newOwner;
     	else {
	     	foreach ($newOwner as $value) {
	     		 $add="0";
	     		 foreach ($regOwner as $key=>$regO) {
	     		 	if ($value['BranchCreator']==$regO['BranchCreator']) {
	     		 		 
	     		 		$ncourse=$regO['nCourse']+$value['nCourse'];
	     		 	 
	     		 		$regOwner[$key]= array('BranchCreator'=>$value['BranchCreator'],
	     		 								'nCourse'=>$ncourse,
	     		 								'BranchMng'=>$value['BranchMng']
	     		 		);
	     		 		
	     		 		$add="1";
	     		 	}
	     		 }
	     		 if ($add=="0") $regOwner[]=array('BranchCreator'=>$value['BranchCreator'],'nCourse'=>$value['nCourse']);
	     	}
	     	
     	}
     	 
     	//check for rule 
     	$deny=null;
     	//echo var_dump($regOwner);exit;
     	foreach ($rules as $item) {
     		$branchrule=$item['branch_course'];
     		$batas=$item['batas'];
     		foreach ($regOwner as $value) {
     			if (($value['nCourse'] > $batas) && $branchrule==$value['BranchCreator']) $deny[]=array('BranchCreator'=>$branchrule,'outcourse'=>($value['nCourse'] - $batas));
     				 
     		}
     	}
     	//remove arbritary course related to deny branch
     	if ($deny!=null) {
     		 
     		 
     		foreach ($deny as $value) {
	     		 $outcourse=$value['outcourse'];
	     		 if ($groupslistNew!=null)
	     			 $newCourse=$courseRegisterDb->getCourse($groupslistNew,$value['BranchCreator']);
	     		 else  
	     		 	$newCourse=array();
	     		 	 
	     		  $delcourse=0;
	     		 $ncourse=count($newCourse);
	     		  
	     		 foreach ($newCourse as $course) {
	     		 	  
	     		 	 //echo $course['IdSubject'].'<br>';
	     		 	 foreach ( $formData['idSubject'] as $index=>$item) {
	     		 			if ($item==$course['IdSubject']) {
	     		 				unset($formData['idSubject'][$index]);
	     		 				$msg=$msg." ".$course['ShortName'].'-'.$course['BahasaIndonesia'].";";
	     		 				 
	     		 				$outcourse--;  
	     		 			}
	     		 	 
	     		 		
	     		 	}
	     		 
	     		 	if ($outcourse==0) {
	     		 		break;
	     		 	}
	     		 }
	     	}
	     	$msg=$msg." melebihi aturan antar kampus atau kelas";
     	}
     	 
    	return $formData;
    }
    
    public function removeclash($schedules,&$msg) {
		
    	$schtmps=$schedules;
    	foreach ($schtmps as $index=>$schtmp) {
    		$schs=$schtmps;
    		foreach ($schs as $key=>$sch) {
    	
		    	if ($schtmp['IdCourseTaggingGroup']!=$sch['IdCourseTaggingGroup']) {
		    	
		    		if ($schtmp['sc_date']==$sch['sc_date'] && $schtmp['sc_date_end']==$sch['sc_date_end']) {
		    			
		    			if ($schtmp['sc_day']==$sch['sc_day'] ) {
		    		 
			    			if (($schtmp['sc_start_time']<=$sch['sc_start_time'] && $sch['sc_start_time'] <$schtmp['sc_end_time']) || ($schtmp['sc_start_time']<$sch['sc_end_time'] && $sch['sc_end_time'] <=$schtmp['sc_end_time'])) {
			    				//drop subject
			    				$idGroup=$schtmp['IdCourseTaggingGroup'];
			    				$temps=$schtmps;
			    				foreach ($temps as $i=>$t) {
			    					if ($t['IdCourseTaggingGroup']==$idGroup) {
			    						unset($schtmps[$i]);
			    						$msg=$schtmp['SubCode'].'-'.$schtmp['BahasaIndonesia'];
			    					}
			    			
			    				}
			    			}
		    			}
		    		} else
		    		if (($schtmp['sc_date']<=$sch['sc_date'] && $sch['sc_date'] < $schtmp['sc_date_end']) || //inner
    					($schtmp['sc_date']<=$sch['sc_date_end'] && $sch['sc_date_end'] <=$schtmp['sc_date_end'])|| //inner end
    					($schtmp['sc_date']<=$sch['sc_date_end'] && $schtmp['sc_date']>=$sch['sc_date'])|| //inner 2nd
    					($sch['sc_date']<=$schtmp['sc_date_end'] && $sch['sc_date']>=$schtmp['sc_date']))//inner star
    					{	//check time clash
		    			//echo var_dump($schtmp);echo '=';var_dump($sch);exit;
    						if ($schtmp['sc_day']==$sch['sc_day'] ) {
	    						if (($schtmp['sc_start_time']<=$sch['sc_start_time'] && $sch['sc_start_time'] <$schtmp['sc_end_time']) || ($schtmp['sc_start_time']<$sch['sc_end_time'] && $sch['sc_end_time'] <=$schtmp['sc_end_time'])) {
			    				//drop subject
			    				$idGroup=$schtmp['IdCourseTaggingGroup'];
			    				$temps=$schtmps;
			    				foreach ($temps as $i=>$t) {
			    					if ($t['IdCourseTaggingGroup']==$idGroup) {
			    						unset($schtmps[$i]);
			    						$msg=$schtmp['SubCode'].'-'.$schtmp['BahasaIndonesia'];
			    					}
			    						
			    				}
    						}   	
		    			}
		    		}
		    	}
    
    		}
    	}
    	return $schtmps;
    	
    }
    
    public function removeclashMinor($schedules,&$msg) {
    
    	$schtmps=$schedules;
    	foreach ($schtmps as $index=>$schtmp) {
    		$schs=$schtmps;
    		foreach ($schs as $key=>$sch) {
    			 
    			if ($schtmp['IdCourseTaggingGroupMinor']!=$sch['IdCourseTaggingGroupMinor']) {
    				 
    				if ($schtmp['sc_date']==$sch['sc_date'] && $schtmp['sc_date_end']==$sch['sc_date_end']) {
    					 
    					if ($schtmp['sc_day']==$sch['sc_day'] ) {
    						 
    						if (($schtmp['sc_start_time']<=$sch['sc_start_time'] && $sch['sc_start_time'] <$schtmp['sc_end_time']) || ($schtmp['sc_start_time']<$sch['sc_end_time'] && $sch['sc_end_time'] <=$schtmp['sc_end_time'])) {
    							//drop subject
    							$idGroup=$schtmp['IdCourseTaggingGroupMinor'];
    							$temps=$schtmps;
    							foreach ($temps as $i=>$t) {
    								if ($t['IdCourseTaggingGroupMinor']==$idGroup) {
    									unset($schtmps[$i]);
    									$msg=$schtmp['SubCode'].'-'.$schtmp['BahasaIndonesia'];
    								}
    
    							}
    						}
    					}
    				} else
    				if (($schtmp['sc_date']<=$sch['sc_date'] && $sch['sc_date'] < $schtmp['sc_date_end']) || //inner
    				($schtmp['sc_date']<=$sch['sc_date_end'] && $sch['sc_date_end'] <=$schtmp['sc_date_end'])|| //inner end
    				($schtmp['sc_date']<=$sch['sc_date_end'] && $schtmp['sc_date']>=$sch['sc_date'])|| //inner 2nd
    				($sch['sc_date']<=$schtmp['sc_date_end'] && $sch['sc_date']>=$schtmp['sc_date']))//inner star
    				{	//check time clash
    					//echo var_dump($schtmp);echo '=';var_dump($sch);exit;
    					if ($schtmp['sc_day']==$sch['sc_day'] ) {
    						if (($schtmp['sc_start_time']<=$sch['sc_start_time'] && $sch['sc_start_time'] <$schtmp['sc_end_time']) || ($schtmp['sc_start_time']<$sch['sc_end_time'] && $sch['sc_end_time'] <=$schtmp['sc_end_time'])) {
    							//drop subject
    							$idGroup=$schtmp['IdCourseTaggingGroupMinor'];
    							$temps=$schtmps;
    							foreach ($temps as $i=>$t) {
    								if ($t['IdCourseTaggingGroupMinor']==$idGroup) {
    									unset($schtmps[$i]);
    									$msg=$schtmp['SubCode'].'-'.$schtmp['BahasaIndonesia'];
    								}
    								 
    							}
    						}
    					}
    				}
    			}
    
    		}
    	}
    	return $schtmps;
    	 
    }
    public function removeclashExam($schedules,&$msg) {
    
    	$schtmps=$schedules;
    	foreach ($schtmps as $index=>$schtmp) {
    		$schs=$schtmps;
    		foreach ($schs as $key=>$sch) {
    			 
    			if ($schtmp['IdSubject']!=$sch['IdSubject']) {
    				 
    				if ($schtmp['eg_date']==$sch['eg_date'] ) {
    					//check time clash
    					//echo var_dump($schtmp);echo '=';var_dump($sch);exit;
    					if (($schtmp['eg_start_time']<=$sch['eg_start_time'] && $sch['eg_start_time'] <$schtmp['eg_end_time']) || ($schtmp['eg_start_time']<$sch['eg_end_time'] && $sch['eg_end_time'] <=$schtmp['eg_end_time'])) {
    						//drop subject
    						$idGroup=$schtmp['eg_id'];
    						$temps=$schtmps;
    						foreach ($temps as $i=>$t) {
    							if ($t['eg_id']==$idGroup)
    								unset($schtmps[$i]);
    								$msg=$msg." ".$schtmp['subject_code'].'-'.$schtmp['subject_name'].";";
    						}
    
    					}
    				}
    			}
    
    		}
    	}
    	return $schtmps;
    	 
    }
    
    public function isClash($schtmp,$sch) {
    	//echo "cek clash 0";exit;
    	if ($schtmp['IdCourseTaggingGroup']!=$sch['IdCourseTaggingGroup']) {
    		// echo "cek clash";exit;
    		if ($schtmp['sc_date_end']==$sch['sc_date_end'] && $schtmp['sc_date']==$sch['sc_date']) {
    				if ($schtmp['sc_day']==$sch['sc_day'] ) {
    					if (($schtmp['sc_start_time']<=$sch['sc_start_time'] && $sch['sc_start_time'] <$schtmp['sc_end_time']) || ($schtmp['sc_start_time']<$sch['sc_end_time'] && $sch['sc_end_time'] <=$schtmp['sc_end_time'])) {
    				 
    						return true;
    					}
    				}  
    		 
    		} else 
    		if (($schtmp['sc_date']<=$sch['sc_date'] && $sch['sc_date'] < $schtmp['sc_date_end']) || //inner
    		($schtmp['sc_date']<=$sch['sc_date_end'] && $sch['sc_date_end'] <=$schtmp['sc_date_end'])|| //inner end
    		($schtmp['sc_date']<=$sch['sc_date_end'] && $schtmp['sc_date']>=$sch['sc_date'])|| //inner 2nd
    		($sch['sc_date']<=$schtmp['sc_date_end'] && $sch['sc_date']>=$schtmp['sc_date']))//inner star
    		{
    			//check time clash
    			//echo 'dateclash';exit;
    			//echo var_dump($schtmp);echo '===';var_dump($sch);
    			if ($schtmp['sc_day']==$sch['sc_day'] ) {
	    			if (($schtmp['sc_start_time']<=$sch['sc_start_time'] && $sch['sc_start_time'] <$schtmp['sc_end_time']) || ($schtmp['sc_start_time']<$sch['sc_end_time'] && $sch['sc_end_time'] <=$schtmp['sc_end_time'])) {
	    				//drop subject
	    				//echo 'date;';exit;
	    				 return true;
	    	
	    			}
    			}
    		}
    	}
    	return false;
    }
    
    public function isClashMinor($schtmp,$sch) {
    	//echo "cek clash 0";exit;
    	if ($schtmp['IdCourseTaggingGroupMinor']!=$sch['IdCourseTaggingGroupMinor']) {
    		// echo "cek clash";exit;
    		if ($schtmp['sc_date_end']==$sch['sc_date_end'] && $schtmp['sc_date']==$sch['sc_date']) {
    			if ($schtmp['sc_day']==$sch['sc_day'] ) {
    				if (($schtmp['sc_start_time']<=$sch['sc_start_time'] && $sch['sc_start_time'] <$schtmp['sc_end_time']) || ($schtmp['sc_start_time']<$sch['sc_end_time'] && $sch['sc_end_time'] <=$schtmp['sc_end_time'])) {
    						
    					return true;
    				}
    			}
    			 
    		} else
    		if (($schtmp['sc_date']<=$sch['sc_date'] && $sch['sc_date'] < $schtmp['sc_date_end']) || //inner
    		($schtmp['sc_date']<=$sch['sc_date_end'] && $sch['sc_date_end'] <=$schtmp['sc_date_end'])|| //inner end
    		($schtmp['sc_date']<=$sch['sc_date_end'] && $schtmp['sc_date']>=$sch['sc_date'])|| //inner 2nd
    		($sch['sc_date']<=$schtmp['sc_date_end'] && $sch['sc_date']>=$schtmp['sc_date']))//inner star
    		{
    			//check time clash
    			//echo 'dateclash';exit;
    			//echo var_dump($schtmp);echo '===';var_dump($sch);
    			if ($schtmp['sc_day']==$sch['sc_day'] ) {
    				if (($schtmp['sc_start_time']<=$sch['sc_start_time'] && $sch['sc_start_time'] <$schtmp['sc_end_time']) || ($schtmp['sc_start_time']<$sch['sc_end_time'] && $sch['sc_end_time'] <=$schtmp['sc_end_time'])) {
    					//drop subject
    					//echo 'date;';exit;
    					return true;
    
    				}
    			}
    		}
    	}
    	return false;
    }
    public function isExamClash($schtmp,$sch) {
    	if ($schtmp['IdSubject']!=$sch['IdSubject']) {
    		 
    		if ($schtmp['eg_date']==$sch['eg_date']  ) {
    			//check time clash
    			//echo var_dump($schtmp);echo '===';var_dump($sch);
    			if (($schtmp['eg_start_time']<=$sch['eg_start_time'] && $sch['eg_start_time'] <$schtmp['eg_end_time']) || ($schtmp['eg_start_time']<$sch['eg_end_time'] && $sch['eg_end_time'] <=$schtmp['eg_end_time'])) {
    				//drop subject
    				return true;
    				 
    			}
    		}
    	}
    	return false;
    }
    
    public function ch_total($registration_id,$stringSemesterIds) {

    	$courseRegisterDb = new App_Model_Record_DbTable_StudentRegistration();
         $courses_credit = $courseRegisterDb->getCourseRegisteredBySemesterId($registration_id,$stringSemesterIds);
         //totalkan kredit
         $total_credit = NULL;
         
         foreach($courses_credit as $a => $b)
         {
         	$total_credit = $total_credit + $b['CreditHours'];
         
         }
         return $total_credit;
    	
    }
    public function blockViewStudentAction() {
    	$auth = Zend_Auth::getInstance();
    	 
    	$this->view->title= 'Pay Attention to Barring';
    	$registration_id = $auth->getIdentity()->registration_id;
    	$this->view->IdStudentRegistration = $registration_id;
    	$idSemester = $this->_getParam('idSemester', 0);
    	$dbSemester=new App_Model_General_DbTable_Semestermaster();
    	$sem=$dbSemester->getSemester($idSemester);
    	$this->view->idSemester = $idSemester;
    	$studentRegDB = new App_Model_Record_DbTable_StudentRegistration();
    	$student = $studentRegDB->getStudentInfo($registration_id);
    	$this->view->student = $student;
    	if ( $this->_getParam('errmsg')==2) {
    		$this->view->semester=$sem['SemesterMainName'];
    		$this->view->errormsg=$this->_getParam('errmsg');
    		$this->view->msg='Silahkan mengisi bimbingan dengan menu Academic Advising, Saudara dapat mengisi KRS setelah mendapatkan persetujuan dari dosen wali ';
    	} else {
	    	$dbrelesese=new App_Model_Record_DbTable_Barringrelease();
	    	$barring=$dbrelesese->getBarringStudent($this->view->IdStudentRegistration, $this->view->idSemester);
	    	$this->view->barring=$barring;
    	}
    	
    }
    public function dropCourseAction(){
    	 
    	$auth = Zend_Auth::getInstance();
    		
    	$dbSub=new App_Model_Record_DbTable_StudentRegSubjects();
    	$dbconfirm=new App_Model_Record_DbTable_Confirmation();
    	if ($this->getRequest()->isPost()) {
    			
    		$formData = $this->getRequest()->getPost();
    		foreach ($formData['drop'] as $key=>$value) {
    			$dbSub->drop($key);
    			
    		}
    		//check invoice if paid move to advantage
    		
    		$dbconfirm->inactive($formData['confirm'], $formData['IdStudentRegistration']);
    		$this->_redirect( $this->baseUrl . '/default/course-registration/course-register');
    			
    	}
    	
    }
}
?>
