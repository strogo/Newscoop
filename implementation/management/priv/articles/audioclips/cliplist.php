<style type="text/css">@import url(<?php echo $Campsite["WEBSITE_URL"]; ?>/javascript/domTT/domTT.css);</style>
<script type="text/javascript" src="<?php echo $Campsite["WEBSITE_URL"]; ?>/javascript/domTT/domLib.js"></script>
<script type="text/javascript" src="<?php echo $Campsite["WEBSITE_URL"]; ?>/javascript/domTT/domTT.js"></script>
<script type="text/javascript">
    var domTT_styleClass = 'domTTOverlib';
</script>
<TABLE class="table_actions">
<TR>
    <TD align="right">
        <INPUT type="button" class="button" value="<?php putGS("Select All"); ?>" onclick="checkAll(<?php p($clipCount); ?>, 'rw_');">
        <INPUT type="button" class="button" value="<?php putGS("Select None"); ?>" onclick="uncheckAll(<?php p($clipCount); ?>, 'rw_');">
    </TD>
</TR>
</TABLE>
<TABLE border="0" cellspacing="1" cellpadding="6" class="table_list">
<FORM method="POST" name="audioclip_list" action="do_link.php">
<INPUT type="hidden" name="f_language_id" value="<?php p($f_language_id); ?>" />
<INPUT type="hidden" name="f_language_selected" value="<?php p($f_language_selected); ?>" />
<INPUT type="hidden" name="f_article_number" value="<?php p($f_article_number); ?>" />
<TR class="table_list_header">
    <?php if ($articleObj->userCanModify($g_user)) { ?>
    <TD align="center" valign="top" style="padding: 3px;"></TD>
    <?php } ?>
    <TD align="left" valign="top">
    <?php putGS("Title"); ?>
    </TD>
    <TD align="left" valign="top">
    <?php putGS("Creator"); ?>
    </TD>
    <TD align="left" valign="top">
    <?php putGS("Duration"); ?>
    </TD>
</TR>

<?php
$color = 0;
$counter = 0;
foreach ($clips as $clip) {
    $allTags = '';
    $aClipMetaTags = $clip->getAvailableMetaTags();
    foreach ($aClipMetaTags as $metaTag) {
        list($nameSpace, $localPart) = explode(':', strtolower($metaTag));
        if ($localPart == 'title') {
            $allTags .= '<strong>'.$metatagLabel[$metaTag] . ': ' . $clip->getMetatagValue($localPart) . '</strong><br />';
        } else {
            $allTags .= $metatagLabel[$metaTag] . ': ' . $clip->getMetatagValue($localPart) . '<br />';
        }
    }
    if ($color == 1) {
        $color = 0;
        $rowClass = 'list_row_even';
    } else {
        $color = 1;
        $rowClass = 'list_row_odd';
    }
?>
    <script>
        default_class[<?php p($counter); ?>] = "<?php p($rowClass); ?>";
    </script>
    <TR id="rw_<?php p($counter); ?>" class="<?php p($rowClass); ?>" onmouseover="setPointer(this, <?php p($counter); ?>, 'over');" onmouseout="setPointer(this, <?php p($counter); ?>, 'out');">
    <?php
    if ($articleObj->userCanModify($g_user)) {
    ?>
        <TD align="center">
            <INPUT type="checkbox" value="<?php p($clip->getGunId()); ?>" name="f_audioclip_code[]" id="checkbox_<?php p($counter); ?>" class="input_checkbox" onclick="checkboxClick(this, <?php p($counter); ?>);" />
        </TD>
    <?php
    } else {
    ?>
        <TD align="center">&nbsp;</TD>
    <?php
    }
    ?>
        <TD>
            <A href="<?php echo camp_html_article_url($articleObj, $f_language_id, "audioclips/edit.php", camp_html_article_url($articleObj, $f_language_id, "audioclips/popup.php"))
            .'&f_action=edit&f_audioclip_id='.$clip->getGunId(); ?>" onmouseover="domTT_activate(this, event, 'content', '<?php p(addslashes($allTags)); ?>', 'trail', true, 'delay', 0);">
            <?php echo htmlspecialchars($clip->getMetatagValue('title')); ?>
            </A>
        </TD>
        <TD style="padding-left: 5px;">
            <A href="<?php echo camp_html_article_url($articleObj, $f_language_id, "audioclips/edit.php", camp_html_article_url($articleObj, $f_language_id, "audioclips/popup.php"))
            .'&f_action=edit&f_audioclip_id='.$clip->getGunId(); ?>"><?php echo htmlspecialchars($clip->getMetatagValue('creator')); ?></A>
        </TD>
        <TD style="padding-left: 5px;">
        <?php echo htmlspecialchars(camp_time_format($clip->getMetatagValue('extent'))); ?>&nbsp;
        </TD>
    </TR>
<?php
    $counter++;
} // foreach
?>

<TR>
    <TD colspan="2" nowrap>
    <?php putGS('$1 audioclips found', $clipCount); ?>
    </TD>
    <TD colspan="2" align="right">
        <INPUT type="button" class="button" onclick="attach_submit(this);" value="Attach" />
    </TD>
</TR>
</FORM>
</TABLE>
<TABLE class="action_buttons">
<TR>
    <TD>
    <?php echo $pager->render(); ?>
    </TD>
</TR>
</TABLE>
