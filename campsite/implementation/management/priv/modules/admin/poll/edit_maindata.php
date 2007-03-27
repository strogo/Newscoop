<?php
require_once $Campsite['HTML_DIR']."/$ADMIN_DIR/modules/start.ini.php";
require_once $Campsite['HTML_DIR']."/classes/Input.php";

$access = startModAdmin ("ManagePoll", "Poll", 'Edit questions');
if ($access) {

    if (file_exists(dirname(__FILE__)."/locals.{$_REQUEST['TOL_Language']}.php")) {
        require_once "locals.{$_REQUEST['TOL_Language']}.php";
    } elseif(file_exists(dirname(__FILE__)."/locals.{$_REQUEST['TOL_Language']}.php"))  {
        require_once 'locals.en.php';
    }

    $poll = Input::Get('poll', 'array', array());
    $act  = Input::Get('act');

    if ($act === 'change') {
        $query = "SELECT *
                  FROM   mod_poll_main 
                  WHERE  Number = {$poll['Number']}";
        $poll = sqlRow ($DB['modules'], $query);

        $query = "SELECT Title, Question
                  FROM   mod_poll_questions
                  WHERE  NrPoll     = {$poll['Number']} AND 
                         IdLanguage = {$poll['DefaultIdLanguage']}";
        $res = sqlRow ($DB['modules'], $query);
        $poll['Title']    = htmlspecialchars($res['Title']);
        $poll['Question'] = htmlspecialchars($res['Question']);

    }

    if ($poll[DateBegin])  list ($curr[DateBegin][year], $curr[DateBegin][month], $curr[DateBegin][day]) = explode ("-", $poll[DateBegin]);
    if ($poll[DateExpire]) list ($curr[DateExpire][year], $curr[DateExpire][month], $curr[DateExpire][day]) = explode ("-", $poll[DateExpire]);

  ?>
  <form name="mod_poll_maindata" action="edit_answers.php" method="post">
  <input type="hidden" name="poll[old_DefaultIdLanguage]" value="<?php p($poll['DefaultIdLanguage']); ?>">
  <input type="hidden" name="poll[Number]" value="<?php p($poll['Number']); ?>">
  <table border="0" width="100%" BGCOLOR="#C0D0FF">
  <tr><td colspan="2"><b>
  <?php
  if ($poll['Number']) {
      putGS('Edit Poll'); 
  } else {
      putGS('New Poll');
  }
  ?>
  </b><br><br></th></tr>

  <tr><td><?php putGS('Default language'); ?></td><td><?php langMenu('poll[DefaultIdLanguage]', false); ?></td></tr>
  <tr><td><?php putGS("Title"); ?></td><td><input type="text" name="poll[Title]" value="<?php phtml($res['Title']); ?>" maxlength="50"></td></tr>
  <tr><td><?php putGS("Question"); ?></td><td><input type="text" name="poll[Question]" value="<?php echo phtml($res['Question']); ?>" maxlength="255" size="80"></td></tr>
  <tr><td><?php putGS("from"); ?></td>
  <td><?php dateSelectMenu ("poll", "DateBegin", $curr); ?></td></tr>
  <tr><td><?php putGS("to"); ?></td>
  <td><?php dateSelectMenu ("poll", "DateExpire", $curr); ?></td></tr>
  <tr><td><?php putGS("number of answers"); ?></td><td><input type="text" name="poll[NrOfAnswers]" value="<?php p($poll['NrOfAnswers']); ?>" maxlength="2" size="2"></tr>
  <tr><td><?php putGS("runout"); ?></td><td><input type="checkbox" name="poll[ShowAfterExpiration]" value="1" <?php if (!$poll['Number'] || $poll['ShowAfterExpiration']) print "checked"; ?>></td></tr>
  <tr><td><?php echo $poll[error]; ?>&nbsp;</td><td align="right"><input type="submit" value="<?php putGS("continue"); ?>"></td></tr>
  <?php
  if ($poll['Number']) {
    ?>
    <tr>
        <td valign="top"><?php putGS("as articles"); ?></td>
        <td>
        <?php
        $query = "SELECT a.Name,
                         a.Number 
                  FROM {$DB['campsite']}.Articles AS a, 
                       {$DB['modules']}.mod_poll_article as pa 
                  WHERE pa.NrPoll    = {$poll['Number']} AND 
                        pa.NrArticle = a.Number AND 
                        a.IdLanguage  = 1 
                        ORDER BY a.NrIssue DESC, a.Name";
        $articles = sqlQuery($DB['campsite'], $query);

        while ($article = mysql_fetch_array($articles)) {
            echo "$article[Name] ($article[Number])<br>";
        }
        ?>
        </td>
    </tr>
    
    <tr>
        <td valign="top"><?php putGS("as sections"); ?></td>
        <td>
        <?php
        $query = "SELECT s.Name,
                         s.Number 
                  FROM {$DB['campsite']}.Sections AS s, 
                       {$DB['modules']}.mod_poll_section as ps 
                  WHERE ps.NrPoll     = {$poll['Number']} AND 
                        ps.NrSection  = s.Number AND 
                        s.IdLanguage  = 1 
                  ORDER BY s.Number";
        $sections = sqlQuery($DB['campsite'], $query);

        while ($section = mysql_fetch_array ($sections)) {
            echo "$section[Name] ($section[Number])<br>";
        }
        ?>
        </td>
    </tr>
    
    <tr>
        <td valign="top"><?php putGS("as issues"); ?></td>
        <td>
        <?php
        $query = "SELECT i.Name,
                         i.Number 
                  FROM   {$DB['campsite']}.Issues AS i, 
                         {$DB['modules']}.mod_poll_issue as pi 
                  WHERE  pi.NrPoll    = {$poll['Number']} AND 
                         pi.NrIssue  = i.Number AND 
                         i.IdLanguage = 1 
                         ORDER BY i.Number DESC";
        $issues = sqlQuery($DB['campsite'], $query);

        while ($issue = mysql_fetch_array ($issues)) {
            echo "$issue[Name] ($issue[Number])<br>";
        }
        ?>
        </td>
    </tr>
    
    <tr>
        <td valign="top"><?php putGS("as pubs"); ?></td>
        <td>
        <?php
        $query = "SELECT p.Name,
                         p.Id 
                  FROM   {$DB['campsite']}.Publications AS p, 
                         {$DB['modules']}.mod_poll_publication as pp 
                  WHERE  pp.NrPoll     = {$poll['Number']} AND 
                         pp.IdPublication  = p.Id 
                  ORDER BY p.Id DESC";
        $pubs = sqlQuery($DB['campsite'], $query);

        while ($pub = mysql_fetch_array ($pubs)) {
            echo "$pub[Name] ($pub[Id])<br>";
        }
        ?>
        </td>
    </tr>
    <?php
  }
  ?>
  </table>
  </form>
  <?php
}
?>