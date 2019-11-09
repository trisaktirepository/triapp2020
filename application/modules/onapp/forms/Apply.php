<?php 
/**
 * apply
 * 
 * @author Muhamad Alif
 * @version 
 */
class Onapp_Form_Apply extends Zend_Form
{
	
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id','leave_type_form');
		
		$this->addElement('text','hl001_desc', array(
			'label'=>'Leave Title',
			'required'=>'true'));
		
		
		$this->addElement('text','hl001_applygap', array(
			'label'=>'Apply Gap',
			'required'=>'true'));
		
		$this->addElement('checkbox','hl001_halfday', array(
			'label'=>'Enable Halfday',
			'setCheck'=>'false'));
		
		$this->addElement('checkbox','hl001_include', array(
			'label'=>'Include Weekend',
			'setCheck'=>'false'));
		
		$this->addElement('text','hl001_limit', array(
			'label'=>'Occurrence(0 - No limit)',
			'setalue'=>'0'
		));
		
		$this->addElement('submit', 'submit', array(
		    'label' => 'Submit'
		));
	}
	
	public function getCheckedValue()
	{
		return $this->_checkedValue;
	} 
}