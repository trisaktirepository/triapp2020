<?php

class Calendar_EventController extends Zend_Controller_Action
{

    public function init()
    {
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('change', 'json')
                    ->addActionContext('add', array('html', 'json'))
                    ->addActionContext('edit', array('html', 'json'))
                    ->addActionContext('delete', array('html', 'json'))
                    ->addActionContext('detail', 'html')
                            ->initContext();
    }

    public function indexAction()
    {   


    }

    /**
     * Add an event to the calendar
     *
     * @return NULL
     */
    public function addAction()
    {
        $form = new Calendar_Form_Event('addevent');

        // Get the post data
        if ($this->_request->isPost()) {
            $data = $this->_request->getPost();

            // If valid create a new event
            if ($form->isValid($data)) {
                $event = new Calendar_Model_Events();
                $event->addEvent($data);
                $this->view->message = "Record added... thanks";
                $this->view->start = date('c', strtotime($event->start));
                $this->view->title = $event->title;
                $this->view->id = $event->id;
                return;
            }

            // Form was not valid
            $this->view->message = "Sorry... your event was not added";
            
            return;
        }
        $form->from->setValue($this->_getParam('date', NULL));
        $form->to->setValue($this->_getParam('date', NULL));
        $form->setAction('/calendar/event/add/format/json');
        $this->view->form = $form;
        


    }

    /**
     * Edit an event on the calendar
     *
     * @return NULL
     */
    public function editAction()
    {
        $event_id = $this->_getParam('id');
        $event = Calendar_Model_Events::getEvent($event_id);
        
        $form = new Calendar_Form_Event('updateevent');

        if ($this->_request->isPost()) {
            $data = $this->_request->getPost();

            if ($form->isValid($data)) {
                $event = Doctrine_Core::getTable('Calendar_Model_Events')->find($event_id);

                if ($event instanceof Calendar_Model_Events) {

                    try {
                        // Check if they have permissions to edit event
                        if (!$event->isAllowed('edit')) {
                            throw new Exception('Not allowed');
                        }

                        // Pass to model to edit the event
                        $event->updateEvent($data);

                        $this->view->message = "Event updated... thanks";
                        $this->view->event = $event->toArray();
                    } catch (Exception $exc) {
                        Zend_Registry::get('log')->debug(__FILE__ . ':' . __LINE__ . ' ' . $exc->getMessage());
                        $this->view->message = "Sorry... your event wasn't updated";
                    }
                    return;
                }
            }

            $this->view->message = "Sorry... your event wasn't updated";
            

        }

        $event = $event->toArray();
        $event['from'] = date('Y-m-d', strtotime($event['start']));
        $event['from_time'] = date('g:i A', strtotime($event['start']));

        $event['to'] = date('Y-m-d', strtotime($event['end']));
        $event['to_time'] = date('g:i A', strtotime($event['end']));

        $form->populate($event);
        $form->setAction('/calendar/event/edit/format/json/id/' . $event_id);
        $this->view->form = $form;
        
    }

    /**
     * Get a range of events for a group
     *
     * @return NULL
     */
    public function getAction()
    {

        $this->_helper->layout->disableLayout();

        $start      = $this->_getParam('start', NULL);
        $end        = $this->_getParam('end', NULL);
        $domain     = Zend_Registry::get('domain');
        $group_id      = $this->_getParam('groupid');

        // Find a group based on it's id
        $group = Doctrine_Core::getTable('Default_Model_Group')->find($group_id);

        if (!$group instanceof Default_Model_Group) {
            return;
        }

        $this->view->dates = Calendar_Model_Events::getEvents($group, $domain['id'], $start, $end);
        

    }

    /**
     * Doesn't do anything right now
     */
    public function eventAction()
    {
        $id = $this->_getParam('id');
        print $id;
        exit;
    }

    /**
     * Action when an event is dragged or resized in the interface
     *
     */
    public function changeAction()
    {
        $this->_helper->layout->disableLayout();

        $event_id       = $this->_getParam('event_id', NULL);

        if (NULL === $event_id) {
            exit;
        }

        // The difference in days the event has moved
        $daydelta       = $this->_getParam('daydelta');

        // The difference in minutes the event has moved
        $minutedelta    = $this->_getParam('minutedelta');

        // If this is a move or an extentions of the end time
        $type           = $this->_getParam('type');

        
        $event = Doctrine_Core::getTable('Calendar_Model_Events')->find($event_id);
        
        if (!$event instanceof Calendar_Model_Events) {
            // Should probably send warning back to client
            exit;
        }

        if ("move" == $type) {
            $event->start = $this->_getNewTime($daydelta, $minutedelta, $event->start);
        }
        $event->end = $this->_getNewTime($daydelta, $minutedelta, $event->end);

        $event->save();
        
    }

    /**
     * Get details about a specific event
     */
    public function detailAction()
    {
        $event_id = $this->_getParam('id', NULL);
        if (NULL === $event_id) {
            exit;
        }

        $this->view->event = Calendar_Model_Events::getEvent($event_id);


    }

    /**
     * Deletes a specific event
     *
     * @return NULL
     */
    public function deleteAction()
    {
        // Get id
        $id = $this->_getParam('id');
        $result = 0;
        // make sure it's a post
        if (!$this->_request->isPost()) {
            return;
        }
         
        // Get event
        $event = Calendar_Model_Events::getEvent($id);

        // If isAllowed... Delete
        if ($event->isAllowed('delete')) {
            $event->delete();
            $result = 1;
            $this->view->message = "Event removed";
        } else {
            $this->view->message = "Hmmm, that didn't work...";
        }
        $this->view->result = $result;
        
    }

    /**
     * Calculate a new date string given an offset in days or minutes
     *
     * @param int $day
     * @param int $minute
     * @param string $original
     * @return string ISO 8601 date
     */
    private function _getNewTime($day, $minute, $original) {

        $bearing = '';

        $day = ( 0 > $day ) ? $day : "+" . $day;
        $minute = ( 0 > $minute ) ? $minute : "+" . $minute;

        if (0 != $day) {
            $bearing .= $day . " days ";
        }
        if (0 != $minute) {
            $bearing .= $minute . " minutes";
        }
        Zend_Registry::get('log')->debug('Bearing = ' . $bearing . " Original = " . $original);
        return date('c', strtotime($bearing, strtotime($original)));
    }






}

