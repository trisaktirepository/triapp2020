<?php
class App_Form_Uploadfile extends Zend_Form {
	
	protected $applnationality;
	protected $appladmissiontype;
	protected $applid;
	
	public function setAphplacementcode($applnationality){
		$this->aphplacementcode = $applnationality;
	}
	
	public function setAppladmissiontype($appladmissiontype){
		$this->appladmissiontype = $appladmissiontype;
	}
	
	public function setApplid($applid){
		$this->applid = $applid;
	}
	
	public function init(){
		$this->setMethod('post');
		$this->setAttrib('id','uploadfile_form');
		$this->setName('document');
		$this->setAction("");
		$this->setAttrib('enctype', 'multipart/form-data');
		
		
		$this->addElement('hidden','appl_id');
		
		$appnationality = $this->createElement('hidden','appl_nationality');
		$appnationality->setValue($this->applnationality);
		$this->addElement($appnationality);
		
		
		$admision_type = $this->createElement('hidden','admision_type');
		$admision_type->setValue($this->appladmissiontype);
		$this->addElement($admision_type);
		 				
		
		$this->addElement('hidden', 'test', array(
		    'description' => '<h3>'.$this->getView()->translate('allowed_file').": ".$this->getView()->translate('jenis_file').'</h3>',
		    'ignore' => true,
		    'decorators' => array(
		        array('Description', array('escape'=>false, 'tag'=>'')),
		    ),
		));
		 
		 // creating object for Zend_Form_Element_File
		 $photograph = new Zend_Form_Element_File('photograph');
		 $photograph->setLabel($this->getView()->translate('photograph'))
				  ->setRequired(true);
				  
		 $nric = new Zend_Form_Element_File('nric');
		 $nric->setLabel($this->getView()->translate('nric'))
				  ->setRequired(true);
				  
		 $passport = new Zend_Form_Element_File('passport');
 		 $passport->setLabel($this->getView()->translate('passport')." (".$this->getView()->translate('passport_msg').")")
		  		->setRequired(true);
				  
		 $academic_award = new Zend_Form_Element_File('academic_award');
 		 $academic_award->setLabel($this->getView()->translate('academic_award')." (".$this->getView()->translate('academic_award_msg').")")
		  		->setRequired(true);
				  
		 $academic_transcript = new Zend_Form_Element_File('academic_transcript');
 		 $academic_transcript->setLabel($this->getView()->translate('academic_transcript')." (".$this->getView()->translate('academic_transcript_msg').")")
		  		->setRequired(true);

		 // creating object for submit button
		 $submit = new Zend_Form_Element_Submit('submit');
		 $submit->setLabel($this->getView()->translate('upload'))
				 ->setAttrib('id', 'submitbutton');

		// adding elements to form Object
		$this->addElements(array($photograph, $nric, $passport, $academic_award, $academic_transcript, $submit));
		
		
		$this->addElement(
				    'hidden',
				    'divparentclose',
				    array(
				        'required' => false,
				        'ignore' => true,
				        'autoInsertNotEmptyValidator' => false,
				        'decorators' => array(
				            array(
				                'HtmlTag', array(
				                    'tag'  => 'div',
				                    'closeOnly' => true
				                )
				            )
				        )
				    )
				);
				
		$this->addElement('submit', 'submit', array(
		    'label' => $this->getView()->translate('upload')
		));
		
		/*** PROGRAM SELECTION 1 ***/
		//get program applied by applicant
//    	$ptestDB = new App_Model_Application_DbTable_ApplicantPtestProgram();	
//    	$program1 = $ptestDB->getProgramPre($this->applid,$this->aphplacementcode,1);
//		
//    	$this->addElement('text','photograph', array(
//			'label'=>$this->getView()->translate('photograph')));	
			
			
//		
//		$programDb = new App_Model_Record_DbTable_PlacementProgram();
//		$this->appl_placement_program1->addMultiOption(0,$this->getView()->translate('Please Select'));
//		foreach ($programDb->getProgrambyPtestCode($this->aphplacementcode) as $list){
//			$this->appl_placement_program1->addMultiOption($list['app_id'],$list['program_name']);
//		}
//		
//		if($program1!=null){
//			$this->appl_placement_program1->setValue($program1['app_ptest_program']);
//		}
//		
//		
//		
//		
//		//button
//		$this->addElement('submit', 'save', array(
//          'label'=>$this->getView()->translate('Save & Next'),
//          'decorators'=>array('ViewHelper')
//        ));
//        
//        
//        $this->addElement('submit', 'cancel', array(
//          'label'=>'Cancel',
//          'decorators'=>array('ViewHelper'),
//          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'default', 'controller'=>'online-application','action'=>'biodata'),'default',true) . "'; return false;"
//        ));
        
      
	}
}
?>