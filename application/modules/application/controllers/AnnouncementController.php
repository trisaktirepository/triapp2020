<?php
class Application_AnnouncementController extends Zend_Controller_Action
{

	public function indexAction(){
	
		$this->view->title = $this->view->translate("Announcement"); 
		
		$form = new Application_Form_Announcement();
		
		
		$ann = new Application_Model_DbTable_Announcement();
		
		
		if ($this->getRequest ()->isPost ()) {
			if ($form->isValid ( $_POST )) {
				$data = $form->getValues ();
				
				$info['sl_english']=$data["title_bi"];
				$info['sl_bahasa']=$data["title_in"];
				
				$ann->modify($info,2);
				
				$info2['sl_english']=$data["mesg_bi"];
				$info2['sl_bahasa']=$data["mesg_in"];
				
				$ann->modify($info2,1);
			
					

			}
		} else {
			$rs=$ann->fetch(2);
			$info['title_bi']=$rs["sl_english"];
			$info['title_in']=$rs["sl_bahasa"];
			$rs=$ann->fetch(1);
			$info['mesg_bi']=$rs["sl_english"];
			$info['mesg_in']=$rs["sl_bahasa"];
			
				
			$form->populate ( $info );
			
		}
		
		$this->view->form = $form;
		
    	
	} 
	
	
}
?>