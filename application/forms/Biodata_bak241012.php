<?
class App_Form_Biodata extends Zend_Form {
	


	public function init(){
		$this->setName('application_registration');
		$this->setMethod('post');
		$this->setAttrib('id','biodata_form');;
		
		$this->addElement('hidden','appl_id');
		
		$this->addElement('hidden', 'test', array(
		    'description' => '<h3>'.$this->getView()->translate('personal_info').'</h3>',
		    'ignore' => true,
		    'decorators' => array(
		        array('Description', array('escape'=>false, 'tag'=>'')),
		    ),
		));
		
		$this->addElement('text','appl_fname', array(
			'label'=>'First Name',
			'required'=>true));
			
		
		
		$this->addElement('text','appl_mname', array(
			'label'=>'Middle Name',
			'required'=>true));
			
			
		
		
		$this->addElement('text','appl_lname', array(
			'label'=>'Last Name',
			'required'=>true));
			
		
		
		$this->addElement('text','appl_email', array(
			'label'=>'email',
			'required'=>true));
			
		
		
		
        $this->addElement('date','appl_dob', array(
			'label'=>$this->getView()->translate('dob'),
			'required'=>true,
			'startYear'=>date('Y')-80,
			'stopYear'=>date('Y')-18
		));
      
		
		
		
		
		
		
		$this->addElement('radio','appl_nationality', array(
			'label'=>'nationality',
			'required'=>true,
			'onchange'=>"changeOrigin(this, $('#appl_state'),$('#mappl_state'));"
		));
				
		
		$this->appl_nationality->setMultiOptions(array('1'=>' Indonesia', '0'=>' Others'))
						->setValue("");
		
		
		
		$this->addElement('select','country_origin', array(
			'label'=>'',
			'disabled'=>'disabled'
		));
		
		$this->country_origin->addMultiOption(null,$this->getView()->translate('Please Select'));
		$countryDb = new App_Model_General_DbTable_Country();
		foreach ($countryDb->getData() as $list){
			$this->country_origin->addMultiOption($list['idCountry'],$list['CountryName']);
		}
		
		
		
		$this->addElement(
				    'hidden',
				    'dummyx',
				    array(
				        'required' => false,
				        'ignore' => true,
				        'autoInsertNotEmptyValidator' => false,				       
				        'decorators' => array(
				            array(
				                'HtmlTag', array(
				                    'tag'  => 'div',
				                    'id'   => 'admission',
				                    'openOnly' => true,
				                    'style'=>'display:""',
				                )
				            )
				        )
				    )
				);
				$this->dummyx->clearValidators();
				
		
		$this->addElement('radio','appl_admission_type', array(
			'label'=>'Admission Type',		   
			'onchange'=>"showdiv(this);"		
		));
		
		
				
		
		$this->appl_admission_type->setMultiOptions(array('1'=>' Placement Test','2'=>' High School Certificate'))->setRequired(true);
		$this->appl_admission_type->setValue(1);		

		
		$this->addElement(
				    'hidden',
				    'dummyy',
				    array(
				        'required' => false,
				        'ignore' => true,
				        'autoInsertNotEmptyValidator' => false,
				        'decorators' => array(
				            array(
				                'HtmlTag', array(
				                    'tag'  => 'div',
				                    'id'   => 'placement',
				                    'closeOnly' => true
				                )
				            )
				        )
				    )
				);
				$this->dummyy->clearValidators();
				
		
		
						
		//religion
	    $this->addElement('select','appl_religion', array(
			'label'=>'Religion'
	       
		));	
		
		
		
		$setupDB = new App_Model_General_DbTable_Setup();
		$list_religion = $setupDB->getData('RELIGION');
		
		
		$this->appl_religion->addMultiOption(0,$this->getView()->translate('Please Select'));
		foreach ($list_religion as $religion){
			$this->appl_religion->addMultiOption($religion['ssd_id'],$religion['ssd_name']);
		}
		
		
		//Marital Status
	    $this->addElement('select','appl_marital_status', array(
			'label'=>$this->getView()->translate('Marital Status'),
	        'onchange'=>"enableChildren(this);"
		));
		
		$list_marital = $setupDB->getData('MARITAL');
		
		$this->appl_marital_status->addMultiOption(0,$this->getView()->translate('Please Select'));
		foreach ($list_marital as $marital){
			$this->appl_marital_status->addMultiOption($marital['ssd_id'],$marital['ssd_name']);
		}
		
			
		
		$this->addElement('text','appl_no_of_child', array(
			'label'=>$this->getView()->translate('No of Children'),
			'required'=>true,
	        'disabled'=>'disabled'));
	        
	    
		
		
		//Jacket Size
	    $this->addElement('select','appl_jacket_size', array(
			'label'=>$this->getView()->translate('Jacket Size')
		));
		
		
		
		$list_size = $setupDB->getData('JS');
		
		$this->appl_jacket_size->addMultiOption(0,$this->getView()->translate('Please Select'));
		foreach ($list_size as $size){
			$this->appl_jacket_size->addMultiOption($size['ssd_id'],$size['ssd_name']);
		}
		
		
		$this->addElement(
				    'hidden',
				    'divparentopen',
				    array(
				        'required' => false,
				        'ignore' => true,
				        'autoInsertNotEmptyValidator' => false,				       
				        'decorators' => array(
				            array(
				                'HtmlTag', array(
				                    'tag'  => 'div',
				                    'id'   => 'divparent',
				                    'openOnly' => true,
				                    'style'=>'display:""',
				                )
				            )
				        )
				    )
				);
				$this->dummyx->clearValidators();
				
				
		$this->addElement('hidden', 'correspondance', array(
		    'description' => '<h3>'.$this->getView()->translate('parents_info').'</h3>',
		    'ignore' => true,
		    'decorators' => array(
		        array('Description', array('escape'=>false, 'tag'=>'')),
		    ),
		));
				
		$this->addElement('text','father_name', array(
			'label'=>$this->getView()->translate("Father's Name"),
			'required'=>true
		));
		
		 
        $this->addElement('hidden','relationtype', 
        array(
		));
		$this->relationtype->setValue('20');
		
		
		$this->addElement('text','appl_address1', array(
			'label'=>$this->getView()->translate('address_1')
			));
		
		$this->addElement('text','appl_address2', array(
			'label'=>$this->getView()->translate('address_2')
			));	
			
		$this->addElement('select','appl_state', array(
			'label'=>$this->getView()->translate('state_province'),
	    	'onChange'=>"changeCity(this, $('#appl_city'));"
		));	
		
		$stateDb = new App_Model_General_DbTable_State();
		$this->appl_state->addMultiOption(0,$this->getView()->translate('please_select'));

		foreach ($stateDb->getState(96) as $list){
			$this->appl_state->addMultiOption($list['idState'],$list['StateName']);
		}

		$this->addElement('select','appl_city', array(
			'label'=>$this->getView()->translate('city')
		));	
		$cityDb = new App_Model_General_DbTable_City();
		$this->appl_city->addMultiOption(0,$this->getView()->translate('please_select'));
		foreach ($cityDb->getData() as $list){
			$this->appl_city->addMultiOption($list['idCity'],$list['CityName']);
		}
			
		
		$this->addElement('text','appl_postcode', array(
			'label'=>$this->getView()->translate('postcode'),
			'maxlength'=>'5'
		));
		
		$this->addElement('text','telephone', array(
			'label'=>$this->getView()->translate("Telephone")
		));
		
		$this->addElement('text','email', array(
			'label'=>$this->getView()->translate("email")
		));
		$this->addElement('text','job', array(
			'label'=>$this->getView()->translate("Job")
		));
		
		//education level
	    $this->addElement('select','edulevel', array(
			'label'=>$this->getView()->translate("Education Level")
	       
		));	
		
		$setupDB = new App_Model_General_DbTable_Setup();
		$list_edulevel = $setupDB->getData('EDULEVEL');
		
		
		$this->edulevel->addMultiOption(0,$this->getView()->translate('Please Select'));
		foreach ($list_edulevel as $edulevel){
			$this->edulevel->addMultiOption($edulevel['ssd_id'],$edulevel['ssd_name']);
		}
		
		
		$this->addElement('text','mother_name', array(
			'label'=>$this->getView()->translate("Mother's Name"),
			'required'=>true
		));
		
		
		$this->addElement('checkbox','check_opt', array(
			'label'=>$this->getView()->translate('same_as_father_address?'),		
			'onclick'=>"filladdress(this);"		
		));	
		
		 $this->addElement('hidden','mrelationtype', 
        array(
		));
		$this->mrelationtype->setValue('21');
		
		
		$this->addElement('text','mappl_address1', array(
			'label'=>$this->getView()->translate('address_1')
			));
		
		$this->addElement('text','mappl_address2', array(
			'label'=>$this->getView()->translate('address_2')
			));	
			
		$this->addElement('select','mappl_state', array(
			'label'=>$this->getView()->translate('state_province'),
	    	'onChange'=>"changeCity(this, $('#mappl_city'));"
		));	
		
		$stateDb = new App_Model_General_DbTable_State();
		$this->mappl_state->addMultiOption(0,$this->getView()->translate('please_select'));

		foreach ($stateDb->getState(96) as $list){
			$this->mappl_state->addMultiOption($list['idState'],$list['StateName']);
		}

		$this->addElement('select','mappl_city', array(
			'label'=>$this->getView()->translate('city')
		));	
		$cityDb = new App_Model_General_DbTable_City();
		$this->mappl_city->addMultiOption(0,$this->getView()->translate('please_select'));
		foreach ($cityDb->getData() as $list){
			$this->mappl_city->addMultiOption($list['idCity'],$list['CityName']);
		}
			
		
		$this->addElement('text','mappl_postcode', array(
			'label'=>$this->getView()->translate('postcode'),
			'maxlength'=>'5'
		));
		
		$this->addElement('text','mtelephone', array(
			'label'=>$this->getView()->translate("Telephone")
		));
		
		$this->addElement('text','memail', array(
			'label'=>$this->getView()->translate("email")
		));
		$this->addElement('text','mjob', array(
			'label'=>$this->getView()->translate("Job")
		));
		
		
		//education level
	    $this->addElement('select','medulevel', array(
			'label'=>$this->getView()->translate("Education Level")
	       
		));	
		
		$setupDB = new App_Model_General_DbTable_Setup();
		$list_edulevel = $setupDB->getData('EDULEVEL');
		
		
		$this->medulevel->addMultiOption(0,$this->getView()->translate('Please Select'));
		foreach ($list_edulevel as $edulevel){
			$this->medulevel->addMultiOption($edulevel['ssd_id'],$edulevel['ssd_name']);
		}
		
		
		$this->addElement(
				    'hidden',
				    'divparentclose',
				    array(
				        'required' => false,
				        'ignore' => true,
				        'autoInsertNotEmptyValidator' => false,
				        'decorators' => array(
				            array(
				                'HtmlTag', array(
				                    'tag'  => 'div',
				                    'closeOnly' => true
				                )
				            )
				        )
				    )
				);
				$this->dummyy->clearValidators();
				
		
				
				
		//button
		$this->addElement('submit', 'save', array(
          'label'=>'submit',
          'decorators'=>array('ViewHelper')
        ));
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'default', 'controller'=>'online-application','action'=>'biodata'),'default',true) . "'; return false;"
        ));
        
        
        
        
      
	}
}
?>