<?php
class AdmissionRecord_Form_Address extends Zend_Form
{
	protected $studentID;	
	protected $addresstypeID;
	
    public function setStudentID($studentID){
		$this->studentID = $studentID;
	}
	
	public function setAddresstypeID($addresstypeID){
		$this->addresstypeID = $addresstypeID;
	}

	public function init()
	{
		
		$this->setMethod('post');
		$this->setAttrib('id','student_form');	
	
		
		$this->addElement('hidden','student_id',array('value' =>$this->studentID));
		$this->addElement('hidden'  ,'address_type_id',array('value' =>$this->addresstypeID));
		
					
		$this->addElement('text','address1', array(
			'label'=>'Address 1',
			'required'=>'true'));
		
		$this->addElement('text','address2', array(
			'label'=>'Address 2',
			'required'=>'true'));
			
		$this->addElement('text','city', array(
			'label'=>'City',
			'required'=>'true'));
			
		$this->addElement('text','postcode', array(
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