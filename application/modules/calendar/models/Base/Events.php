<?php

/**
 * Calendar_Model_Base_Events
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $domain_id
 * @property string $title
 * @property string $description
 * @property timestamp $start
 * @property timestamp $end
 * @property boolean $allday
 * @property integer $group_id
 * @property string $location
 * @property Default_Model_Domain $Domain
 * @property Default_Model_Group $Group
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class Calendar_Model_Base_Events extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('events');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'unsigned' => true,
             'primary' => true,
             'autoincrement' => true,
             'length' => '4',
             ));
        $this->hasColumn('domain_id', 'integer', 4, array(
             'type' => 'integer',
             'unsigned' => true,
             'length' => '4',
             ));
        $this->hasColumn('title', 'string', 255, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '255',
             ));
        $this->hasColumn('description', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('start', 'timestamp', null, array(
             'type' => 'timestamp',
             'notnull' => true,
             ));
        $this->hasColumn('end', 'timestamp', null, array(
             'type' => 'timestamp',
             ));
        $this->hasColumn('allday', 'boolean', null, array(
             'type' => 'boolean',
             ));
        $this->hasColumn('group_id', 'integer', 4, array(
             'type' => 'integer',
             'unsigned' => true,
             'length' => '4',
             ));
        $this->hasColumn('location', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));

        $this->option('type', 'InnoDB');
        $this->option('collate', 'utf8_unicode_ci');
        $this->option('charset', 'utf8');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Default_Model_Domain as Domain', array(
             'local' => 'domain_id',
             'foreign' => 'id'));

        $this->hasOne('Default_Model_Group as Group', array(
             'local' => 'group_id',
             'foreign' => 'id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}