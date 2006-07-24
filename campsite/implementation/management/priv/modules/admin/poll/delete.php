<?php
require_once $Campsite['HTML_DIR']."/$ADMIN_DIR/modules/start.ini.php";

$access = startModAdmin ("ManagePoll", "Poll", 'Delete poll');
if ($access) {

    if (file_exists(dirname(__FILE__)."/locals.{$_REQUEST['TOL_Language']}.php")) {
        require_once "locals.{$_REQUEST['TOL_Language']}.php";
    } elseif(file_exists(dirname(__FILE__)."/locals.{$_REQUEST['TOL_Language']}.php"))  {
        require_once 'locals.en.php';
    }

    $poll         = $_REQUEST['poll'];
    $delete_ready = $_REQUEST['delete_ready'];

    if (!$delete_ready) {
        ?>
        <form name="fake">
        <table border="0" width="100%" bgcolor="#C0D0FF">
         <tr><th colspan="2" align="left"><?php putGS("delete poll"); ?><br><br></th></tr>
         <tr><td colspan="2" align="left"><font color="red"><b><?php putGS("del attention", $poll[title]); ?></font></b><br><br></td></tr>
         <tr><td><input type="button" onClick="location.href='index.php'" value="<?php putGS("cancel del"); ?>"></td>
         <td align="right"><input type="button" onClick="location.href='<?php print $PHP_SELF; ?>?delete_ready=1&poll[id]=<?php echo $poll[id]; ?>'" value="<?php putGS("submit del", $poll[title]); ?>"></td></tr>
        </table>
        </form>
        <?php

        return;
    }

    $query[] = "DELETE FROM poll_main WHERE id=$poll[id]";
    $query[] = "DELETE FROM poll_publication WHERE id_poll=$poll[id]";
    $query[] = "DELETE FROM poll_issue WHERE id_poll=$poll[id]";
    $query[] = "DELETE FROM poll_section WHERE id_poll=$poll[id]";
    $query[] = "DELETE FROM poll_article WHERE id_poll=$poll[id]";
    $query[] = "DELETE FROM poll_questions WHERE id_poll=$poll[id]";
    $query[] = "DELETE FROM poll_answers WHERE id_poll=$poll[id]";

    sqlQuery($DB[poll], $query);

    print '<META HTTP-EQUIV="Refresh" CONTENT="0; URL=index.php">';
}
?>