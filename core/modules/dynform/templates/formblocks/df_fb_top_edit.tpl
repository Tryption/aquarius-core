<h1 >{#edit#}: {dynform_fieldtype id=$field_type}</h1>
<div id="outer">
    <h2>&nbsp;</h2>
    <div class="toolbox">
        {action action="dynform:changefieldtype:`$content->id`:`$content->lg`:`$node->id`:`$block->id`:`$field->id`"}
            <form action="{url action=$lastaction}"  style="display: inline" method="post">
                {#change_type#}&nbsp;&nbsp;{dynform_fieldtypes_popup}
                <input type="submit" name="{$action}" value="ok" class="button" />
            </form>
        {/action}
    </div>
    <form action="{url action=$saveaction}" method="post" id="dynformform" style="display: inline">
		<div class='contentedit contentedit_ef'> 
			<label for="name" title="name">{#name#}</label>
			<input class="ef" type="text" name="field[name]" value="{$field->name}" id="name"/>
		</div>