{assign var=list value=$poll_list->get()}

{Local}
{Section Name=poll-id}

{foreach from=$list.entrys item=entry}
    <a href="{URL}&poll[Id]={$entry.IdPoll}">
        <b>{$entry.Title}:<br>
        {if $entry.Question}
            {$entry.Question}<br>
        {/if}
        </b>
    {$entry.DateBegin_stamp|date_format:'%Y-%m-%d'} - {$entry.DateExpire_stamp|date_format:'%Y-%m-%d'}
    </a>
    <br><br>
{/foreach}

{EndLocal}

{if $list.previousPage} 
    <a href='{$URL}?poll[page]={$list.previousPage}' id="col_schwarz"><span  id="col_mitmachen">&laquo;</span> back</a>&nbsp;&nbsp;&nbsp;
{/if}

{if $list.nextPage}
    <a href='{$URL}?poll[page]={$list.nextPage}' id="col_schwarz">more <span  id="col_mitmachen">&raquo;</span></a>
{/if}