<?php
class Finance_InvoiceController extends Zend_Controller_Action {
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		//title
    	$this->view->title="Invoice";
    	
    	//paginator
    	
		$invDB = new App_Model_Finance_DbTable_Invoice();
		$invData = $invDB->getPaginateData();
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbSelect($invData));
		$paginator->setItemCountPerPage(50);
		$paginator->setCurrentPageNumber($this->_getParam('page',1));
		
		$this->view->paginator = $paginator;
		
		$this->view->page = $this->_getParam('page',1);
		
		$this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
		$msg = $this->_flashMessenger->getMessages();
		if($msg!=null){
			$this->view->noticeMessage = $msg[0];
		}
		
	}
	
	public function generateInvoiceAction()
    {
    	//title
    	$this->view->title="Generate Invoice";
    	echo "Generate Invoice";
    	
    	$invDB = new App_Model_Finance_DbTable_Invoice();
    	$invData = $invDB->getDataGenerate();
    	exit;
    	echo "
    	<table width='100%' border='1'>
    	<td>No</td>
			 	<td>idApplication</td>
			 	<td>Name</td>
			    <td>IC</td>
			    <td>regid</td>
			    <td>schedule</td>
			    <td>mode</td>
			    <td>invoice</td>
			    </tr>";
    	$i=1;
    	foreach ($invData as $data){
    		
    		//programcode-coursecode-idregistration-runningnumber
    		//TBE-02-01-000001
    		$programDB = new App_Model_Record_DbTable_Program();
    		$programData = $programDB->getData($data['idProgram']);
    		$programCode = $programData['code'];
    		
    		$courseDB = new App_Model_Record_DbTable_Course();
    		$courseData = $courseDB->getData($data['idCourse']);
    		$courseCode = $courseData['code'];
    		
    		$latestInv = $invDB->getLastInvoice();
    		
    		if($latestInv){
    			$currentNum = $latestInv['runningNumber'];
    		}
    		
    		
    		
    		$currentNum = $currentNum+1;
    		
			$runningNumber = str_pad($currentNum,6,'0',STR_PAD_LEFT);
    		
    		$invoiceFormat = $programCode."-".$courseCode."-".$data['id']."-".$runningNumber;
    		
    	 	echo " <tr valign='top'>
			 	<td>$i)</td>
			 	<td>$data[id] + $currentNum</td>
			 	<td>$data[ARD_NAME]</td>
			    <td>$data[ARD_IC]</td>
			    <td>$data[regId]</td>
			    <td>$data[idSchedule]</td>
			    <td>$data[paymentMode]</td>
			    <td>$invoiceFormat</td>
			    </tr>
			    ";
    	 	
    	 	$date = date('Y-m-d H:i:s');
    	 	
    	 	$dataInsert = array(
	    	 	'idApplication' => $data['id'],
	    	 	'idClienttype' => 1,
	    	 	'paymentmode' => $data['paymentMode'],
	    	 	'receiptNo' => $invoiceFormat,
	    	 	'runningNumber' => $currentNum,
	    	 	'txnAmount' => $data['amount'],
	    	 	'txnCurrency' => $data['currency'],
	    	 	'txnDate' => $date,
	    	 	'txnStatus' => $data['paymentStatus'],
    	 	
    	 	);
    	 	
    	 	$insert = $invDB->addData($dataInsert);
    	 	
    	 $i++;
//    	 $currentNum = $currentNum+1;
    	}
    	
    	
    	exit;
    	
    }
    
	public function updateAction(){
		//title
    	$this->view->title="Update Payment Details";
    	
    	//redirect
		$this->_redirect($this->view->url(array('module'=>'finance','controller'=>'individual', 'action'=>'index'),'default',true));		
    	
    }
    
	public function deleteTypeAction($id = null){
    	$id = $this->_getParam('id', 0);
    	
    	if($id>0){
    		$type = new Finance_Model_DbTable_Paymentmode();
    		$type->deleteData($id);
    	}
    		
    	$this->_redirect($this->view->url(array('module'=>'general-setup','controller'=>'takaful', 'action'=>'index'),'default',true));
    	
    }
    
}

