<?php

class Calendar_IcalController extends Zend_Controller_Action
{

    public function init()
    {

    }

    public function indexAction()
    {

    


    }

    /**
     * Fetch events in ical format
     * 
     */
    public function getAction()
    {
        $this->_helper->layout->disableLayout();

        $ids = $this->_getParam('ids');


        $ids = explode(":", $ids);

        $start = mktime(0, 0, 0, date('n'), 0, date('Y'));
        $end = strtotime("+1 year", $start);

        $data = Calendar_Model_Events::getIcalEvents($ids, $start, $end);

        $this->view->data = $data;

        $this->getResponse()
                ->setHttpResponseCode(200)
                ->setHeader('Content-type', 'text/calendar', true)
                ;

    }

}

