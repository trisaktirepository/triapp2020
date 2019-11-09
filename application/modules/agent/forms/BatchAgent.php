<?php
class Agent_Form_BatchAgent extends Zend_Form {
	
	public function init()
	{
        //parent::__construct($options);

        $this->setName('upload');
        $this->setAttrib('enctype', 'multipart/form-data');
		$this->setAttrib('action', $this->getView()->url(array('module'=>'agent', 'controller'=>'batch-agent','action'=>'upload-omr'),'default',true) );
          
        $file = new Zend_Form_Element_File('file');
        $file->setLabel('File')
            ->setDestination(APPLICATION_PATH  . '/tmp')
            ->setRequired(true);
		$this->addElement($file);
		
		//ApplicantID
/*
		$this->addElement('hidden', 'applicant_id', array(
			'description' => 'Applicant ID',
		    'ignore' => true,
		    'decorators' => array(
		        	array('Description', array('escape'=>false, 'tag'=>'<h1>')),
		    	),
		));
		
		$format = new Zend_Form_Element_Text('applicantid_start');
        $format->setLabel('Start Char')
        			->setAttrib('class','num')
                  ->setRequired(true)
                  ->addValidator('NotEmpty')
                  ->addValidator('Digits')
                  ->setValue(40);
		$this->addElement($format);		

		$format = new Zend_Form_Element_Text('applicantid_length');
        $format->setLabel('Length')
        			->setAttrib('class','num')
                  ->setRequired(true)
                  ->addValidator('NotEmpty')
                  ->addValidator('Digits')
                  ->setValue(8);
		$this->addElement($format);	

		//Religion

		$this->addElement('hidden', 'religion', array(
			'description' => 'Religion',
		    'ignore' => true,
		    'decorators' => array(
		        	array('Description', array('escape'=>false, 'tag'=>'<h1>')),
		    	),
		));
		
		$format = new Zend_Form_Element_Text('religion_start');
        $format->setLabel('Start Char')
        			->setAttrib('class','num')
                  ->setRequired(true)
                  ->addValidator('NotEmpty')
                  ->addValidator('Digits')
                  ->setValue(188);
		$this->addElement($format);		

		$format = new Zend_Form_Element_Text('religion_length');
        $format->setLabel('Length')
        			->setAttrib('class','num')
                  ->setRequired(true)
                  ->addValidator('NotEmpty')
                  ->addValidator('Digits')
                  ->setValue(1);
		$this->addElement($format);			

		//APplicant Address

		$this->addElement('hidden', 'applicantaddress', array(
			'description' => 'Applicant Address',
		    'ignore' => true,
		    'decorators' => array(
		        	array('Description', array('escape'=>false, 'tag'=>'<h1>')),
		    	),
		));
		
		$format = new Zend_Form_Element_Text('appaddress_start');
        $format->setLabel('Start Char')
        			->setAttrib('class','num')
                  ->setRequired(true)
                  ->addValidator('NotEmpty')
                  ->addValidator('Digits')
                  ->setValue(123);
		$this->addElement($format);		

		$format = new Zend_Form_Element_Text('appaddress_length');
        $format->setLabel('Length')
        			->setAttrib('class','num')
                  ->setRequired(true)
                  ->addValidator('NotEmpty')
                  ->addValidator('Digits')
                  ->setValue(25);
		$this->addElement($format);					*/
	}
		
}