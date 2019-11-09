<?php
class Cms_SemesterNameCheck extends Zend_Validate_Abstract {
    const NOT_MATCH = 'notMatch';

    protected $_messageTemplates = array(
        self::NOT_MATCH => 'Semester Name already exist'
    );

    public function isValid($value, $context = null) {
        $value = (string) $value;
        $validator = new Zend_Validate_Db_RecordExists(
                        array(
                            'table' => 'tbl_semestermaster',
                            'field' => 'SemesterMainName'
                        )
        );
        if ($validator->isValid($value)) {
            $this->_error(self::NOT_MATCH);
            return false;
        } else {
            return true;
        }
    }

}

?>
