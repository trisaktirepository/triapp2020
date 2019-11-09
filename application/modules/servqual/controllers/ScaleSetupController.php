<?php 
class Servqual_ScaleSetupController extends Zend_Controller_Action {

	private $_sis_session;

	public function init(){
		$this->_sis_session = new Zend_Session_Namespace('sis');
	}

	public function indexAction() {

		$id=$this->getRequest()->getParam('id',0);
		$this->view->titles= $this->view->translate("Scale Entry");
		$servqualDb = new Servqual_Model_DbTable_ServqualScale();
		$formData['IdScale']='';
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			//echo var_dump($formData);exit;
			if (!isset($formData['Active'])) $formData['Active']='0';
				unset($formData['submit']);
			if ($formData['IdScaleDetail']!='') {
				$idservqual = $formData['IdScaleDetail'];
				unset($formData['IdScaleDetail']);
				$servqualDb->updateData($formData, $idservqual);
			}
				
			else {
				//echo var_dump($formData);exit;
				if ($formData['Title']!='') 
					$servqualDb->insertData($formData);
			}
		}
		
		
		$scalelist = $servqualDb->getDataPerScale($formData['IdScale']);
		$this->view->scalesdetail = $scalelist;
		$question=$servqualDb->getData($id);
		if (count($question)>0) {
			$this->view->id = $question[0]['IdScaleDetail'];
			$this->view->title = $question[0]['title'];
			$this->view->idscale = $question[0]['IdScale'];
			$this->view->active = $question[0]['Active'];
			$this->view->score = $question[0]['score'];
		}
		else {
			$this->view->id = '';
			$this->view->title = '';
			$this->view->idscale ='';
			$this->view->active='';
			$this->view->score='';
		}
		$commonDb=new App_Model_Common();
		$this->view->scales=$commonDb->fnGetScaleType();
			
	}
}


?>