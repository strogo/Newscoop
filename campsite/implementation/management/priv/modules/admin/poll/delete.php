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
         <tr><td colspan="2" align="left"><font color="red"><b><?php putGS("del attention", $poll['Title']); ?></font></b><br><br></td></tr>
         <tr><td><input type="button" onClick="location.href='index.php'" value="<?php putGS("cancel del"); ?>"></td>
         <td align="right"><input type="button" onClick="location.href='<?php print $PHP_SELF; ?>?delete_ready=1&poll[Id]=<?php echo $poll['Id']; ?>'" value="<?php putGS("submit del", $poll['Title']); ?>"></td></tr>
        </table>
        </form>
        <?php

        return;
    }

    $query[] = "DELETE FROM poll_main           WHERE id     = {$poll['Id']}";
    $query[] = "DELETE FROM poll_publication    WHERE IdPoll = {$poll['Id']}";
    $query[] = "DELETE FROM poll_issue          WHERE IdPoll = {$poll['Id']}";
    $query[] = "DELETE FROM poll_section        WHERE IdPoll = {$poll['Id']}";
    $query[] = "DELETE FROM poll_article        WHERE IdPoll = {$poll['Id']}";
    $query[] = "DELETE FROM poll_questions      WHERE IdPoll = {$poll['Id']}";
    $query[] = "DELETE FROM poll_answers        WHERE IdPoll = {$poll['Id']}";

    sqlQuery($DB['modules'], $query);

    print '<META HTTP-EQUIV="Refresh" CONTENT="0; URL=index.php">';
}
?>