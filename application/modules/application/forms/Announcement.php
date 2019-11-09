<?php

class Application_Form_Announcement extends Zend_Form
{
	
protected $_courseid;


public function setCourseid($value)
{
$this->_courseid = $value;
}

public function init ()
    {
        
    	$this->setMethod('post');
    	
    	
    	
    	$this->addElement('hidden', 'english', array(
		    'description' => '<h3>'.$this->getView()->translate('English').'</h3>',
		    'ignore' => true,
		    'decorators'=>array(
	          'ViewHelper',
               'Description',
               'Errors', 
               array(array('data'=>'HtmlTag'), array('tag' => 'td', 'colspan'=>'2')),
               array('Description', array('escape'=>false, 'tag'=>'')),
               array(array('row'=>'HtmlTag'),array('tag'=>'tr', 'openOnly'=>true))
               ),
		    
		));
		
		$this->addElement('hidden', 'indonesia', array(
		    'description' => '<h3>'.$this->getView()->translate('Bahasa indonesia').'</h3>',
		    'ignore' => true,
		    'decorators'=>array(
	          'ViewHelper',
               'Description',
               'Errors', 
               array(array('data'=>'HtmlTag'), array('tag' => 'td', 'colspan'=>'2')),
               array('Description', array('escape'=>false, 'tag'=>'')),
               array(array('row'=>'HtmlTag'),array('tag'=>'tr', 'closeOnly'=>true))
               ),
		    
		));
		
    	
        
    	$titlebi = $this->createElement('text', 'title_bi');
        $titlebi->setLabel('Title :');
        
        $titlebi->setDecorators(array(
					'ViewHelper',
 					'Description',
					'Errors',
					array(array('data'=>'HtmlTag'), array('tag' => 'td')),
					array('Label', array('tag' => 'td' )),
					array(array('row'=>'HtmlTag'),array('tag'=>'tr', 'openOnly'=>true))

        ));
        
        $titlein = $this->createElement('text', 'title_in');
        $titlein->setLabel('Tajuk :');
        
        $titlein->setDecorators(array(
					'ViewHelper',
 					'Description',
					'Errors',
					array(array('data'=>'HtmlTag'), array('tag' => 'td')),
					array('Label', array('tag' => 'td' )),
					array(array('row'=>'HtmlTag'),array('tag'=>'tr', 'closeOnly'=>true))

        ));
        
        $mesgbi = $this->createElement('textarea', 'mesg_bi');
        $mesgbi->setLabel('Announcement :');
        
        $mesgbi->setDecorators(array(
					'ViewHelper',
 					'Description',
					'Errors',
					array(array('data'=>'HtmlTag'), array('tag' => 'td')),
					array('Label', array('tag' => 'td')),
					array(array('row'=>'HtmlTag'),array('tag'=>'tr', 'openOnly'=>true))

        ));
        
        
        
        
        $mesgin = $this->createElement('textarea', 'mesg_in');
        $mesgin->setLabel('Pengumuman :');
        
        $mesgin->setDecorators(array(
					'ViewHelper',
 					'Description',
					'Errors',
					array(array('data'=>'HtmlTag'), array('tag' => 'td')),
					array('Label', array('tag' => 'td')),
					array(array('row'=>'HtmlTag'),array('tag'=>'tr', 'closeOnly'=>true))

        ));
        
               
               
       	
        $this->addElements(
        array($titlebi, $titlein,$mesgbi, $mesgin));
        
        //button
		$this->addElement('submit', 'save', array(
          'label'=>$this->getView()->translate('submit'),
          'decorators'=>array(
	          'ViewHelper',
               'Description',
               'Errors', 
               array(array('data'=>'HtmlTag'), array('tag' => 'td',
               'align'=>'left', 'colspan'=>'2')),
               array(array('emptyrow'=>'HtmlTag'), array('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'tag'=>'td')),
               array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
               )
        ));
        
        
        $this->setDecorators(array(

               'FormElements',

               array(array('data'=>'HtmlTag'),array('tag'=>'table')),

               'Form'

  

       ));
    }
}