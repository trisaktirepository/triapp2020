<?php

class GeneralSetup_Form_VenueDetail extends Zend_Form
{
	protected $venueid, $branchid, $officeid, $url;
	
	public function setVenueid($venueid){
		$this->venueid = $venueid;
	}
	
	public function setBranchid($branchid){
		$this->branchid = $branchid;
	}
	
	public function setOfficeid($officeid){
		$this->officeid = $officeid;
	}
	
	public function setUrl($url){
		$this->url = $url;
	}
	
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id','venue_form');

		
		$id = $this->createElement('hidden', 'venue_id');
		$id->setValue($this->venueid);
		$id->removeDecorator('DtDdWrapper');
		$id->removeDecorator('HtmlTag');
		$id->removeDecorator('Label');
		$this->addElement($id);
		
		$this->addElement('text','name', array(
			'label'=>'Venue Name',
			'required'=>'true'));
		
		
		
		//type
		$this->addElement('select','type_id', array(
			'label'=>'Venue Type'
		));
		
		$venueTypeDB = new App_Model_General_DbTable_VenueType();
		$venue_type = $venueTypeDB->getData();
		
		$this->type_id->addMultiOption(null,"-- Select Venue Type --");
		foreach ($venue_type as $list){
			$this->type_id->addMultiOption($list['id'],$list['name']);
		}
		$this->type_id->setRequired('true');
		
		//capacity
		$this->addElement('text','capacity', array(
			'label'=>'Capacity',
			'required'=>'true'));
		
		//BRANCH
		/*$branchDB = new App_Model_General_DbTable_Branch();
		$branch_data = $branchDB->getData();
		
		$element = new Zend_Form_Element_Select('branch_id_disable');
		$element	->setLabel('Branch')
					->addMultiOption(0,"-- Not applicable --");
		foreach ($branch_data as $list){
			$element->addMultiOption($list['id'],$list['name']);
		}		

		$element->setValue($this->branchid); 
		$element->setAttrib('disabled','true');
		$this->addElement($element);
		
		$id = $this->createElement('hidden', 'branch_id');
		$id->setValue($this->branchid);
		$id->removeDecorator('DtDdWrapper');
		$id->removeDecorator('HtmlTag');
		$id->removeDecorator('Label');
		$this->addElement($id);*/
		
		//OFFICE
		/*$officeDB = new App_Model_General_DbTable_Office();
		$office_data = $officeDB->getData();
		
		$element = new Zend_Form_Element_Select('office_id_disable');
		$element	->setLabel('Office')
					->addMultiOption(0,"-- Not Applicable --");
		foreach ($office_data as $list){
			$element->addMultiOption($list['id'],$list['name']);
		}		

		$element->setValue($this->officeid); 
		$element->setAttrib('disabled','true');
		$this->addElement($element);
		
		$id = $this->createElement('hidden', 'office_id');
		$id->setValue($this->officeid);
		$id->removeDecorator('DtDdWrapper');
		$id->removeDecorator('HtmlTag');
		$id->removeDecorator('Label');
		$this->addElement($id);*/
		
		
		/*$this->addElement('text','address1', array(
			'label'=>'Address'));*/
		
		/*$this->addElement('text','address2', array(
			'label'=>''));*/
		
		/*$this->addElement('text','city', array(
			'label'=>'City'));*/
		
		//STATE
		/*$this->addElement('select','state_id', array(
			'label'=>'State',
			'inArrayValidator'=>'false'
		
		));
		
		$countryDB = new App_Model_General_DbTable_State();
		$country_data = $countryDB->getData();
		
		$this->state_id->addMultiOption(0,"-- Select State --");
		foreach ($country_data as $list){
			$this->state_id->addMultiOption($list['id'],$list['name']);
		}*/
		
		//COUNTRY
		/*$this->addElement('select','country_id', array(
			'label'=>'Country',
			'inArrayValidator'=>'false'
		
		));
		
		$countryDB = new App_Model_General_DbTable_Country();
		$country_data = $countryDB->getData();
		
		$this->country_id->addMultiOption(0,"-- Select Country --");
		foreach ($country_data as $list){
			$this->country_id->addMultiOption($list['id'],$list['name']);
		}*/
		
		/*$this->addElement('text','postcode', array(
			'label'=>'Postcode'));*/
				
	
		//button
		$this->addElement('submit', 'save', array(
          'label'=>'Submit',
          'decorators'=>array('ViewHelper')
        ));
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'Cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->url . "'; return false;"
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