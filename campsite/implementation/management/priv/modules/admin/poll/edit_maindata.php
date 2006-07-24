<?php
require_once $Campsite['HTML_DIR']."/$ADMIN_DIR/modules/start.ini.php";

$access = startModAdmin ("ManagePoll", "Poll", 'Edit questions');
if ($access) {

    if (file_exists(dirname(__FILE__)."/locals.{$_REQUEST['TOL_Language']}.php")) {
        require_once "locals.{$_REQUEST['TOL_Language']}.php";
    } elseif(file_exists(dirname(__FILE__)."/locals.{$_REQUEST['TOL_Language']}.php"))  {
        require_once 'locals.en.php';
    }

    $poll = $_REQUEST['poll'];
    $act  = $_REQUEST['act'];

    if ($act=="change") {
        $query = "SELECT * FROM poll_main WHERE id=$poll[id]";
        $poll = sqlRow ($DB[poll], $query);

        $query = "SELECT title, question FROM poll_questions WHERE id_poll=$poll[id] AND id_language='$SYS[default_lang]'";
        $res = sqlRow ($DB[poll], $query);
        $poll[title] = htmlspecialchars ($res[title]);
        $poll[question] = htmlspecialchars ($res[question]);

    }

    if ($poll[dfrom]) list ($curr[dfrom][year], $curr[dfrom][month], $curr[dfrom][day]) = explode ("-", $poll[dfrom]);
    if ($poll[dto])   list ($curr[dto][year], $curr[dto][month], $curr[dto][day]) = explode ("-", $poll[dto]);

  ?>
  <form name="poll_maindata" action="edit_answers.php" method="post">
  <table border="0" width="100%" BGCOLOR="#C0D0FF">
  <tr><td colspan="2"><b>
  <?php
  if ($lang) print "<input type='hidden' name='lang' value='$lang'>";
  if ($poll[id]) putGS("edit poll")."\n<input type='hidden' name='poll[id]' value='$poll[id]'>";
  else putGS("make new"); ?>
  </b><br><br></th></tr>

  <tr><td><?php putGS("title"); ?></td><td><input type="text" name="poll[title]" value="<?php echo $poll[title]; ?>" maxlength="50"></td></tr>
  <tr><td><?php putGS("question"); ?></td><td><input type="text" name="poll[question]" value="<?php echo $poll[question]; ?>" maxlength="255" size="80"></td></tr>
  <tr><td><?php putGS("from"); ?></td>
  <td><?php dateSelectMenu ("poll", "dfrom", $curr); ?></td></tr>
  <tr><td><?php putGS("to"); ?></td>
  <td><?php dateSelectMenu ("poll", "dto", $curr); ?></td></tr>
  <tr><td><?php putGS("number of answers"); ?></td><td><input type="text" name="poll[answers]" value="<?php echo $poll[answers]; ?>" maxlength="2" size="2"></tr>
  <tr><td><?php putGS("runout"); ?></td><td><input type="checkbox" name="poll[beyond]" value="1" <?php if (!$poll[id] || $poll[beyond]) print "checked"; ?>></td></tr>
  <tr><td><?php echo $poll[error]; ?>&nbsp;</td><td align="right"><input type="submit" value="<?php putGS("continue"); ?>"></td></tr>
  <?php
  if ($poll[id])
  {
    ?>
    <input type="hidden" name="poll[id]" value="<?php print $poll[id]; ?>">

    <tr><td valign="top"><?php putGS("as articles"); ?></td><td><?php
    $articles = sqlQuery($DB[campsite], "SELECT a.Name, a.NUMBER FROM $DB[campsite].Articles AS a, $DB[poll].poll_article as pa WHERE pa.id_poll=$poll[id] AND pa.id_article=a.NUMBER AND a.IdLanguage=1 ORDER BY a.NrIssue DESC, a.Name");
    while ($article = mysql_fetch_array ($articles)) echo "$article[Name] ($article[NUMBER])<br>";
    if (!$article[Name]) print "-";
    ?></td></tr>
    <tr><td valign="top"><?php putGS("as sections"); ?></td><td><?php
    $sections = sqlQuery ($DB[campsite], "SELECT s.Name, s.NUMBER FROM $DB[campsite].Sections AS s, $DB[poll].poll_section as ps WHERE ps.id_poll=$poll[id] AND ps.id_section=s.NUMBER AND s.IdLanguage=1 ORDER BY s.Number");
    while ($section = mysql_fetch_array ($sections)) echo "$section[Name] ($section[NUMBER])<br>";
    if (!$section[Name]) print "-";
    ?></td></tr>
    <tr><td valign="top"><?php putGS("as issues"); ?></td><td><?php
    $issues = sqlQuery($DB[campsite], "SELECT i.Name, i.NUMBER FROM $DB[campsite].Issues AS i, $DB[poll].poll_issue as pi WHERE pi.id_poll=$poll[id] AND pi.id_issue=i.NUMBER AND i.IdLanguage=1 ORDER BY i.NUMBER DESC");
    while ($issue = mysql_fetch_array ($issues)) echo "$issue[Name] ($issue[NUMBER])<br>";
    if (!$issue[Name]) print "-";
    ?></td></tr>
    <tr><td valign="top"><?php putGS("as pubs"); ?></td><td><?php
    $pubs = sqlQuery($DB[campsite], "SELECT p.Name, p.Id FROM $DB[campsite].Publications AS p, $DB[poll].poll_article as pa WHERE pa.id_poll=$poll[id] AND pa.id_article=p.Id ORDER BY p.Id DESC");
    while ($pub = mysql_fetch_array ($pubs)) echo "$pub[Name] ($pub[Id])<br>";
    if (!$pub[Name]) print "-";
    ?></td></tr>
    <?php
  }
  ?>
  </table>
  </form>

  <?php
}
?>