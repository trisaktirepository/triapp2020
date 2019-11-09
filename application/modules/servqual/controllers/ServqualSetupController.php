<?php
/**
 * @author Muhamad Alif
 * @version 1.0
 */

class Servqual_ServqualSetupController extends Zend_Controller_Action {

	private $_sis_session;

	public function init(){
		$this->_sis_session = new Zend_Session_Namespace('sis');
	}

	public function indexAction() {

	    $status = $this->_getParam('status', null);
	    
	    if($status){
    	    if($status==1){
    	      $this->view->noticeSuccess = "Batch Invoice Created";
    	    }else
            if($status==0){
              $this->view->noticeError = "Unable to create batch invoice";
    	    }
	    }
		//title
		$this->view->title= $this->view->translate("SERVQUAL Setup");
		
		$servqualDb = new Servqual_Model_DbTable_Servqual();
		$servqual_list = $servqualDb->getData();
	
		$this->view->servqual_list = $servqual_list;
		 
	}
	
	public function newServqualAction(){
		//title
		$this->view->title= $this->view->translate("Servqual Setup - Create New");
		$dbQuest=new Servqual_Model_DbTable_ServqualQuestion();
		$dbCommon=new App_Model_Common();
		$dbServqual=new Servqual_Model_DbTable_Servqual();
		$dbServqualDetail=new Servqual_Model_DbTable_ServqualDetail();
		$dbServqualDimension=new Servqual_Model_DbTable_ServqualDimension();
		$ses_servqual_setup = new Zend_Session_Namespace('servqual_setup');
		$ses_servqual_setup->setExpirationSeconds(900);
		$edit = $this->_getParam('id', null);
		$new = $this->_getParam('new', null);
		if ($new!=null) {
			unset($ses_servqual_setup->grp_list);
			unset($ses_servqual_setup->grp_question);
			unset($ses_servqual_setup->servqual);
		}
		if ($edit!=null) {
			// initialization
			//$ses_servqual_setup->edit="1";
			$ses_servqual_setup->grp_list=array();
			$ses_servqual_setup->grp_question=array();
			$ses_servqual_setup->servqual=$dbServqual->getRows($edit);
			$grplist=$dbServqualDimension->getRows($edit);
			$detils=$dbServqualDetail->getRows($edit);
			foreach ($grplist as $grp) $ses_servqual_setup->grp_list[$grp['Order']]=array('IdDimension'=>$grp['IdDimension'],'IdServqualDimension'=>$grp['IdServqualDimension']);
			foreach ($detils as $detil) {
				//echo var_dump($detil);
				$question=$dbQuest->getData($detil['Question_id'],$detil['Question_seq'],$detil['Scale_id'],$detil['IdServqualDetail']);
				$ses_servqual_setup->grp_question[$detil['Category']][$detil['Question_id']]=$question;
			}
			//echo var_dump($ses_servqual_setup->grp_question);exit;
		}
		
		$step = $this->_getParam('step', 1);
		$this->view->step = $step;
		
		
		
		if($step==1){ //STEP 1
			
			//set name of survey
			//$idservqual=$this->getRequest()->getParam('id',null);
			
			if ($this->getRequest()->isPost()) {
					
				$formData = $this->getRequest()->getPost();
				//echo var_dump($formData);exit;
				if($formData['Title']!=null){
					
					$ses_servqual_setup->servqual = $formData;
				
					//redirect
					$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'servqual-setup', 'action'=>'new-servqual', 'step'=>2),'default',true));
				}
				
			}
			//type survey
			
			$typeList=$dbCommon->fnGetSurveyType();
			//echo var_dump($typeList);exit;
			$this->view->surveytype=$typeList;
			if(isset($ses_servqual_setup->servqual)){
				$this->view->servqual = $ses_servqual_setup->servqual;
			}
			
			
		}else
		if($step==2){ //STEP 2
			
			//Servqual Dimension
			if(!isset($ses_servqual_setup->servqual)){
				$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'servqual-setup', 'action'=>'new-servqual', 'step'=>1),'default',true));
			}
			
			if ($this->getRequest()->isPost()) {
					
				$formData = $this->getRequest()->getPost();
			
				if($formData['idgrp']!=null){
						
					//$ses_servqual_setup->servqualDimension = $formData;
					//redirect
					//$ses_servqual_setup->grp_list = $formData['idgrp'];
					
					foreach ($formData['idgrp'] as $key=>$grp) {
						$grplist[$formData['ordergrp'][$key]]=array('IdDimension'=>$grp,'IdServqualDimension'=>$formData['IdServqualDimension'][$key]);
					}
					if ($ses_servqual_setup->grp_list!=$grplist) {
						$oldgrp=$ses_servqual_setup->grp_list;
						foreach ($oldgrp as $olddimension) {
							$exclude='';
							foreach ($grplist as $newdimension) {
								if ($olddimension==$newdimension)
									$exclude='1';
							}
							if ($exclude=='') {
								$ses_servqual_setup->del[$olddimension['IdDimension']][]=array();
								//echo var_dump($olddimension);
								unset($ses_servqual_setup->grp_question[$olddimension['IdDimension']]);
							}
						}
						//echo var_dump($ses_servqual_setup->del);exit;
						$ses_servqual_setup->grp_list = $grplist;
						//unset($ses_servqual_setup->grp_question);
						//echo var_dump($ses_servqual_setup->grp_question);exit;
					}
					//$ses_servqual_setup->ordergrp_list = $formData['ordergrp'];
					//echo var_dump($ses_servqual_setup->grp_list);exit;
					$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'servqual-setup', 'action'=>'new-servqual', 'step'=>3),'default',true));
				}
			
			}
			
			//$db=new Servqual_Model_DbTable_ServqualDimension();
			//$this->view->servqualDimension = $db->getData($idservqualDimension);
			//$dbCommon = new App_Model_Common();
			$this->view->dimension_list = $dbCommon->fnGetProcessCategory();
			if (isset($ses_servqual_setup->grp_list)){
				$ses_servqual_setup->del=array();
				$this->view->grp_list= $ses_servqual_setup->grp_list;
				//echo var_dump($ses_servqual_setup->grp_list);exit;
			}
						
		}else
		if ($step==3){ //STEP 3
			
    		//Detail
    		if(!isset($ses_servqual_setup->servqual)){
    		  	$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'servqual-setup', 'action'=>'new-servqual', 'step'=>1),'default',true));
    		}else
    		  if(!isset($ses_servqual_setup->grp_list)){
    		  	$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'servqual-setup', 'action'=>'new-servqual',  'step'=>2),'default',true));
    		}
    		
    		if ($this->getRequest()->isPost()) {
    			$formData = $this->getRequest()->getPost();
    		  	$ses_servqual_setup->servqualDetail=$formData;
    		 	//echo var_dump($formData);
    			if( isset($formData['questionid']) && isset($formData["adds"]) ) {
    				//echo var_dump($formData);exit;
					foreach ($ses_servqual_setup->grp_list as $grpid):
						if (isset($formData["adds"][$grpid['IdDimension']])) $idgrp=$grpid['IdDimension'];
						//echo var_dump($idgrp);exit;
					endforeach;
					
					if (isset($idgrp)) {
						//if (isset($formData['IdServqualDetail'][$idgrp])) $idServqualDetail=$formData['IdServqualDetail'][$idgrp]; else $idServqualDetail=null;
						$question=$dbQuest->getData($formData['questionid'][$idgrp],$formData['order'][$idgrp],$formData['Scale_id'][$idgrp],null);
						//echo var_dump($question);exit;
					}
					if (isset($ses_servqual_setup->grp_question[$idgrp])) {
						//$count=
						//$ses_servqual_setup->grp_question[$idgrp][]=$question;
						$ses_servqual_setup->grp_question[$idgrp][$question['IdServqualQuestion']]=$question;
					} else {
						$ses_servqual_setup->grp_question[$idgrp][$question['IdServqualQuestion']]=$question;
					}
					//echo var_dump($ses_servqual_setup->grp_question);exit;
					$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'servqual-setup', 'action'=>'new-servqual',  'step'=>3),'default',true));
					
				}else {
					//echo var_dump($formData);echo var_dump($ses_servqual_setup->grp_question);exit;
						if (isset($formData['del'])) {
							$del=$formData['del'];
							foreach ($del as $key=>$idgrp) {
								foreach ($idgrp as $index=>$idQuestion) {
									//echo $idgrp.'-';echo $idQuestion;exit;
									$ses_servqual_setup->del[$key][$index]=$ses_servqual_setup->grp_question[$key][$index];
									unset($ses_servqual_setup->grp_question[$key][$index]);
								}
							}
							$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'servqual-setup', 'action'=>'new-servqual','step'=>3),'default',true));
								
						}
							
				}	
						
					//echo var_dump($ses_transcript_profile_list->grp_subject);
					//exit;
					
					
				//	$this->_redirect($this->view->url(array('module'=>'generalsetup','controller'=>'transcript-profile', 'action'=>'index','step'=>4),'default',true));
				//}
				//exit;
    		  $this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'servqual-setup', 'action'=>'new-servqual',  'step'=>4),'default',true));
    		  
    		}
    		//get question
    		//$dbQuest=new Servqual_Model_DbTable_ServqualQuestion();
    		$questions=$dbQuest->getData();
    		$this->view->questions=$questions;
    		$scale=$dbCommon->fnGetScaleType();
    		$this->view->scales=$scale;
    		//echo var_dump($scale);exit;
    		$this->view->dimension_list = $dbCommon->fnGetProcessCategory($ses_servqual_setup->grp_list);
    		//echo var_dump($ses_servqual_setup->grp_question);exit;
    		$this->view->grp_list=$ses_servqual_setup->grp_list;
    		if (isset($ses_servqual_setup->grp_question)) {
    			
    			$this->view->grp_question=$ses_servqual_setup->grp_question;
    			//echo var_dump($ses_servqual_setup->grp_question);exit;
    		}
    		  		
    		//echo var_dump($ses_servqual_setup->grp_question);exit;
			
		}else if ($step==4 ) {
			//net 4
			//$dbCommon = new App_Model_Common();
    		$this->view->dimension_list = $dbCommon->fnGetProcessCategory($ses_servqual_setup->grp_list);
			$this->view->grp_question=$ses_servqual_setup->grp_question;
			if ($this->getRequest()->isPost()) {
				$formData = $this->getRequest()->getPost();
				//save data
				$this->saveServqualSetup();
				$this->_redirect($this->view->url(array('module'=>'servqual','controller'=>'servqual-setup', 'action'=>'index'),'default',true));
				//echo var_dump($ses_servqual_setup->grp_question);exit;
			}
			
		}
		
	}
	
	
	
	private function saveServqualSetup(){
	  
	  //data from configuration screen
	$ses_servqual_setup = new Zend_Session_Namespace('servqual_setup');
	   
	  if(
	      !isset($ses_servqual_setup->servqual) ||
	      !isset($ses_servqual_setup->grp_list) ||
	      !isset($ses_servqual_setup->grp_question)
	       ){
	  
	    throw new Exception("No dat to be saved");
	    exit;
	  }
	  //save Servqual
	  $servqual=$ses_servqual_setup->servqual;
	  $db=new Servqual_Model_DbTable_Servqual();
	  if ($servqual['IdServqual']=='') {
	  	$servqual['update_date']=date('d-m-yyyy');
	    $id = $db->insertData($servqual);
	  }
	   else {
	   	
	   		$id=$servqual['IdServqual'];
	   		unset($servqual['IdServqual']);
	   		$db->updateData($servqual,$id );
	   }
	  //save Dimension
	  $db=new Servqual_Model_DbTable_ServqualDimension();
	  $dimensions = $ses_servqual_setup->grp_list;
	  //echo var_dump($dimensions);exit;
	  $dimension['IdServqual']=$id;
	  foreach ($dimensions as $key=>$grp) {
	  	if ($grp['IdServqualDimension']=='') {
	  		$dimension['IdDimension']=$grp['IdDimension'];
	  		$dimension['Order']=$key;
	   		$db->insertData($dimension);
	  	}
	  	else {
	  		$idservqualdimension=$grp['IdServqualDimension'];
	  		$db->updateData($dimension, $idservqualdimension);
	  	}
	  }
	  //Save Detail
	  $db=new Servqual_Model_DbTable_ServqualDetail();
	  $detail['IdServqual']=$id;
	  $details=$ses_servqual_setup->grp_question;
	 // echo var_dump($details);exit;
	  foreach ($details as $key=>$questions) {
	  	$detail['Category']=$key;
	  //	echo var_dump($questions);exit;
	  	if ($key!='') {
		  	foreach ($questions as $index=>$question) {
		  		
		  		$detail["Question_id"]=$question['IdServqualQuestion'];
		  		$detail["Question_seq"]=$question['order'];
		  		$detail["Scale_id"]=$question['Scale_id'];
		  		
		  		if ($question['IdServqualDetail']==null) {
		  			
		  			$db->insertData($detail);}
		  		else {
		  			
		  			$idServqualDetail=$question['IdServqualDetail'];
		  			$db->updateData($detail, $idServqualDetail);
		  		}
		  	}
		  	
	  	}
	  }
	  //delete if any 
	  
	  $dels=$ses_servqual_setup->del;
	// echo var_dump($dels);exit;
	  if (isset($dels)) {
	  	//foreach ($dels as $dimension) {
	  		foreach ($dels as $key=>$del) {
	  			foreach ($del as $id) {
	  					if ($id==array()) {
	  						//delete base on dimension
	  						$dbDimension=new Servqual_Model_DbTable_ServqualDimension();
	  						$dimensions=$db->getDataDetail($ses_servqual_setup->servqual['IdServqual'], $key);
	  						//delete all question in certain dimension
	  						foreach ($dimensions as $id) $db->deleteData($id['IdServqualDetail']);
	  						//delete dimension itself
	  						$dbDimension->deleteDimension($ses_servqual_setup->servqual['IdServqual'], $key);
	  					} else
	  					if (isset($id['IdServqualDetail'])) $db->deleteData($id['IdServqualDetail']);
	  			}
	  		}
	  	
	  }
	
	 
	 }
	 public function viewSerqualAction(){
	 	
	 	$id=$this->_getParam('id');
	 	$dbServqual=new Servqual_Model_DbTable_Servqual();
	 	$this->view->servqual=$dbServqual->getData($id);
	 	$dbDimension = new Servqual_Model_DbTable_ServqualDimension();
	 	$this->view->dimension_list=$dbDimension->getRows($id);
	 	$this->view->title='View Servqual';
	 	//echo var_dump($this->view->dimension_list);exit;
	 	
	 	
	 }
}
?>