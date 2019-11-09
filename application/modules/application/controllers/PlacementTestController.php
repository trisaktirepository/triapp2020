<?php

class Application_PlacementTestController extends Zend_Controller_Action {

	public function indexAction() {
		//title
    	$this->view->title="Placement Test";
    	
    	//paginator
		$placementTestDb = new App_Model_Application_DbTable_PlacementTest();
		$dataList = $placementTestDb->getPaginateData();
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($dataList));
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
	}
	
	public function addAction()
    {
    	//title
    	$this->view->title="Placement Test - Add";
    	
    	$form = new Application_Form_PlacementTestHead();
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$placementTestDb = new App_Model_Application_DbTable_PlacementTest();
				$placementTestDb->addData($formData);
				
				//redirect		
				$this->_redirect($this->view->url(array('module'=>'application','controller'=>'placement-test', 'action'=>'index'),'default',true));
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        //$this->_helper->layout->disableLayout();
        $this->view->form = $form;
        
    }
    
	public function editAction(){
    	//title
    	$this->view->title="Placement Test - Edit";
    	
    	$form = new Application_Form_PlacementTestHead();
    	    	
    	$this->view->form = $form;
    	
    	$id = $this->_getParam('id', 0);
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
				
				$placementTestDb = new App_Model_Application_DbTable_PlacementTest(); 
				$placementTestDb->updateData($formData,$id);
				
				$this->_redirect($this->view->url(array('module'=>'application','controller'=>'placement-test', 'action'=>'index'),'default',true));
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id>0){
    			$placementTestDb = new App_Model_Application_DbTable_PlacementTest();
    			$form->populate($placementTestDb->getData($id));
    		}
    		
    	}
    }
    
	public function deleteAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		
    		//delete placement test head
    		$placementTestDb = new App_Model_Application_DbTable_PlacementTest();
    		$placementTestDb->deleteData($id);
    	}
    		
    	$this->_redirect($this->view->url(array('module'=>'application','controller'=>'placement-test', 'action'=>'index'),'default',true));
    	
    }
    
	public function detailAction() {
		$id = $this->_getParam('id', 0);
		$this->view->id = $id;
		//title
    	$this->view->title="Placement Test - Detail";
    	
    	$programDb = new App_Model_Record_DbTable_Program();
		$placementTestDb = new App_Model_Application_DbTable_PlacementTest();
		$placementTestProgramDb = new App_Model_Application_DbTable_PlacementTestProgram();
		$placementTestComponentDb = new App_Model_Application_DbTable_PlacementTestComponent();
		$placementTestDetailDb = new App_Model_Application_DbTable_PlacementTestDetail();
		$programComponentDb = new App_Model_Application_DbTable_PlacementTestProgramComponent();
		$placementTestWeightageDb = new App_Model_Application_DbTable_PlacementTestWeightage();
		$placementTestScheduleDb = new App_Model_Application_DbTable_PlacementTestSchedule();
		
    	//placement test data
    	$placementTestData = $placementTestDb->getData($id);
    	$this->view->placement_test = $placementTestData;
    	
    	//check wizard mode
    	if($placementTestData['aph_wizard']==1){
    		$this->_redirect($this->view->url(array('module'=>'application','controller'=>'placement-test', 'action'=>'wizard','id'=>$id),'default',true));
    	}else{
	    	
    		//placement test program list
			$placementTestProgramList = $placementTestProgramDb->getPlacementtestProgramData($placementTestData['aph_placement_code']);
			//inject program component
			$i=0;
			foreach($placementTestProgramList as $program){
				$programcomponent = $programComponentDb->getProgramData($program['IdProgram']);
				
				//inject placement test detail
				if($programcomponent){
					$j=0;
			    	foreach ($programcomponent as $program_component){
			    		$programcomponent[$j]['detail'] = $placementTestDetailDb->getPlacementTestComponentData($program['app_placement_code'], $program_component['apps_comp_code']);
			    		$j++;
			    	}
				}
				
				//inject program component weightage
				if($programcomponent){
					$j=0;
					foreach($programcomponent as $program_component){
						$programcomponent[$j]['weightage'] = $placementTestWeightageDb->getWeightageData($program['app_id'], $programcomponent[$j]['detail']['apd_id'] );
						$j++;
					}
				}
				
				$placementTestProgramList[$i]['component'] = $programcomponent;
				
				
				$i++;
			}
			$this->view->placementtestProgramList = $placementTestProgramList;
			
			//component list
	    	$componentList = $placementTestComponentDb->getData();
	    	//inject placement Test detail
	    	$i=0;
	    	foreach ($componentList as $component){
	    		$componentList[$i]['detail'] = $placementTestDetailDb->getPlacementTestComponentData($placementTestData['aph_placement_code'], $component['ac_comp_code']);
	    		$i++;
	    	}
			$this->view->componentList = $componentList;
			
			//placement test schedule list
    		$this->view->placement_test_ScheduleList = $placementTestScheduleDb->getPlacementTestData($placementTestData['aph_placement_code']);
    	}
		
	}
	
	public function wizardAction(){
		$id = $this->_getParam('id', 0);
		$step = $this->_getParam('step', 1);
		$this->view->step = $step;
		
		//title
    	$this->view->title="Placement Test - Wizard Setup";
    	
    	//placement test data
    	$placementTestDb = new App_Model_Application_DbTable_PlacementTest();
    	$placementTestData = $placementTestDb->getData($id);
    	$this->view->placement_test = $placementTestData;
    	
    	if($step==1){
    		$this->_redirect($this->view->url(array('module'=>'application','controller'=>'placement-test', 'action'=>'wizard-step1','id'=>$id),'default',true));		
    	}else
    	if($step==2){
    		$this->_redirect($this->view->url(array('module'=>'application','controller'=>'placement-test', 'action'=>'wizard-step2','id'=>$id),'default',true));		
    	}else
    	if($step==3){
    		$this->_redirect($this->view->url(array('module'=>'application','controller'=>'placement-test', 'action'=>'wizard-step3','id'=>$id),'default',true));		
    	}else
    	if($step==4){
    		$this->_redirect($this->view->url(array('module'=>'application','controller'=>'placement-test', 'action'=>'wizard-step4','id'=>$id),'default',true));		
    	}else
    	if($step==5){
    		$this->_redirect($this->view->url(array('module'=>'application','controller'=>'placement-test', 'action'=>'wizard-step5','id'=>$id),'default',true));		
    	}
		
		
	}
	
	public function wizardStep1Action(){
		$this->view->title="Placement Test - Wizard Setup";
		
		$id = $this->_getParam('id', 0);
		$this->view->id = $id;
		
		$placementTestDb = new App_Model_Application_DbTable_PlacementTest();
		$placementTestComponentDb = new App_Model_Application_DbTable_PlacementTestComponent();
		$placementTestDetailDb = new App_Model_Application_DbTable_PlacementTestDetail();
		
		//placement test data
    	$placementTestData = $placementTestDb->getData($id);
    	$this->view->placement_test = $placementTestData;
    	
		if ($this->getRequest()->isPost()) {
			
			$postData = $this->getRequest()->getPost();
			
			for($i=0; $i<sizeof($postData['apd_comp_code']); $i++) {
				
				if( isset($postData['apd_id'][$i]) && $postData['apd_id'][$i]!=null ){
					$data = array(
						'apd_id' => $postData['apd_id'][$i],
						'apd_placement_code' => $postData['apd_placement_code'],
						'apd_comp_code' => $postData['apd_comp_code'][$i],
						'apd_total_question' => $postData['apd_total_question'][$i]!=null?$postData['apd_total_question'][$i]:0,
						'apd_questno_start' => $postData['apd_questno_start'][$i]!=null?$postData['apd_questno_start'][$i]:0,
						'apd_questno_end' => $postData['apd_questno_end'][$i]!=null?$postData['apd_questno_end'][$i]:0
					);	
					
					if( $data['apd_total_question'] ){
						$placementTestDetailDb->updateData($data, $data['apd_id']);
					}
					
				}else{
					$data = array(
						'apd_placement_code' => $postData['apd_placement_code'],
						'apd_comp_code' => $postData['apd_comp_code'][$i],
						'apd_total_question' => $postData['apd_total_question'][$i]!=null?$postData['apd_total_question'][$i]:0,
						'apd_questno_start' => $postData['apd_questno_start'][$i]!=null?$postData['apd_questno_start'][$i]:0,
						'apd_questno_end' => $postData['apd_questno_end'][$i]!=null?$postData['apd_questno_end'][$i]:0
					);
										
					if( $data['apd_total_question']!=null ){
						$placementTestDetailDb->addData($data);
					}
				}
								
			}
			
			$this->_redirect($this->view->url(array('module'=>'application','controller'=>'placement-test', 'action'=>'wizard-step2','id'=>$placementTestData['aph_id']),'default',true));
		}
		
		
    	
    	//component list
    	$componentList = $placementTestComponentDb->getData();
    	
    	//placement Test detail
    	$i=0;
    	foreach ($componentList as $component){
    		$componentList[$i]['detail'] = $placementTestDetailDb->getPlacementTestComponentData($placementTestData['aph_placement_code'], $component['ac_comp_code']);
    		$i++;
    	}
		$this->view->componentList = $componentList;
	}
	
	public function wizardStep2Action(){
		$this->view->title="Placement Test - Wizard Setup";
		
		$id = $this->_getParam('id', 0);
		$this->view->id = $id;
		
		$placementTestDb = new App_Model_Application_DbTable_PlacementTest();
		$placementTestProgramDb = new App_Model_Application_DbTable_PlacementTestProgram();
		$programDb = new App_Model_Record_DbTable_Program();
		
		//placement test data
    	$placementTestData = $placementTestDb->getData($id);
    	$this->view->placement_test = $placementTestData;
    	
		if ($this->getRequest()->isPost()) {
			
			$postData = $this->getRequest()->getPost();
			
			//save placement test program & minimum passing mark
			if( isset($postData['delete']) && $postData['delete']==1 ){
				$placementTestProgramDb->deleteData($postData['id']);
			}else{
				$placementTestProgramDb->addData($postData);
			}
		}
		
		//placement test program list
		$placementTestProgramList = $placementTestProgramDb->getPlacementtestProgramData($placementTestData['aph_placement_code']);
		$this->view->placementtestProgramList = $placementTestProgramList;
		
    	//Program List
		$programList = $programDb->getData();
		$this->view->programList = $programList;		
	}
	
	public function wizardStep3Action(){
		$this->view->title="Placement Test - Wizard Setup";
		
		$id = $this->_getParam('id', 0);
		$this->view->id = $id;
		
		$programDb = new App_Model_Record_DbTable_Program();
		$placementTestDb = new App_Model_Application_DbTable_PlacementTest();
		$placementTestProgramDb = new App_Model_Application_DbTable_PlacementTestProgram();
		$placementTestComponentDb = new App_Model_Application_DbTable_PlacementTestComponent();
		$placementTestDetailDb = new App_Model_Application_DbTable_PlacementTestDetail();
		$programComponentDb = new App_Model_Application_DbTable_PlacementTestProgramComponent();
		$placementTestWeightageDb = new App_Model_Application_DbTable_PlacementTestWeightage();
		
		//placement test data
    	$placementTestData = $placementTestDb->getData($id);
    	$this->view->placement_test = $placementTestData;
    	
    	if ($this->getRequest()->isPost()) {
			
			$postData = $this->getRequest()->getPost();
			
			for($i=0; $i<sizeof($postData['apw_weightage']); $i++ ){
				
				if( $postData['apw_weightage'][$i] != null ){
					
					$data = array(
						'apw_id' => $postData['apw_id'][$i],
						'apw_app_id' => $postData['apw_app_id'][$i],
						'apw_apd_id' => $postData['apw_apd_id'][$i],
						'apw_weightage' => $postData['apw_weightage'][$i]
					);
					
					if($data['apw_id']!=null){
						$placementTestWeightageDb->updateData($data,$data['apw_id']);
					}else{
						$placementTestWeightageDb->addData($data);
					}
				}
			}
			
			$this->_redirect($this->view->url(array('module'=>'application','controller'=>'placement-test', 'action'=>'wizard-step4','id'=>$placementTestData['aph_id']),'default',true));
    	}
    	
    	//placement test program list
		$placementTestProgramList = $placementTestProgramDb->getPlacementtestProgramData($placementTestData['aph_placement_code']);
		//inject program component
		if($placementTestProgramList){
		$i=0;
		foreach($placementTestProgramList as $program){
			$programcomponent = $programComponentDb->getProgramData($program['IdProgram']);
			
			//inject placement test detail
			if($programcomponent){
				$j=0;
		    	foreach ($programcomponent as $program_component){
		    		$programcomponent[$j]['detail'] = $placementTestDetailDb->getPlacementTestComponentData($program['app_placement_code'], $program_component['apps_comp_code']);
		    		$j++;
		    	}
			}
			
			//inject program component weightage
			if($programcomponent){
				$j=0;
				foreach($programcomponent as $program_component){
					$programcomponent[$j]['weightage'] = $placementTestWeightageDb->getWeightageData($program['app_id'], $programcomponent[$j]['detail']['apd_id'] );
					$j++;
				}
			}
			
			$placementTestProgramList[$i]['component'] = $programcomponent;
			
			
			$i++;
		}
		$this->view->placementtestProgramList = $placementTestProgramList;
		}
		
		//component list
    	$componentList = $placementTestComponentDb->getData();
    	//inject placement Test detail
    	$i=0;
    	foreach ($componentList as $component){
    		$componentList[$i]['detail'] = $placementTestDetailDb->getPlacementTestComponentData($placementTestData['aph_placement_code'], $component['ac_comp_code']);
    		$i++;
    	}
		$this->view->componentList = $componentList;
	}
	
	public function wizardStep4Action(){
		$this->view->title="Placement Test - Wizard Setup";
		
		$id = $this->_getParam('id', 0);
		$this->view->id = $id;
		
		$placementTestDb = new App_Model_Application_DbTable_PlacementTest();
		$placementTestLocationDb = new App_Model_Application_DbTable_PlacementTestLocation();
		$placementTestScheduleDb = new App_Model_Application_DbTable_PlacementTestSchedule();
		
		//placement test data
    	$placementTestData = $placementTestDb->getData($id);
    	$this->view->placement_test = $placementTestData;
    	
    	//component type
    	$testTypeDb = new App_Model_Application_DbTable_PlacementTestType();
    	$testTypeList = $testTypeDb->getData();
    	$this->view->testTypeList = $testTypeList;
    	
		if ($this->getRequest()->isPost()) {
			
			$postData = $this->getRequest()->getPost();

			if( isset($postData['delete']) && $postData['delete']==1 ){
				$placementTestScheduleDb->deleteData($postData['id']);
			}else{
				$placementTestScheduleDb->addData($postData);
			}
		}
		
    	//location list
    	$placementTestLocationList = $placementTestLocationDb->getData();
    	$this->view->locationList = $placementTestLocationList;
    	
    	//placement test schedule list
    	$this->view->placement_test_ScheduleList = $placementTestScheduleDb->getPlacementTestData($placementTestData['aph_placement_code']);    	
	}
	
	public function wizardStep5Action(){
		$id = $this->_getParam('id', 0);
		$this->view->id = $id;
		
		$placementTestDb = new App_Model_Application_DbTable_PlacementTest();
		$placementTestData = $placementTestDb->getData($id);
		
		$placementTestDb->update(array('aph_wizard'=>0), 'aph_id = '. (int)$id);
		
		//redirect
		$this->_redirect($this->view->url(array('module'=>'application','controller'=>'placement-test', 'action'=>'detail','id'=>$placementTestData['aph_id']),'default',true));
		
	}
	
	public function ajaxGetPtTypeScheduleAction(){
    	$schedule_id = $this->_getParam('schedule_id', 0);
    	
        $this->_helper->layout->disableLayout();
        
     	$ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('view', 'html');
        $ajaxContext->initContext();
            
	  	$db = Zend_Db_Table::getDefaultAdapter();
	  	$select = $db->select()
	                 ->from(array('att'=>'appl_test_type'))
	                 ->joinLeft(array('apst'=>'appl_placement_schedule_time'),'apst.apst_test_type = att.act_id and apst.apst_aps_id = '.$schedule_id, array('start_time'=>'apst_time_start', 'apst_id'=>'apst_id'));
	    
        $stmt = $db->query($select);
        $row = $stmt->fetchAll();
	  	
		$ajaxContext->addActionContext('view', 'html')
                    ->addActionContext('form', 'html')
                    ->addActionContext('process', 'json')
                    ->initContext();

		$json = Zend_Json::encode($row);
		
		echo $json;
		exit();
    }
    
    public function ajaxSavePtTypeScheduleAction(){
    	if ($this->_request->isPost()) {   
			$formData = $this->getRequest()->getPost();
			
			$i=0;
			foreach ($formData['apst_time_start'] as $timeStart){
				$scheduleTimeDb = new App_Model_Application_DbTable_ApplicantPlacementScheduleTime();
				
				if($timeStart!=""){
					
					$data = array(
						'apst_aps_id' => $formData['apst_aps_id'],
						'apst_test_type' => $formData['apst_test_type'][$i],
						'apst_time_start' => date('h:i:s', strtotime($timeStart))			
					);
					
							
					if($formData['apst_id'][$i]!=null && $formData['apst_id'][$i]!='null'){
						
						$id = $formData['apst_id'][$i];
						
						$scheduleTimeDb->updateData($data, $id);
						
					}else{
						$scheduleTimeDb->addData($data);
					}
				}
								
				$i++;
			}
			
			
			exit;
         }
    }
	
	public function addComponentAction(){		
		
	    if ($this->_request->isPost()) {   
			$formData = $this->getRequest()->getPost();
			
			echo "<pre>";
			print_r($formData);
			echo "</pre>";
			exit;
         }
         
         //redirect
		  $this->_redirect('/application/placement-test/viewdetail/id/'.$place_id);	
				
    }
    
    public function editdetailAction() {
		//title
    	$this->view->title="Edit Component";
    	$this->_helper->layout->disableLayout();
    	
    	$id_comp = $this->_getParam('id', 0);
    	$this->view->id = $id_comp;
    	
    	//paginator
		$markDB = new App_Model_Application_DbTable_PlacementTestDistribution();
		$mark_data = $markDB->getData($id_comp);
		
		$this->view->mark_component = $mark_data;
		
	}
	
	 public function editdetailsaveAction() {
    	
    	$id_comp = $this->_getParam('id', 0);
    	$place_id = $this->_getParam('place_id', 0);
    	
		$markDB = new App_Model_Application_DbTable_PlacementTestDistribution();
		
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
				
			$markDB->updateData($formData,$id_comp);
        }
        
        $this->_redirect($this->view->url(array('module'=>'application','controller'=>'placement-test', 'action'=>'viewdetail','id'=>$place_id),'default',true));
	 }
	 
	 public function deletedetailAction(){
    	$id_comp = $this->_getParam('id', 0);
    	$place_id = $this->_getParam('place_id', 0);
    	
    	if($id_comp>0){
    		$markDB = new App_Model_Application_DbTable_PlacementTestDistribution();
    		$markDB->deleteData($id_comp);
    	}
    	
    	$this->_redirect($this->view->url(array('module'=>'application','controller'=>'placement-test', 'action'=>'viewdetail','id'=>$place_id),'default',true));
    	
    }
}

