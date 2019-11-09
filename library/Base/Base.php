<?php
class Base_Base extends Zend_Controller_Action {	
	public $lobjCommon;
	public $lobjform;
	public $initialconfig;
	public $gobjsessionsis;
	public $gintPageCount;
	
	public function preDispatch() {
		$this->view->translate =Zend_Registry::get('Zend_Translate'); 
   	    Zend_Form::setDefaultTranslator($this->view->translate);
   	    $this->fnsetObject();
	}
	
	public function fnsetObject() {
		$this->lobjCommon = new App_Model_Common();
		$this->lobjform = new App_Form_Search ();
		$this->gobjsessionsis = Zend_Registry::get('triapp');
		$this->initialconfig = new GeneralSetup_Model_DbTable_Initialconfiguration ();
		$larrInitialSettings = $this->initialconfig->fnGetInitialConfigDetails($this->gobjsessionsis->idUniversity);
		$this->gintPageCount = empty($larrInitialSettings['noofrowsingrid']) ? "5":$larrInitialSettings['noofrowsingrid'];
		$this->view->College = empty($larrInitialSettings['CollegeAliasName']) ? "College":$larrInitialSettings['CollegeAliasName'];
		$this->view->Registrar = empty($larrInitialSettings['RegisterAliasName']) ? "Registrar":$larrInitialSettings['RegisterAliasName'];
		$this->view->Dean = empty($larrInitialSettings['DeanAliasName']) ? "Dean":$larrInitialSettings['DeanAliasName'];
		$this->view->Department = empty($larrInitialSettings['DepartmentAliasName']) ? "Department":$larrInitialSettings['DepartmentAliasName'];
		$this->view->Subject = empty($larrInitialSettings['SubjectAliasName']) ? "Subject":$larrInitialSettings['SubjectAliasName'];
		$this->view->Program = empty($larrInitialSettings['ProgramAliasName']) ? "Program":$larrInitialSettings['ProgramAliasName'];
		$this->view->Branch = empty($larrInitialSettings['BranchAliasName']) ? "Branch":$larrInitialSettings['BranchAliasName'];
		$this->view->Completionofyears = empty($larrInitialSettings['Completionofyears']) ? "Completed Min Years(25)":$larrInitialSettings['Completionofyears'];
		$this->view->PPField1 = $larrInitialSettings['PPField1'];
		$this->view->PPField2 = $larrInitialSettings['PPField2'];
		$this->view->PPField3 = $larrInitialSettings['PPField3'];
		$this->view->PPField4 = $larrInitialSettings['PPField4'];
		$this->view->PPField5 = $larrInitialSettings['PPField5'];
		$this->view->VisaDetailField1 = $larrInitialSettings['VisaDetailField1'];
		$this->view->VisaDetailField2 = $larrInitialSettings['VisaDetailField2'];
		$this->view->VisaDetailField3 =$larrInitialSettings['VisaDetailField3'];
		$this->view->VisaDetailField4 = $larrInitialSettings['VisaDetailField4'];
		$this->view->VisaDetailField5 = $larrInitialSettings['VisaDetailField5'];
		$this->view->Landscape = empty($larrInitialSettings['LandscapeAliasName']) ? "Landscape":$larrInitialSettings['LandscapeAliasName'];
		$this->view->Language = ($larrInitialSettings['Language'] == 1) ? "Arabic":"Bahasa Indonesia";
	    $this->view->Languageid = $larrInitialSettings['Language'];
         
		$this->view->AccDtlCount = $larrInitialSettings['AccDtlCount'];
		$this->view->AccDtl1 = $larrInitialSettings['AccDtl1'];
		$this->view->AccDtl2 = $larrInitialSettings['AccDtl2'];
		$this->view->AccDtl3 = $larrInitialSettings['AccDtl3'];
		$this->view->AccDtl4 = $larrInitialSettings['AccDtl4'];
		$this->view->AccDtl5 = $larrInitialSettings['AccDtl5'];
		$this->view->AccDtl6 = $larrInitialSettings['AccDtl6'];
		$this->view->AccDtl7 = $larrInitialSettings['AccDtl7'];
		$this->view->AccDtl8 = $larrInitialSettings['AccDtl8'];
		$this->view->AccDtl9 = $larrInitialSettings['AccDtl9'];
		$this->view->AccDtl10 = $larrInitialSettings['AccDtl10'];
		$this->view->AccDtl11 = $larrInitialSettings['AccDtl11'];
		$this->view->AccDtl12 = $larrInitialSettings['AccDtl12'];
		$this->view->AccDtl13 = $larrInitialSettings['AccDtl13'];
		$this->view->AccDtl14 = $larrInitialSettings['AccDtl14'];
		$this->view->AccDtl15 = $larrInitialSettings['AccDtl15'];
		$this->view->AccDtl16 = $larrInitialSettings['AccDtl16'];
		$this->view->AccDtl17 = $larrInitialSettings['AccDtl17'];
		$this->view->AccDtl18 = $larrInitialSettings['AccDtl18'];
		$this->view->AccDtl19 = $larrInitialSettings['AccDtl19'];
		$this->view->AccDtl20 = $larrInitialSettings['AccDtl20'];
		
		
		
		$this->view->MoheDtlCount = $larrInitialSettings['MoheDtlCount'];
		$this->view->MoheDtl1 = $larrInitialSettings['MoheDtl1'];
		$this->view->MoheDtl2 = $larrInitialSettings['MoheDtl2'];
		$this->view->MoheDtl3 = $larrInitialSettings['MoheDtl3'];
		$this->view->MoheDtl4 = $larrInitialSettings['MoheDtl4'];
		$this->view->MoheDtl5 = $larrInitialSettings['MoheDtl5'];
		$this->view->MoheDtl6 = $larrInitialSettings['MoheDtl6'];
		$this->view->MoheDtl7 = $larrInitialSettings['MoheDtl7'];
		$this->view->MoheDtl8 = $larrInitialSettings['MoheDtl8'];
		$this->view->MoheDtl9 = $larrInitialSettings['MoheDtl9'];
		$this->view->MoheDtl10 = $larrInitialSettings['MoheDtl10'];
		$this->view->MoheDtl11 = $larrInitialSettings['MoheDtl11'];
		$this->view->MoheDtl12 = $larrInitialSettings['MoheDtl12'];
		$this->view->MoheDtl13 = $larrInitialSettings['MoheDtl13'];
		$this->view->MoheDtl14 = $larrInitialSettings['MoheDtl14'];
		$this->view->MoheDtl15 = $larrInitialSettings['MoheDtl15'];
		$this->view->MoheDtl16 = $larrInitialSettings['MoheDtl16'];
		$this->view->MoheDtl17 = $larrInitialSettings['MoheDtl17'];
		$this->view->MoheDtl18 = $larrInitialSettings['MoheDtl18'];
		$this->view->MoheDtl19 = $larrInitialSettings['MoheDtl19'];
		$this->view->MoheDtl20 = $larrInitialSettings['MoheDtl20'];
		$this->view->LocalStudent = $larrInitialSettings['LocalStudent'];
		$this->view->BlockType = $larrInitialSettings['BlockType'];
		$this->view->BlockName = $larrInitialSettings['BlockName'];
		$this->view->BlockSeparator = $larrInitialSettings['BlockSeparator'];
		
		$this->view->InternationalPlacementTest = $larrInitialSettings['InternationalPlacementTest'];
		$this->view->InternationalCertification = $larrInitialSettings['InternationalCertification'];
		$this->view->InternationalAndOr = $larrInitialSettings['InternationalAndOr'];

		$this->view->LocalPlacementTest = $larrInitialSettings['LocalPlacementTest'];
		$this->view->LocalCertification = $larrInitialSettings['LocalCertification'];
		$this->view->LocalAndOr = $larrInitialSettings['LocalAndOr'];
		$this->view->DefaultCountry = $larrInitialSettings['DefaultCountry'];
		$this->view->DefaultDropDownLanguage = $larrInitialSettings['DefaultDropDownLanguage'];
		$this->view->TakeMarks = $larrInitialSettings['TakeMarks'];
		
		$this->view->AccountCode= $larrInitialSettings['AccountCode'];
		$this->view->base= $larrInitialSettings['base'];
		$this->view->levelbase= $larrInitialSettings['levelbase']; 
		$this->view->FixedSeparator= $larrInitialSettings['FixedSeparator'];
		$this->view->FixedText= $larrInitialSettings['FixedText'];
		$this->view->FirstletterSeparator= $larrInitialSettings['FirstletterSeparator'];
		
		$this->view->ItemCode= $larrInitialSettings['ItemCode'];
		$this->view->Itembase= $larrInitialSettings['Itembase'];	
		$this->view->itemlevelbase = $larrInitialSettings['itemlevelbase'];
		$this->view->FixeditemText = $larrInitialSettings['FixeditemText'];
		$this->view->FixeditemSeparator = $larrInitialSettings['FixeditemSeparator'];
		$this->view->FirstletteritemSeparator = $larrInitialSettings['FirstletteritemSeparator'];
		$this->view->BillNoType = $larrInitialSettings['BillNoType'];
		$this->view->markdentrytype = $larrInitialSettings['MarksAppeal'];
		$this->view->TakeMarks = $larrInitialSettings['TakeMarks'];
		$this->view->Failedsubjects = $larrInitialSettings['Failedsubjects'];
		
		//REciept
		$this->view->ReceiptType = $larrInitialSettings['ReceiptType'];
		
		//branch office scheme
		$this->view->Branch1 = $larrInitialSettings['Branch'];
		$this->view->Office = $larrInitialSettings['Office'];
		$this->view->Venue = $larrInitialSettings['Venue'];
		$this->view->Scheme = $larrInitialSettings['Scheme'];
		$this->view->LandscapeLevel = $larrInitialSettings['Landscape'];
		
		
	
	}
	
	
} 