<?php

$Campsite['APACHE_USER'] = 'www-data';
$Campsite['APACHE_GROUP'] = 'www-data';
$Campsite['CAMPSITE_DIR'] = '/usr/local/campsite_a1335';
$Campsite['BIN_DIR'] = '/usr/local/campsite_1335/bin';
$Campsite['SBIN_DIR'] = '/usr/local/campsite_1335/sbin';
$Campsite['ETC_DIR'] = '/usr/local/campsite_1335/etc';
$Campsite['WWW_DIR'] = '/usr/local/apache-1.3.35/htdocs/campsite/www';
$Campsite['WWW_COMMON_DIR'] = '/usr/local/apache-1.3.35/htdocs/campsite/www-common';
$Campsite['DEFAULT_SMTP_SERVER_ADDRESS'] = 'localhost';
$Campsite['DEFAULT_SMTP_SERVER_PORT'] = '25';
$Campsite['DEFAULT_DATABASE_SERVER_ADDRESS'] = 'localhost';

$Campsite['HTML_COMMON_DIR'] = $Campsite['WWW_COMMON_DIR'] . "/html";
$Campsite['CGI_COMMON_DIR'] = $Campsite['WWW_COMMON_DIR'] . "/cgi-bin";
$Campsite['HTML_DIR'] = $Campsite['WWW_DIR'].'/'.$Campsite['DATABASE_NAME'].'/html';
$Campsite['CGI_DIR'] = $Campsite['WWW_DIR'].'/'.$Campsite['DATABASE_NAME'].'/cgi-bin';
$Campsite['PEAR_LOCAL'] = $Campsite['WWW_COMMON_DIR']."/html/include/pear";

if (!isset($_SERVER['SERVER_PORT']))
{
        $_SERVER['SERVER_PORT'] = 80;
}
$scheme = $_SERVER['SERVER_PORT'] == 443 ? 'https://' : 'http://';
$Campsite['HOSTNAME'] = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : "";
if (($_SERVER['SERVER_PORT'] != 80) && ($_SERVER['SERVER_PORT'] != 443)) {
    $Campsite['HOSTNAME'] .= ':'.$_SERVER['SERVER_PORT'];
}
$Campsite['WEBSITE_URL'] = $scheme.$Campsite['HOSTNAME'];
unset($scheme);


$Campsite['TEMPLATE_DIRECTORY'] = $Campsite['HTML_DIR']."/look";
$Campsite['TEMPLATE_BASE_URL'] = $Campsite['WEBSITE_URL']."/look/";
$Campsite['IMAGE_DIRECTORY'] = $Campsite['HTML_DIR'].'/images/';
$Campsite['IMAGE_BASE_URL'] = $Campsite['WEBSITE_URL'].'/images/';
$Campsite['ADMIN_IMAGE_BASE_URL'] = $Campsite['WEBSITE_URL'].'/css';
$Campsite['IMAGE_PREFIX'] = 'cms-image-';
$Campsite['IMAGEMAGICK_INSTALLED'] = true;
$Campsite['THUMBNAIL_MAX_SIZE'] = 64;
$Campsite['THUMBNAIL_COMMAND'] = 'convert -sample '
                .$Campsite['THUMBNAIL_MAX_SIZE'].'x'.$Campsite['THUMBNAIL_MAX_SIZE'];
$Campsite['THUMBNAIL_DIRECTORY'] = $Campsite['IMAGE_DIRECTORY'].'/thumbnails/';
$Campsite['THUMBNAIL_BASE_URL'] = $Campsite['WEBSITE_URL'].'/images/thumbnails/';
$Campsite['THUMBNAIL_PREFIX'] = 'cms-thumb-';
$Campsite['FILE_BASE_URL'] = $Campsite['WEBSITE_URL'].'/files/';
$Campsite['FILE_DIRECTORY'] = $Campsite['HTML_DIR'].'/files';
$Campsite['FILE_NUM_DIRS_LEVEL_1'] = "1000";
$Campsite['FILE_NUM_DIRS_LEVEL_2'] = "1000";
$Campsite['TMP_DIRECTORY'] = '/tmp/';
$Campsite['HELP_URL'] = 'http://code.campware.org/manuals/campsite/2.6/';
$Campsite['ABOUT_URL'] = 'http://www.campware.org/en/camp/campsite_news/';
$Campsite['SUPPORT_EMAIL'] = 'campsite-support@lists.campware.org';
$Campsite['DEBUG'] = true;

?>