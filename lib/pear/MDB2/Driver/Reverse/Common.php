<?php
// +----------------------------------------------------------------------+
// | PHP versions 4 and 5                                                 |
// +----------------------------------------------------------------------+
// | Copyright (c) 1998-2006 Manuel Lemos, Tomas V.V.Cox,                 |
// | Stig. S. Bakken, Lukas Smith                                         |
// | All rights reserved.                                                 |
// +----------------------------------------------------------------------+
// | MDB2 is a merge of PEAR DB and Metabases that provides a unified DB  |
// | API as well as database abstraction for PHP applications.            |
// | This LICENSE is in the BSD license style.                            |
// |                                                                      |
// | Redistribution and use in source and binary forms, with or without   |
// | modification, are permitted provided that the following conditions   |
// | are met:                                                             |
// |                                                                      |
// | Redistributions of source code must retain the above copyright       |
// | notice, this list of conditions and the following disclaimer.        |
// |                                                                      |
// | Redistributions in binary form must reproduce the above copyright    |
// | notice, this list of conditions and the following disclaimer in the  |
// | documentation and/or other materials provided with the distribution. |
// |                                                                      |
// | Neither the name of Manuel Lemos, Tomas V.V.Cox, Stig. S. Bakken,    |
// | Lukas Smith nor the names of his contributors may be used to endorse |
// | or promote products derived from this software without specific prior|
// | written permission.                                                  |
// |                                                                      |
// | THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS  |
// | "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT    |
// | LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS    |
// | FOR A PARTICULAR PURPOSE ARE DISCLAIMED.  IN NO EVENT SHALL THE      |
// | REGENTS OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,          |
// | INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, |
// | BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS|
// |  OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED  |
// | AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT          |
// | LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY|
// | WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE          |
// | POSSIBILITY OF SUCH DAMAGE.                                          |
// +----------------------------------------------------------------------+
// | Author: Lukas Smith <smith@pooteeweet.org>                           |
// +----------------------------------------------------------------------+
//
// $Id: Common.php,v 1.24 2006/07/19 07:18:52 lsmith Exp $
//

/**
 * @package MDB2
 * @category Database
 */

/**
 * These are constants for the tableInfo-function
 * they are bitwised or'ed. so if there are more constants to be defined
 * in the future, adjust MDB2_TABLEINFO_FULL accordingly
 */

define('MDB2_TABLEINFO_ORDER',      1);
define('MDB2_TABLEINFO_ORDERTABLE', 2);
define('MDB2_TABLEINFO_FULL',       3);

/**
 * Base class for the schema reverse engineering module that is extended by each MDB2 driver
 *
 * @package MDB2
 * @category Database
 * @author  Lukas Smith <smith@pooteeweet.org>
 */
class MDB2_Driver_Reverse_Common extends MDB2_Module_Common
{
    // }}}
    // {{{ getTableFieldDefinition()

    /**
     * Get the stucture of a field into an array
     *
     * @param string    $table         name of table that should be used in method
     * @param string    $fields     name of field that should be used in method
     * @return mixed data array on success, a MDB2 error on failure
     * @access public
     */
    function getTableFieldDefinition($table, $field)
    {
        $db =& $this->getDBInstance();
        if (PEAR::isError($db)) {
            return $db;
        }

        return $db->raiseError(MDB2_ERROR_UNSUPPORTED, null, null,
            'method not implemented', __FUNCTION__);
    }

    // }}}
    // {{{ getTableIndexDefinition()

    /**
     * Get the stucture of an index into an array
     *
     * @param string    $table      name of table that should be used in method
     * @param string    $index      name of index that should be used in method
     * @return mixed data array on success, a MDB2 error on failure
     * @access public
     */
    function getTableIndexDefinition($table, $index)
    {
        $db =& $this->getDBInstance();
        if (PEAR::isError($db)) {
            return $db;
        }

        return $db->raiseError(MDB2_ERROR_UNSUPPORTED, null, null,
            'method not implemented', __FUNCTION__);
    }

    // }}}
    // {{{ getTableConstraintDefinition()

    /**
     * Get the stucture of an constraints into an array
     *
     * @param string    $table      name of table that should be used in method
     * @param string    $index      name of index that should be used in method
     * @return mixed data array on success, a MDB2 error on failure
     * @access public
     */
    function getTableConstraintDefinition($table, $index)
    {
        $db =& $this->getDBInstance();
        if (PEAR::isError($db)) {
            return $db;
        }

        return $db->raiseError(MDB2_ERROR_UNSUPPORTED, null, null,
            'method not implemented', __FUNCTION__);
    }

    // }}}
    // {{{ getSequenceDefinition()

    /**
     * Get the stucture of a sequence into an array
     *
     * @param string    $sequence   name of sequence that should be used in method
     * @return mixed data array on success, a MDB2 error on failure
     * @access public
     */
    function getSequenceDefinition($sequence)
    {
        $db =& $this->getDBInstance();
        if (PEAR::isError($db)) {
            return $db;
        }

        $start = $db->currId($sequence);
        if (PEAR::isError($start)) {
            return $start;
        }
        if ($db->supports('current_id')) {
            $start++;
        } else {
            $db->warnings[] = 'database does not support getting current
                sequence value, the sequence value was incremented';
        }
        $definition = array();
        if ($start != 1) {
            $definition = array('start' => $start);
        }
        return $definition;
    }

    // }}}
    // {{{ getTriggerDefinition()

    /**
     * Get the stucture of an trigger into an array
     *
     * @param string    $trigger    name of trigger that should be used in method
     * @return mixed data array on success, a MDB2 error on failure
     * @access public
     */
    function getTriggerDefinition($trigger)
    {
        $db =& $this->getDBInstance();
        if (PEAR::isError($db)) {
            return $db;
        }

        return $db->raiseError(MDB2_ERROR_UNSUPPORTED, null, null,
            'method not implemented', __FUNCTION__);
    }

    // }}}
    // {{{ tableInfo()

    /**
     * Returns information about a table or a result set
     *
     * The format of the resulting array depends on which <var>$mode</var>
     * you select.  The sample output below is based on this query:
     * <pre>
     *    SELECT tblFoo.fldID, tblFoo.fldPhone, tblBar.fldId
     *    FROM tblFoo
     *    JOIN tblBar ON tblFoo.fldId = tblBar.fldId
     * </pre>
     *
     * <ul>
     * <li>
     *
     * <kbd>null</kbd> (default)
     *   <pre>
     *   [0] => Array (
     *       [table] => tblFoo
     *       [name] => fldId
     *       [type] => int
     *       [len] => 11
     *       [flags] => primary_key not_null
     *   )
     *   [1] => Array (
     *       [table] => tblFoo
     *       [name] => fldPhone
     *       [type] => string
     *       [len] => 20
     *       [flags] =>
     *   )
     *   [2] => Array (
     *       [table] => tblBar
     *       [name] => fldId
     *       [type] => int
     *       [len] => 11
     *       [flags] => primary_key not_null
     *   )
     *   </pre>
     *
     * </li><li>
     *
     * <kbd>MDB2_TABLEINFO_ORDER</kbd>
     *
     *   <p>In addition to the information found in the default output,
     *   a notation of the number of columns is provided by the
     *   <samp>num_fields</samp> element while the <samp>order</samp>
     *   element provides an array with the column names as the keys and
     *   their location index number (corresponding to the keys in the
     *   the default output) as the values.</p>
     *
     *   <p>If a result set has identical field names, the last one is
     *   used.</p>
     *
     *   <pre>
     *   [num_fields] => 3
     *   [order] => Array (
     *       [fldId] => 2
     *       [fldTrans] => 1
     *   )
     *   </pre>
     *
     * </li><li>
     *
     * <kbd>MDB2_TABLEINFO_ORDERTABLE</kbd>
     *
     *   <p>Similar to <kbd>MDB2_TABLEINFO_ORDER</kbd> but adds more
     *   dimensions to the array in which the table names are keys and
     *   the field names are sub-keys.  This is helpful for queries that
     *   join tables which have identical field names.</p>
     *
     *   <pre>
     *   [num_fields] => 3
     *   [ordertable] => Array (
     *       [tblFoo] => Array (
     *           [fldId] => 0
     *           [fldPhone] => 1
     *       )
     *       [tblBar] => Array (
     *           [fldId] => 2
     *       )
     *   )
     *   </pre>
     *
     * </li>
     * </ul>
     *
     * The <samp>flags</samp> element contains a space separated list
     * of extra information about the field.  This data is inconsistent
     * between DBMS's due to the way each DBMS works.
     *   + <samp>primary_key</samp>
     *   + <samp>unique_key</samp>
     *   + <samp>multiple_key</samp>
     *   + <samp>not_null</samp>
     *
     * Most DBMS's only provide the <samp>table</samp> and <samp>flags</samp>
     * elements if <var>$result</var> is a table name.  The following DBMS's
     * provide full information from queries:
     *   + fbsql
     *   + mysql
     *
     * If the 'portability' option has <samp>MDB2_PORTABILITY_FIX_CASE</samp>
     * turned on, the names of tables and fields will be lower or upper cased.
     *
     * @param object|string  $result  MDB2_result object from a query or a
     *                                string containing the name of a table.
     *                                While this also accepts a query result
     *                                resource identifier, this behavior is
     *                                deprecated.
     * @param int  $mode   either unused or one of the tableInfo modes:
     *                     <kbd>MDB2_TABLEINFO_ORDERTABLE</kbd>,
     *                     <kbd>MDB2_TABLEINFO_ORDER</kbd> or
     *                     <kbd>MDB2_TABLEINFO_FULL</kbd> (which does both).
     *                     These are bitwise, so the first two can be
     *                     combined using <kbd>|</kbd>.
     *
     * @return array  an associative array with the information requested.
     *                 A MDB2_Error object on failure.
     *
     * @see MDB2_Driver_Common::setOption()
     */
    function tableInfo($result, $mode = null)
    {
        $db =& $this->getDBInstance();
        if (PEAR::isError($db)) {
            return $db;
        }

        if (!is_string($result)) {
            return $db->raiseError(MDB2_ERROR_UNSUPPORTED, null, null,
                'method not implemented', __FUNCTION__);
        }

        $db->loadModule('Manager');
        $fields = $db->manager->listTableFields($result);
        if (PEAR::isError($fields)) {
            return $fields;
        }

        $flags = array();

        $indexes = $db->manager->listTableIndexes($result);
        if (PEAR::isError($indexes)) {
            return $indexes;
        }

        foreach ($indexes as $index) {
            $definition = $this->getTableIndexDefinition($result, $index);
            if (PEAR::isError($definition)) {
                return $definition;
            }
            if (count($definition['fields']) > 1) {
                foreach ($definition['fields'] as $field => $sort) {
                    $flags[$field] = 'multiple_key';
                }
            }
        }

        $constraints = $db->manager->listTableConstraints($result);
        if (PEAR::isError($constraints)) {
            return $constraints;
            $flag = !empty($definition['primary'])
                ? 'primary_key' : (!empty($definition['unique'])
                    ? 'unique_key' : false);
            if ($flag) {
                foreach ($definition['fields'] as $field => $sort) {
                    if (!empty($flags[$field]) || $flags[$field] != 'primary_key') {
                        $flags[$field] = $flag;
                    }
                }
            }
        }

        if ($mode) {
            $res['num_fields'] = count($fields);
        }

        foreach ($fields as $i => $field) {
            $definition = $this->getTableFieldDefinition($result, $field);
            if (PEAR::isError($definition)) {
                return $definition;
            }
            $res[$i] = $definition[0];
            $res[$i]['name'] = $field;
            $res[$i]['table'] = $result;
            $res[$i]['type'] = $definition[0]['nativetype'];
            // 'primary_key', 'unique_key', 'multiple_key'
            $res[$i]['flags'] = empty($flags[$field]) ? '' : $flags[$field];
            // not_null', 'unsigned', 'auto_increment', 'default_[rawencodedvalue]'
            if (!empty($res[$i]['notnull'])) {
                $res[$i]['flags'].= ' not_null';
            }
            if (!empty($res[$i]['unsigned'])) {
                $res[$i]['flags'].= ' unsigned';
            }
            if (!empty($res[$i]['auto_increment'])) {
                $res[$i]['flags'].= ' autoincrement';
            }
            if (!empty($res[$i]['default'])) {
                $res[$i]['flags'].= ' default_'.rawurlencode($res[$i]['default']);
            }

            if ($mode & MDB2_TABLEINFO_ORDER) {
                $res['order'][$res[$i]['name']] = $i;
            }
            if ($mode & MDB2_TABLEINFO_ORDERTABLE) {
                $res['ordertable'][$res[$i]['table']][$res[$i]['name']] = $i;
            }
        }

        return $res;
    }
}
?>