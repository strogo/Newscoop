<?php

/**
 * @param array p_dbColumns
 * @param User p_user
 * @return void
 */
function editor_load_xinha($p_dbColumns, $p_user) {
	global $Campsite;
	$stylesheetFile = $Campsite['HTML_COMMON_DIR']
		."/priv/articles/article_stylesheet.css";
	?>
<script type="text/javascript">
	//<![CDATA[
      _editor_url = "/javascript/xinha/";
      _editor_lang = "<?php p($_REQUEST['TOL_Language']); ?>";
      _campsite_article_id = <?php echo $_REQUEST['f_article_number']; ?>;
	//]]>
</script>

<!-- Load the HTMLArea file -->
<script type="text/javascript" src="/javascript/xinha/htmlarea.js"></script>

<!-- Special Campsite functionality -->
<script type="text/javascript">
function CampsiteSubhead(editor, objectName, object) {
	if (!HTMLArea.is_ie) {
		// This statement crashes in the most bizarre way on IE.
		// If you remove the "parent = " here, then it doesnt crash.
		// So IE will allow subheads within subheads (bad), while
		// the code below will prevent that for the other browsers.
		parent = editor.getParentElement();
		if ((parent.tagName.toLowerCase() == "span") &&
			(parent.className.toLowerCase()=="campsite_subhead")) {
			editor.selectNodeContents(parent);
			editor.updateToolbar();
			return false;
		} else {
			editor.surroundHTML('<span class="campsite_subhead">', '</span>');
		}
	} else {
		editor.surroundHTML('<span class="campsite_subhead">', '</span>');
	}
} // fn CampsiteSubhead

/**
 * Handler for creating an internal campsite link.
 * This is a copy of the _createlink function, except that it calls
 * a different popup window.
 */
function CampsiteInternalLink(editor, objectName, object, link) {
	var outparam = null;
	if (typeof link == "undefined") {
		link = editor.getParentElement();
		if (link) {
			if (/^img$/i.test(link.tagName)) {
				link = link.parentNode;
			}
			if (!/^a$/i.test(link.tagName)) {
				link = null;
			}
		}
	}
	popupWindowTarget = "campsite_internal_link.php?TOL_Language=<?php p($_REQUEST["TOL_Language"]); ?>";
	if (!link) {
    	var sel = editor._getSelection();
    	var range = editor._createRange(sel);
    	var compare = 0;
    	if (HTMLArea.is_ie) {
      		compare = range.compareEndPoints("StartToEnd", range);
    	}
    	else {
      		compare = range.compareBoundaryPoints(range.START_TO_END, range);
    	}
    	if (compare == 0) {
      		alert(HTMLArea._lc("You need to select some text before creating a link"));
      		return;
    	}
	    outparam = {
		      f_href : '',
		      f_title : '',
		      f_target : '',
		      f_usetarget : editor.config.makeLinkShowsTarget
		    };
	}
	else {
	    outparam = {
	      f_href   : HTMLArea.is_ie ? editor.stripBaseURL(link.href) : link.getAttribute("href"),
	      f_title  : link.title,
	      f_target : link.target,
	      f_usetarget : editor.config.makeLinkShowsTarget
	    };
		// Pass the arguments to the popup window so that it
		// can populate the dropdown controls.
		popupWindowTarget += "&" + outparam["f_href"].replace("campsite_internal_link?", "");
	}
	editor._popupDialog(popupWindowTarget, function(param) {
    	if (!param) {
      		return false;
    	}
    	var a = link;
    	if (!a) {
    		try {
      			editor._doc.execCommand("createlink", false, param.f_href);
      			a = editor.getParentElement();
      			var sel = editor._getSelection();
      			var range = editor._createRange(sel);
      			if (!HTMLArea.is_ie) {
        			a = range.startContainer;
        			if (!/^a$/i.test(a.tagName)) {
          				a = a.nextSibling;
          				if (a == null) {
            				a = range.startContainer.parentNode;
          				}
        			}
      			}
    		}
    		catch(e) {}
    	}
    	else {
      		var href = param.f_href.trim();
      		editor.selectNodeContents(a);
      		if (href == "") {
        		editor._doc.execCommand("unlink", false, null);
        		editor.updateToolbar();
        		return false;
      		}
      		else {
        		a.href = href;
      		}
    	}
    	if (!(a && /^a$/i.test(a.tagName))) {
      		return false;
    	}
    	a.target = param.f_target.trim();
    	a.title = param.f_title.trim();
    	editor.selectNodeContents(a);
    	editor.updateToolbar();
  	}, outparam);
};

xinha_editors = null;
xinha_init    = null;
xinha_config  = null;
xinha_plugins = null;

// This contains the names of textareas we will make into Xinha editors
xinha_init = xinha_init ? xinha_init : function()
{
  /** STEP 1 ***************************************************************
   * First, what are the plugins you will be using in the editors on this
   * page.  List all the plugins you will need, even if not all the editors
   * will use all the plugins.
   ************************************************************************/

  xinha_plugins = xinha_plugins ? xinha_plugins :
  [
  	<?php if ($p_user->hasPermission("EditorImage")) { ?>
	'ImageManager',
	<?php } ?>
	<?php if ($p_user->hasPermission('EditorTable')) { ?>
	'TableOperations',
	<?php } ?>
	<?php if ($p_user->hasPermission('EditorListNumber')) { ?>
	'ListType',
	<?php } ?>
	<?php if ($p_user->hasPermission("EditorEnlarge")) { ?>
    'FullScreen',
    <?php } ?>
    <?php if ($p_user->hasPermission('EditorCharacterMap')) { ?>
    'CharacterMap',
    <?php } ?>
    <?php if ($p_user->hasPermission('EditorFindReplace')) { ?>
    'FindReplace',
    <?php } ?>
    'WordPaste'
  ];
	// THIS BIT OF JAVASCRIPT LOADS THE PLUGINS, NO TOUCHING  :)
	if(!HTMLArea.loadPlugins(xinha_plugins, xinha_init)) return;

  /** STEP 2 ***************************************************************
   * Now, what are the names of the textareas you will be turning into
   * editors?
   ************************************************************************/

  xinha_editors = xinha_editors ? xinha_editors :
  [
  	<?php
  	$xinhaEditors = array();
	foreach ($p_dbColumns as $dbColumn) {
		if (stristr($dbColumn->getType(), "blob")) {
			$xinhaEditors[] = "'".$dbColumn->getName()."'";
		}
	}
	echo implode(",", $xinhaEditors);
	?>
  ];

  /** STEP 3 ***************************************************************
   * We create a default configuration to be used by all the editors.
   * If you wish to configure some of the editors differently this will be
   * done in step 4.
   *
   * If you want to modify the default config you might do something like this.
   *
   *   xinha_config = new HTMLArea.Config();
   *   xinha_config.width  = 640;
   *   xinha_config.height = 420;
   *
   *************************************************************************/

   xinha_config = xinha_config ? xinha_config : new HTMLArea.Config();
   xinha_config.statusBar = false;
   xinha_config.htmlareaPaste =  true;
   xinha_config.killWordOnPaste =  true;
   xinha_config.flowToolbars = false;
   xinha_config.mozParaHandler = "built-in";
   // Change the built-in icon for "web link"
   linkIcon = _editor_url + xinha_config.imgURL + "ed_campsite_link.gif";
   xinha_config.btnList["createlink"] = [ "Insert Web Link", linkIcon, false, function(e) {e._createLink();} ],
   // Change the removeformat button to work in text mode.
   xinha_config.btnList["removeformat"] = [ "Remove formatting", ["ed_buttons_main.gif",4,4], true, function(e) {e.execCommand("removeformat");} ],

   <?php if ($p_user->hasPermission('EditorFindReplace')) { ?>
   // Put the "find-replace" plugin in a better location
   xinha_config.addToolbarElement([], ["FR-findreplace"], 0);
   <?php } ?>

   // Add in our style sheet for the "subheads".
   xinha_config.pageStyle = "<?php echo str_replace("\n", "", file_get_contents($stylesheetFile)); ?>";

   <?php if ($p_user->hasPermission('EditorSubhead')) { ?>
   subheadTooltip = HTMLArea._lc('Subhead', 'Campsite');
   xinha_config.registerButton({
       // The ID of the button.
       id        : "campsite-subhead",
       // The tooltip.
       tooltip   : subheadTooltip,
       // Image to be displayed in the toolbar.
       image     : "/javascript/xinha/images/campsite_subhead.gif",
       // TRUE = enabled in text mode
       // FALSE = disabled in text mode
       textMode  : false,
       // Called when the button is clicked.
       action    : CampsiteSubhead,
       // The button will be disabled if outside
       // the specified element.
       context   : ''
   });
   <?php } ?>

   <?php if ($p_user->hasPermission('EditorLink')) { ?>
   internalLinkTooltip = HTMLArea._lc('Insert Internal Link', 'Campsite');
   xinha_config.registerButton({
       // The ID of the button.
       id        : "campsite-internal-link",
       // The tooltip.
       tooltip   : internalLinkTooltip,
       // Image to be displayed in the toolbar.
       image     : "/javascript/xinha/images/campsite_internal_link.gif",
       // TRUE = enabled in text mode
       // FALSE = disabled in text mode
       textMode  : false,
       // Called when the button is clicked.
       action    : CampsiteInternalLink,
       // The button will be disabled if outside
       // the specified element.
       context   : ''
   });
   <?php }

    $toolbar1 = array();
    if ($p_user->hasPermission('EditorBold')) {
	  	$toolbar1[] = "\"bold\"";
	}
	if ($p_user->hasPermission('EditorItalic')) {
		$toolbar1[] = "\"italic\"";
	}
	if ($p_user->hasPermission('EditorUnderline')) {
		$toolbar1[] = "\"underline\"";
	}
	if ($p_user->hasPermission('EditorStrikethrough')) {
		$toolbar1[] = "\"strikethrough\"";
	}
	if ($p_user->hasPermission('EditorTextAlignment')) {
		$toolbar1[] = "\"justifyleft\"";
		$toolbar1[] = "\"justifycenter\"";
		$toolbar1[] = "\"justifyright\"";
		$toolbar1[] = "\"justifyfull\"";
	}
	if ($p_user->hasPermission('EditorIndent')) {
		$toolbar1[] = "\"outdent\"";
		$toolbar1[] = "\"indent\"";
	}
	if ($p_user->hasPermission('EditorCopyCutPaste')) {
		$toolbar1[] = "\"copy\"";
		$toolbar1[] = "\"cut\"";
		$toolbar1[] = "\"paste\"";
		$toolbar1[] = "\"space\"";
	}
	if ($p_user->hasPermission('EditorUndoRedo')) {
		$toolbar1[] = "\"undo\"";
		$toolbar1[] = "\"redo\"";
	}
	if ($p_user->hasPermission('EditorTextDirection')) {
		 $toolbar1[] = "\"lefttoright\"";
		 $toolbar1[] = "\"righttoleft\"";
	}
	if ($p_user->hasPermission('EditorLink')) {
		$toolbar1[] = "\"campsite-internal-link\"";
		$toolbar1[] = "\"createlink\"";
	}
	if ($p_user->hasPermission('EditorSubhead')) {
		$toolbar1[] = "\"campsite-subhead\"";
	}
	if ($p_user->hasPermission('EditorImage')) {
		$toolbar1[] = "\"insertimage\"";
	}
	if ($p_user->hasPermission('EditorSourceView')) {
		$toolbar1[] = "\"htmlmode\"";
	}
	if ($p_user->hasPermission('EditorEnlarge')) {
		$toolbar1[] = "\"popupeditor\"";
	}

	if ($p_user->hasPermission('EditorHorizontalRule')) {
		$toolbar1[] = "\"inserthorizontalrule\"";
	}
	if ($p_user->hasPermission('EditorFontColor')) {
		$toolbar1[] = "\"forecolor\"";
		$toolbar1[] = "\"hilitecolor\"";
	}
	if ($p_user->hasPermission('EditorSubscript')) {
		$toolbar1[] = "\"subscript\"";
	}
	if ($p_user->hasPermission('EditorSuperscript')) {
		$toolbar1[] = "\"superscript\"";
	}

	$toolbar2 = array();
	// Slice up the first toolbar if it is too long.
	if (count($toolbar1) > 24) {
		$toolbar2 = array_splice($toolbar1, 24);
	}

	// This is to put the bulleted and numbered list controls
	// on the most appropriate line of the toolbar.
	if ($p_user->hasPermission('EditorListBullet') && $p_user->hasPermission('EditorListNumber') && count($toolbar1) < 15) {
		$toolbar1[] = "\"insertunorderedlist\"";
		$toolbar1[] = "\"insertorderedlist\"";
	}
	elseif ($p_user->hasPermission('EditorListBullet') && !$p_user->hasPermission('EditorListNumber') && count($toolbar1) < 24) {
		$toolbar1[] = "\"insertunorderedlist\"";
	}
	elseif (!$p_user->hasPermission('EditorListBullet') && $p_user->hasPermission('EditorListNumber') && count($toolbar1) < 16) {
		$toolbar1[] = "\"insertorderedlist\"";
	}
	else {
		if ($p_user->hasPermission('EditorListBullet')) {
			$toolbar2[] = "\"insertunorderedlist\"";
		}
		if ($p_user->hasPermission('EditorListNumber')) {
			$toolbar2[] = "\"insertorderedlist\"";
	 	}
	}

	if ($p_user->hasPermission('EditorFontFace')) {
		$toolbar2[] = "\"formatblock\"";
		$toolbar2[] = "\"fontname\"";
	}

	if ($p_user->hasPermission('EditorFontSize')) {
		$toolbar2[] = "\"fontsize\"";
	}

	// This is to fix ticket #1602.  You only want the line break if
	// there is more than one line in the toolbar.
	if (count($toolbar2) > 0 || $p_user->hasPermission('EditorTable'))  {
		$toolbar1[] = "\"linebreak\"";
	}
   ?>

   xinha_config.toolbar = [
		[ <?php echo implode(",", $toolbar1); ?> ],

		<?php if (count($toolbar2) > 0) { ?>
		[ <?php echo implode(",", $toolbar2); ?> ],
		<?php } ?>

		<?php if ($p_user->hasPermission('EditorTable')) { ?>
		[ "linebreak", "inserttable", "toggleborders" ],
		<?php } ?>
	];

  /** STEP 3 ***************************************************************
   * We first create editors for the textareas.
   *
   * You can do this in two ways, either
   *
   *   xinha_editors   = HTMLArea.makeEditors(xinha_editors, xinha_config, xinha_plugins);
   *
   * if you want all the editor objects to use the same set of plugins, OR;
   *
   *   xinha_editors = HTMLArea.makeEditors(xinha_editors, xinha_config);
   *   xinha_editors['myTextArea'].registerPlugins(['Stylist','FullScreen']);
   *   xinha_editors['anotherOne'].registerPlugins(['CSS','SuperClean']);
   *
   * if you want to use a different set of plugins for one or more of the
   * editors.
   ************************************************************************/

  xinha_editors   = HTMLArea.makeEditors(xinha_editors, xinha_config, xinha_plugins);

  /** STEP 4 ***************************************************************
   * If you want to change the configuration variables of any of the
   * editors,  this is the place to do that, for example you might want to
   * change the width and height of one of the editors, like this...
   *
   *   xinha_editors.myTextArea.config.width  = 640;
   *   xinha_editors.myTextArea.config.height = 480;
   *
   ************************************************************************/


  /** STEP 5 ***************************************************************
   * Finally we "start" the editors, this turns the textareas into
   * Xinha editors.
   ************************************************************************/
  HTMLArea.startEditors(xinha_editors);
}

window.onload = xinha_init;
</script>
  <!--<link href="/javascript/xinha/skins/xp-blue/skin.css" rel="Stylesheet" />-->
<?php
} // fn editor_load_xinha
?>