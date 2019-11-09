<?php 

class Assistant_Form_MarkEntrySearchStudent extends Zend_Form
{
	protected $idSemesterx;
	protected $idSubjectx;
		
	public function setIdSemesterx($idSemesterx){
		$this->idSemesterx = $idSemesterx;
	}
	
		
	public function setIdSubjectx($idSubjectx){
		$this->idSubjectx = $idSubjectx;
	}
	
	public function init()
	{
						
		$this->setMethod('post');
		$this->setAttrib('id','myform');

		$Semester = new Zend_Form_Element_Hidden('idSemester');
		$Semester->setValue($this->idSemesterx);
		$this->addElement($Semester);
				
		$Subject = new Zend_Form_Element_Hidden('idSubject');
		$Subject->setValue($this->idSubjectx);
		$this->addElement($Subject);
		
		
		//Applicant Name
		$this->addElement('text','student_name', array(
			'label'=>$this->getView()->translate('Student Name')
		));
		
		//Student ID
		$this->addElement('text','student_id', array(
			'label'=>$this->getView()->translate('Student ID / NIM')
		));
								
		
		//button
		/*$this->addElement('submit', 'Search', array(
          'label'=>$this->getView()->translate('Search'),
          'decorators'=>array('ViewHelper')
        ));
        
        
        $this->addDisplayGroup(array('Search'),'buttons', array(
	      'decorators'=>array(
	        'FormElements',
	        array('HtmlTag', array('tag'=>'div', 'class'=>'buttons')),
	        'DtDdWrapper'
	      )
	    ));*/
        	    
		
        		
	}
	
	
}
?>