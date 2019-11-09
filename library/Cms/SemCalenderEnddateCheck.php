<?php
class Cms_SemCalenderEnddateCheck extends Zend_Validate_Abstract {
    const NOT_MATCH = 'notMatch';


    public $startDate ;
    public $endDate ;

    public function __construct(){
        if(isset($_POST['Save']) ){         
          $post = Zend_Controller_Front::getInstance()->getRequest()->getPost();
          //$ret = GeneralSetup_Model_DbTable_Semester::getsemDet($post['IdSemester']);
          //$this->startDate = date('d/m/y',strtotime($ret[0]['SemesterStartDate']));
          //$this->endDate = date('d/m/y',strtotime($ret[0]['SemesterEndDate']));
          
          $idSem = explode('_',$post['IdSemester']);
          if($idSem['1']=='detail') { 
            $ret = GeneralSetup_Model_DbTable_Semester::getsemDet($idSem['0']);
            $this->startDate = date('d/m/y',strtotime($ret[0]['SemesterStartDate']));
            $this->endDate = date('d/m/y',strtotime($ret[0]['SemesterEndDate']));
          } else if ($idSem['1']=='main') {
            $ret = GeneralSetup_Model_DbTable_Semester::getsemMainDet($idSem['0']);
            $this->startDate = date('d/m/y',strtotime($ret[0]['SemesterMainStartDate']));
            $this->endDate = date('d/m/y',strtotime($ret[0]['SemesterMainEndDate']));
          }
          
          
        }
    }

    protected $_messageVariables = array(
        'start' => 'startDate',
        'end' => 'endDate'
    );

    protected $_messageTemplates = array(
        self::NOT_MATCH => "End date should be lies between semester start date (%start%) and semester end date (%end%)",
    );

    public function isValid($value, $context = null) {
      if(GeneralSetup_Model_DbTable_Semester::checkstartdate($value,Zend_Controller_Front::getInstance()->getRequest()->getPost())){
        return true;
      }else{
        $this->_error(self::NOT_MATCH);
        return false;
      }

    }

}

?>