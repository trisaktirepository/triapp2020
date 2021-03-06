<?php
class App_Form_PlacementTest extends Zend_Form {
	
	protected $aphplacementcode;
	protected $aphfeesprogram;
	protected $aphfeeslocation;
	protected $transactionid;
	protected $applid;
	protected $fix_schedule;
	protected $change;
	
	public function setAphplacementcode($aphplacementcode){
		$this->aphplacementcode = $aphplacementcode;
	}
	
	public function setAphfeesprogram($aphfeesprogram){
		$this->aphfeesprogram = $aphfeesprogram;
	}
	
	public function setAphfeeslocation($aphfeeslocation){
		$this->aphfeeslocation = $aphfeeslocation;
	}
	
	public function setTransactionid($transactionid){
		$this->transactionid = $transactionid;
	}
	
	public function setApplid($applid){
		$this->applid = $applid;
		
		
	}
	
	
	
	public function setFixSchedule($fix_schedule){
		$this->fix_schedule = $fix_schedule;
	
	
	}
	
	public function setChange($change){
		$this->change = $change;
	
	
	}
	
	public function init(){
		$this->setName('application_registration');
		$this->setMethod('post');
		$this->setAttrib('id','placement_test_form');;
		
		$applide=$this->createElement('hidden','appl_id');
		$applide->setValue($this->applid);
		$this->addElement($applide);
		$apptest = $this->createElement('hidden','app_ptest_code');
		$apptest->setValue($this->aphplacementcode);
		$this->addElement($apptest);
		
		$feesprogram = $this->createElement('hidden','aph_fees_program');
		$feesprogram->setValue($this->aphfeesprogram);
		$this->addElement($feesprogram);
		
		$feeslocation = $this->createElement('hidden','aph_fees_location');
		$feeslocation->setValue($this->aphfeeslocation);
		$this->addElement($feeslocation);
		
		$transactionid = $this->createElement('hidden','transaction_id');
		$transactionid->setValue($this->transactionid);
		$this->addElement($transactionid);
		
		
		
		$this->addElement('hidden', 'contact', array(
		    'description' => '<h3>'.$this->getView()->translate('placement_test_info').'</h3>',
		    'ignore' => true,
		    'decorators' => array(
		        array('Description', array('escape'=>false, 'tag'=>'')),
		    ),
		));
		
		/* $this->addElement('select','aph_placement_code', array(
				'label'=>'USM Name',
				'required'=>true,
				'onChange'=>"getDate(this);"
		));
		
		//to list available placement test from schedule
		$applicantPlacementDB = new App_Model_Application_DbTable_PlacementTest();
		//$placement_test_info = $applicantPlacementScheduleDB->getInfo();
		if ($this->change=="1") 
			$placement_test = $applicantPlacementDB->getPlacementTest($this->aphplacementcode);
		else $placement_test = $applicantPlacementDB->getPlacementTest();
		
		$this->aph_placement_code->addMultiOption(null,$this->getView()->translate('Please Select'));
		foreach ($placement_test as $list){
			$this->aph_placement_code->addMultiOption($list['aph_placement_code'],$list['aph_placement_name']);
		}
		
		 */
		$this->addElement('radio','aph_placement_code', array(
				'label'=>'Pilih Jalur USM yang diinginkan',
				'required'=>true,
				'escape'=>false, 
				'onClick'=>"getDate(this);"
		));
		//to list available placement test from schedule
		$applicantPlacementDB = new App_Model_Application_DbTable_PlacementTest();
		//$placement_test_info = $applicantPlacementScheduleDB->getInfo();
		if ($this->change=="1")
			$placement_test = $applicantPlacementDB->getPlacementTest($this->aphplacementcode);
		else $placement_test = $applicantPlacementDB->getPlacementTest();
		
		//$this->aph_placement_code->addMultiOption(null,$this->getView()->translate('Please Select'));
		foreach ($placement_test as $list){
			$this->aph_placement_code->addMultiOption($list['aph_placement_code'],$list['aph_placement_name'].': '.$list['aph_description']);
		}
		
    	
		   
		$this->addElement('select','aps_test_date', array(
			'label'=>'Date',
			'required'=>true,
		    'onChange'=>"getLocation(this);"
		));	
		
		//to list available placement test from schedule
    	$applicantPlacementScheduleDB = new App_Model_Application_DbTable_ApplicantPlacementSchedule();
    	//$placement_test_info = $applicantPlacementScheduleDB->getInfo();
    	$placement_test_info = $applicantPlacementScheduleDB->getAvailableDate($this->applid,$this->transactionid);
    	
		$this->aps_test_date->addMultiOption(null,$this->getView()->translate('Please Select'));
		foreach ($placement_test_info as $list){
			$this->aps_test_date->addMultiOption($list['aps_id'],$list['aps_test_date']);
		}
		
		
		//select placement location will retiurn aps_id
		$this->addElement('select','aps_id', array(
			'label'=>'Location','required'=>true,'onChange'=>"getFees(this);"
		));	
		
		$this->aps_id->setRegisterInArrayValidator(false);
		
		/*$setupDB = new App_Model_General_DbTable_Setup();
		$list_currency = $setupDB->getData('CURRENCY');
		
		$this->addElement('select','currency', array(
			'label'=>$this->getView()->translate('Currency'),'required'=>true
		));	
		$this->currency->addMultiOption(0,$this->getView()->translate('Please Select'));
		foreach ($list_currency as $list){
			$this->currency->addMultiOption($list['ssd_id'],$list['ssd_name']);
		}*/
		
		
		$this->addElement('text','apt_fee_amt', array(
			'label'=>'Total Fee','required'=>true,'readonly'=>true
		));	
		
		//button
		$this->addElement('submit', 'save', array(
          'label'=>$this->getView()->translate('Save & Next'),
          'decorators'=>array('ViewHelper')
        ));
        
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'Cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'default', 'controller'=>'online-application','action'=>'biodata'),'default',true) . "'; return false;"
        ));
        
      
	}
}
?>