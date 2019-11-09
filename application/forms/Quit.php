<?
class App_Form_Quit extends Zend_Form {
	
	public function init(){
		
		$this->setName('quit');
		$this->setMethod('post');
		$this->setAttrib('id','quit_form');
		$this->setAttrib('enctype', 'multipart/form-data');
		
		$auth = Zend_Auth::getInstance();
		
		
		
		$this->addElement('hidden','transaction_id',array('value'=>$auth->getIdentity()->transaction_id));
		
		$this->addElement('select','aq_reason', array(
			'label'=>'Reason for QUITTING'
		));		
		
		$definationDb = new App_Model_General_DbTable_Definationms();
		
		$this->aq_reason->addMultiOption(0,'please select');
		foreach ($definationDb->getDataByType(86) as $list){
			$this->aq_reason->addMultiOption($list['idDefinition'],$list['BahasaIndonesia']);
		}
		
		$this->addElement('text','aq_authorised_personnel', array(
			'label'=>$this->getView()->translate('Authorised Personnel'),
			'required'=>true,
		    'size'=>40));
		
		
		
		$this->addElement('select','aq_relationship', array(
			'label'=>'Relationship'
		));	
		
		$setupDb = new App_Model_General_DbTable_Setup();
		
		$this->aq_relationship->addMultiOption(0,'please select');
		foreach ($setupDb->getData('RELATION') as $list){
			$this->aq_relationship->addMultiOption($list['ssd_id'],$list['ssd_name_bahasa']);
		}
		
		$this->addElement('select','aq_identity_type', array(
			'label'=>'Identity Type'
		));		
		
		
		$this->aq_identity_type->addMultiOption(0,'please select');
		foreach ($definationDb->getDataByType(87) as $list){
			$this->aq_identity_type->addMultiOption($list['idDefinition'],$list['BahasaIndonesia']);
		}
		
		$this->addElement('text','aq_identity_no', array(
			'label'=>$this->getView()->translate('Identity No'),
			'required'=>true));	
		
		$this->addElement('textarea','aq_address', array(
			'label'=>$this->getView()->translate('address'),
			'required'=>true,
		    'COLS'=>'20',
		    'ROWS'=>'4'
		));	
		
		$this->addElement('hidden', 'test', array(
		    'description' => '<h4>'.$this->getView()->translate('Required Documents').'</h4>',
		    'ignore' => true,
		    'decorators' => array(
		        array('Description', array('escape'=>false, 'tag'=>'')),
		    ),
		));

	   /*
		$this->addElement('file','aq_document_kartu', array(
			'label'=>$this->getView()->translate('Kartu Peserta Ujian'),
			'required'=>true,
		));	
		$this->aq_document_kartu->setValueDisabled(true);
		//$this->aq_document_kartu->setDestination(DOCUMENT_PATH);
		 * 
		 */
		
		
		$this->addElement('hidden', 'label1', array(
		    'description' => $this->getView()->translate('1.Slip Asli tanda pembayaran yang telah dilakukan'),
		    'ignore' => true,
		    'decorators' => array(
		        array('Description', array('escape'=>false, 'tag'=>'')),
		    ),
		));
				
		$this->addElement('file','aq_document_payment_slip', array(
			'label'=>$this->getView()->translate('File')));	
		$this->aq_document_payment_slip->setValueDisabled(true);
		
		
		$this->addElement('hidden', 'label2', array(
		    'description' => $this->getView()->translate('2.Tanda Pengenal Asli (SIM/KTP)'),
		    'ignore' => true,
		    'decorators' => array(
		        array('Description', array('escape'=>false, 'tag'=>'')),
		    ),
		));
		
		$this->addElement('file','aq_document_identity_card', array(
			'label'=>$this->getView()->translate('File')
			));	
		
		
		$this->addElement('hidden', 'label3', array(
		    'description' => $this->getView()->translate('3.Surat Pernyataan Kesediaan Memenuhi Kewajiban Administrasi Keuangan, Tatatertib, sanksi-sanksi Mahasiswa Baru'),
		    'ignore' => true,
		    'decorators' => array(
		        array('Description', array('escape'=>false, 'tag'=>'')),
		    ),
		));
		
		$this->addElement('file','aq_document_surat_pernyataan', array(
			'label'=>$this->getView()->translate('File')));	
		
		
		//button
		$this->addElement('submit', 'save', array(
          'label'=>'submit',
          'decorators'=>array('ViewHelper')
        ));
        
        $this->addElement('submit', 'cancel', array(
          'label'=>'cancel',
          'decorators'=>array('ViewHelper'),
          'onClick'=>"window.location ='" . $this->getView()->url(array('module'=>'default', 'controller'=>'quit','action'=>'index'),'default',true) . "'; return false;"
        ));
		
	}
	
}