<?php
class Assistant_Form_Branchofficevenue extends Zend_Dojo_Form {
	public function init(){
		$gstrtranslate =Zend_Registry::get('Zend_Translate');

		$IdBranch = new Zend_Form_Element_Hidden('IdBranch');
		$IdBranch->setAttrib('id','IdBranch')
		->removeDecorator("Label")
		->removeDecorator("DtDdWrapper")
		->removeDecorator('HtmlTag');
			
		$IdRegistration = new Zend_Form_Element_Hidden('IdRegistration');
		$IdRegistration	->removeDecorator("Label")
		->removeDecorator("DtDdWrapper")
		->removeDecorator('HtmlTag');
			
		$UpdDate = new Zend_Form_Element_Hidden('UpdDate');
		$UpdDate->removeDecorator("DtDdWrapper");
		$UpdDate->removeDecorator("Label");
		$UpdDate->removeDecorator('HtmlTag');
		 
		$UpdUser = new Zend_Form_Element_Hidden('UpdUser');
		$UpdUser->setAttrib('id','UpdUser')
		->removeDecorator("Label")
		->removeDecorator("DtDdWrapper")
		->removeDecorator('HtmlTag');


		$BranchName = new Zend_Form_Element_Text('BranchName');
		$BranchName  ->setAttrib('dojoType',"dijit.form.ValidationTextBox")
		-> setAttrib('required',"true")
		->setAttrib('maxlength','50')
		->addFilter('StripTags')
		->addFilter('StringTrim')
		->removeDecorator("Label")
		->removeDecorator("DtDdWrapper")
		->removeDecorator('HtmlTag');
		$Addr1 = new Zend_Form_Element_Text('Addr1');
		$Addr1->setAttrib('dojoType',"dijit.form.ValidationTextBox");
		$Addr1->setAttrib('required',"true")
		->setAttrib('maxlength','20')
		->removeDecorator("DtDdWrapper")
		->removeDecorator("Label")
		->removeDecorator('HtmlTag');

		$Addr2 = new Zend_Form_Element_Text('Addr2');
		$Addr2->setAttrib('dojoType',"dijit.form.ValidationTextBox");
		$Addr2->setAttrib('required',"false")
		->setAttrib('maxlength','20')
		->removeDecorator("DtDdWrapper")
		->removeDecorator("Label")
		->removeDecorator('HtmlTag');

		$idCountry = new Zend_Dojo_Form_Element_FilteringSelect('idCountry');
		$idCountry->removeDecorator("DtDdWrapper");
		$idCountry->setAttrib('required',"true") ;
		$idCountry->removeDecorator("Label");
		$idCountry->removeDecorator('HtmlTag');
		$idCountry->setAttrib('OnChange', 'fnGetCountryStateList');
		$idCountry->setRegisterInArrayValidator(false);
		$idCountry->setAttrib('dojoType',"dijit.form.FilteringSelect");

		$idState = new Zend_Dojo_Form_Element_FilteringSelect('idState');
		$idState->removeDecorator("DtDdWrapper");
		$idState->setAttrib('required',"true") ;
		$idState->removeDecorator("Label");
		$idState->removeDecorator('HtmlTag');
		$idState->setRegisterInArrayValidator(false);
		$idState->setAttrib('dojoType',"dijit.form.FilteringSelect");

		$Email = new Zend_Form_Element_Text('Email',array('regExp'=>"^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,3})$",'invalidMessage'=>"Not a valid email"));
		$Email->setAttrib('dojoType',"dijit.form.ValidationTextBox");
		$Email->setAttrib('required',"false")
		->setAttrib('maxlength','50')
		->removeDecorator("DtDdWrapper")
		->removeDecorator("Label")
		->removeDecorator('HtmlTag');

		$countrycode = new Zend_Form_Element_Text('countrycode',array('regExp'=>"[0-9]+",'invalidMessage'=>"Only digits"));
		$countrycode->setAttrib('dojoType',"dijit.form.ValidationTextBox");
		$countrycode->setAttrib('maxlength','3');
		$countrycode->setAttrib('style','width:30px');
		$countrycode->removeDecorator("DtDdWrapper");
		$countrycode->removeDecorator("Label");
		$countrycode->removeDecorator('HtmlTag');

		$statecode = new Zend_Form_Element_Text('statecode',array('regExp'=>"[0-9]+",'invalidMessage'=>"Only digits"));
		$statecode->setAttrib('dojoType',"dijit.form.ValidationTextBox");
		$statecode->setAttrib('maxlength','2');
		$statecode->setAttrib('style','width:30px');
		$statecode->removeDecorator("DtDdWrapper");
		$statecode->removeDecorator("Label");
		$statecode->removeDecorator('HtmlTag');


		$Phone = new Zend_Form_Element_Text('Phone',array('regExp'=>"[0-9]+",'invalidMessage'=>"Not a valid Home Phone No."));
		$Phone->setAttrib('dojoType',"dijit.form.ValidationTextBox");
		$Phone->setAttrib('maxlength','9');
		$Phone->setAttrib('style','width:93px');
		$Phone->removeDecorator("DtDdWrapper");
		$Phone->removeDecorator("Label");
		$Phone->removeDecorator('HtmlTag');


		$zipCode = new Zend_Form_Element_Text('zipCode');
		$zipCode->setAttrib('dojoType',"dijit.form.ValidationTextBox");
		$zipCode->setAttrib('maxlength','20');
		$zipCode->removeDecorator("DtDdWrapper");
		$zipCode->removeDecorator("Label");
		$zipCode->removeDecorator('HtmlTag');

		$IdType = new Zend_Dojo_Form_Element_FilteringSelect('IdType');
		$IdType->addMultiOptions(array('1' => 'Branch',
									    '2' => 'Office',
        								'3' => 'Venue'));
		$IdType->setAttrib('required',"true");
		$IdType->removeDecorator("DtDdWrapper");
		$IdType->removeDecorator("Label");
		$IdType->removeDecorator('HtmlTag');
		$IdType->setAttrib('dojoType',"dijit.form.FilteringSelect");

		$IdLink = new Zend_Dojo_Form_Element_FilteringSelect('IdLink');
		$IdLink->addMultiOptions(array('1' => 'Faculty',
									    '2' => 'Department',
        								'3' => 'Others'));
		$IdLink->setAttrib('required',"false");
		$IdLink->removeDecorator("DtDdWrapper");
		$IdLink->removeDecorator("Label");
		$IdLink->removeDecorator('HtmlTag');
		$IdLink->setAttrib('dojoType',"dijit.form.FilteringSelect");

		$IdLinkType = new Zend_Dojo_Form_Element_FilteringSelect('IdLinkType');
		$IdLinkType->addMultiOptions(array('1' => 'Faculty',
									    '2' => 'Department',
        								'3' => 'Others'));
		$IdLinkType->setAttrib('required',"false");
		$IdLinkType->removeDecorator("DtDdWrapper");
		$IdLinkType->removeDecorator("Label");
		$IdLinkType->removeDecorator('HtmlTag');
		$IdLinkType->setAttrib('dojoType',"dijit.form.FilteringSelect");




		$Active  = new Zend_Form_Element_Checkbox('Active');
		$Active->setAttrib('dojoType',"dijit.form.CheckBox");
		$Active->setvalue('1');
		$Active->removeDecorator("DtDdWrapper");
		$Active->removeDecorator("Label");
		$Active->removeDecorator('HtmlTag');

		$AffiliatedTo = new Zend_Dojo_Form_Element_FilteringSelect('AffiliatedTo');
		$AffiliatedTo->removeDecorator("DtDdWrapper");
		$AffiliatedTo->setAttrib('required',"true") ;
		$AffiliatedTo->removeDecorator("Label");
		$AffiliatedTo->removeDecorator('HtmlTag');
		$AffiliatedTo->setRegisterInArrayValidator(false);
		$AffiliatedTo->setAttrib('dojoType',"dijit.form.FilteringSelect");

		$RegistrationLoc = new Zend_Dojo_Form_Element_FilteringSelect('RegistrationLoc');
		$RegistrationLoc->removeDecorator("DtDdWrapper");
		$RegistrationLoc->setAttrib('required',"false") ;
		$RegistrationLoc->removeDecorator("Label");
		$RegistrationLoc->removeDecorator('HtmlTag');
		$RegistrationLoc->setRegisterInArrayValidator(false);
		$RegistrationLoc->setAttrib('dojoType',"dijit.form.FilteringSelect");

		$Programme = new Zend_Dojo_Form_Element_FilteringSelect('Programme');
		$Programme->removeDecorator("DtDdWrapper");
		$Programme->setAttrib('required',"false") ;
		$Programme->removeDecorator("Label");
		$Programme->removeDecorator('HtmlTag');
		$Programme->setRegisterInArrayValidator(false);
		$Programme->setAttrib('dojoType',"dijit.form.FilteringSelect");

		$Branch = new Zend_Dojo_Form_Element_FilteringSelect('Branch');
		$Branch->removeDecorator("DtDdWrapper");
		$Branch->setAttrib('required',"true") ;
		$Branch->removeDecorator("Label");
		$Branch->removeDecorator('HtmlTag');
		$Branch->setRegisterInArrayValidator(false);
		$Branch->setAttrib('dojoType',"dijit.form.FilteringSelect");


		$Add = new Zend_Form_Element_Button('Add');
		$Add->setAttrib('class', 'NormalBtn');
		$Add->setAttrib('dojoType',"dijit.form.Button");
		$Add->setAttrib('OnClick', 'registrationLocInsert()')
		->removeDecorator("Label")
		->removeDecorator("DtDdWrapper")
		->removeDecorator('HtmlTag');

		$Save = new Zend_Form_Element_Submit('Save');
		$Save->dojotype="dijit.form.Button";
		$Save->label = $gstrtranslate->_("Save");
		$Save->setAttrib('class', 'NormalBtn');
		$Save->removeDecorator("DtDdWrapper")
		->removeDecorator("Label")
		->removeDecorator('HtmlTag');

		$Arabic = new Zend_Form_Element_Text('Arabic');
		$Arabic->setAttrib('dojoType',"dijit.form.ValidationTextBox");
		$Arabic->setAttrib('required',"false")
		->setAttrib('maxlength','20')
		->removeDecorator("DtDdWrapper")
		->removeDecorator("Label")
		->removeDecorator('HtmlTag');

		$ShortName = new Zend_Form_Element_Text('ShortName');
		$ShortName->setAttrib('dojoType',"dijit.form.ValidationTextBox");
		$ShortName->setAttrib('required',"true")
		->setAttrib('maxlength','50')
		->removeDecorator("DtDdWrapper")
		->removeDecorator("Label")
		->removeDecorator('HtmlTag');

		$BranchCode = new Zend_Form_Element_Text('BranchCode');
		$BranchCode->setAttrib('dojoType',"dijit.form.ValidationTextBox");
		$BranchCode->setAttrib('required',"true")
		->setAttrib('maxlength','20')
		->removeDecorator("DtDdWrapper")
		->removeDecorator("Label")
		->removeDecorator('HtmlTag');

		 
		$this->addElements(
		array(
		$Branch,
		$IdRegistration,
		$RegistrationLoc,
		$AffiliatedTo,
		$Programme,
		$BranchCode,
		$Add,
		$ShortName,
		$Arabic,
		$IdBranch,
		$UpdDate,
		$UpdUser,
		$BranchName,
		$Addr1,
		$Addr2,
		$idCountry,
		$idState,
		$Email,
		$countrycode,
		$statecode,
		$Phone,
		$zipCode,
		$IdType,
		$IdLink,
		$IdLinkType,
		$Save,
		$Active
			
		)
		);
	}
}
?>