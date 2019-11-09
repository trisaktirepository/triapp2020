<?php class Application_Form_BatchUpload extends Zend_Form
{
    public function __construct($options = null)
	 {
		 parent::__construct($options);
		 // setting Form name, Form action and Form Ecryption type
		 $this->setName("document");
		 $this->setAction("");
		 $this->setAttrib("enctype", "multipart/form-data");
		 
		 // creating object for Zend_Form_Element_File
		 $doc_file = new Zend_Form_Element_File("doc_path");
		 $doc_file->setLabel("Document File Path");
		 $doc_file->setRequired(true);

		 // creating object for submit button
		 $submit = new Zend_Form_Element_Submit("submit");
		 $submit->setLabel("Upload File")
				 ->setAttrib("d", "submitbutton");

		// adding elements to form Object
		$this->addElements(array($doc_file, $submit));
	 }

}

?>