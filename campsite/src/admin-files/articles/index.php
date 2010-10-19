<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/articles/article_common.php");
require_once($GLOBALS['g_campsiteDir']. '/classes/DbObjectArray.php');
require_once($GLOBALS['g_campsiteDir']. '/classes/ArticlePublish.php');
require_once($GLOBALS['g_campsiteDir']. '/classes/ArticleImage.php');
require_once($GLOBALS['g_campsiteDir']. '/classes/ArticleTopic.php');
require_once($GLOBALS['g_campsiteDir']. '/classes/ArticleComment.php');
require_once($GLOBALS['g_campsiteDir']. '/classes/SimplePager.php');

require_once dirname(__FILE__) . '/../smartlist/Smartlist.php';

camp_load_translation_strings("api");

$f_publication_id = Input::Get('f_publication_id', 'int', 0);
$f_issue_number = Input::Get('f_issue_number', 'int', 0);
$f_section_number = Input::Get('f_section_number', 'int', 0);
$f_language_id = Input::Get('f_language_id', 'int', 0);
if (isset($_SESSION['f_language_selected'])) {
	$f_old_language_selected = (int)$_SESSION['f_language_selected'];
} else {
	$f_old_language_selected = 0;
}
$f_language_selected = (int)camp_session_get('f_language_selected', 0);
$offsetVarName = "f_article_offset_".$f_publication_id."_".$f_issue_number."_".$f_language_id."_".$f_section_number;
$f_article_offset = camp_session_get($offsetVarName, 0);
$ArticlesPerPage = 15;

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
	exit;
}

if ($f_old_language_selected != $f_language_selected) {
	camp_session_set('f_article_offset', 0);
	$f_article_offset = 0;
}

if ($f_article_offset < 0) {
	$f_article_offset = 0;
}

$sectionObj = new Section($f_publication_id, $f_issue_number, $f_language_id, $f_section_number);
if (!$sectionObj->exists()) {
	camp_html_display_error(getGS('Section does not exist.'));
	exit;
}

$publicationObj = new Publication($f_publication_id);
if (!$publicationObj->exists()) {
	camp_html_display_error(getGS('Publication does not exist.'));
	exit;
}

$issueObj = new Issue($f_publication_id, $f_language_id, $f_issue_number);
if (!$issueObj->exists()) {
	camp_html_display_error(getGS('Issue does not exist.'));
	exit;
}

$allArticleLanguages = $issueObj->getLanguages();
if (!in_array($f_language_selected, DbObjectArray::GetColumn($allArticleLanguages, 'Id'))) {
	$f_language_selected = 0;
}

$sqlOptions = array("LIMIT" => array("START" => $f_article_offset,
									 "MAX_ROWS" => $ArticlesPerPage));
if ($f_language_selected) {
	// Only show a specific language.
	$totalArticles = Article::GetArticles($f_publication_id,
										  $f_issue_number,
										  $f_section_number,
										  $f_language_selected,
										  null,
										  true);
	$allArticles = Article::GetArticles($f_publication_id,
										$f_issue_number,
										$f_section_number,
										$f_language_selected,
										$sqlOptions);
	$numUniqueArticles = $totalArticles;
	$numUniqueArticlesDisplayed = count($allArticles);
} else {
	// Show articles in all languages.
	$totalArticles = Article::GetArticles($f_publication_id,
										  $f_issue_number,
										  $f_section_number,
										  null,
										  null,
										  true);
	$allArticles = Article::GetArticlesGrouped($f_publication_id,
											   $f_issue_number,
											   $f_section_number,
											   null,
											   $f_language_id,
											   $sqlOptions);
	$numUniqueArticles = Article::GetArticlesGrouped($f_publication_id,
													 $f_issue_number,
													 $f_section_number,
													 null,
													 null,
													 null,
													 true);
	$numUniqueArticlesDisplayed = count(array_unique(DbObjectArray::GetColumn($allArticles, 'Number')));
}
$numArticlesThisPage = count($allArticles);

$previousArticleNumber = 0;

$pagerUrl = "index.php?f_publication_id=".$f_publication_id
	."&f_issue_number=".$f_issue_number
	."&f_section_number=".$f_section_number
	."&f_language_id=".$f_language_id
	."&f_language_selected=".$f_language_selected."&";
$pager = new SimplePager($numUniqueArticles, $ArticlesPerPage, $offsetVarName, $pagerUrl);

$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj,
				  'Section' => $sectionObj);
camp_html_content_top(getGS('Article List'), $topArray);

include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");

?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1" class="action_buttons" style="padding-top: 5px;">
<TR>
	<TD><A HREF="/<?php echo $ADMIN; ?>/sections/?Pub=<?php p($f_publication_id); ?>&Issue=<?php p($f_issue_number); ?>&Language=<?php p($f_language_id); ?>"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" BORDER="0"></A></TD>
	<TD><A HREF="/<?php echo $ADMIN; ?>/sections/?Pub=<?php p($f_publication_id); ?>&Issue=<?php p($f_issue_number); ?>&Language=<?php p($f_language_id); ?>"><B><?php  putGS("Section List"); ?></B></A></TD>
	<?php if ($g_user->hasPermission('AddArticle')) { ?>
	<TD style="padding-left: 20px;"><A HREF="add.php?f_publication_id=<?php p($f_publication_id); ?>&f_issue_number=<?php p($f_issue_number); ?>&f_section_number=<?php p($f_section_number); ?>&f_language_id=<?php p($f_language_id); ?>" ><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0"></A></TD>
	<TD><A HREF="add.php?f_publication_id=<?php p($f_publication_id); ?>&f_issue_number=<?php p($f_issue_number); ?>&f_section_number=<?php p($f_section_number); ?>&f_language_id=<?php p($f_language_id); ?>" ><B><?php  putGS("Add new article"); ?></B></A></TD>
	<?php  } ?>
</tr>
</TABLE>

<?php
    $smartlist = new Smartlist();

    $smartlist->setPublication($f_publication_id);
    $smartlist->setIssue($f_issue_number);
    $smartlist->setSection($f_section_number);
    $smartlist->setOrder(TRUE);

    $smartlist->renderActions();
    $smartlist->render();
?>
<FORM name="article_list" action="do_article_list_action.php" method="POST">
<?php echo SecurityToken::FormParameter(); ?>
<INPUT TYPE="HIDDEN" NAME="f_publication_id" VALUE="<?php p($f_publication_id); ?>">
<INPUT TYPE="HIDDEN" NAME="f_issue_number" VALUE="<?php p($f_issue_number); ?>">
<INPUT TYPE="HIDDEN" NAME="f_section_number" VALUE="<?php p($f_section_number); ?>">
<INPUT TYPE="HIDDEN" NAME="f_language_id" VALUE="<?php p($f_language_id); ?>">
<INPUT TYPE="HIDDEN" NAME="f_language_selected" VALUE="<?php p($f_language_selected); ?>">
<INPUT TYPE="HIDDEN" NAME="f_total_articles" VALUE="<?php p($totalArticles); ?>">
<TABLE CELLSPACING="0" CELLPADDING="0" class="table_actions">
<TR>
	<TD>
		<TABLE cellpadding="0" cellspacing="0">
		<TR>
			<TD ALIGN="left">
				<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3" >
				<TR>
					<TD><?php  putGS('Language'); ?>:</TD>
					<TD valign="middle">
						<SELECT NAME="f_language_selected" id="f_language_selected" class="input_select" onchange="location.href='index.php?f_publication_id=<?php p($f_publication_id); ?>&f_issue_number=<?php p($f_issue_number); ?>&f_section_number=<?php p($f_section_number); ?>&f_language_id=<?php p($f_language_id); ?>&<?php p($offsetVarName); ?>=0&f_language_selected='+document.getElementById('f_language_selected').options[document.getElementById('f_language_selected').selectedIndex].value;">
						<option value="0"><?php putGS("All"); ?></option>
						<?php
						foreach ($allArticleLanguages as $languageItem) {
							echo '<OPTION value="'.$languageItem->getLanguageId().'"' ;
							if ($languageItem->getLanguageId() == $f_language_selected) {
								echo " selected";
							}
							echo '>'.htmlspecialchars($languageItem->getNativeName()).'</option>';
						} ?>
						</SELECT>
					</TD>
				</TR>
				</TABLE>
			</TD>
			<TD style="padding-left: 20px;">
				<script>
				function action_selected(dropdownElement)
				{
					// Verify that at least one checkbox has been selected.
					checkboxes = document.forms.article_list["f_article_code[]"];
					if (checkboxes) {
						isValid = false;
						numCheckboxesChecked = 0;
						// Special case for single checkbox
						// (when there is only one article in the section).
						if (!checkboxes.length) {
							isValid = checkboxes.checked;
							numCheckboxesChecked = isValid ? 1 : 0;
						}
						else {
							// Multiple checkboxes
							for (var index = 0; index < checkboxes.length; index++) {
								if (checkboxes[index].checked) {
									isValid = true;
									numCheckboxesChecked++;
								}
							}
						}
						if (!isValid) {
							alert("<?php putGS("You must select at least one article to perform an action."); ?>");
							dropdownElement.options[0].selected = true;
							return;
						}
					}
					else {
						dropdownElement.options[0].selected = true;
						return;
					}

					// Get the index of the "delete" option.
					deleteOptionIndex = -1;
//					translateOptionIndex = -1;
					for (var index = 0; index < dropdownElement.options.length; index++) {
						if (dropdownElement.options[index].value == "delete") {
							deleteOptionIndex = index;
						}
//						if (dropdownElement.options[index].value == "translate") {
//							translateOptionIndex = index;
//						}
					}

					// if the user has selected the "delete" option
					if (dropdownElement.selectedIndex == deleteOptionIndex) {
						ok = confirm("<?php putGS("Are you sure you want to delete the selected articles?"); ?>");
						if (!ok) {
							dropdownElement.options[0].selected = true;
							return;
						}
					}

					// if the user selected the "translate" option
//					if ( (dropdownElement.selectedIndex == translateOptionIndex)
//						 && (numCheckboxesChecked > 1) ) {
//						alert("<?php putGS("You may only translate one article at a time."); ?>");
//						dropdownElement.options[0].selected = true;
//						return;
//					}

					// do the action if it isnt the first or second option
					if ( (dropdownElement.selectedIndex != 0) &&  (dropdownElement.selectedIndex != 1) ) {
						dropdownElement.form.submit();
					}
				}
				</script>
				<SELECT name="f_article_list_action" class="input_select" onchange="action_selected(this);">
				<OPTION value=""><?php putGS("Actions"); ?>...</OPTION>
				<OPTION value="">-----------------------</OPTION>

				<?php if ($g_user->hasPermission('Publish') && $issueObj->isPublished()) { ?>
				<OPTION value="workflow_publish"><?php echo getGS("Status") . ': ' . getGS("Publish"); ?></OPTION>
				<?php } ?>

				<?php if ($g_user->hasPermission('Publish') && !$issueObj->isPublished()) { ?>
				<OPTION value="workflow_publish"><?php echo getGS("Status") . ': ' . getGS("Publish with issue"); ?></OPTION>
				<?php } ?>

				<?php if ($g_user->hasPermission('ChangeArticle')) { ?>
				<OPTION value="workflow_submit"><?php echo getGS("Status") . ': ' . getGS("Submit"); ?></OPTION>
				<?php } ?>

				<?php if ($g_user->hasPermission('Publish')) { ?>
				<OPTION value="workflow_new"><?php echo getGS("Status") . ': ' . getGS("New"); ?></OPTION>
				<?php } ?>

				<?php if ($g_user->hasPermission('ChangeArticle')) { ?>
				<OPTION value="toggle_front_page"><?php putGS("Toggle '$1'", getGS("On Front Page")); ?></OPTION>
				<OPTION value="toggle_section_page"><?php putGS("Toggle '$1'", getGS("On Section Page")); ?></OPTION>
				<?php } ?>

				<?php if ($g_user->hasPermission('CommentEnable')) { ?>
				<OPTION value="toggle_comments"><?php putGS("Toggle '$1'", getGS("Comments")); ?></OPTION>
				<?php } ?>

				<OPTION value="schedule_publish"><?php putGS("Publish Schedule"); ?></OPTION>
				<OPTION value="unlock"><?php putGS("Unlock"); ?></OPTION>

				<?php if ($g_user->hasPermission('DeleteArticle')) { ?>
				<OPTION value="delete"><?php putGS("Delete"); ?></OPTION>
				<?php } ?>

				<?php if ($g_user->hasPermission('AddArticle')) { ?>
				<OPTION value="copy"><?php putGS("Duplicate"); ?></OPTION>
				<OPTION value="copy_interactive"><?php putGS("Duplicate to another section"); ?></OPTION>
				<?php } ?>

				<?php if ($g_user->hasPermission("MoveArticle")) { ?>
				<option value="move"><?php putGS("Move"); ?></OPTION>
				<?php } ?>
				</SELECT>
			</TD>

			<TD style="padding-left: 5px; font-weight: bold;">
				<input type="button" class="button" value="<?php putGS("Select All"); ?>" onclick="checkAll(<?php p($numArticlesThisPage); ?>);">
				<input type="button" class="button" value="<?php putGS("Select None"); ?>" onclick="uncheckAll(<?php p($numArticlesThisPage); ?>);">
			</TD>
		</TR>
		</TABLE>
	</TD>

</TR>
</TABLE>
<?php
camp_html_display_msgs("0.5em", 0);

if ($numUniqueArticlesDisplayed > 0) {
	$counter = 0;
	$color = 0;
?>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" class="table_list" style="padding-top: 5px;">
<TR class="table_list_header">
	<TD>&nbsp;</TD>
	<TD ALIGN="LEFT" VALIGN="TOP"><?php  putGS("Name <SMALL>(click to edit)</SMALL>"); ?></TD>
	<?php if ($g_user->hasPermission("Publish")) { ?>
	<TD align="center" valign="top"><?php putGS("Order"); ?></TD>
	<?php } ?>
	<TD ALIGN="center" VALIGN="TOP"><?php  putGS("Type"); ?></TD>
	<TD ALIGN="center" VALIGN="TOP"><?php  putGS("Created by"); ?></TD>
	<TD ALIGN="center" VALIGN="TOP"><?php  putGS("Status"); ?></TD>
	<TD ALIGN="center" VALIGN="TOP"><?php  echo str_replace(' ', '<br>', getGS("On Front Page")); ?></TD>
	<TD ALIGN="center" VALIGN="TOP"><?php  echo str_replace(' ', '<br>', getGS("On Section Page")); ?></TD>
	<TD ALIGN="center" VALIGN="TOP"><?php  putGS("Images"); ?></TD>
	<TD ALIGN="center" VALIGN="TOP"><?php  putGS("Topics"); ?></TD>
	<TD ALIGN="center" VALIGN="TOP"><?php  putGS("Comments"); ?></TD>
    <TD ALIGN="center" VALIGN="TOP"><?php  putGS("Reads"); ?></TD>
	<TD align="center" valign="top"><?php //putGS("Preview"); ?></TD>
	<?php  if ($g_user->hasPermission('AddArticle')) { ?>
	<TD align="center" valign="top"><?php //putGS("Translate"); ?></TD>
	<?php } ?>
</TR>
<?php
$articleTopicNames = array();
$uniqueArticleCounter = 0;
foreach ($allArticles as $articleObj) {
	if ($articleObj->getArticleNumber() != $previousArticleNumber) {
		$uniqueArticleCounter++;
	}
	if ($uniqueArticleCounter > $ArticlesPerPage) {
		break;
	}
	$timeDiff = camp_time_diff_str($articleObj->getLockTime());
	if ($articleObj->isLocked() && ($timeDiff['days'] <= 0) && ($articleObj->getLockedByUser() != $g_user->getUserId())) {
	    $rowClass = "article_locked";
	}
	else {
    	if ($color) {
    	    $rowClass = "list_row_even";
    	} else {
    	    $rowClass = "list_row_odd";
    	}
	}
	$color = !$color;

	// Get article topic list to show up in tooltip
	$topics = ArticleTopic::GetArticleTopics($articleObj->getArticleNumber());
	$topicNames = array();
	foreach($topics as $topic) {
	    $path = $topic->getPath();
	    $pathStr = '';
	    foreach ($path as $element) {
	        $name = $element->getName($f_language_id);
		if (empty($name)) {
		    $name = $element->getName(1);
		    if (empty($name)) {
		        $name = "-----";
		    }
		}
		$pathStr .= ' / '. htmlspecialchars($name);
	    }
	    $topicNames[] = $pathStr;
	}
	if ($topicNames) {
	    $articleTopicNames[$articleObj->getArticleNumber()] = $topicNames;
	}

	// Remember the default class so we can restore it when "Select None" is clicked
	// or the mouse leaves the row after hovering on it.
	?>
	<script>default_class[<?php p($counter); ?>] = "<?php p($rowClass); ?>";</script>
	<TR id="row_<?php p($counter); ?>" class="<?php p($rowClass); ?>" onmouseover="setPointer(this, <?php p($counter); ?>, 'over');" onmouseout="setPointer(this, <?php p($counter); ?>, 'out');">
		<TD>
			<input type="checkbox" value="<?php p((int)$articleObj->getArticleNumber().'_'.(int)$articleObj->getLanguageId()); ?>" name="f_article_code[]" id="checkbox_<?php p($counter); ?>" class="input_checkbox" onclick="checkboxClick(this, <?php p($counter); ?>);">
		</TD>

		<TD <?php if ($articleObj->getArticleNumber() == $previousArticleNumber) { ?>class="translation_indent"<?php } ?>>

		<?php
		if ($articleObj->getArticleNumber() != $previousArticleNumber) {
			echo $f_article_offset + $uniqueArticleCounter.". ";
		}
		// Is article locked?
		if ($articleObj->isLocked() && ($timeDiff['days'] <= 0)) {
            $lockUserObj = new User($articleObj->getLockedByUser());
			if ($timeDiff['hours'] > 0) {
				$lockInfo = getGS('The article has been locked by $1 ($2) $3 hour(s) and $4 minute(s) ago.',
					  htmlspecialchars($lockUserObj->getRealName()),
					  htmlspecialchars($lockUserObj->getUserName()),
					  $timeDiff['hours'], $timeDiff['minutes']);
			}
			else {
				$lockInfo = getGS('The article has been locked by $1 ($2) $3 minute(s) ago.',
					  htmlspecialchars($lockUserObj->getRealName()),
					  htmlspecialchars($lockUserObj->getUserName()),
					  $timeDiff['minutes']);
			}

		    ?>
		    <img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/lock-16x16.png" width="16" height="16" border="0" alt="<?php  p($lockInfo); ?>" title="<?php p($lockInfo); ?>">
		    <?php
		}
		?>
		<A HREF="/<?php echo $ADMIN; ?>/articles/edit.php?f_publication_id=<?php  p($f_publication_id); ?>&f_issue_number=<?php  p($f_issue_number); ?>&f_section_number=<?php  p($f_section_number); ?>&f_article_number=<?php p($articleObj->getArticleNumber()); ?>&f_language_id=<?php  p($f_language_id); ?>&f_language_selected=<?php p($articleObj->getLanguageId()); ?>"><?php  p(wordwrap(htmlspecialchars($articleObj->getTitle()), 80, "<br>")); ?>&nbsp;</A> (<?php p(htmlspecialchars($articleObj->getLanguageName())); ?>)
		</TD>

		<?php
		// The MOVE links
		if ($g_user->hasPermission('Publish')) {
			if (($articleObj->getArticleNumber() == $previousArticleNumber) || ($numUniqueArticles <= 1))  {
				?>
				<TD ALIGN="CENTER" valign="middle" NOWRAP></TD>
				<?php
			}
			else {
				?>
				<TD ALIGN="right" valign="middle" NOWRAP style="padding: 1px;">
					<table cellpadding="0" cellspacing="0">
					<tr>
						<td width="18px">
							<?php if (($f_article_offset > 0) || ($uniqueArticleCounter != 1)) { ?>
								<A HREF="/<?php echo $ADMIN; ?>/articles/do_position.php?f_publication_id=<?php p($f_publication_id); ?>&f_issue_number=<?php p($f_issue_number); ?>&f_section_number=<?php p($f_section_number); ?>&f_article_number=<?php p($articleObj->getArticleNumber()); ?>&f_article_language=<?php p($articleObj->getLanguageId());?>&f_language_id=<?php p($f_language_id); ?>&f_language_selected=<?php p($f_language_selected); ?>&f_move=up_rel&f_position=1&<?php echo SecurityToken::URLParameter(); ?>"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/up-16x16.png" width="16" height="16" border="0"></A>
							<?php } ?>
						</td>
						<td width="20px">
							<?php if (($uniqueArticleCounter + $f_article_offset) < $numUniqueArticles) { ?>
								<A HREF="/<?php echo $ADMIN; ?>/articles/do_position.php?f_publication_id=<?php p($f_publication_id); ?>&f_issue_number=<?php p($f_issue_number); ?>&f_section_number=<?php p($f_section_number); ?>&f_article_number=<?php p($articleObj->getArticleNumber()); ?>&f_article_language=<?php p($articleObj->getLanguageId());?>&f_language_id=<?php p($f_language_id); ?>&f_language_selected=<?php p($f_language_selected); ?>&f_move=down_rel&f_position=1&<?php echo SecurityToken::URLParameter(); ?>"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/down-16x16.png" width="16" height="16" border="0" style="padding-left: 3px; padding-right: 3px;"></A>
							<?php } ?>
						</td>

						<td>
							<select name="f_position_<?php p($counter);?>" onChange="positionValue = document.forms.article_list.f_position_<?php p($counter); ?>.options[document.forms.article_list.f_position_<?php p($counter); ?>.selectedIndex].value; url = '/<?php p($ADMIN);?>/articles/do_position.php?f_publication_id=<?php p($f_publication_id); ?>&f_issue_number=<?php p($f_issue_number); ?>&f_section_number=<?php p($f_section_number); ?>&f_language_id=<?php p($f_language_id); ?>&f_language_selected=<?php p($f_language_selected);?>&f_article_language=<?php p($articleObj->getLanguageId()); ?>&f_article_number=<?php p($articleObj->getArticleNumber());?>&f_move=abs&<?php echo SecurityToken::URLParameter(); ?>&f_position='+positionValue; location.href=url;" class="input_select" style="font-size: smaller;">
							<?php
							for ($j = 1; $j <= $numUniqueArticles; $j++) {
								if (($f_article_offset + $uniqueArticleCounter) == $j) {
									echo "<option value=\"$j\" selected>$j</option>\n";
								} else {
									echo "<option value=\"$j\">$j</option>\n";
								}
							}
							?>
							</select>
						</td>

					</tr>
					</table>
				</TD>
				<?php
				}
		} // if user->hasPermission('publish')
		?>

		<TD ALIGN="center">
			<?php p(htmlspecialchars($articleObj->getTranslateType()));  ?>
		</TD>

		<TD ALIGN="center">
			<?php
			$articleCreator = new User($articleObj->getCreatorId());
			p(htmlspecialchars($articleCreator->getRealName()));  ?>
		</TD>

		<TD ALIGN="CENTER" valign="middle" nowrap>
			<table cellpadding=0 cellspacing=0><tr><td>
			<?php p($articleObj->getWorkflowDisplayString()); ?>
			</td>
			<?php
			if ($articleObj->getWorkflowStatus() != 'N') {
				$hasPendingActions =
					ArticlePublish::ArticleHasFutureActions($articleObj->getArticleNumber(),
					$articleObj->getLanguageId());
				if ($hasPendingActions) { ?>
			<td style="padding-left: 3px"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/automatic_publishing.png" alt="<?php  putGS("Scheduled Publishing"); ?>" title="<?php  putGS("Scheduled Publishing"); ?>" border="0" width="22" height="22" align="middle" ></td>
				<?php
				}
			}
			?>
			</tr>
			</table>
		</TD>

		<TD align="center"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/<?php p($articleObj->onFrontPage() ? "is_shown.png" : "is_hidden.png"); ?>" border="0"></TD>
		<TD align="center"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/<?php p($articleObj->onSectionPage() ? "is_shown.png" : "is_hidden.png"); ?>" border="0"></TD>
		<TD align="center"><?php echo ArticleImage::GetImagesByArticleNumber($articleObj->getArticleNumber(), true); ?></TD>
                <?php
                $nrOfTopics = ArticleTopic::GetArticleTopics($articleObj->getArticleNumber(), true);
		if ($nrOfTopics) {
                ?>
                <TD align="center" id="<?php echo 'ttctx'.$articleObj->getArticleNumber(); ?>" class="ttControl">
		<? } else { ?>
                <TD align="center">
		<?php } echo $nrOfTopics; ?>
                </TD>
		<TD align="center"><?php if ($articleObj->commentsEnabled()) { echo ArticleComment::GetArticleComments($articleObj->getArticleNumber(), $articleObj->getLanguageId(), null, true); } else { ?><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/is_hidden.png" border="0"><?php } ?></TD>
		<TD align="center">
		<?php
		  if ($articleObj->isPublished()) {
		      $requestObject = new RequestObject($articleObj->getProperty('object_id'));
		      if ($requestObject->exists()) {
		          echo $requestObject->getRequestCount();
		      } else {
		          echo "0";
		      }
		  } else {
		      putGS("N/A");
		  }
		?>
		</TD>

		<TD ALIGN="CENTER">
			<A HREF="" ONCLICK="window.open('/<?php echo $ADMIN; ?>/articles/preview.php?f_publication_id=<?php  p($f_publication_id); ?>&f_issue_number=<?php p($f_issue_number); ?>&f_section_number=<?php p($f_section_number); ?>&f_article_number=<?php p($articleObj->getArticleNumber()); ?>&f_language_id=<?php p($f_language_id); ?>&f_language_selected=<?php p($articleObj->getLanguageId()); ?>', 'fpreview', 'resizable=yes, menubar=no, toolbar=yes, width=800, height=600'); return false"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/preview-16x16.png" alt="<?php  putGS("Preview"); ?>" title="<?php putGS('Preview'); ?>" border="0" width="16" height="16"></A>
		</TD>

		<?php  if ($g_user->hasPermission('TranslateArticle')) { ?>
		<TD ALIGN="CENTER">
			<A HREF="/<?php echo $ADMIN; ?>/articles/translate.php?f_publication_id=<?php p($f_publication_id); ?>&f_issue_number=<?php p($f_issue_number); ?>&f_section_number=<?php p($f_section_number); ?>&f_article_code=<?php p($articleObj->getArticleNumber()); ?>_<?php p($articleObj->getLanguageId()); ?>&f_language_id=<?php p($f_language_id); ?>"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/translate-16x16.png" alt="<?php  putGS("Translate"); ?>" title="<?php  putGS("Translate"); ?>" border="0" width="16" height="16"></A>
		</TD>
		<?php } ?>


		<?php
		if ($articleObj->getArticleNumber() != $previousArticleNumber) {
			$previousArticleNumber = $articleObj->getArticleNumber();
		}
		$counter++;
		?>
	</TR>
	<?php
} // foreach
?>
</table>
<table class="table_list" style="padding-top: 5px;">
<tr>
	<td>
		<b><?php putGS("$1 articles found", $numUniqueArticles); ?></b>
	</td>
</tr>
</TABLE>
<table class="indent">
<TR>
	<TD NOWRAP>
		<?php echo $pager->render(); ?>
	</TD>
</TR>
</table>
</form>
<?php
if (is_array($articleTopicNames) && sizeof($articleTopicNames) > 0) {
?>
<script type="text/javascript">
    YAHOO.namespace("example.container");
<?php
    $x = 1;
    foreach ($articleTopicNames as $articleNr => $articleTopicNameArray) {
        $articleTopicPath = '';
	foreach ($articleTopicNameArray as $articleTopicName) {
	    $articleTopicPath .= addslashes($articleTopicName).'<br/>';
	}
	echo "YAHOO.example.container.tt$x = new YAHOO.widget.Tooltip('tt$x', { context:'ttctx$articleNr', text:'$articleTopicPath' } );\n";
	$x++;
    }
?>
</script>
<?php }
       } else { ?><BLOCKQUOTE>
	<LI><?php  putGS('No articles.'); ?></LI>
	</BLOCKQUOTE>
<?php  } ?>
<?php camp_html_copyright_notice(); ?>
