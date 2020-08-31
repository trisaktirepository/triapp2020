<?php

class Application_Model_DbTable_ProformaInvoiceVa extends Zend_Db_Table {

	protected $_name = 'proforma_invoice_va';

	public function init() {
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
  	}

	public function getData($id=""){
		
		$db = $this->lobjDbAdpt;
		
		$select = $db ->select()
					  ->from($this->_name)
					  ->order("id desc");
					  
		if($id)	{			
			 $select->where("id ='".$id."'");
			 $row = $db->fetchRow($select);				 
		}else{
			 $row = $db->fetchAll($select);			
		}	 
		
		if($row){
			return $row;
		}else{
			return null;
		}
		
	}
	public function isIn($billnumber,$applid=null){
	
		$db = $this->lobjDbAdpt;
	
		$select = $db ->select()
		->from($this->_name) 
		->where("bill_number =?",$billnumber);
		if ($applid!=null) $select->where('appl_id=?',$applid);
		
		return $db->fetchRow($select);
	
	}
	
	public function isInByTrx($trx){
	
		$db = $this->lobjDbAdpt;
	
		$select = $db ->select()
		->from(array('a'=>$this->_name))
		->join(array('b'=>'applicant_transaction'),'a.appl_id=b.at_appl_id')
		
		->where("at_trans_id =?",$trx); 
	
		return $db->fetchRow($select);
	
	}
	
	public function isInByNoForm($noform){
	
		$db = $this->lobjDbAdpt;
	
		$select = $db ->select()
		->from(array('a'=>$this->_name))
		//->join(array('b'=>'applicant_transaction'),'a.appl_id=b.at_appl_id')
	
		->where("no_fomulir =?",$noform);
	
		return $db->fetchRow($select);
	
	}
	
	public function getDataByVa($va){
	
		$db = $this->lobjDbAdpt;
	
		$select = $db ->select()
		->from($this->_name)
		->where("va =?",$va); 
		return $db->fetchRow($select);
	
	}
	
	public function getDataByNoForm($noform,$paket){
	
		$db = $this->lobjDbAdpt;
	
		$select = $db ->select()
		->from($this->_name)
		->where("no_fomulir =?",$noform)
		->where("MID(bill_number,1,1) =?",$paket);
		return $db->fetchRow($select);
	
	}
	
	public function updateData($data,$id){
		$id = $this->update($data,'id='.$id);
		return $id;
	}
	
	public function updateDataByBilling($data,$billing){
		$id = $this->update($data,"bill_number='".$billing."'");
		return $id;
	}
	
	public function addData($data){
		$id = $this->insert($data);
		return $id;
	}
	
	public function getDataByNomor($nomor=""){
	
		$db = $this->lobjDbAdpt;
	
		$select = $db ->select()
		->from(array('a'=>$this->_name))
		->join(array('at'=>'applicant_transaction'),'a.appl_id=at.at_appl_id')
		->join(array('as'=>'applicant_assessment'),'at.at_trans_id=as.aar_trans_id')
		->join(array('asd'=>'applicant_selection_detl'),'asd.asd_id=as.aar_rector_selectionid')
		->where("asd.asd_nomor=?",$nomor);
		 
		$row=$db->fetchAll($select);
		if (!$row) {
			//USM
			$select = $db ->select()
			->from(array('a'=>$this->_name))
			->join(array('at'=>'applicant_transaction'),'a.appl_id=at.at_appl_id')
			->join(array('as'=>'applicant_assessment_usm'),'at.at_trans_id=as.aau_trans_id')
			->join(array('aaud'=>'applicant_assessment_usm_detl'),'aaud.aaud_id=as.aau_rector_selectionid')
			->where("aaud.aaud_nomor=?",$nomor);
			
			$row=$db->fetchAll($select);
		}
		
			return $row;
	}
	
	 
}