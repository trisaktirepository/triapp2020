<?php 
class Servqual_QuestionEntryController extends Zend_Controller_Action {

	private $_sis_session;

	public function init(){
		$this->_sis_session = new Zend_Session_Namespace('sis');
	}

	public function indexAction() {

		$id=$this->getRequest()->getParam('id');
		$this->view->title= $this->view->translate("Question Entry");
		$servqualDb = new Servqual_Model_DbTable_ServqualQuestion();
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			//echo var_dump($formData);exit;
			if (!isset($formData['active'])) $formData['active']='0';
			unset($formData['submit']);
			if ($formData['IdServqualQuestion']!='') {
				$idservqual = $formData['IdServqualQuestion'];
				unset($formData['IdServqualQuestion']);
				$servqualDb->updateData($formData, $idservqual);
			}
				
			else
				$servqualDb->insertData($formData);
		}
		
		
		$question_list = $servqualDb->getData();
		$this->view->questions = $question_list;
		if ($id > 0) 
			$question=$servqualDb->getData($id);
		if (count($question)>0) {
			$this->view->id = $question[0]['IdServqualQuestion'];
			$this->view->question = $question[0]['Question'];
			$this->view->category = $question[0]['Category'];
			$this->view->active = $question[0]['active'];
		}
		else {
			$this->view->id = '';
			$this->view->question = '';
			$this->view->category ='';
			$this->view->active='';
		}
		$commonDb=new App_Model_Common();
		$this->view->categories=$commonDb->fnGetQuestionCategory();
			
	}
}


?>