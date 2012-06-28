{assign var='ilink_action' value=$simpleurl->with_param($rte_options.popup_ilink_url)}
{assign var='rte_file_action_img' value=$simpleurl->with_param($rte_options.popup_filebrowser_url_img)}
{assign var='rte_file_action_file value=$simpleurl->with_param($rte_options.popup_filebrowser_url_file)}

{js}
		
    var myilink = "{$ilink_action->str(false)}"; 	
		
	function rte_file_select_img(target_id, file) {literal}{{/literal}	    
	    rte_file_select(target_id, 'img', file);
	{literal}}{/literal}
	
	function rte_file_select_file(target_id, file) {literal}{{/literal}
	    rte_file_select(target_id, 'file', file);
	{literal}}{/literal}
	
	function rte_file_select(target_id, type, file) {literal}{
		var file_path;
	    if(file == "") file_path = "";
	    else {{/literal}
	        if(type == "img") file_path = "/{$rte_options.image_path}";
    		else file_path = "/{$rte_options.file_path}";
            
            var arr = file.split("/");
    		if(arr.length == 1) file_path += "/";
            
    		file_path += file;
        {literal}}
        
        CKEDITOR.tools.callFunction(target_id, file_path);
        }{/literal}
{/js}

<textarea name="{$RTEformname}" class="mle">
	{$RTEformvalue}
</textarea>
<div id="counterDIV_{$RTEformname}" style="width:110px; padding-top:1px; font-size:11px; float:right; text-align:right;">
    
    <label style="float:left; font-size:11px;">{#Anzahl_Zeichen#}:</label>
    <label id="counterDIV_label_{$RTEformname}" style="font-size:11px;font-weight:bold;"></label>
    
</div><div class="clear"></div>
{js}
    CKEDITOR.config.language = '{$rte_options.rte_lg}';
    CKEDITOR.config.filebrowserImageBrowseUrl = '{$rte_file_action_img->str(false)}';
    CKEDITOR.config.filebrowserBrowseUrl = '{$rte_file_action_file->str(false)}';
    CKEDITOR.config.filebrowserWindowWidth = 500;
    CKEDITOR.config.filebrowserWindowHeight = 600;

    var editor_{$RTEhtmlID} = CKEDITOR.replace('{$RTEformname}');
    editor_{$RTEhtmlID}.config.counter = 'counterDIV_label_{$RTEformname}';
    {if $rte_options.height > 50}		
        editor_{$RTEhtmlID}.config.height = '{$rte_options.height}px';
    {/if}
{/js}
