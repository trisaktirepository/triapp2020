<?php
class App_Model_General_DbTable_Bankdetails extends Zend_Db_Table { //Model Class for Bank Details
	protected $_name = 'tbl_bankdetails';
	private $lobjDbAdpt;
    
public function init()
{
	    $this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
}
// Function to get all the details of Bank Details
public function fnGetBankDetails()
{
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()		 				 
								 ->from(array("a"=>"tbl_bankdetails"),array("a.*"))
								 ->join(array("b" => "tbl_definationms"),"a.AccountType=b.idDefinition",array("b.DefinitionDesc"))
								 ->join(array("c" => "tbl_bank"),"a.IdBank=c.IdBank",array("c.BankName"))
		 				 		 ->where("a.Active = 1");
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		
		return $larrResult;
}   
// Function to get all the Bank Names
public function fngetBankNameList()
{
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()		 				 
								 ->from(array("a"=>"tbl_bank"),array("key"=>"a.IdBank","value"=>"a.BankName"));
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		return $larrResult;
}
// Function to get all the Account Types
public function fngetBankAccountTypeList()
{
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()
								 ->from(array("a"=>"tbl_definationms"),array("key"=>"a.idDefinition","value"=>"a.DefinitionDesc"))
								 ->join(array("b"=>"tbl_definationtypems"),"a.idDefType = b.idDefType AND defTypeDesc='Account Type'",array());
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		
		return $larrResult;
}	
// Function to Search all the details of Bank Details
public function fnSearchBankDetails($post = array()) 
{ 
		$lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
		$lstrSelect = $lobjDbAdpt->select()		 				 
								 ->from(array("a"=>"tbl_bankdetails"),array("a.*"))
								 ->join(array("b" => "tbl_definationms"),"a.AccountType=b.idDefinition",array("b.DefinitionDesc"))
								 ->join(array("c" => "tbl_bank"),"a.IdBank=c.IdBank",array("c.BankName"));
		if(isset($post['field5']) && !empty($post['field5']) ){
				$lstrSelect = $lstrSelect->where("a.IdBank = ?",$post['field5']);
		}	
		if(isset($post['field8']) && !empty($post['field8']) ){
				$lstrSelect = $lstrSelect->where("a.AccountType = ?",$post['field8']);
		}		
		$lstrSelect	->where('a.AccountNumber like "%" ? "%"',$post['field3'])
       				->where("a.Active = ".$post["field7"]);				
		$larrResult = $lobjDbAdpt->fetchAll($lstrSelect);
		      
		return $larrResult;
}	
// Function to add Bank Details
public function fnaddBankDetails($formData) 
{
   	$this->insert($formData);
} 	    
// Function to View the Bank Details	
public function fnViewBankDetails($IdAccount) 
{
    	$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
				->from(array('a' => 'tbl_bankdetails'),array('a.*'))
                ->where('a.IdAccount = '.$IdAccount);
               
		$result = $db->fetchRow($select);	
		return $result;
}
 // Function to update Bank Details   
public function fnupdateBankDetails($formData,$lintIdAccount) 
{ 
    	unset ( $formData ['Save'] );
    	$where = 'IdAccount = '.$lintIdAccount;
		$this->update($formData,$where);
}   	
}