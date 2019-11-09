<?php
//require_once '../application/modules/setup/models/DbTable/Country.php';

class Application_Form_UploadAttachment extends Zend_Form
{
	//utk tesst upload + progress bar
//	public function init(){
//	 $this->setMethod('post');
//        
//        // set form attributes
//        $this->setAttrib('enctype', 'multipart/form-data');
//        $this->setAttrib('target', 'progressFrame');
//        $this->setAttrib('id', 'files-upload-form');
//        
//        // create multiple file element
//        $files = new Zend_Form_Element_File('files');
//        $files->setDestination(APPLICATION_PATH."/upload/");
//        $files->setMultiFile(3);
//        
//        // set this flag for manual reception of uploaded files
//        $files->setValueDisabled(true);
//        
//        $this->addElements(array($files));
//
//        // add submit button
//        $this->addElement('submit', 'Upload');
//    }

 
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id','uploadfile_form');
		
		$this->setName('document');
		 $this->setAction("");
		 $this->setAttrib('enctype', 'multipart/form-data');
		 
		 $this->addElement('text','filename', array(
			'label'=>'File Name',
			'required'=>true));
		 
		 // creating object for Zend_Form_Element_File
		 $doc_file = new Zend_Form_Element_File('doc_path');
		 $doc_file->setLabel('Document File Path')
				  ->setRequired(true);

		 // creating object for submit button
		 $submit = new Zend_Form_Element_Submit('submit');
		 $submit->setLabel('Upload File')
				 ->setAttrib('id', 'submitbutton');

		// adding elements to form Object
		$this->addElements(array($doc_file, $submit));
		

		$this->addElement('submit', 'submit', array(
		    'label' => 'Upload Picture'
		));

	}
	
//	public function __construct($options = null)
//	 {
//		 parent::__construct($options);
//		 // setting Form name, Form action and Form Ecryption type
//		 $this->setName('document');
//		 $this->setAction("");
//		 $this->setAttrib('enctype', 'multipart/form-data');
//		 
//		 // creating object for Zend_Form_Element_File
//		 $doc_file = new Zend_Form_Element_File('doc_path');
//		 $doc_file->setLabel('Document File Path')
//				  ->setRequired(true);
//
//		 // creating object for submit button
//		 $submit = new Zend_Form_Element_Submit('submit');
//		 $submit->setLabel('Upload File')
//				 ->setAttrib('id', 'submitbutton');
//
//		// adding elements to form Object
//		$this->addElements(array($doc_file, $submit));
//	 }
}
?>