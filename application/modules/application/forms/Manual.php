<?php
class Application_Form_Manual extends Zend_Form
{
	
//	FIRSTNAME	I.C NO.	CITIZENSHIP	GENDER	RACE	TELEPHONE	MOBILE	ADDRESS	POSTCODE	STATE	EMAIL
//	ARD_IC	ARD_IC_PLACE	ARD_IC_DATE	ARD_IC_EXPIRE	ARD_CITIZEN	ARD_STATUS	ARD_BATCH	ARD_COUNTRY	ARD_CONFIRM_ACCEPT	ARD_RACE	ARD_RELIGION	ARD_ADDRESS1	ARD_ADDRESS2	ARD_TOWN	ARD_POSTCODE	ARD_STATE	ARD_TELEPHONE	ARD_OFF_TEL	ARD_HPHONE	ARD_FAX	ARD_EMAIL	ARD_DATE_APP month day, year, hour:minute 24hrs	ARD_OFFERED offer or not by system	ARD_PROC_FEE_PAID	ARD_BRANCH_ID	ARD_CREDIT_TRANS	ARD_INTAKE	ARD_OFFERED_BY
	
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id','manual_form');

		$this->addElement('text','ARD_NAME', array(
			'label'=>'FULLNAME',
			'required'=>true));
		
		$this->addElement('text','ARD_IC', array(
			'label'=>'IC NO',
			'required'=>true));
		
		
		
		$this->addElement('select','ARD_CITIZEN', array(
			'label'=>'CITIZENSHIP',
			'inArrayValidator'=>false
		
		));
		
		$countryDB = new App_Model_General_DbTable_Country();
		$country_data = $countryDB->getData();
		
		$this->ARD_CITIZEN->addMultiOption(0,"-- Select Country --");
		foreach ($country_data as $list){
			$this->ARD_CITIZEN->addMultiOption($list['id'],$list['name']);
		}
		
		$this->addElement('radio', 'ARD_SEX', array(
            'label'      => 'GENDER',
            'required'   => true,
            'multioptions'   => array(
                            'M' => 'Male',
                            'F' => 'Female',
                            ),
        ));
       // $this->status->setSeparator('')->setValue('F');
		
		$this->addElement('text','ARD_RACE', array(
			'label'=>'RACE',
			'required'=>false));
		
		$this->addElement('text','ARD_HPHONE', array(
			'label'=>'MOBILE',
			'required'=>false));
		
		$this->addElement('textarea','ARD_ADDRESS', array(
			'label'=>'ADDRESS'));
		
		$this->addElement('text','ARD_POSTCODE', array(
			'label'=>'POSTCODE',
			'required'=>false));
		
		$this->addElement('select','ARD_STATE', array(
			'label'=>'STATE',
			'inArrayValidator'=>false
		
		));
		
		$countryDB = new App_Model_General_DbTable_State();
		$country_data = $countryDB->getData();
		
		$this->ARD_STATE->addMultiOption(0,"-- Select State --");
		foreach ($country_data as $list){
			$this->ARD_STATE->addMultiOption($list['id'],$list['name']);
		}
			
		$this->addElement('text','ARD_EMAIL', array(
			'label'=>'EMAIL',
			'required'=>false));
		
		//OFFER OR NOT OFFER
		$this->addElement('radio', 'ARD_OFFERED', array(
            'label'      => 'OFFER?',
            'required'   => false,
            'multioptions'   => array(
                            '0' => 'No',
                            '1' => 'Yes',
                            ),
        ));
        
        //program offered
        
        $this->addElement('select','ARD_PROGRAM', array(
			'label'=>'PROGRAM OFFERED',
			'inArrayValidator'=>false
		
		));
		
		$programDB = new App_Model_Record_DbTable_Program();
		$programList = $programDB->getData();
		
		$this->ARD_PROGRAM->addMultiOption(0,"-- Select Program --");
		foreach ($programList as $list){
			$this->ARD_PROGRAM->addMultiOption($list['id'],$list['main_name']);
		}
        
		//button
		$this->addElement('submit', 'save', array(
          'label'=>'Submit',
          'decorators'=>array('ViewHelper')
        ));
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'Cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'application', 'controller'=>'manual','action'=>'index'),'default',true) . "'; return false;"
        ));
        
        $this->addDisplayGroup(array('save','cancel'),'buttons', array(
	      'decorators'=>array(
	        'FormElements',
	        array('HtmlTag', array('tag'=>'div', 'class'=>'buttons')),
	        'DtDdWrapper'
	      )
	    ));
		
	}
}
?>