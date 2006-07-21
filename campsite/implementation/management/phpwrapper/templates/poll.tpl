{if $poll->getPoll()}

    {if $poll->userCanVote() && !$smarty.request.poll.showResult} 
        <table border="0" cellspacing="2" cellpadding="2">
        <form name="{$poll->mainData.id}" action="{$functions->getTPL($poll->usestartparams)}" method="post">
        {$functions->getPostParams(null, true, true)}
        
        <tr>
            <td align="left">
                <h2 id='col_schwarz'>{$poll->mainData.question}</h2>
                <table border="0" cellspacing="0" cellpadding="2">
       
                 {foreach from=$poll->getAnswers() item=answer}
                      <tr valign="top">
                       <td align="left" valign="middle"><input type="radio" name="pollres[answer][{$poll->mainData.id}]" value="{$answer.nr_answer}"></td>
                       <td align="left" valign="middle" width="90%">{$answer.answer}</td>
                      </tr>
                 {/foreach}
           
                 <tr>
                    <td colspan=2>
                    <input type="submit" value="Abstimmen">
                    &nbsp;&nbsp;<a href="/look/{$functions->getURL()}&pollid={$poll->mainData.id}&poll[showResult]=1" id="col_schwarz"><span id="col_mitmachen">&raquo; </span>Ergebnis</a>
                    </td>
                    
                 </tr>
        
                </table>
        
            </td>
        </tr>
        </form>
        </table>
    {/if}
    
    {if !$poll->userCanVote() || $smarty.request.poll.showResult}
        <table border="0" cellspacing="2" cellpadding="0">
        
        <tr><td>
            <h2 id='col_schwarz'>{$poll->mainData.question}</h2>        
            <table border="0" cellspacing="0" cellpadding="2">
        
            <tr>
                <td colspan="2">        
                    {foreach from=$poll->getResult() item=answer}
						<div class="DIV_padding_minus">
                        {$answer.nr_answer}: 
                        <img src="/look/phpwrapper/images/poll/mainbarlinks.png" width="1" height="9" class="IMG_norm"><img src="/look/phpwrapper/images/poll/mainbar.png" width="{math equation="2 * x" x=$answer.prozent}" height="9" class="IMG_norm"><img src="/look/phpwrapper/images/poll/mainbarrechts.png" width="1" height="9" class="IMG_norm"> 
                        <span class="text_mini">{$answer.prozent}%</span>
                        </div>
                    {/foreach}
                </td>
            </tr>
            
            {assign var=sum value=$poll->getSum()} 
            <tr>
          <td colspan="2">Gesamtzahl Stimmen: {$sum.allsum}</td>
        </tr>
            
            {foreach from=$poll->getResult() item=answer}
                <tr>
                    <td valign='top' width='1%'>{$answer.nr_answer}:</td>
                    <td width='90%'  valign='top'>{$answer.answer}</td>
                </tr>
            {/foreach}
    
            {if $poll->linklist !== 'off'}
            {assign var=p value=$functions->assignArray('NrSection', 120)}
                <tr>
                    <td></td>
                    <td><a href="/look/{$functions->getURL($p)}" id="col_schwarz"><span id="col_mitmachen">&raquo; </span>Alle Umfragen</a></td>
                </tr>
            {/if}
    
        </table>
        
        </td></tr>
        </table>    
    {/if}
{/if}