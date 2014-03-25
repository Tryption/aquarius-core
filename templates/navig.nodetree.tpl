
<link rel="stylesheet" href="css/nodetree.css" type="text/css" />

{include_javascript file='nodetree.js'}

<form id="leftnav">
    <div class="nodetree_container container_{$tree.node->id}" id="root_{$tree.node->id}" data-node="{$tree.node->id}">
        {include file='navig.nodetree_container.tpl' base=true}
    </div>
</form>

{js}
    var request_url = '{url url=$adminurl escape=false action0=$lastaction|default:false action1="nodetree:navig_children:$lg"}'
    var nodetree;

    var root = document.getElementById("root_{$tree.node->id}")
    var nodetree = new NodeTree(root, request_url)
{/js}