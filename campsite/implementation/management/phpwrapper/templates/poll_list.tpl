{assign var=list value=$poll_list->get()}
{foreach from=$list.entrys item=entry}
<a href="{$entry.link}" id="col_schwarz" class="A_spacing"><b>{$entry.text} <span  id="col_mitmachen">&raquo;</span></b><br />
{$entry.when}</a>
{/foreach}
{if $list.prevlink} 
<a href='{$list.prevlink}' id="col_schwarz"><span  id="col_mitmachen">&laquo;</span> zur&uuml;ck</a>&nbsp;&nbsp;&nbsp;
{/if}
{if $list.nextlink}
<a href='{$list.nextlink}' id="col_schwarz">mehr <span  id="col_mitmachen">&raquo;</span></a>
{/if}