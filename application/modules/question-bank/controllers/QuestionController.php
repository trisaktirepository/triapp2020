<?php

class QuestionBank_QuestionController extends Zend_Controller_Action {
	
	public function indexAction() {
		//title
    	$this->view->title="Manage Question";
    	
    	//paginator
		$poolDB = new App_Model_Question_DbTable_Pool();
		$poolData = $poolDB->getPaginateData();
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($poolData));
		$paginator->setItemCountPerPage(20);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
	}
	
	
	public function addAction()
    {
    	//title
    	$this->view->title="Add New Question Pool";
    	
    	$form = new QuestionBank_Form_Pool();
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				$date = date('Y-m-d H:i:s');
				
				$auth = Zend_Auth::getInstance();
				$idUpd = $auth->getIdentity()->id;
				
				$data = array(
					'name' => $formData['name'],
					'desc' => $formData['desc'],
					'status' => $formData['status'],
					'dateUpd' => $date,
					'updUser' =>$idUpd
				);
				
				
				//process form 
				$pool = new App_Model_Question_DbTable_Pool();
				$pool->addData($data);
				
				//redirect	
				$this->_redirect($this->view->url(array('module'=>'question-bank','controller'=>'pool', 'action'=>'index'),'default',true));	
			}else{
				$form->populate($formData);
			}
        	
        }
        $this->view->form = $form;
        
        
    }
    
	
    
	public function deleteAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$pool = new App_Model_Question_DbTable_Pool();
    		$pool->deleteData($id);
    	}
    		
    	$this->_redirect($this->view->url(array('module'=>'question-bank','controller'=>'pool', 'action'=>'index'),'default',true));
    	
    }
    
	public function viewAction(){
		//title
    	$this->view->title="Question Bank";
    	
    	$pool_id= $this->_getParam('pool_id', 0);
    	$this->view->pool_id = $pool_id;
    	
    	//pool info
    	$poolDB = new App_Model_Question_DbTable_Pool();
    	$poolData = $poolDB->getData($pool_id);
    	$this->view->pool = $poolData;   
    	
    	$topicDB = new App_Model_Question_DbTable_Chapter(); 
    	$topic = $topicDB->getTopicbyPool($pool_id);
    	$this->view->topic = $topic;   	
    
    	if ($this->getRequest()->isPost()) {
    		
    		$topic_id = $this->getRequest()->getPost('topic_id');
    		$question = $this->getRequest()->getPost('question');
    		$qid = $this->getRequest()->getPost('question_id');
    		
    		$condition = array('pool_id'=>$pool_id,'topic_id'=>$topic_id,'question'=>$question,'question_id'=>$qid);
    		
	    	$questionDB = new App_Model_Question_DbTable_Question();
	    	$this->view->question = $questionDB->getQuestion($condition);
	    	
    	}else{
    		//question
	    	$condition = array('pool_id'=>$pool_id,'topic_id'=>"",'question'=>"");
	    	$questionDB = new App_Model_Question_DbTable_Question();
	    	$this->view->question = $questionDB->getQuestion($condition);
    	}
    	
    	
    	

    	//check for course
    	if($this->view->question==null){
    		$this->view->noticeMessage = "No question";	
    	}
    	
    	   	
	}
	
	
	public function addQuestionAction()
    {    	
    	
    	//title
    	$this->view->title="Add Question";
    	
    	if ($this->getRequest()->isPost()) {
			
			$formdata = $this->getRequest()->getPost();
			
			$auth = Zend_Auth::getInstance();
			$userid   = $auth->getIdentity()->id;
			
			$total = $formdata["total_language"]; 	  
			
			
				    $q["pool_id"]          = $formdata["pool_id"];	
				    $q["topic_id"]         = $formdata["topic_id"];	
		     		$q["question_type"]    = $formdata["question_type"];	
		     		$q["difficulty_level"] = $formdata["difficulty_level"];	
		     		$q["status"]           = $formdata["status"];	
		     		$q["point"]            = 1;	
		     		$q["review"]           = 1;	
		     		$q["english"]          = stripslashes($formdata["question1"]);	
		     		$q["malay"]            = stripslashes($formdata["question2"]);	
		     		$q["createdby"]        = $userid;
		     		$q["createddt"]        = date('Y-m-d H:i:s');
		     		
		     		//print_r($q);
		     		
					$questionDB = new App_Model_Question_DbTable_Question();
		            $question_id = $questionDB->addData($q);  
					
						for($j=1; $j<=4; $j++){ 	

							$a["question_id"] = $question_id;			  
						    $a["english"] = stripslashes($formdata["answer1".$j]);
						    $a["malay"]   = stripslashes($formdata["answer2".$j]);						    				    
						    
						    if($formdata["correct_answer"]==$j)
						      $a["correct_answer"] =1;
						    else 
						      $a["correct_answer"] =0;	 		
						    
						      print_r($a);
							$answerDB = new App_Model_Question_DbTable_Answer();
							$answerDB->addData($a); 
									
						} // end for j
						
						
				//redirect	
				$this->_redirect($this->view->url(array('module'=>'question-bank','controller'=>'question', 'action'=>'edit','qid'=>$question_id,'pid'=>$formdata["pool_id"]),'default',true));	
			
			
			  
	
    	}else{
    	
	    	$pool_id= $this->_getParam('pool_id', 0);
		    $this->view->pool_id = $pool_id;
		    
		    //topic info
	    	$topicDB = new App_Model_Question_DbTable_Chapter();
	    	$topicData = $topicDB->getTopicbyPool($pool_id);
	    	$this->view->topic = $topicData;  
	    	
	    	//type
	    	$typeDB = new App_Model_Question_DbTable_QuestionType();
	    	$typeData = $typeDB->getData();
	    	$this->view->type = $typeData;  
	    	
    	}

    	//check for course
    	if($this->view->question==null){
    		$this->view->noticeMessage = "No question";	
    	}
    }
    
    
    public function editAction(){
    	
    	$this->view->title="Edit Question";
    	
    	
    	
    	if ($this->getRequest()->isPost()) {
			
			$formdata = $this->getRequest()->getPost();
			
			$total = $formdata["total_language"]; 	    	
		     		
		     				     	
		     		$q["topic_id"]         = $formdata["topic_id"];	
		     		$q["question_type"]    = $formdata["question_type"];	
		     		$q["difficulty_level"] = $formdata["difficulty_level"];	
		     		$q["status"]           = $formdata["status"];	
		     		$q["english"]          = stripslashes($formdata["question1"]);	
		     		$q["malay"]            = stripslashes($formdata["question2"]);	
		     		
		     		
					$questionDB = new App_Model_Question_DbTable_Question();
		            $questionDB->updateData($q,$formdata["qid"]);  
					
						for($j=1; $j<=4; $j++){ 	
													  
						    $a["english"] = stripslashes($formdata["answer1".$j]);
						    $a["malay"]   = stripslashes($formdata["answer2".$j]);						    				    
						    
						    if($formdata["correct_answer"]==$j)
						      $a["correct_answer"] =1;
						    else 
						      $a["correct_answer"] =0;	 		
						    
							$answerDB = new App_Model_Question_DbTable_Answer();
							$answerDB->updateData($a,$formdata["answer_id".$j]); 
									
						} // end for j
						
				//redirect	
				$this->_redirect($this->view->url(array('module'=>'question-bank','controller'=>'question', 'action'=>'edit','qid'=>$formdata["qid"],'pid'=>$formdata["pid"]),'default',true));	
			
			
    	}else{
    		
    		$qid = $this->_getParam('qid', 0);
	    	$this->view->qid = $qid;
	    	
	    	$pool_id= $this->_getParam('pid', 0);
	    	$this->view->pool_id = $pool_id;
	    	
	    	
	    	//question
	    	$condition = array('qid'=>$qid);
	    	$questionDB = new App_Model_Question_DbTable_Question();
	    	$question = $questionDB->getQuestion($condition);
	    	$this->view->question = $question;
	    	
	    	//answer
	    	$answerDB = new App_Model_Question_DbTable_Answer();
	    	$answer = $answerDB->getAnswerbyQuestion($qid);
	    	$this->view->answer = $answer;    
	    	   	    	    	
	    	//topic info
	    	$topicDB = new App_Model_Question_DbTable_Chapter();
	    	$topicData = $topicDB->getTopicbyPool($pool_id);
	    	$this->view->topic = $topicData;  
	    	
	    	//type
	    	$typeDB = new App_Model_Question_DbTable_QuestionType();
	    	$typeData = $typeDB->getData();
	    	$this->view->type = $typeData;  
    		
    	}
    	
    	
    }
    
    public function deleteQuestionAction($id = null){
    	$qid = $this->_getParam('qid', 0);
    	
    	if($qid>0){
    		$questionDB = new App_Model_Question_DbTable_Question();
    		$questionDB->deleteData($qid);
    		
    		//delete answer
    		$answerDB = new App_Model_Question_DbTable_Answer();
    		$answerDB->deleteAnswer($qid);
    	}
    		    	
    	$this->_redirect($this->view->url(array('module'=>'question-bank','controller'=>'question', 'action'=>'view')));
    	
    }
    
    
    
    
    
    
    
    
    
    /*============== MIGRATION FROM DBASE SACHIN SECTION ============*/
    
	public function importQuestionAction(){
    	
    	$questionDB = new App_Model_Question_DbTable_Questionimport();
    	$questionData = $questionDB->getQuestionSachin('C');
    	
    	exit;
    	
    	$total = count($questionData);
    	
    	echo "<br>total = ".$total."<hr>";
    	echo "
    	<table width='100%' border='1'>";
     	$i=1;
    	foreach ($questionData as $data){
    	
    	$answerData = $questionDB->getanswerSachin($data['idquestions']);
    	
			 echo " <tr valign='top'>
			 	<td>$i)</td>
			 	<td>$data[QuestionGroup]</td>
			    <td>$data[QuestionNumber]</td>
			    <td>$data[QuestionLevel]</td>";
			 
			 $getChapter = $questionDB->getChapter($data['QuestionNumber']);
			 $chapter = $getChapter['id'];
			 
			 $idQuestion = $data['idquestions'];
			 
			 	$date = date('Y-m-d H:i:s');
				
				$auth = Zend_Auth::getInstance();
				$idUpd = $auth->getIdentity()->id;
				
			if($data['QuestionGroup'] == "A"){
				$poolID = 1;
			}elseif($data['QuestionGroup'] == "B"){
				$poolID = 2;
    		}elseif($data['QuestionGroup'] == "C"){
				$poolID = 3;
	    	}				
			  $dataMain = array(
				  'pool_id'=>$poolID,
				  'question_type'=>1,
				  'topic_id'=>$chapter,
				  'difficulty_level'=>$data['QuestionLevel'],
				  'english'=>$data['Question'],
				  'malay'=>$data['Malay'],
				  'tamil'=>'',
				  'chinese'=>'',
				  'status'=>'1',
				  'point'=>1,
				  'createdby'=>$idUpd,
				  'createddt'=>$date
			  );
			      
			   $addMain = $questionDB->addData('q003_question',$dataMain);   
			 	
			 	//=============english=================
			    echo "<td>$data[Question]</td>";
			      echo "<td>";
			      
//			      $dataEng = array(
//				      'question_main_id'=>$addMain,
//				      'question'=>$data['Question'],
//				      'language'=>1
//			      );
//			      
//			   $addEnglish = $questionDB->addData('q004_question',$dataEng);   
//			   
			    foreach ($answerData as $ans){
			    	echo "<input type='radio' name='answer' id='answer' value='$ans[idanswers]'>";
			    	echo $ans['answers'];
			    	echo "<br>";
			    	
			    	$idAnwer = $ans['idanswers'];
			    	
			    	$answerDB = new App_Model_Question_DbTable_Answer();
			    	
			    	$checkCorrectAns = $questionDB->getCorrectanswerSachin($idQuestion,$idAnwer);
			    	
			    	if($checkCorrectAns){
			    		$corAns = 1;
			    	}else{
			    		$corAns = 0;
			    	}
			    	
			    	$dataEngAns = array(
				      	'question_id'=>$addMain,
			    	  	'english'=>$ans['answers'],
			    		'malay'=>$ans['Malay'],
				      	'correct_answer'=>$corAns
			     	 );
			    	$addEnglishAns = $questionDB->addData('q006_answer',$dataEngAns);  
			    	
			    }
			    
			    
//			    =============malay==================
			    
			    echo "</td>";
//			    <td>$data[Malay]</td>";
//			    echo "<td>";
//			    
//			    $dataMalay = array(
//				      'question_main_id'=>$addMain,
//				      'question'=>$data['Malay'],
//				      'language'=>2
//			      );
//			      
//			   $addMalay = $questionDB->addData('q004_question',$dataMalay);   
//			   
//			   
//			    foreach ($answerData as $ans){
//			    	echo "<input type='radio' name='answer' id='answer' value='$ans[idanswers]'>";
//			    	echo $ans['Malay'];
//			    	echo "<br>";
//			    	
//			    	$idAnwer = $ans['idanswers'];
//			    	
//			    	$answerDB = new App_Model_Question_DbTable_Answer();
//			    	
//			    	$checkCorrectAns = $questionDB->getCorrectanswerSachin($idQuestion,$idAnwer);
//			    	
//			    	if($checkCorrectAns){
//			    		$corAns = 1;
//			    	}else{
//			    		$corAns = 0;
//			    	}
//			    	
//			    	$dataMalAns = array(
//				      'question_main_id'=>$addMain,
//				      'question_id'=>$addMalay,
//			    	  'answer'=>$ans['Malay'],
//				      'correct_answer'=>$corAns
//			     	 );
//			    	
//			    	$addMalayAns = $answerDB->addData($dataMalAns);  
//			    	
//			    	
//			    }
//			    
//			    
//			    echo "</td>
			   
			 echo " </tr>
			  ";
			 $i++;
    	}
			echo "</table>";
    	
    	
    	exit;
    	
    }
    
    public function importStudentAction(){
    	//kena set kan date exam and exam venue
    	//check kat ibfimsis, da ade schedulenya ke tak. kalau takde, kena create kan schedule
    	//pass import, check attendacne = 1 , utk mark entry purpose
    	$applicationDb = new App_Model_Question_DbTable_Studentimport();
    	$appData = $applicationDb->getApplicationSachin();
    	
    	echo $total = count($appData);
    	echo "lala";
//    	
//    	echo "<pre>";
//    	print_r($appData);
//    	echo "</pre>";
    	
//    	exit;
    	echo "<br>total = ".$total."<hr>";
    	echo "
    	<table width='100%' border='1'>
    	<td>No</td>
			 	<td>idApplication</td>
			 	<td>Name</td>
			    <td>IC</td>
			    <td>qua</td>
			    <td>gender</td>
			    <td>dob</td>
			    <td>race</td>
			    <td>religion</td>
			    <td>address1</td>
			    <td>address2</td>
			    <td>postcode</td>
			    <td>state</td>
			    <td>mobile</td>
			    <td>contact</td>
			    <td>TO</td>
			    <td>username</td>
			    <td>password</td>
			     <td>+</td>
			     <td>-</td>
			    <td>Payment</td>
			    <td>Amount</td>
			    <td>paymentmode</td>
			    <td>Regid</td>
			    <td>approved</td>
			    <td>attendance</td>
			   
			    </tr>
			";    
     	$i=1;
    	foreach ($appData as $data){
    		
    		 echo " <tr valign='top'>
			 	<td>$i)</td>
			 	<td>$data[IDApplication]</td>
			 	<td>$data[FName]</td>
			    <td>$data[ICNO]</td>
			    <td>$data[Qualification]</td>
			    <td>$data[Gender]</td>
			    <td>$data[DateOfBirth]</td>
			    <td>$data[Race]</td>
			    <td>$data[Religion]</td>
			    <td>$data[PermAddressDetails]</td>
			    <td>$data[CorrAddress]</td>
			    <td>$data[PostalCode]</td>
			    <td>$data[State]</td>
			    <td>$data[MobileNo]</td>
			    <td>$data[ContactNo]</td>
			    <td>$data[Takafuloperator]</td>
			    <td>$data[username]</td>
			    <td>$data[password]</td>
			    ";
    		 
    		 if($data['RegistrationPin'] == "0000000"){
    		 	$client = 1;
    		 }else{
    		 	$client = 2;
    		 }
			    
    		 if($data['Gender'] ==1){
    		 	$gender = "M";
    		 }elseif($data['Gender'] == 0){
    		 	$gender = "F";
    		 }
			    $dataStudent = array(
			    'ARD_NAME'=>$data['FName'],
			    'ARD_QUALIFICATION'=>$data['Qualification'],
			    'ARD_SEX'=>$gender,
			    'ARD_DOB'=>$data['DateOfBirth'],
			    'ARD_IC'=>$data['ICNO'],
			    'ARD_COUNTRY'=>129,
			    'ARD_RELIGION'=>$data['Religion'],
			    'ARD_ADDRESS1'=>$data['PermAddressDetails'],
			    'ARD_ADDRESS2'=>$data['CorrAddress'],
			    'ARD_POSTCODE'=>$data['PostalCode'],
			    'ARD_STATE'=>$data['State'],
			    'ARD_EMAIL'=>$data['EmailAddress'],
			    'ARD_PHONE'=>$data['MobileNo'],
			    'ARD_HPHONE'=>$data['ContactNo'],
			    'ARD_TAKAFUL'=>$data['Takafuloperator'],
			    'ARD_CLIENTTYPE'=>$client,
			    'ARD_DATE_APP'=>$data['UpdDate'],
			    'username'=>$data['username'],
			    'password'=>md5($data['password']),
			    'clearpass'=>$data['password'],
			    
			    
			    );
			    
			    $checkApp = $applicationDb->checkApplication($data['ICNO']);
			    
			    
				    if(!$checkApp){
				   		$insertApplication = $applicationDb->addData('r015_student',$dataStudent);
				   		$idApplication = $insertApplication;
				    }else{
				    	$idApplication = $checkApp['ID'];
				    }
				    
				    	

				    $getVenue = $applicationDb->checkCenter($data['Examvenue']);
				    
				    $dateExam = "2012-0".$data['Exammonth']."-".$data['Examdate']; 
				    
				    if($getVenue){
				    	$idVenue = $getVenue['id'];
				    	
					     //check 0 nya utk date
					    $getSchedule = $applicationDb->checkSchedule($dateExam,$idVenue);
				   
				    
					    if($getSchedule){
//					    	 $idSchedule = $getSchedule['id'];
					    	 $idSchedule = 2785;
					    }else{
//					    	$idSchedule = 0;
 							$idSchedule = 2785;
					    }
				    
				    }else{
				    	$idVenue = 0;
				    	$idSchedule = 0;
				    }
				
				    
				   
				    
				  
				    	
				    	$dataRegis = array(
					    	'idApplication'=>$idApplication,
					    	'idSachin'=>$data['IDApplication'],
					    	'idProgram'=>1,
					    	'idCourse'=>$data['Program'],
					    	'idSchedule'=>$idSchedule,
					    	'idVenue'=>$idVenue,
					    	'paymentMode'=>$data['paymentmode'],
					    	'paymentStatus'=>$data['Payment'],
				    		'seatStatus'=>$data['Payment'],
					    	'dateApplied'=>$data['UpdDate'],
					    	'regId'=>$data['Regid'],
					    	'verified'=>$data['approved'],
					    	'attendance'=>$data['attendance'],
				    		'dateUpload'=>date('Y-m-d H:i:s')
				    	);
				    	
				    	$insertApplication = $applicationDb->addData('r016_registrationdetails',$dataRegis);
				    
				   
			    
			   echo "
			   <td>$dateExam + $data[Examvenue]</td>
			    <td>$idSchedule + $idVenue</td>
			    <td>$data[Payment]</td>
			    <td>$data[Amount]</td>
			    <td>$data[paymentmode]</td>
			    <td>$data[Regid]</td>
			    <td>$data[approved]</td>
			    <td>$data[attendance]</td>
			    
			    </tr>
			    ";
			   
    		 
    		 
			 $i++;
    	}
    	
    	echo "<table>";
    	
    	  exit;
    }
  

	public function importTosAction(){
		
		
		#!/opt/lampp/etc

  		printf("Today is %s", date('F j, Y'));


		exit;
     	$questionDB = new App_Model_Question_DbTable_Questionimport();
    	$tosData = $questionDB->gettossachin();
//    	exit;
     	
//    	echo "<pre>";
//    	print_r($tosData);
//    	echo "</pre>";
//    	
    	
    	foreach($tosData as $data){
    		
    		$date = date('Y-m-d H:i:s');
				
			$auth = Zend_Auth::getInstance();
			$idUpd = $auth->getIdentity()->id;
		
    		 $getChapter = $questionDB->getChapter($data['IdSection']);
			 $chapter = $getChapter['id'];
			 $pool = $getChapter['idPool'] ;
			 
	    	$dataRegis = array(
		    	'idTos'=>$data['IdTOS'],
	    		'pool_id'=>$pool,//kena betulkn ckit
		    	'idChapter'=>$chapter,
		    	'difficulty'=>$data['IdDiffcultLevel'],
		    	'NosOfQuestion'=>$data['NoofQuestions'],
		    	'status'=>1,
		    	'UpdDate'=>$data['UpdDate'],
		    	'UpdUser'=>$idUpd
	    	);
	    	
	    	$applicationDb = new App_Model_Question_DbTable_Studentimport();
	    	$insertApplication = $applicationDb->addData('q009_tos_details',$dataRegis);
    	}
    	
    	exit;
     	
     }
     
     public function generateQuestionSetAction(){
     	
     	
     	
//     	 $examDate = "2012-05-17";
//     	 
//     	 $scheduleDb = new App_Model_Schedule_DbTable_Schedule();
//     	 $scheduleData = $scheduleDb-> getScheduleId($examDate);

     	$questionDB = new App_Model_Question_DbTable_Question();
    		$questionData = $questionDB->getQuestionTos(1,1,1);
    		
			 	$listQuestion = $this->array_random_assoc($questionData, 3);
			 	exit;

     	$regisDb = new App_Model_Record_DbTable_Registrationdetails();
     	$registeredData = $regisDb->getApplication(42); //idSchedule
     	 
    exit;
     	 
     	 foreach($registeredData as $stud){
     	 	$idApplication = $stud['ID'];
     	 	$idApplicationRegis = $stud['id'];
     	 	$idSchedule = $stud['idSchedule'];
     	 	$idVenue = $stud['idVenue'];
     	 	$idCourse = $stud['idCourse'];
     	 	
     	 
     	
     	
//     	======= generate question set ======
     	
     	$questionDB = new App_Model_Question_DbTable_Questionimport();
    	$tosData = $questionDB->gettos($idCourse);
    	
    	$total = count($tosData);
    	
    	echo "<br>total = ".$total."<hr>";
			 echo "
    				<table width='100%' border='1'>
    				<tr>
    					<td>No</td>
    					<td>Question</td>
    					<td>Answer</td>
    					<td>Correct Answer</td>
    				</tr>";
    	$i=1;   
    	foreach ($tosData as $data){
    		
    		$idtos = $data['id'];
    		$pool = $data['pool_id'];
    		$difficulty = $data['difficulty'];
    		$chapter = $data['idChapter'];
    		$noQuestion = $data['NosOfQuestion'];
    		
    		$date = date('Y-m-d H:i:s');
				
					$auth = Zend_Auth::getInstance();
					$idUpd = $auth->getIdentity()->id;
    		
    		$dataCand = array(
		 		'idApplication'=>$idApplicationRegis,
		 		'idCourse'=>$idCourse,
    			'idSchedule'=>$idSchedule,
		 		'idTos'=>$idtos,
		 		'dateUpd'=>$date,
		 		'usrUpd'=>$idUpd
		 		);
		 		
		 	$setDb = new App_Model_Question_DbTable_QuestionSet();
		 	$checkCand = $setDb->checkSet($idApplicationRegis);
		 	$applicationDb = new App_Model_Question_DbTable_Studentimport();
		 	
		 	if($checkCand){
		 		$idSetCand = $checkCand['id'];
    			
		 	}else{
		 		$insertApplication = $applicationDb->addData('q010_setcandidate',$dataCand);
    			$idSetCand = $insertApplication;
		 	}
	    			
    		
    		$questionDB = new App_Model_Question_DbTable_Question();
    		$questionData = $questionDB->getQuestionTos($pool,$difficulty,$chapter);
    		
			 	$listQuestion = $this->array_random_assoc($questionData, $noQuestion);
			 	
			 	$m=1;
			 	foreach ($listQuestion as $ques){
			 		
			 		
					
			 		
//			 		$idMainQues = $ques['id'];
			 		$idQuestion = $ques['id'];
			 		
			 		echo " <tr valign='top'>
			 		<td>$i)  $idQuestion</td>
			 		<td>$ques[english]</td>";
			 		
			 		$answerDb = new App_Model_Question_DbTable_Answer();
			 		$answerData = $answerDb->getAnswerbyQuestion($idQuestion);
			 		echo "<td>";
			 		echo "<ul style='list-style-type:upper-alpha'>";
			 		foreach($answerData as $ans){
			 			
			 			echo "<li> ".$ans['answer']."</li>";
			 		}
			 		echo "</ul>";
			 		echo "</td>";
			 		
			 		$correctAnswer = $answerDb->getCorrectAnswer($idQuestion);
			 		echo "<td>".$correctAnswer['id']."</td>";
			 		echo "</tr>
			 		";
			 		
			 		
				 		
			 		$dataSet = array(
				 		'idSet'=>$idSetCand,
			 			'idPool'=>$pool,
				 		'idQuestion'=>$idQuestion,
				 		'idAnswer'=>$correctAnswer['id']
				 		);
				 		
				 	$insertApplication2 = $applicationDb->addData('q011_setdetails',$dataSet);
				 		
				 		
			 		
			 		$m++;
			 		$i++;
			 	}
			 	
    	}
     	echo "</table>";
     	 }
     	
     	exit;
     	
     }
     
    function array_random_assoc($arr, $num = 1) {
    	echo "<pre>";
    	print_r($arr);
    	echo "</pre>";
	    $keys = array_keys($arr);
	    shuffle($keys);
	   
	    $r = array();
	    for ($i = 0; $i < $num; $i++) {
	        $r[$keys[$i]] = $arr[$keys[$i]];
	    }
    return $r;
    }
    
    
     public function importMarkManualAction(){
     	
     	//import result based on table mark_manual_<dateexam>
     	//mesti tukar nama table kat function getMark
     	//set kan id schedule
     	$idschedule = 2786;
     	$markDB = new App_Model_Question_DbTable_Markimport();
    	$markData = $markDB->getMark(); //mesti
     	
    	echo $total = count($markData);
    	echo "lala";
//    	
//    	echo "<pre>";
//    	print_r($appData);
//    	echo "</pre>";
    	
//    	exit;
    	echo "<br>total = ".$total."<hr>";
    	echo "
    	<table width='100%' border='1'>
    	<td>No</td>
			 	<td>Name</td>
			    <td>IC</td>
			    <td>course</td>
			    <td>center</td>
			    <td>a</td>
			    <td>b</td>
			    <td>c</td>
			    <td>total</td>
			    <td>grade</td>
			    <td>examstatus</td>
			    </tr>";
    	
    	$i=1;
    	foreach ($markData as $data){
    		
    		$checkApp = $markDB->checkApplication($data['icno'],$data['course'],$data['examcenter'],$idschedule);
			 $id = $checkApp['id'];
			 $date = date('Y-m-d H:i:s');
			 
			 if($id){
			 //insert e004_student_course_mark
			 
			 if($data['course']==2){  //course a
			 	$compID = 12;
			 }elseif($data['course']==1){  //course b
			 	$compID=10;
			 }
			 
			 if($data['a'] != NULL){
				 $datamark = array (
				 	 'rd_id'=>$id,
					 'component_id' => $compID,
					 'component_item_id' => '29', //part a
					 'component_student_mark' =>$data['a'],
					 'createddt'=>$date,
					 'createdby'=>1
				 );
				 $insertApplication = $markDB->addData('e004_student_course_mark',$datamark);
			 }
			 if($data['b'] != NULL){
				 $datamark = array (
				 	 'rd_id'=>$id,
					 'component_id' => $compID,
					 'component_item_id' => '30', //part b
					 'component_student_mark' =>$data['b'],
					 'createddt'=>$date,
					 'createdby'=>1
				 );
				 $insertApplication = $markDB->addData('e004_student_course_mark',$datamark);
			 }
			 if($data['c'] != NULL){
				 $datamark = array (
				 	 'rd_id'=>$id,
					 'component_id' => $compID,
					 'component_item_id' => '31', //part c
					 'component_student_mark' =>$data['c'],
					 'createddt'=>$date,
					 'createdby'=>1
				 );
				 $insertApplication = $markDB->addData('e004_student_course_mark',$datamark);
			 }
			 
			//update
			 $updatedata = array(
				 'course_mark'=>$data['total'],
				 'course_grade'=>$data['examstatus'],
				 'grade_symbol'=>$data['grade'],
			 );
			 
    		$markDB->updateData($updatedata,$id);
			 }
			 
    		echo " <tr valign='top'>
			 	<td>$i)</td>
			 	<td>$data[name] + $id</td>
			 	<td>$data[icno]</td>
			    <td>$data[course]</td>
			    <td>$data[examcenter]</td>
			    <td>$data[a]</td>
			    <td>$data[b]</td>
			    <td>$data[c]</td>
			    <td>$data[total]</td>
			    <td>$data[grade]</td>
			    <td>$data[examstatus]</td>
			";
    		$i++;
    	}
    	exit;
			    
     }
}
