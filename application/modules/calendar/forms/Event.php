<?php

/**
 * @copyright Restricted
 * @author Shane A. Stillwell *
 */

class Calendar_Form_Event extends Default_Form_Base_Form
{
    public function  __construct($formname = null, $options = null) {
        parent::__construct($options);

        if (NULL !== $formname) {
            $this->setName($formname);
        }
    }

    public function  init() {
        parent::init();

        $this->setMethod('POST');

        $title = new Zend_Form_Element_Text('title');
        $title->setLabel('Title')
                 ->setAttrib('class', 'autofocus')
                 ->setRequired()
                   ;

        $description = new Zend_Form_Element_Textarea('description');
        $description->setLabel('Description')
                    ->setAttrib('cols', 20)
                    ->setAttrib('rows', 4)
                    ->setAttrib('class', 'wysiwyg');


        $from = new Zend_Form_Element_Text('from');
        $from->setLabel("From")
            ->setAttrib('class', 'datepicker')
            ->addValidator('Date', false, array('format' => 'yyyy-MM-dd'));

        $time = $this->_getTimes();

        $from_time = new Zend_Form_Element_Select('from_time');
        $from_time->addMultiOptions($time)
                  ->setValue("6:30 PM")
                    ;

        $to = new Zend_Form_Element_Text('to');
        $to->setLabel("To")
            ->setAttrib('class', 'datepicker')
            ->addValidator('Date', false, array('format' => 'yyyy-MM-dd'));

        $to_time = new Zend_Form_Element_Select('to_time');
        $to_time->addMultiOptions($time)
                ->setValue("8:00 PM");

        $location = new Zend_Form_Element_Text('location');
        $location->setLabel('Location');


        $group_id = new Zend_Form_Element_Select('group_id', array(
                        'label'         => "Groups",
                        'multiOptions'  => Default_Model_Group::getGroupList('key_value')
                        ));

        $this->addElements(array($title, $from, $from_time, $to, $to_time, $location, $description, $group_id));
    }


    /**
     * Simply return an array with time in 15 minute increments
     * 
     * @return array
     */
    protected function _getTimes()
    {
        $time = array(
            "5:00 AM"  =>  "5:00 AM",
            "5:15 AM"  =>  "5:15 AM",
            "5:30 AM"  =>  "5:30 AM",
            "5:45 AM"  =>  "5:45 AM",

            "6:00 AM"  =>  "6:00 AM",
            "6:15 AM"  =>  "6:15 AM",
            "6:30 AM"  =>  "6:30 AM",
            "6:45 AM"  =>  "6:45 AM",

            "7:00 AM"  =>  "7:00 AM",
            "7:15 AM"  =>  "7:15 AM",
            "7:30 AM"  =>  "7:30 AM",
            "7:45 AM"  =>  "7:45 AM",

            "8:00 AM"  =>  "8:00 AM",
            "8:15 AM"  =>  "8:15 AM",
            "8:30 AM"  =>  "8:30 AM",
            "8:45 AM"  =>  "8:45 AM",

            "9:00 AM"  =>  "9:00 AM",
            "9:15 AM"  =>  "9:15 AM",
            "9:30 AM"  =>  "9:30 AM",
            "9:45 AM"  =>  "9:45 AM",

            "10:00 AM"  =>  "10:00 AM",
            "10:15 AM"  =>  "10:15 AM",
            "10:30 AM"  =>  "10:30 AM",
            "10:45 AM"  =>  "10:45 AM",

            "11:00 AM"  =>  "11:00 AM",
            "11:15 AM"  =>  "11:15 AM",
            "11:30 AM"  =>  "11:30 AM",
            "11:45 AM"  =>  "11:45 AM",

            "12:00 PM"  =>  "12:00 PM",
            "12:15 PM"  =>  "12:15 PM",
            "12:30 PM"  =>  "12:30 PM",
            "12:45 PM"  =>  "12:45 PM",

            "1:00 PM"  =>  "1:00 PM",
            "1:15 PM"  =>  "1:15 PM",
            "1:30 PM"  =>  "1:30 PM",
            "1:45 PM"  =>  "1:45 PM",

            "2:00 PM"  =>  "2:00 PM",
            "2:15 PM"  =>  "2:15 PM",
            "2:30 PM"  =>  "2:30 PM",
            "2:45 PM"  =>  "2:45 PM",

            "3:00 PM"  =>  "3:00 PM",
            "3:15 PM"  =>  "3:15 PM",
            "3:30 PM"  =>  "3:30 PM",
            "3:45 PM"  =>  "3:45 PM",

            "4:00 PM"  =>  "4:00 PM",
            "4:15 PM"  =>  "4:15 PM",
            "4:30 PM"  =>  "4:30 PM",
            "4:45 PM"  =>  "4:45 PM",

            "5:00 PM"  =>  "5:00 PM",
            "5:15 PM"  =>  "5:15 PM",
            "5:30 PM"  =>  "5:30 PM",
            "5:45 PM"  =>  "5:45 PM",

            "6:00 PM"  =>  "6:00 PM",
            "6:15 PM"  =>  "6:15 PM",
            "6:30 PM"  =>  "6:30 PM",
            "6:45 PM"  =>  "6:45 PM",

            "7:00 PM"  =>  "7:00 PM",
            "7:15 PM"  =>  "7:15 PM",
            "7:30 PM"  =>  "7:30 PM",
            "7:45 PM"  =>  "7:45 PM",

            "8:00 PM"  =>  "8:00 PM",
            "8:15 PM"  =>  "8:15 PM",
            "8:30 PM"  =>  "8:30 PM",
            "8:45 PM"  =>  "8:45 PM",

            "9:00 PM"  =>  "9:00 PM",
            "9:15 PM"  =>  "9:15 PM",
            "9:30 PM"  =>  "9:30 PM",
            "9:45 PM"  =>  "9:45 PM",

            "10:00 PM"  =>  "10:00 PM",
            "10:15 PM"  =>  "10:15 PM",
            "10:30 PM"  =>  "10:30 PM",
            "10:45 PM"  =>  "10:45 PM",

            "11:00 PM"  =>  "11:00 PM",
            "11:15 PM"  =>  "11:15 PM",
            "11:30 PM"  =>  "11:30 PM",
            "11:45 PM"  =>  "11:45 PM",
        );

        return $time;
    }

}