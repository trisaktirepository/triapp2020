<?
class App_Form_ForgotPassword extends Zend_Form {
		
	public function init(){
		$this->setName('form_applicant_forgot_password');
		$this->setMethod('post');
		$this->setAttrib('id','form_applicant_forgot_password');;
		
		$this->addElement('text','appl_email', array(
			'label'=>$this->getView()->translate('email'),
			'required'=>true
		));
		
		$this->addElement('date','appl_dob', array(
			'label'=>$this->getView()->translate('dob'),
			'required'=>true,
			'startYear'=>date('Y')-63,
			'stopYear'=>date('Y')
		));
		
		$captcha_element = new Zend_Form_Element_Captcha(
				'captcha',
				array('label' => 'Verifikasi Foto',
						'captcha' => array(
								'captcha' => 'Image',
								'wordLen' => 4,
								'timeout' => 300,
								'font' => APPLICATION_PATH."/font/ARIAL.TTF",
								'imgDir' => CAPCHA_PATH,
								'imgUrl' => '/capcha/'
						)
				)
		);
		
		$this->addElement($captcha_element);
		
		//button
		$this->addElement('submit', 'save', array(
          'label'=>$this->getView()->translate('submit'),
          'decorators'=>array('ViewHelper')
        ));
        
        
        $this->addElement('submit', 'cancel', array(
          'label'=>$this->getView()->translate('cancel'),
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'default', 'controller'=>'online-application','action'=>'index'),'default',true) . "'; return false;"
        ));
		
	}
}
?>