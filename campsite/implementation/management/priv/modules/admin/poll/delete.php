<?php
require_once $Campsite['HTML_DIR']."/$ADMIN_DIR/modules/start.ini.php";
require_once $Campsite['HTML_DIR']."/classes/Input.php";

$access = startModAdmin ("ManagePoll", "Poll", 'Delete poll');
if ($access) {

    if (file_exists(dirname(__FILE__)."/locals.{$_REQUEST['TOL_Language']}.php")) {
        require_once "locals.{$_REQUEST['TOL_Language']}.php";
    } elseif(file_exists(dirname(__FILE__)."/locals.{$_REQUEST['TOL_Language']}.php"))  {
        require_once 'locals.en.php';
    }

    $poll = Input::Get('poll', 'array', array());
    $delete_ready = Input::Get('delete_ready', 'boolean', false);

    if (!$delete_ready) {
        ?>
        <form name="fake">
        <table border="0" width="100%" bgcolor="#C0D0FF">
         <tr><th colspan="2" align="left"><?php putGS("delete poll"); ?><br><br></th></tr>
         <tr><td colspan="2" align="left"><font color="red"><b><?php putGS('Poll "$1" is going to be deleted.', $poll['Title']); ?></font></b></td></tr>
         <?php
         if ($poll['IdLanguage'] === $poll['DefaultIdLanguage']) {
             ?>
            <tr><td colspan="2" align="left"><font color="red"><b><?php putGS('Deleting poll in default language will remove it\'s translations too.'); ?></font></b></td></tr>
            <?php
         }
         ?>
         <tr><td colspan="2"><br></td></tr>
         <tr>
            <td><input type="button" onClick="location.href='index.php'" value="<?php putGS("cancel del"); ?>"></td>
            <td align="right"><input type="button" onClick="location.href='<?php print $PHP_SELF; ?>?delete_ready=1&poll[Number]=<?php echo $poll['Number']; ?>&poll[IdLanguage]=<?php echo $poll['IdLanguage']; ?>'" value="<?php putGS("submit del", $poll['Title']); ?>"></td>
        </tr>
        </table>
        </form>
        <?php

        return;
    }

    $query[] = "DELETE FROM poll_main           WHERE Number = {$poll['Number']} AND DefaultIdLanguage = {$poll['IdLanguage']}";
    $query[] = "DELETE FROM poll_publication    WHERE NrPoll = {$poll['Number']} AND IdLanguage = {$poll['IdLanguage']}";
    $query[] = "DELETE FROM poll_issue          WHERE NrPoll = {$poll['Number']} AND IdLanguage = {$poll['IdLanguage']}";
    $query[] = "DELETE FROM poll_section        WHERE NrPoll = {$poll['Number']} AND IdLanguage = {$poll['IdLanguage']}";
    $query[] = "DELETE FROM poll_article        WHERE NrPoll = {$poll['Number']} AND IdLanguage = {$poll['IdLanguage']}";
    $query[] = "DELETE FROM poll_questions      WHERE NrPoll = {$poll['Number']} AND IdLanguage = {$poll['IdLanguage']}";
    $query[] = "DELETE FROM poll_answers        WHERE NrPoll = {$poll['Number']} AND IdLanguage = {$poll['IdLanguage']}";

    sqlQuery($DB['modules'], $query);

    print '<META HTTP-EQUIV="Refresh" CONTENT="0; URL=index.php">';
}
?>