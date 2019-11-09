<?php
//require_once '../application/modules/setup/models/DbTable/Country.php';

class AnrSetup_Form_Course extends Zend_Form
{
	
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id','course_form');
		
		
		/*Country ID*/
		// $this->addElement('select','state_country_id', array(
			// 'label'=>'Country',
			// 'required'=>'true'));
		
		// $countryDB = new Setup_Model_DbTable_Country();
		// $country_data = $countryDB->getCountry();
		
		// $this->state_country_id->addMultiOption(0,"-- Select Country --");
		// foreach ($country_data as $list){
			// $this->state_country_id->addMultiOption($list['country_id'],$list['country_desc']);
		// }
		
		
		$this->addElement('text','code', array(
			'label'=>'Course Code',
			'required'=>'true'));
		
		$this->addElement('text','lmsCode', array(
			'label'=>'Code for LMS',
			'required'=>'true'));
			
		$this->addElement('text','name', array(
			'label'=>'Course Name',
			'required'=>'true'));
			
		$this->addElement('textarea','synopsis', array(
			'label'=>'Course Synopsis'));
			
		$this->addElement('text','credit_hour', array(
			'label'=>'Course Credit Hours',
			'required'=>'true'));
		
		/* faculty */
		$facultyDB = new App_Model_General_DbTable_Faculty();
		$faculty_data = $facultyDB->getData();
		
		$element = new Zend_Form_Element_Select('faculty_id');
		$element->setLabel('Faculty');
		$element->addMultiOption(null,'-- Select Faculty --');
		foreach ($faculty_data as $fac){
			$element->addMultiOption($fac['id'],$fac['code']." - ".$fac['name']);
		}
		$this->addElement($element);
		
		
		/* Department*/
		$departmentDB = new App_Model_General_DbTable_Department();
		$department_data = $departmentDB->getData();
		
		$element = new Zend_Form_Element_Select('department_id');
		$element->setLabel('Department');
		
		$element->addMultiOption(null,'-- Select Department --');
		foreach ($department_data as $dept){
			$element->addMultiOption($dept['id'],$dept['faculty_code']." - ".$dept['name']);
		}
		$this->addElement($element);
		
		$this->addElement('radio', 'status', array(
            'label'      => 'Is Active?',
            'required'   => true,
            'multioptions'   => array(
                            '0' => 'No',
                            '1' => 'Yes',
                            ),
        ));
        $this->status->setSeparator('')->setValue('1');
        
			
		//button
		$this->addElement('submit', 'save', array(
          'label'=>'Submit',
          'decorators'=>array('ViewHelper')
        ));
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'Cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'anr-setup', 'controller'=>'course','action'=>'index'),'default',true) . "'; return false;"
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