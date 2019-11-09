<?php
class Counseling_Form_Issue extends Zend_Dojo_Form { //Formclass for the Programmaster	 module
	public function init() {
		$gstrtranslate =Zend_Registry::get('Zend_Translate');
		$month= date("m"); // Month value
		$day= date("d"); //today's date
		$year= date("Y"); // Year value
		$yesterdaydate= date('Y-m-d', mktime(0,0,0,$month,($day),$year));
		$dateformat = "{datePattern:'dd-MM-yyyy'}";

		
		$appointmentDate = new Zend_Form_Element_Text('appt_date');
		$appointmentDate->setAttrib('dojoType',"dijit.form.DateTextBox");
		//$SemesterEndDate->setAttrib('onChange',"if(arguments[0] != null) { dijit.byId('SemesterStartDate').constraints.max = arguments[0];}") ;
		$appointmentDate->setAttrib('onChange',"dijit.byId('AppointmentDate').constraints.max = arguments[0];") ;
		//$SemesterEndDate->setAttrib('required',"true")
		$appointmentDate->setAttrib('constraints', "$dateformat")
			->removeDecorator("DtDdWrapper")
			->removeDecorator("Label")
			->removeDecorator('HtmlTag');
		
		$subject = new Zend_Dojo_Form_Element_FilteringSelect('IdSubject');
		$subject->removeDecorator("DtDdWrapper");
		$subject->setAttrib('required',"true") ;
		$subject->removeDecorator("Label");
		$subject->removeDecorator('HtmlTag');
		$subject->setRegisterInArrayValidator(false);
		$subject->setAttrib('dojoType',"dijit.form.FilteringSelect");
		
		//form elements
		$this->addElements(array($appointmentDate,$subject));

	}
}