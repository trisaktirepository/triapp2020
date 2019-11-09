<?php

class GeneralSetup_Form_SchoolDisciplineSubject extends Zend_Form
{
	
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id','form_sds');
		
		
		$subject= new Zend_Form_Element_Multiselect('sds_subject',array(
			'label' => 'Subject',
			'required' => true
		));
		
		$subjectDb = new App_Model_General_DbTable_SchoolSubject();
		$subjectList = $subjectDb->getData();
		
		$registry = Zend_Registry::getInstance();
		$locale = $registry->get('Zend_Locale');
		
		if ($locale=="en_US"){	
			foreach ($subjectList as $list){
				$subject->addMultiOption($list['ss_id'],$list['ss_subject']);
			}	
		}else if ($locale=="id_ID"){
			foreach ($subjectList as $list){
				$subject->addMultiOption($list['ss_id'],$list['ss_subject_bahasa']);
			}
		}
		
		$this->addElement($subject);
		
	
		//button
		$this->addElement('submit', 'save', array(
          'label'=>'Submit',
          'decorators'=>array('ViewHelper')
        ));
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'Cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'general-setup', 'controller'=>'highschool-discipline-subject','action'=>'index'),'default',true) . "'; return false;"
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