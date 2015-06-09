{strip}
<div class="nodetree_row {if !$entry.node->newaction}nodetree_node{/if}" style="clear:both">
    {if $entry.show_toggle}
         <a class="nodetree_toggle {if $entry.open}expand{else}contract{/if} {if $entry.open}open{/if}"></a>
    {/if}
    <div style="width: 15px; padding-top: 2px; display:inline">
        {if $entry.selectable}
            {if $multi}
                <input id="select_{$entry.node->id}" name="node_select" class="node_select" type="checkbox" data-title="{$entry.title|escape:htmlall:'UTF-8'}" {if $entry.selected}checked="checked"{/if} value='{$entry.node->id}'/>
            {else}
                <input id="select_{$entry.node->id}" name="node_select" class="node_select" type="radio" data-title="{$entry.title|escape:htmlall:'UTF-8'}" {if $entry.selected}checked="checked"{/if} value='{$entry.node->id}'/>
            {/if}
        {/if}
    </div>
    <label  style="display:inline" for="select_{$entry.node->id}" class="nodetree_title{if $entry.node->is_content()} nodetree_title_content{/if}">
        &nbsp;{$entry.title}
    </label>
</div>
{if $entry.open}
    <ul class="nodetree_children">
    {foreach from=$entry.children item=childentry}
        <li class="nodetree_entry{if !$childentry.last} nodetree_connection{/if}">
        {if $childentry.last}
            <img class="nodetree_connection" src="picts/joinbottom.gif" alt="" />
        {else}
            <img class="nodetree_connection" src="picts/join.gif" alt="" />
        {/if}
            <div class="nodetree_container" {if $childentry.node->id} id="nodetree_entry_{$childentry.node->id}"{/if} data-node="{$childentry.node->id}">
                {include file="nodes_select_container.tpl" entry=$childentry hide_root=false}
            </div>
        </li>
    {/foreach}
    </ul>
{/if}
{/strip}