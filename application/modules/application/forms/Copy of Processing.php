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
		
		
		
		$this->addElement('radio','dean_rating', array(
			'label'=>'ranking_by_dean' ,
		     'onclick'=>"setFinalRate();"	       
				
		));	
		
		$this->dean_rating->setMultiOptions(array('1'=>'1','2'=>'2','3'=>'3'))->setRequired(true);
		$this->dean_rating->setValue(1);	

		
		
		$this->addElement('radio','rector_verification', array(
			'label'=>'wakil_rector_verification',
		    'onclick'=>"getvalue(this);" 
				
		));	
		
		$this->rector_verification->setMultiOptions(array('1'=>'Accept','2'=>'Reject'))->setRequired(true);
		
		
		$this->addElement(
				    'hidden',
				    'dummyx',
				    array(
				        'required' => false,
				        'ignore' => true,
				        'autoInsertNotEmptyValidator' => false,				       
				        'decorators' => array(
				            array(
				                'HtmlTag', array(
				                    'tag'  => 'div',
				                    'id'   => 'rector',
				                    'openOnly' => true,
				                    'style'=>'display:none',
				                )
				            )
				        )
				    )
				);
				$this->dummyx->clearValidators();
		
		$this->addElement('radio','rector_rating', array(
			'label'=>'rating_by_rector',
		    'onclick'=>"setFinalRate();"	   
		));	
		
		$this->rector_rating->setMultiOptions(array('1'=>'1','2'=>'2','3'=>'3'))->setRequired(true);
		
		$this->addElement(
				    'hidden',
				    'dummyy',
				    array(
				        'required' => false,
				        'ignore' => true,
				        'autoInsertNotEmptyValidator' => false,
				        'decorators' => array(
				            array(
				                'HtmlTag', array(
				                    'tag'  => 'div',
				                    'id'   => 'rector',
				                    'closeOnly' => true
				                )
				            )
				        )
				    )
				);
				$this->dummyy->clearValidators();
		
		
		
		$this->addElement('text','acceptance_rank', array(
			'label'=>'rank_of_acceptance',
		    'maxlength'=>'2',
		    'readonly'=>true,
			'required'=>true));
				
		
		$this->addElement('radio','application_status', array(
			'label'=>'application_status'
		  
		   
				
		));	
		
		$this->application_status->setMultiOptions(array('1'=>'Offer','2'=>'Reject','3'=>'Incomplete'))->setRequired(true);
				
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