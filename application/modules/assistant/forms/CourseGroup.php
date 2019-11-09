<?php
/**
 *  @author alif
 *  @date Mar 3, 2014
 */

class Assistant_Form_CourseGroup extends Zend_Form
{
	protected $idSubject;
	protected $IdSemester;
	protected $idGroup;
	protected $idProgram;
	
	
	public function setIdSubject($idSubject){
		$this->idSubject = $idSubject;
	}
	public function setIdSemester($idSemester){
		$this->IdSemester = $idSemester;
	}
	public function setIdGroup($idGroup){
		$this->idGroup = $idGroup;
	}
	public function setIdProgram($idProgram){
		$this->idProgram = $idProgram;
	}
	
	public function init()
	{
		$this->setMethod('post');
		$this->setAttrib('id','GroupForm');
		
		$registry = Zend_Registry::getInstance();
		$locale = $registry->get('Zend_Locale');
		$this->addElement(
				'text',
				'register_username',
				array(
						'required' => false,
						'decorators' => array(
								array(
										'HtmlTag', array(
												'tag'  => 'div',
												'class' => 'validate',
												'id' => 'msg'
												
										)
								)
						)
				)
		);
		
		$this->addElement(
				'text',
				'msgtable',
				array(
						'required' => false,
						'decorators' => array(
								array(
										'HtmlTag', array(
												'tag'  => 'div',
												'class' => 'validate',
												'id' => 'msgtbl'
		
										)
								)
						)
				)
		);
		
		$this->addElement('hidden','status' );
		
		$this->addElement('hidden','IdProgram' );
		$this->IdProgram->setValue($this->idProgram);
		
		$this->addElement('hidden','IdSubject' );
		$this->IdSubject->setValue($this->idSubject);
		
		$this->addElement('hidden','idSemester');
		$this->idSemester->setValue($this->IdSemester);
		
		$this->addHid('IdCourseTaggingGroup',$this->idGroup);
		
		$this->addElement('text','GroupName', 
			array(
				'label'=>'Group Name',
				'required'=>'true'
			)
		);
		
		$this->addElement('text','GroupCode',
				array(
						'label'=>'Group Code',
						'required'=>'true'
				)
		);
		
		$this->addElement('text','maxstud',
		    array(
		        'label'=>'Max Occupancy',
		        'required'=>'true'
		    )
		);
		$this->maxstud->addValidator('Digits');
		 
	 
		
		$foo = new Zend_Form_Element_Text('IdLecturerName');
		$foo->setLabel('Assistant Name')
		->setDescription('<a href="#" class="btn" id="assign-lecturer">Assign</a> <a href="#" class="btn" onclick="$(\'#IdLecturer\').val(\'\');$(\'#IdLecturerName1\').val(\'\'); return false;">Reset</a>')
		->setAttrib('readonly', true)
		->setDecorators(array(
		    'ViewHelper',
		    array('Description', array('escape' => false, 'tag' => false)),
		    array('HtmlTag', array('tag' => 'dd')),
		    array('Label', array('tag' => 'dt')),
		    'Errors',
		));
		$this->addElement($foo);  
		
		$this->addHid('IdLecturer',null);
		
		
		$foo2 = new Zend_Form_Element_Text('VerifyByName');
		$foo2->setLabel('Mark Verificator')
		->setDescription('<a href="#" class="btn" id="assign-verificator">Assign</a> <a href="#" class="btn" onclick="$(\'#VerifyBy\').val(\'\'); $(\'#VerifyByName\').val(\'\'); return false;">Reset</a>')
		->setAttrib('readonly', true)
		->setAttrib('ignore', true)
		->setDecorators(array(
		    'ViewHelper',
		    array('Description', array('escape' => false, 'tag' => false)),
		    array('HtmlTag', array('tag' => 'dd')),
		    array('Label', array('tag' => 'dt')),
		    'Errors',
		));
		$this->addElement($foo2);
		
		
		$this->addHid('VerifyBy',null);
		
		$this->addElement('textarea','remark',
		    array(
		        'label'=>'Remark',
		    )
		);
		
		$table = '<table id="program" class="table" width="500px"><thead><tr><th>Program</th><th width="30px">Action</th></tr></thead><tbody></tbody><tfoot><tr><td>&nbsp;</td><td>&nbsp;</td></tr></tfoot></table>';
		$this->addElement('hidden', 'program_list', array(
		    'label' => 'Program',
            'ignore' => true,
		    'description' => '<a href="#" class="btn" id="assign-program">Add Program</a><br />'.$table
        ));
		
		$this->program_list
		->setDecorators(array(
		    'ViewHelper',
		    array('Description', array('escape' => false, 'tag' => false)),
		    array('HtmlTag', array('tag' => 'dd')),
		    array('Label', array('tag' => 'dt')),
		    'Errors',
		));
		
		$table = '<table id="branch" class="table" width="500px"><thead><tr><th>Branch</th><th width="30px">Action</th></tr></thead><tbody></tbody><tfoot><tr><td>&nbsp;</td><td>&nbsp;</td></tr></tfoot></table>';
		$this->addElement('hidden', 'branch_list', array(
				'label' => 'Branch',
				'ignore' => true,
				'description' => '<a href="#" class="btn" id="assign-branch">Add Branch</a><br />'.$table
		));
		
		$this->branch_list
		->setDecorators(array(
				'ViewHelper',
				array('Description', array('escape' => false, 'tag' => false)),
				array('HtmlTag', array('tag' => 'dd')),
				array('Label', array('tag' => 'dt')),
				'Errors',
		));
		
		
		//button
		$this->addElement('submit', 'save', array(
		    'label'=>'Submit',
		    'decorators'=>array('ViewHelper')
		));
		
		$this->addElement('submit', 'cancel', array(
		    'label'=>'Cancel',
		    'decorators'=>array('ViewHelper'),
		    'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'general-setup', 'controller'=>'highschool-subject','action'=>'index'),'default',true) . "'; return false;"
		));
		
		$this->addDisplayGroup(array('save','cancel'),'buttons', array(
		    'decorators'=>array(
		        'FormElements',
		        array('HtmlTag', array('tag'=>'div', 'class'=>'buttons')),
		        'DtDdWrapper'
		    )
		));
		
		
	}
	
	public function addHid($field, $value){
	  $hiddenIdField = new Zend_Form_Element_Hidden($field);
	  $hiddenIdField->setValue($value)
	  ->removeDecorator('label')
	  ->removeDecorator('HtmlTag');
	  $this->addElement($hiddenIdField);
	}
}
?>