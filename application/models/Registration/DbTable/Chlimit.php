<?php 
class App_Model_Registration_DbTable_Chlimit extends Zend_Db_Table_Abstract {
	protected $_name = 'tbl_chlimit_head';
	protected $_primary = "clid";

	function isLimitset($progid,$intake){
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$select = $db ->select()
					  ->from(array('ch'=>$this->_name))
					  ->where('progid = ?',$progid)
					  ->where('intakeid = ?',$intake);
		$row = $db->fetchRow($select);

		if($row){
			return $row;
		}else{
			return false;
		}
	}
	
	
	function getLimit($progid,$intake,$gpa){
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$select = $db ->select()
					  ->from(array('ch'=>$this->_name))
					  ->join(array('pr'=>'tbl_program'),'ch.progid=pr.IdProgram')
					  ->where('progid = ?',$progid)
					  ->where('intakeid = ?',$intake);
		$row = $db->fetchRow($select);

		if($row && $row['paket']=='0'){
			//get limit
			$limit=$this->checklimit($row['clid'], $gpa);
			return $limit;
		} else return 24;
	}
	
	
	
	function checklimit($clid,$cgpa){
		$db = Zend_Db_Table::getDefaultAdapter();		
		
		$select = $db ->select() 
				  	  ->from('tbl_chlimit_detl')
				  	  ->where('clid = ?',$clid)
				  	  ->where('rstart <= ?',$cgpa)
				  	  ->where('rend >= ?',$cgpa);
		$row = $db->fetchrow($select);
		return $row["chlimit"];
	}
}
?>