<?php 

class Application_Form_Processing extends Zend_Form
{
	protected $transactionID;
	
	public function setTransactionID($transactionID){
		$this->transactionIDID = $transactionID; 
	}
	
	public function init()
	{
		
		$this->setName('application_processing');
		$this->setMethod('post');
		$this->setAttrib('id','processing_form');
		
		$this->addElement('hidden','transaction_id');
		
		$this->addElement('hidden', 'test', array(
		    'description' => '<h3>'.$this->getView()->translate('approval_form').'</h3>',
		    'ignore' => true,
		    'decorators' => array(
		        array('Description', array('escape'=>false, 'tag'=>'')),
		    ),
		));
		
		
		
		
		$this->addElement('radio','aar_rating_dean', array(
			'label'=>'ranking_by_dean' ,
		     'onclick'=>"setFinalRate();"	       
				
		));	
		
		$this->aar_rating_dean->setMultiOptions(array('1'=>'1','2'=>'2','3'=>'3'))->setRequired(true);
		$this->aar_rating_dean->setValue(1);	

		
		
		
		$this->addElement('radio','aar_dean_status', array(
			'label'=>'status_by_dean'
		));	
		
		$this->aar_dean_status->setMultiOptions(array('1'=>'Accept','2'=>'Reject'))->setRequired(true);
		
		
		
		$this->addElement('radio','aar_rating_rector', array(
			'label'=>'ranking_by_rector' 
		));	
		
		$this->aar_rating_rector->setMultiOptions(array('1'=>'1','2'=>'2','3'=>'3'))->setRequired(true);
		$this->aar_rating_rector->setValue(1);	

		
		$this->addElement('radio','aar_rector_status', array(
			'label'=>'status_by_rector'
		));	
		
		$this->aar_rector_status->setMultiOptions(array('1'=>'Accept','2'=>'Reject'))->setRequired(true);
		
		
	
		
		$this->addElement('radio','application_status', array(
			'label'=>'application_status'
		));	
		
		$this->application_status->setMultiOptions(array('1'=>'Offer','2'=>'Reject'))->setRequired(true);
				
		$this->addElement('textarea','remarks', array(
			'label'=>'remarks',	
		    'cols'=>'40',
		    'rows'=>'5'
			   
			));
		
		if($this->getView()->transaction_status=="PROCESS"){
			//button
			$this->addElement('submit', 'save', array(
	          'label'=>'submit',
	          'decorators'=>array('ViewHelper')
	        ));
		}
        
        
        
	}		
	
	
}
?>