{Local}
{Section Name="Poll archive"}

{PollList_Get length=10}
    <a href="{URL}&poll[NrPoll]=##NrPoll##"}">
        <b>##Title##:  <br>
        ##Question##<br>
        </b>
    ##DateBegin## - ##DateExpire##
    </a>
    <br><br>
{/PollList_Get}

{PollList_IfPrevItem} 
    <a href='{URL}&poll[page]=##previousItem##' id="col_schwarz"><span  id="col_mitmachen">&laquo;</span> back</a>&nbsp;&nbsp;&nbsp;
{/PollList_IfPrevItem} 

{PollList_IfNextItem} 
    <a href='{URL}&poll[page]=##nextItem##' id="col_schwarz">more <span  id="col_mitmachen">&raquo;</span></a>
{/PollList_IfNextItem} 

{EndLocal}