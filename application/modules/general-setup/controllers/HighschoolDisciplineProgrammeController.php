<?php
/**
 * @author Muhamad Alif
 * @version 1.0
 */

class GeneralSetup_HighschoolDisciplineProgrammeController extends Zend_Controller_Action {
	
	private $_DbObj;
	
	public function init(){
		$this->_DbObj = new GeneralSetup_Model_DbTable_SchoolDisciplineProgramme();
	}
	
	public function indexAction() {
		//title
    	$this->view->title= $this->view->translate("High School Discipline-Programme Set-up");
    	
    	//paginator
    	$schoolDisciplineDb = new GeneralSetup_Model_DbTable_SchoolDiscipline();
		
		$data = $schoolDisciplineDb->getPaginateData();
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($data));
		$paginator->setItemCountPerPage(PAGINATION_SIZE);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
	}
	
	public function detailAction(){
    	$this->view->title= $this->view->translate("High School Discipline-Programme Set-up")." - ".$this->view->translate("Detail");
    	
		$code = $this->_getParam('code', null);
		$this->view->code = $code;
		
		$academicYear = $this->_getParam('year', null);
		
    	if($code){
    		//discipline data
    		$schoolDisciplineDb = new GeneralSetup_Model_DbTable_SchoolDiscipline();
    		$discipline = $schoolDisciplineDb->getData($code);
    		$this->view->discipline = $discipline;
    		
    		//academicYear
    		$academicYearDb = new App_Model_Record_DbTable_AcademicYear();
    		$this->view->academicYearList = $academicYearDb->getData(); 
    		
    		//current academic year
    		if($academicYear==null){
    			$this->view->selected_year = $academicYearDb->getCurrentAcademicYearData();
    		}else{
    			$this->view->selected_year = $academicYearDb->getData($academicYear);
    		}
    		  		
    		//programme data
    		$disciplineProgramme = $this->_DbObj->getDisciplineData($discipline['smd_code'],$this->view->selected_year['ay_id']);
    		$this->view->disciplineProgramme = $disciplineProgramme;
    					
		}else{
			$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'highschool-discipline-programme', 'action'=>'index'),'default',true));
		}
    }
	
	public function addAction()
    {
    	//title
    	$this->view->title= $this->view->translate("High School Discipline-Programme Set-up")." - ".$this->view->translate("Add");
    	
		$code = $this->_getParam('code', null);
		$this->view->code = $code;
		
		$year = $this->_getParam('year', null);
		$this->view->year = $year;
    	
    	$form = new GeneralSetup_Form_SchoolDisciplineProgramme(array('year'=>$year));
    	
    	$form->cancel->onClick = "window.location ='".$this->view->url(array('module'=>'general-setup','controller'=>'highschool-discipline-programme', 'action'=>'detail', 'code'=>$code),'default',true)."'; return false;";
    	    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			
			if ($form->isValid($formData)) {
				
				$schoolDisciplineProgrammeDb = new GeneralSetup_Model_DbTable_SchoolDisciplineProgramme();
				
				foreach( $formData['apr_program_code'] as $program ){
					$data = array(
						'apr_academic_year' => $formData['apr_academic_year'],
						'apr_decipline_code' => $code,
						'apr_program_code' => $program
					);
					
					$schoolDisciplineProgrammeDb->addData($data);
					
				}
				
				//redirect
				$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'highschool-discipline-programme', 'action'=>'detail', 'code'=>$code),'default',true));	
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        $this->view->form = $form;
        
        
    }
    
	public function deleteAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$schoolDisciplineProgramDb = new GeneralSetup_Model_DbTable_SchoolDisciplineProgramme();
    		$data = $schoolDisciplineProgramDb->getData($id);
    		
    		$schoolDisciplineProgramDb->deleteData($id);
    	}
    		
    	$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'highschool-discipline-programme', 'action'=>'detail', 'code'=>$data['apr_decipline_code']),'default',true));
    	
    }
}

