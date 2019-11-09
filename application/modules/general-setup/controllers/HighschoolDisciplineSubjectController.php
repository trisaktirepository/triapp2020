<?php
/**
 * @author Muhamad Alif
 * @version 1.0
 */

class GeneralSetup_HighschoolDisciplineSubjectController extends Zend_Controller_Action {
	
	private $_DbObj;
	
	public function init(){
		$this->_DbObj = new GeneralSetup_Model_DbTable_SchoolDisciplineSubject();
	}
	
	public function indexAction() {
		//title
    	$this->view->title= $this->view->translate("High School Discipline-Subject Set-up");
    	
    	//paginator
    	$schoolDisciplineDb = new GeneralSetup_Model_DbTable_SchoolDiscipline();
		
		$data = $schoolDisciplineDb->getPaginateData();
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($data));
		$paginator->setItemCountPerPage(PAGINATION_SIZE);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
	}
	
	public function detailAction(){
    	$this->view->title= $this->view->translate("High School Discipline-Subject Set-up")." - ".$this->view->translate("Detail");
    	
		$code = $this->_getParam('code', null);
		$this->view->code = $code;
		
    	if($code){
    		//discipline data
    		$schoolDisciplineDb = new GeneralSetup_Model_DbTable_SchoolDiscipline();
    		$discipline = $schoolDisciplineDb->getData($code);
    		$this->view->discipline = $discipline;
    		
    		//subject data
    		$disciplineSubjectDb = new GeneralSetup_Model_DbTable_SchoolDisciplineSubject();
    		$disciplineSubject = $disciplineSubjectDb->getDisciplineData($discipline['smd_code']);
    		$this->view->disciplineSubject = $disciplineSubject;
			
		}else{
			$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'highschool-discipline-subject', 'action'=>'index'),'default',true));
		}
    }
	
	public function addAction()
    {
    	//title
    	$this->view->title= $this->view->translate("High School Discipline-Subject Set-up")." - ".$this->view->translate("Add");
    	
		$code = $this->_getParam('code', null);
		$this->view->code = $code;
    	
    	$form = new GeneralSetup_Form_SchoolDisciplineSubject();
    	
    	$form->cancel->onClick = "window.location ='".$this->view->url(array('module'=>'general-setup','controller'=>'highschool-discipline-subject', 'action'=>'detail', 'code'=>$code),'default',true)."'; return false;";
    	    	
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			
			if ($form->isValid($formData)) {
				
				$schoolDisciplineSubjectDb = new GeneralSetup_Model_DbTable_SchoolDisciplineSubject();
				
				foreach( $formData['sds_subject'] as $subject ){
					$data = array(
						'sds_discipline_code' => $code,
						'sds_subject' => $subject
					);
					
					$schoolDisciplineSubjectDb->addData($data);
					
				}
				
				//redirect
				$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'highschool-discipline-subject', 'action'=>'detail', 'code'=>$code),'default',true));	
			}else{
				$form->populate($formData);
			}
        	
        }
    	
        $this->view->form = $form;
        
        
    }
    
	public function deleteAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$schoolDisciplineSubjectDb = new GeneralSetup_Model_DbTable_SchoolDisciplineSubject();
    		$data = $schoolDisciplineSubjectDb->getData($id);
    		
    		$schoolDisciplineSubjectDb->deleteData($id);
    	}
    		
    	$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'highschool-discipline-subject', 'action'=>'detail', 'code'=>$data['sds_discipline_code']),'default',true));
    	
    }
}

