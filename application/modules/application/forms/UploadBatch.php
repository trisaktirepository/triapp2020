<?php
class Application_Form_UploadBatch extends Zend_Form
{
	protected $_facultyid;
	protected $_programcode;
	protected $_academicyear;
	
	
	public function setFacultyid($facultyid)
	{
		$this->_facultyid = $facultyid;
	}
	
	public function setProgramcode($programcode)
	{
		$this->_programcode = $programcode;
	}

	public function setAcademicyear($academicyear)
	{
		$this->_academicyear = $academicyear;
	}
 
	public function init()
	{
		
		$this->setAttrib('enctype', 'multipart/form-data');	
		$this->setAttrib('id','upload_form');		 
		
				 
		$facultyid = $this->createElement('hidden', 'faculty');
        $facultyid->setValue($this->_facultyid);
        
		$programcode = $this->createElement('hidden', 'program_code');
        $programcode->setValue($this->_programcode);
        
        $academicyear = $this->createElement('hidden', 'academic_year');
        $academicyear->setValue($this->_academicyear);
        
        $nomor = $this->createElement('text', 'nomor');
        $nomor->setLabel('Nomor')
              ->setRequired(true);
        
        $decree_date = $this->createElement('text', 'decree_date');
        $decree_date->setLabel('Decree Date')
                    ->setRequired(true);
        
		  
		$filepath = DOCUMENT_PATH."/download/".date("mY")."/";
		//$filepath = "/data/apps/triapp/documents/download/112012/";
		
        //create directory to locate file			
		if (!is_dir($filepath)) {
			 mkdir($filepath, 0775);
		}
		
		 // creating object for Zend_Form_Element_File
		 $filename = new Zend_Form_Element_File('filename');
		 $filename->setLabel('Filename')
				  ->setRequired(true)
				  ->addValidator('Extension', true, 'csv')				
				  ->setDestination($filepath);			  
		
					 		

		// adding elements to form Object
		$this->addElements(array($filename,$facultyid,$programcode,$academicyear,$nomor,$decree_date));
		
		//add submit button
        $this->addElement('submit', 'Upload');

		
	}
	
}
?>