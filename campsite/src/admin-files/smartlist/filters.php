<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

require_once $GLOBALS['g_campsiteDir'] . '/classes/Publication.php';
require_once $GLOBALS['g_campsiteDir'] . '/classes/Issue.php';
require_once $GLOBALS['g_campsiteDir'] . '/classes/Section.php';
require_once $GLOBALS['g_campsiteDir'] . '/classes/Topic.php';
require_once $GLOBALS['g_campsiteDir'] . '/classes/Author.php';

// get publications
$publications = Publication::GetPublications();
$publicationsNo = is_array($publications) ? sizeof($publications) : 0;
$menuPubTitle = $publicationsNo > 0 ? getGS('All Publications') : getGS('No publications found');

// get issues
$issues = Issue::GetIssues($this->publication, $this->language);
$issuesNo = is_array($issues) ? sizeof($issues) : 0;
$menuIssueTitle = $issuesNo > 0 ? getGS('All Issues') : getGS('No issues found');

// get sections
$sections = Section::GetSections($this->publication, $this->issue, $this->language);
$sectionsNo = is_array($sections) ? sizeof($sections) : 0;
$menuSectionTitle = $sectionsNo > 0 ? getGS('All Sections') : getGS('No sections found');
?>
<div class="smartlist filters">

<fieldset class="filters">
    <legend><?php putGS('Filter'); ?></legend>
    <select name="publication">
        <?php if ($publicationsNo > 0) { ?>
        <option value="0"><?php p($menuPubTitle); ?></option>
        <?php foreach($publications as $tmpPublication) { ?>
        <option value="<?php echo $tmpPublication->getPublicationId(); ?>"><?php echo $tmpPublication->getName(); ?></option>
        <?php }
        } ?>
    </select>

    <select name="issue">
        <?php if ($issuesNo > 0) { ?>
        <option value="0"><?php p($menuIssueTitle); ?></option>
        <?php foreach($issues as $issue) { ?>
        <option value="<?php echo $issue->getIssueNumber(); ?>"><?php echo $issue->getName(); ?></option>
        <?php }
        } ?>
    </select>

    <select name="section">
        <?php if ($sectionsNo > 0) { ?>
        <option value="0"><?php p($menuSectionTitle); ?></option>
        <?php foreach($sections as $section) { ?>
        <option value="<?php echo $section->getSectionNumber(); ?>"><?php echo $section->getName(); ?></option>
        <?php }
        } ?>
    </select>

    <div class="extra">

    <dl>
        <dt><label for="filter_date"><?php putGS('Date'); ?></label></dt>
        <dd><input id="filter_date" type="text" name="publish_date" class="date" /></dd>
    </dl>
    <dl>
        <dt><label for="filter_from"><?php putGS('From'); ?></label></dt>
        <dd><input id="filter_from" type="text" name="publish_date_from" class="date" /></dd>
    </dl>
    <dl>
        <dt><label for="filter_to"><?php putGS('To'); ?></label></dt>
        <dd><input id="filter_to" type="text" name="publish_date_to" class="date" /></dd>
    </dl>
    <dl>
        <dt><label for="filter_author"><?php putGS('Author'); ?></label></dt>
        <dd><select name="author">
            <option value=""><?php putGS('All'); ?></option>
            <?php foreach (Author::GetAuthors() as $author) { ?>
            <option value="<?php echo $author->getId(); ?>"><?php echo $author->getName(); ?></option>
            <?php } ?>
        </select></dd>
    </dl>
    <dl> <dt><label for="filter_creator"><?php putGS('Creator'); ?></label></dt>
        <dd><select name="creator">
            <option value=""><?php putGS('All'); ?></option>
            <?php foreach (User::GetUsers() as $creator) { ?>
            <option value="<?php echo $creator->getUserId(); ?>"><?php echo $creator->getRealName(); ?></option>
            <?php } ?>
        </select></dd>
    </dl>
    <dl>
        <dt><label for="filter_status"><?php putGS('Status'); ?></label></dt>
        <dd><select name="workflow_status">
            <option value=""><?php putGS('All'); ?></option>
            <option value="published"><?php putGS('Published'); ?></option>
            <option value="new"><?php putGS('New'); ?></option>
            <option value="submitted"><?php putGS('Submitted'); ?></option>
            <option value="withissue"><?php putGS('Publish with issue'); ?></option>
        </select></dd>
    </dl>
    <dl>
        <dt><label for="filter_topic"><?php putGS('Topic'); ?></label></dt>
        <dd><select name="topic">
            <option value=""><?php putGS('All'); ?></option>
            <?php foreach (Topic::GetTopics($f_language_id) as $topic) { ?>
            <option value="<?php echo $topic->getTopicId(); ?>"><?php echo $topic->getName($f_language_id); ?></option>
            <?php } ?>
        </select></dd>
    </dl>

    </div>
</fieldset>

</div><!-- /.smartlist-filters -->

<script type="text/javascript">
var filters = [];
$(document).ready(function() {

// filters handle
$('.filters select, .filters input').change(function() {
    var name = $(this).attr('name');
    var value = $(this).val();
    filters[name] = value;
    table.fnDraw(true);
    return false;
});

// filters managment
$('fieldset.filters .extra').each(function() {
    $('dl', $(this)).hide();
    $('<select class="filters"></select>')
        .appendTo($(this))
        .change(function() {
        var value = $(this).val();
        $('option', $(this)).detach();
        $(this).append('<option value=""><?php putGS('Filter by...'); ?></option>');
        $('dl', $(this).parent()).each(function() {
            var label = $('label', $(this)).text();
            if (label == value) {
                $(this).show();
                $(this).insertBefore($('select.filters', $(this).parent()));
                if ($('a', $(this)).length == 0) {
                    $('<a class="detach">X</a>').appendTo($('dd', $(this)))
                        .click(function() {
                            $(this).parents('dl').hide();
                            $('input, select', $(this).parent()).val('').change();
                            $('select.filters').change();
                        });
                }
            } else if ($(this).css('display') == 'none') {
                $(this).siblings('select.filters').append('<option value="'+label+'">'+label+'</option>');
            }
        });
    }).change();
});

}); // document.ready
</script>
