<?php
/* Edit form has the commands
    edit: shows a form to edit the form (sic)
    save: saves changes to the form
*/

class action_formedit extends AdminAction { 
    
    var $props = array("class", "command", "id");
    
    /** permits superadmin */
    function permit_user($user) {
      return $user->isSuperadmin();
    }

    function load_form() {
        $form = DB_DataObject::factory('form');
        $form->id = $this->id;
        if ($form->id !== 'new') {
            $found = $form->get($this->id);
            if (!$found) throw new Exception("Couldn't load form with id $this->id");
        }
        return $form;
    }
}

class action_formedit_save extends action_formedit implements ChangeAction {
    function process($aquarius, $post, $result) {
        $result->touch_region('content');
        $form = $this->load_form();

        // Save form properties
        $form->title = get($post, "formtitle");
        $form->template = get($post, "formtemplate");
        $form->sort_by = get($post, "formsortby");
        $form->sort_reverse = get($post, "formsortreverse", 0);
        $form->fall_through = get($post, "formfallthrough");
        $form->show_in_menu = get($post, "formshowinmenu", 0);
        $form->fieldgroup_selection_id = get($post, 'fieldgroup_selection');
        $form->permission_level = get($post, 'permission_level');
        if ($form->id == 'new') {
            $form->insert();
        } else {
            $form->update();
        }

        // Save fields
        $fielddata = get($post, "field");
        $maxweight = 0;
        if (is_array($fielddata)) {
            foreach($fielddata as $id => $data) {
                $field = DB_DataObject::factory('form_field');

                // See whether we have to insert it
                $new = (bool)preg_match('/^new.*/', $id);

                if ($new) {
                    $field->form_id = $form->id;
                } else {
                    if (!$field->get($id)) throw new Exception("Missing form field with id '$id'; it doesn't exist");
                }

                // Read field properties
                $props = array('name', 'description', 'weight', 'type', 'sup1', 'sup2', 'sup3', 'sup4');
                foreach($props as $prop) {
                    $val = get($data, $prop);
                    if ($val === false) throw new Exception("Expected val '$prop' for field $id not in REQUEST");
                    $field->$prop = $val;
                }

                // Special handling for checkboxes
                $field->multi                = get($data, 'multi', 0);
                $field->language_independent = get($data, 'language_independent', 0);
                $field->add_to_title         = get($data, 'add_to_title', 0);
                $active                      = get($data, 'active');

                // save new permission_level
                $field->permission_level = get($data, 'permission_level', 2);

                // Adjust weight if it's not set
                if (!is_numeric($field->weight)) $field->weight = $maxweight + 10;
                $maxweight = max($maxweight, $field->weight);

                if ($new) {
                    // Insert new fields only if they were given a name
                    if ($active && strlen($field->name) > 0) {
                        $field->id = null;
                        $saved = $field->insert();
                        if (!$saved) throw new Exception("Unable to save new field $field->name");
                        $result->add_message(new Translation('s_message_form_inserted_field',  $field->name));
                    }
                } else {
                    if ($active) {
                        $field->update();
                    } else {
                        $field->delete();
                        $result->add_message(new Translation('s_message_form_deleted_field',  $field->name));
                    }
                }
            }
        }
    }
}

class action_formedit_edit extends action_formedit implements DisplayAction {
    function process($aquarius, $request, $smarty, $result) {
        $form = $this->load_form();
        $smarty->assign('form', $form);

        // Load all form fields
        $fields = $form->get_fields();

        // Add ten empty fields at the end
        for($i = 0; $i < 10; $i++) {
            $newfield = DB_DataObject::factory('form_field');
            $newfield->id = 'new'.$i;
            $newfield->permission_level=2;
            $fields[] =  $newfield;
        }
        $smarty->assign('fields', $fields);

        // Prepare fallthrough options
        $opts = array('-none-', 'all', 'category', 'box', 'parent');
        $smarty->assign('fallthroughoptions', array_combine($opts, $opts)); // FIXME: Use translation

        // Prepare fieldgrouping selection
        $selection = DB_DataObject::factory('fieldgroup_selection');
        $selection->find();
        $selections = array(0 => '-none-');
        while($selection->fetch()) {
            $selections[$selection->fieldgroup_selection_id] = $selection->name;
        }
        $smarty->assign('fieldgroup_selections', $selections);

        $smarty->assign('formtypes', $aquarius->get_formtypes()->get_formtypes());

        $smarty->assign('permission_levels', db_Users::$status_names);

        $page_requisites = new Page_Requisites();
        $page_requisites->add_managed_js('jquery.js',          true);
        $page_requisites->add_managed_js('jquery.labelify.js', true);
        $smarty->assign('page_requisites', $page_requisites);

        $result->use_template("formedit.tpl");
    }
}