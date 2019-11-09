<?php

class Counseling_Model_IssueType extends Zend_Db_Table
{

    protected $_name = 'counseling_issue_types';
    protected $_primary = 'id';
    
    public function getIssueType() {
    	$db = Zend_Db_Table::getDefaultAdapter();
    	
    	$select = $db->select()
    	->from(array('i'=>'counseling_issue_types'));
    	  
    	    	
    	$issues = $db->fetchAll($select);
    	return ($issues);
    }

}
