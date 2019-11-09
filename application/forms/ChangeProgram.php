<?
class App_Form_ChangeProgram extends Zend_Form {
	
	public function init(){
		
		$this->setName('change_program');
		$this->setMethod('post');
		$this->setAttrib('id','change_program_form');
		$this->setAttrib('onsubmit', 'return confirmSubmit()');
		
		
		$auth = Zend_Auth::getInstance();
		
		$this->addElement('hidden','appl_id',array('value'=>$auth->getIdentity()->appl_id));
		
		$this->addElement('select','acp_trans_id_from', array(
			'label'=>'From Program',
		    'required'=>true,
		    'onchange'=>'changeProgram(this);'
		));		
		
		$this->acp_trans_id_from->addMultiOption(null,'-- Please Select --');
		
		$txnDb = new App_Model_Application_DbTable_ApplicantTransaction();		
		foreach ($txnDb->getPaidAndOfferChangeProgram($auth->getIdentity()->appl_id) as $list){
			$this->acp_trans_id_from->addMultiOption($list['at_trans_id'],'('.$list['ap_prog_code'].') '.$list['ArabicName'].' - '.$list['at_pes_id'].' - '.$list['rank']);
		}
		
		
		
		$this->addElement('select','acp_trans_id_to', array(
			'label'=>'To Program',
		    'required'=>true
		));
		$this->acp_trans_id_to->addMultiOption(null,'-- Please Select --');
		$this->acp_trans_id_to->setRegisterInArrayValidator(false);
		
		//button
		$this->addElement('submit', 'save', array(
          'label'=>'submit',
		  'decorators'=>array('ViewHelper')
        ));
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'default', 'controller'=>'change-program','action'=>'index'),'default',true) . "'; return false;"
        ));
		
	}
	
}