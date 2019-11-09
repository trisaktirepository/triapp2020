<?php
 error_reporting(E_ALL);
 ini_set("display_errors", 1);
 
class AnrSetup_SemesterController extends Zend_Controller_Action {

    public function indexAction()
    {
    	$this->view->title = "Semester Setup";
		
    	$semesterDB = new App_Model_Record_DbTable_Semester();
    	$semester = $semesterDB->getPaginateData();
    			
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($semester));
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
    }
    
    public function viewAction(){
    	$this->view->title = "Semester Details";
    	
    	$id = $this->_getParam('id', 0);
    	$this->view->id = $id;
    	
    	$semesterDB = new App_Model_Record_DbTable_Semester();
    	$semester_programDB = new App_Model_Record_DbTable_SemesterProgram();
    	
    	if($id != 0){
    		try {
    			/*semester data*/
    			$semesterData = $semesterDB->getData($id);
    			$this->view->semester = $semesterData;

    			
    			/*program data*/
    			$programData = $semester_programDB->getDataBySemester($id);
    			$this->view->program = $programData;
    			
    		} catch (Exception $e) {
    			$this->view->noticeError = "No semester Data found.";
    		}
    		
    		
    	}else{
    		$this->view->noticeError = "No semester Data found.";
    	}
    }

    public function addAction()
    {
       	/*
       	 * notes:
       	 * We use zend_form only for validation
       	 * 
       	 * */
    	
		//title
    	$this->view->title="Add Semester";
    	
    	$form = new AnrSetup_Form_Semester();
    	
    	//Program Data
    	$programDB = new App_Model_Record_DbTable_Program();
    	$program = $programDB->getData();
    	$this->view->programlist = $program;
    	
    	//list of semester name
    	$semesterNameDB = new App_Model_Record_DbTable_SemesterName();
    	$semesterName = $semesterNameDB->getData();
    	$this->view->semesterName = $semesterName;
    	
    	if ($this->getRequest()->isPost()) {
    		$formData = $this->getRequest()->getPost();
    		
    		//$selProgram = explode(",", $formData['selProgram']);
    		
    		
			if ($form->isValid($formData)) {				
				
				//add semester
				$semesterDB = new App_Model_Record_DbTable_Semester();
				$semID = $semesterDB->addSemester(
					array(
						'name'=>$formData['name'],
						'year'=>$formData['year'],
						'code'=>$formData['code'],
						'start_date'=>$formData['start_date'],
						'end_date'=>$formData['end_date']
					));		
					
				//add semester_program	
				$programSelected = $this->getRequest()->getPost('selProgram');   
				$n = count($programSelected);
				$i=0;
				$bil = 1;
				while ($i < $n) {	
					echo $prog = $programSelected[$i];
					$semester_programDB = new App_Model_Record_DbTable_SemesterProgram();
					$semester_programDB->addSemesterProgram($semID,$prog);
					$i++;
				}
				
				
//				$semester_programDB = new App_Model_Record_DbTable_SemesterProgram();
//				foreach ($selProgram as $prog){
//					$semester_programDB->addSemesterProgram($semID,$prog);
//				}
				//redirect
				$this->_redirect($this->view->url(array('module'=>'anr-setup','controller'=>'semester', 'action'=>'index'),'default',true));
			}else{
				//$form->populate($formData);
				$this->view->noticeError = "Please Check your form. <br />Make sure this element are fill up: <br /><ul><li>Semester Name</li><li>Start Date</li><li>End Date</li></ul>";
			}
    	}
    }

    public function editAction()
    {		
		//title
    	$this->view->title="Edit Semester";
    	
    	$form = new AnrSetup_Form_Semester();

    	//semester Data
    	$id = $this->_getParam('id', 0);
    	$this->view->id = $id;
    	
    	$semesterDB = new App_Model_Record_DbTable_Semester();
    	$semester = $semesterDB->getData($id);
    	$this->view->semester = $semester;
    	
    	//list of semester name
    	$semesterNameDB = new App_Model_Record_DbTable_SemesterName();
    	$semesterName = $semesterNameDB->getData();
    	$this->view->semesterName = $semesterName;
    	
    	
    	$semesterProgramDB = new App_Model_Record_DbTable_SemesterProgram();
    	
    	//selected Data
    	$selProgram = $semesterProgramDB->getDataBySemester($id);
    	$this->view->program = $selProgram;
		
    	//unselected Data
    	$program = $semesterProgramDB->getProgramSemester($id);
    	$this->view->programlist = $program;
    	
    	
    	
    	if ($this->getRequest()->isPost()) {
    		$formData = $this->getRequest()->getPost();
    		
    		echo $selProgram = explode(",", $formData['selProgram']);
    		
    		
			if ($form->isValid($formData)) {				
				
				//update semester
				$semesterDB->updateData($id,$formData);			
				
				//update semester_program
				$semester_programDB = new App_Model_Record_DbTable_SemesterProgram();
				//delete
				$semester_programDB->deleteSemesterProgram($id);
				//add
				foreach ($selProgram as $prog){
					echo $prog;
					
					$semester_programDB->addSemesterProgram($id,$prog);
				}
				//redirect
				$this->_redirect($this->view->url(array('module'=>'anr-setup','controller'=>'semester', 'action'=>'view', 'id'=>$id),'default',true));
			}else{
				//$form->populate($formData);
				$this->view->noticeError = "Please Check your form. <br />Make sure this element are fill up: <br /><ul><li>Semester Name</li><li>Start Date</li><li>End Date</li></ul>";
			}
    	}
    }

    public function deleteAction($id = null)
    {
        $id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$semesterDB = new App_Model_Record_DbTable_Semester();
    		$semesterDB->deleteSemester($id);
    	}
    		
    	$this->_redirect($this->view->url(array('module'=>'anr-setup','controller'=>'semester', 'action'=>'index'),'default',true));
    }

    public function semesternameAction()
    {
    	$this->view->title = "Semester Name Setup";
		
    	$semesterDB = new App_Model_Record_DbTable_SemesterName();
    	$semester = $semesterDB->getPaginateData();
    			
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($semester));
		$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
    }   
    
     public function addnameAction()
    {
       	/*
       	 * notes:
       	 * We use zend_form only for validation
       	 * 
       	 * */
       	
       	//title
    	$this->view->title="Add Semester Name";
    	
    	$form = new AnrSetup_Form_SemesterName();
    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			if ($form->isValid($formData)) {
				
				//process form 
				$program = new App_Model_Record_DbTable_SemesterName();
				$program->addSemester($formData);
				
				//redirect
				$this->_redirect($this->view->url(array('module'=>'anr-setup','controller'=>'semester', 'action'=>'semestername'),'default',true));
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        $this->view->form = $form;
        
    	
		
    }
    
    public function editnameAction()
    {		
		//title
    	$this->view->title="Edit Semester Name";
    	
    	$form = new AnrSetup_Form_SemesterName();
    	//$form->submit->setLabel('Update');
    	
    	$this->view->form = $form;
    	
    	$id = $this->_getParam('id', 0);
    	$this->view->id = $id;
    	
    	
    	if ($this->getRequest()->isPost()) {
    		
    		$formData = $this->getRequest()->getPost();
    		
	    	if ($form->isValid($formData)) {
				
				$award = new App_Model_Record_DbTable_SemesterName(); 
				$award->updateData($formData,$id);
				
				$this->_redirect($this->view->url(array('module'=>'anr-setup','controller'=>'semester', 'action'=>'semestername'),'default',true));
			}else{
				$form->populate($formData);	
			}
    	}else{
    		if($id>0){
    			$award = new App_Model_Record_DbTable_SemesterName();
    			$form->populate($award->getData($id));
    		}
    		
    	}
    	
    }
    
}
