<?php
class App_Model_General_DbTable_Bank extends Zend_Db_Table { 	
	protected $_name = 'tbl_bank'; // table name
	
	/*
	 * fetch all  Active Bank details
	 */
    public function fnGetBankDetails($id=null) {
		$select = $this->select()
			->setIntegrityCheck(false)  	
			->join(array('a' => 'tbl_bank'),array('IdBank'))			
			->where("Active = 1");
		if ($id!=null) {
			$select->where('IdBank=?',$id);
			$result = $this->fetchRow($select);
		} else
			$result = $this->fetchAll($select);
		
		return $result->toArray();    	  
    }
    
	public function fnGetBankList(){
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
		 				 ->from(array("a"=>"tbl_bank"),array("key"=>"a.IdBank","value"=>"a.BankName"))
		 				 ->where("a.Active = 1")
		 				 ->order("a.BankName");
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
	}

    /*
     * search method
     */
	public function fnSearchbank($post = array()) {
		    $db = Zend_Db_Table::getDefaultAdapter();
		    $field7 = "Active = ".$post["field7"];
		    $select = $this->select()
			->setIntegrityCheck(false)  	
			->join(array('a' => 'tbl_bank'),array('IdBank'))
			->where("a.BankName LIKE '%".$post['field3']."%'")
			->where("a.Email LIKE '%".$post['field2']."%'")
			->where($field7);
		$result = $this->fetchAll($select);
		
		return $result->toArray();
	}
	
	/*
	 * add bank row
	 */
	public function fnAddBank($post) {		
		if($post['Country']== "") {
				$post['Country']='0';
		} 
		if($post['State']== "") {
				$post['State']='0';
		} 
		
		$post['Phone'] = $post['Phonecountrycode']."-".$post['Phonestatecode']."-".$post['Phone'];
		unset($post['Phonecountrycode']);
		unset($post['Phonestatecode']);
		
		$post['Fax'] = $post['faxcountrycode']."-".$post['faxstatecode']."-".$post['Fax'];
		unset($post['faxcountrycode']);
		unset($post['faxstatecode']);
		
		$post['ContactPhone'] = $post['ContactPhonecountrycode']."-".$post['ContactPhonestatecode']."-".$post['ContactPhone'];
		unset($post['ContactPhonecountrycode']);
		unset($post['ContactPhonestatecode']);
		
		$post['ContactCell'] = $post['countrycode']."-".$post['ContactCell'];
		unset($post['countrycode']);
		
						
		$this->insert($post);
	}
	
	/*
	 * fetch row by id 
	 */
    public function fnViewBank($lintIdBank) {
    	$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
				->from(array('a' => 'tbl_bank'),array('a.*'))
                        ->where('a.IdBank = '.$lintIdBank);
		$result = $db->fetchRow($select);	
		return $result;
    }
    
    /*
     * update bank row
     */
    
     public function fnupdateCountrymaster($lintidCountry,$larrformData) { 
    	$where = 'idCountry = '.$lintidCountry;
		$this->update($larrformData,$where);
    } 
    
    public function fnUpdateBank($lintIdBank, $formData) {
    	
    	if($formData['Country']== "") {
				$post['Country']='0';
		} 
		if($formData['State']== "") {
				$post['State']='0';
		} 
		
		$formData['Phone'] = $formData['Phonecountrycode']."-".$formData['Phonestatecode']."-".$post['Phone'];
		unset($formData['Phonecountrycode']);
		unset($formData['Phonestatecode']);
		
		$formData['Fax'] = $formData['faxcountrycode']."-".$formData['faxstatecode']."-".$formData['Fax'];
		unset($formData['faxcountrycode']);
		unset($formData['faxstatecode']);
		
		$formData['ContactPhone'] = $formData['ContactPhonecountrycode']."-".$formData['ContactPhonestatecode']."-".$formData['ContactPhone'];
		unset($formData['ContactPhonecountrycode']);
		unset($formData['ContactPhonestatecode']);
		
		$formData['ContactCell'] = $formData['countrycode']."-".$formData['ContactCell'];
		unset($formData['countrycode']);
		
		$where = 'IdBank = '.$lintIdBank;
		$this->update($formData,$where);
    }
}
