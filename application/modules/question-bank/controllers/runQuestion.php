<?php 

echo "lala";
exit;

$regisDb = new App_Model_Record_DbTable_Registrationdetails();
     	$registeredData = $regisDb->getApplication(4);
     	 
    
     	 
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
			 		
			 		
					
			 		
			 		$idMainQues = $ques['id'];
			 		$idQuestion = $ques['idQuestion'];
			 		
			 		echo " <tr valign='top'>
			 		<td>$i) $idMainQues + $idQuestion</td>
			 		<td>$ques[question]</td>";
			 		
			 		$answerDb = new App_Model_Question_DbTable_Answer();
			 		$answerData = $answerDb->getAnswerbyQuestion($idMainQues,$idQuestion);
			 		echo "<td>";
			 		echo "<ul style='list-style-type:upper-alpha'>";
			 		foreach($answerData as $ans){
			 			
			 			echo "<li> ".$ans['answer']."</li>";
			 		}
			 		echo "</ul>";
			 		echo "</td>";
			 		
			 		$correctAnswer = $answerDb->getCorrectAnswer($idMainQues,$idQuestion);
			 		echo "<td>".$correctAnswer['id']."</td>";
			 		echo "</tr>
			 		";
			 		
			 		
				 		
			 		$dataSet = array(
				 		'idSet'=>$idSetCand,
			 			'idPool'=>$pool,
				 		'idQuestion'=>$idMainQues,
				 		'idAnswer'=>$correctAnswer['id']
				 		);
				 		
				 	$insertApplication2 = $applicationDb->addData('q011_setdetails',$dataSet);
				 		
				 		
			 		
			 		$m++;
			 		$i++;
			 	}
			 	
    	}
     	echo "</table>";
     	 }
?>