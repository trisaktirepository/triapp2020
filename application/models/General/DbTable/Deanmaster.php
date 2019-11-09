<?php 
class App_Model_General_DbTable_Deanmaster extends Zend_Db_Table_Abstract
{
    protected $_name = 'tbl_deanlist';
    private $lobjDbAdpt;
    
	public function init()
	{
		$this->lobjDbAdpt = Zend_Db_Table::getDefaultAdapter();
	}    
	
	public function fnaddDean($result,$larrformData) { //Function for adding the University details to the table
		$data = array('IdCollege'=>$result,
					  'IdStaff'=>$larrformData['IdStaff'],
		  			  'FromDate'=>$larrformData['FromDate'],
		 			  'ToDate'=>$larrformData['FromDate'],
					  'Active'=>$larrformData['Active'],
					  'UpdDate'=>$larrformData['UpdDate'],
					  'UpdUser'=>$larrformData['UpdUser']);
		$this->insert($data);
	}
	
	public function fnupdateDeanList($formData,$lintIdCollege)
	{

/*		$lstrselectsql = $this->lobjDbAdpt->select()
									->from(array('dean'=>'tbl_deanlist'),array('IdDeanList '=>'MAX(dean.IdDeanList )'))
									->where("dean.IdCollege = $lintIdCollege" );
		$larrresultset = $this->lobjDbAdpt->fetchRow($lstrselectsql);	
		if(!empty($larrresultset['IdDeanList']))
		{
	    	$larrdeanlist['ToDate'] = $formData['FromDate'];
	    	$lstrwhere = "IdDeanList = ".$larrresultset['IdDeanList'];*/
		
	    	$larrdeanlist['IdStaff'] = $formData['IdStaff'];
	    	$larrdeanlist['FromDate'] = $formData['FromDate'];
	    	$larrdeanlist['ToDate'] = $formData['FromDate'];
	    	$larrdeanlist['Active'] = $formData['Active'];
	    	$larrdeanlist['UpdDate'] = $formData['UpdDate'];
	    	$larrdeanlist['UpdUser'] = $formData['UpdUser'];
			$lstrwhere = "IdCollege = $lintIdCollege";
			$this->lobjDbAdpt->update('tbl_deanlist',$larrdeanlist,$lstrwhere);
		//}
	}
	

	 public function getCollegeDean($IdCollege) { //Function for the view University
	
	 	$db = Zend_Db_Table::getDefaultAdapter();		
		
		$select = $db ->select()
					  ->from(array('d'=>$this->_name))
					  ->join(array('c'=>'tbl_collegemaster'), 'c.IdCollege = d.IdCollege')
					  ->join(array('s'=>'tbl_staffmaster'), 's.IdStaff=d.IdStaff',array('Fullname','FrontSalutation','BackSalutation','StaffId'))
					  ->where('c.IdCollege = ?',$IdCollege)
					  ->where('d.FromDate < now()');
							  
		 return $row = $db->fetchRow($select);	
    }
}
?>