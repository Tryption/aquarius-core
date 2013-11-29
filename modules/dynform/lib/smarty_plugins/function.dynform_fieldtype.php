<?php

/** 
  * Convenience method to transform a field type id to the current admin language
  * Requires "id"
  * Assigns nothing
  */
  
function smarty_function_dynform_fieldtype($params, &$smarty) 
{
    require_once $smarty->_get_plugin_filepath('modifier','makeaction') ;
    
    $id = get($params, 'id') ;
    if (!$id) $smarty->trigger_error("dynform_fieldtype: require parameter id missing") ;
        
    $type = DB_DataObject::factory('dynform_field_type') ; 
    $type->id = $id ; 
    $found = $type->find() ; 
    if ($found) 
    {
		$type->fetch() ; 
		return str(new Translation($type->name)) ;     
	}    
}


?>