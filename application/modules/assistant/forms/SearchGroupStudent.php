<?php 

class Assistant_Form_SearchGroupStudent extends Zend_Form
{
		
		
	public function init()
	{
						
		$this->setMethod('post');
		$this->setAttrib('id','myform');
						       
		
						
		//name
		$this->addElement('text','idStudent', array(
			'label'=>'Student ID'
		));
		
		//name
		$this->addElement('text','student_name', array(
			'label'=>'Student Name'
		));

		$this->addElement('text','bil_student', array(
			'label'=>'Number of Student'
		));
		//button
		$this->addElement('submit', 'Search', array(
          'label'=>$this->getView()->translate('Search'),
          'decorators'=>array('ViewHelper')
        ));
        
        
        $this->addDisplayGroup(array('Search'),'buttons', array(
	      'decorators'=>array(
	        'FormElements',
	        array('HtmlTag', array('tag'=>'div', 'class'=>'buttons')),
	        'DtDdWrapper'
	      )
	    ));
        	    
		
        		
	}
	
	
}
?>