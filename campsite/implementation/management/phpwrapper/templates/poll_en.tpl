{PollIsDefined} 

    {PollIsVotable}
        <table border="0" cellspacing="2" cellpadding="2">
        <form name="poll{PollPrint subject='Number'}" action="{URIPath}" method="post">
        {FormParameters}
        
        <tr>
            <td align="left">
                <h2 id='col_schwarz'>{PollPrint subject='Question'}</h2>
                <table border="0" cellspacing="0" cellpadding="2">
       
                 {PollListQuestion}
                      <tr valign="top">
                       <td align="left" valign="middle">
                            <input type="radio" name="poll[result][answer][##IdPoll##]" value="##NrAnswer##">
                       </td>
                       <td align="left" valign="middle" width="90%">##Answer##</td>
                      </tr>
                 {/PollListQuestion}
           
                 <tr>
                    <td colspan=2>
                    <input type="submit" value="Submit">
                    &nbsp;&nbsp;
                    <a href="{URL}&poll[IdPoll]={PollPrint subject='Number'}&poll[showResult]=1" id="col_schwarz">
                        <span id="col_mitmachen">&raquo; </span>Result
                    </a>
                    </td>
                    
                 </tr>
        
                </table>
        
            </td>
        </tr>
        </form>
        </table>
    {/PollIsVotable}
    
    {PollIsNotVotable}
        <table border="0" cellspacing="2" cellpadding="0">
        
        <tr><td>
            <h2 id='col_schwarz'>{PollPrint subject='Question'}</h2>        
            <table border="0" cellspacing="0" cellpadding="2">
        
            <tr>
                <td colspan="2">        
                    {PollListAnswer}
						<div class="DIV_padding_minus">
                        ##NrAnswer##: 
                        <img src="/phpwrapper/images/poll/mainbarlinks.png" width="1" height="9" class="IMG_norm"><img src="/phpwrapper/images/poll/mainbar.png" width="##Percentage##" height="9" class="IMG_norm"><img src="/phpwrapper/images/poll/mainbarrechts.png" width="1" height="9" class="IMG_norm"> 
                        <span class="text_mini">##Percentage##%</span>
                        </div>
                    {/PollListAnswer}
                </td>
            </tr>
            
            <tr>
          <td colspan="2">Number of Votes: {PollPrint subject='Sum'}</td>
        </tr>
            
            {PollListAnswer}
                <tr>
                    <td valign='top' width='1%'>##NrAnswer##:</td>
                    <td width='90%'  valign='top'>##Answer##</td>
                </tr>
            {/PollListAnswer}
    
            {Local}
            {Section Number=300}
            <tr>
                <td></td>
                <td><a href="{URL}" id="col_schwarz"><span id="col_mitmachen">&raquo; </span>All Polls</a></td>
            </tr>
            {EndLocal}
    
        </table>
        
        </td></tr>
        </table>    
    {/PollIsNotVotable}
{/PollIsDefined}
