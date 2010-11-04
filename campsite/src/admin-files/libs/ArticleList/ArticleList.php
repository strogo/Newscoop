<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

/**
 * Article list component
 */
class ArticleList
{
    /** @var string */
    private $id = '';

    /** @var string */
    private $web = '';

    /** @var string */
    private $admin = '';

    /** @var int */
    private $publication = NULL;

    /** @var int */
    private $issue = NULL;

    /** @var int */
    private $section = NULL;

    /** @var int */
    private $language = NULL;

    /** @var array */
    private $filters = array();

    /** @var array */
    private $items = NULL;

    /** @var bool */
    private $search = FALSE;

    /** @var bool */
    private $colVis = FALSE;

    /** @var bool */
    private $order = FALSE;

    /** @var bool */
    private static $renderTable = FALSE;

    /** @var bool */
    private static $renderFilters = FALSE;

    /** @var bool */
    private static $renderActions = FALSE;

    /**
     * @param string $web
     * @param string $admin
     */
    public function __construct()
    {
        global $Campsite, $ADMIN;

        // set paths
        $this->web = $Campsite['WEBSITE_URL'];
        $this->path = $this->web . '/admin/libs/ArticleList';

        camp_load_translation_strings('articles');
        camp_load_translation_strings('universal_list');

        $this->id = substr(sha1((string) mt_rand()), -6);

        echo '<div id="smartlist-' . $this->id . '" class="smartlist">';
    }

    /**
     * Set publication.
     * @param int $publication
     * @return ArticleList
     */
    public function setPublication($publication)
    {
        $this->publication = empty($publication) ? NULL : (int) $publication;
        return $this;
    }

    /**
     * Set issue.
     * @param int $issue
     * @return ArticleList
     */
    public function setIssue($issue)
    {
        $this->issue = empty($issue) ? NULL : (int) $issue;
        return $this;
    }

    /**
     * Set section.
     * @param int $section
     * @return ArticleList
     */
    public function setSection($section)
    {
        $this->section = empty($section) ? NULL : (int) $section;
        return $this;
    }

    /**
     * Set language.
     * @param int $language
     * @return ArticleList
     */
    public function setLanguage($language)
    {
        $this->language = empty($language) ? NULL : (int) $language;
        return $this;
    }

    /**
     * Set filter.
     * @param string $name
     * @param mixed $value
     * @return ArticleList
     */
    public function setFilter($name, $value)
    {
        $this->filters[$name] = $value;
        return $this;
    }

    /**
     * Set items.
     * @param array $items
     * @return ArticleList
     */
    public function setItems($items)
    {
        if (is_array($items[0])) {
            $items = $items[0];
        }
        $this->items = array();
        foreach ((array) $items as $item) {
            $this->items[] = self::ProcessArticle($item);
        }
        return $this;
    }

    /**
     * Get sDom property.
     * @return string
     */
    public function getSDom()
    {
        $colvis = $this->colVis ? 'C' : '';
        $search = $this->search ? 'f' : '';
        $paging = $this->items === NULL ? 'ip' : 'i';
        return sprintf('<"H"%s%s%s>t<"F"%s%s>',
            $colvis,
            $search,
            $paging,
            $paging,
            $this->items === NULL ? 'l' : ''
        );
    }

    /**
     * Set search.
     * @param bool $search
     * @return ArticleList
     */
    public function setSearch($search = FALSE)
    {
        $this->search = (bool) $search;
        return $this;
    }

    /**
     * Set ColVis.
     * @param bool $colVis
     * @return ArticleList
     */
    public function setColVis($colVis = FALSE)
    {
        $this->colVis = (bool) $colVis;
        return $this;
    }

    /**
     * Set order.
     * @param bool $order
     * @return ArticleList
     */
    public function setOrder($order = FALSE)
    {
        $this->order = (bool) $order;
        return $this;
    }

    /**
     * Render filters.
     * @return ArticleList
     */
    public function renderFilters()
    {
        include dirname(__FILE__) . '/filters.php';
        self::$renderFilters = TRUE;
        return $this;
    }

    /**
     * Render actions.
     * @return ArticleList
     */
    public function renderActions()
    {
        include dirname(__FILE__) . '/actions.php';
        self::$renderActions = TRUE;
        return $this;
    }

    /**
     * Render table.
     * @return ArticleList
     */
    public function render()
    {
        include dirname(__FILE__) . '/table.php';
        self::$renderTable = TRUE;
        echo '</div><!-- /#list-' . $this->id . ' -->';
        return $this;
    }

    /**
     * Process article for rendering.
     * @param Article $article
     * @return array
     */
    public static function ProcessArticle(Article $article)
    {
        global $g_user, $Campsite;

        $articleLinkParams = '?f_publication_id=' . $article->getPublicationId()
        . '&amp;f_issue_number=' . $article->getIssueNumber() . '&amp;f_section_number=' . $article->getSectionNumber()
        . '&amp;f_article_number=' . $article->getArticleNumber() . '&amp;f_language_id=' . $article->getLanguageId()
        . '&amp;f_language_selected=' . $article->getLanguageId();
        $articleLink = $Campsite['WEBSITE_URL'].'/admin/articles/edit.php' . $articleLinkParams;
        $previewLink = $Campsite['WEBSITE_URL'].'/admin/articles/preview.php' . $articleLinkParams;

    $lockInfo = '';
    $lockHighlight = false;
    $timeDiff = camp_time_diff_str($article->getLockTime());
    if ($article->isLocked() && ($timeDiff['days'] <= 0)) {
        $lockUser = new User($article->getLockedByUser());
        if ($timeDiff['hours'] > 0) {
            $lockInfo = getGS('The article has been locked by $1 ($2) $3 hour(s) and $4 minute(s) ago.',
                htmlspecialchars($lockUser->getRealName()),
                htmlspecialchars($lockUser->getUserName()),
                $timeDiff['hours'], $timeDiff['minutes']);
        } else {
            $lockInfo = getGS('The article has been locked by $1 ($2) $3 minute(s) ago.',
                htmlspecialchars($lockUser->getRealName()),
                htmlspecialchars($lockUser->getUserName()),
                $timeDiff['minutes']);
        }
        if ($article->getLockedByUser() != $g_user->getUserId()) {
            $lockHighlight = true;
        }
    }

    $tmpUser = new User($article->getCreatorId());
    $tmpAuthor = new Author($article->getAuthorId());
    $tmpArticleType = new ArticleType($article->getType());

    $onFrontPage = $article->onFrontPage() ? getGS('Yes') : getGS('No');
    $onSectionPage = $article->onSectionPage() ? getGS('Yes') : getGS('No');

    $imagesNo = (int) ArticleImage::GetImagesByArticleNumber($article->getArticleNumber(), true);
    $topicsNo = (int) ArticleTopic::GetArticleTopics($article->getArticleNumber(), true);
    $commentsNo = '';
    if ($article->commentsEnabled()) {
        $commentsNo = (int) ArticleComment::GetArticleComments($article->getArticleNumber(), $article->getLanguageId(), null, true);
    } else {
        $commentsNo = 'No';
    }

    return array(
        $article->getArticleNumber(),
        $article->getLanguageId(),
        $article->getOrder(),
        sprintf('<a href="%s" title="%s %s">%s</a>',
            $articleLink,
            getGS('Edit'), $article->getName(),
            $article->getName()),
        $tmpArticleType->getDisplayName(),
        $tmpUser->getRealName(),
        $tmpAuthor->getFirstName(),
        $article->getWorkflowStatus(),
        $onFrontPage,
        $onSectionPage,
        $imagesNo,
        $topicsNo,
        $commentsNo,
        (int) $article->getReads(),
        $article->getCreationDate(),
        $article->getPublishDate(),
        $article->getLastModified(),
    );
    }
}
