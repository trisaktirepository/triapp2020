<?php 

class Application_Form_RequirementApplicant extends Zend_Form
{
	
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id','requirementapplicant_form');
		
		$this->addElement('radio', 'place_obtained', array(
            'label'      => 'Place Obtained',
            'required'   => true,
            'multioptions'   => array(
                            '1' => 'Local',
                            '2' => 'Outside',
                            ),
        ));
		
		$this->addElement('select','year_obtained', array(
			'label'=>'Year Obtained',
			'inArrayValidator'=>true
		
		));
	
		$this->year_obtained->addMultiOption(0,"-- Please select --");
		for ($i=2010;$i>1950;$i--){
			$this->year_obtained->addMultiOption($i,$i);
		}
		
		$this->addElement('select','award', array(
			'label'=>'Award',
			'inArrayValidator'=>false
		
		));
		
		$awardDB = new App_Model_Record_DbTable_Award();
		$award_data = $awardDB->getData();
		
		$this->award->addMultiOption(0,"-- Please select --");
		foreach ($award_data as $list){
			$this->award->addMultiOption($list['id'],$list['name']);
		}
		
		$this->addElement('text','cert', array(
			'label'=>'Qualification/Certificate',
			'required'=>false));
		
		$this->addElement('text','school_name', array(
			'label'=>'Institution Name',
			'required'=>false));
		
		$this->addElement('text','result', array(
			'label'=>'Result/ Total Score',
			'required'=>false));
			
		$this->addElement('text','point', array(
			'label'=>'Point/ Average (%)',
			'required'=>false));
			
//		$this->addElement('text','app_id',array('value' =>$this->id));
//		$this->addElement('text','app_id', array(
//			'value'=>$this->id,
//			'required'=>true));
			
			
	}
}
?>