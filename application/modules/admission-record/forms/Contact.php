<?php
class AdmissionRecord_Form_Contact extends Zend_Form
{
	protected $studentID;	
	
    public function setStudentID($studentID){
		$this->studentID = $studentID;
	}
	
	public function init()
	{
		
		$this->setMethod('post');
		$this->setAttrib('id','student_contact_form');	
	
		
		$this->addElement('hidden','student_id',array('value' =>$this->studentID));
		
					
		$this->addElement('text','Email', array(
			'label'=>'Email',
			'required'=>'true'));
		
			$this->addElement('text','house_phone', array(
			'label'=>'Telephone (Home)',
			'required'=>'true'));
			
			$this->addElement('text','office_phone', array(
			'label'=>'Telephone (Office)',
			'required'=>'true'));
			
			$this->addElement('text','mobile', array(
			'label'=>'Mobile',
			'required'=>'true'));
		
		$this->addElement('text','ec_name', array(
			'label'=>'Name',
			'required'=>'true'));
		
		$this->addElement('text','ec_address', array(
			'label'=>'Address',
			'required'=>'true'));
			
		$this->addElement('text','ec_city', array(
			'label'=>'City',
			'required'=>'true'));
			
		$this->addElement('text','ec_postcode', array(
			'label'=>'Postcode',
			'required'=>'true'));

		$oCountry = new App_Model_General_DbTable_Country();
        $country_list = $oCountry->selectCountry();
		
		$this->addElement('select', 'country_id', array(
		    'label'=>'Country',
		    'required'=>'true',
		    'multioptions'=>$country_list));   

		$oState = new App_Model_General_DbTable_State();
        $state_list = $oState->selectState();
		
		$this->addElement('select', 'state_id', array(
		    'label'=>'State',
		    'required'=>'true',
		    'multioptions'=>$state_list));  			
		
		
	}
	
	
	
	
	
	
}
?>