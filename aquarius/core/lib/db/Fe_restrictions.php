<?php
/**
 * Table Definition for fe_restrictions
 */

class db_Fe_restrictions extends DB_DataObject 
{
    ###START_AUTOCODE
    /* the code below is auto generated do not remove the above tag */

    public $__table = 'fe_restrictions';                 // table name
    public $node_id;                         // int(11)  not_null primary_key
    public $group_id;                        // int(11)  not_null primary_key

    /* Static get */
    function staticGet($k,$v=NULL) { return DB_DataObject::staticGet('db_Fe_restrictions',$k,$v); }

    /* the code above is auto generated do not remove the tag below */
    ###END_AUTOCODE
}
