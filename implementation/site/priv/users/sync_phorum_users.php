<?php

if (!$g_user->hasPermission('SyncPhorumUsers')) {
    camp_html_display_error(getGS("You do not have the right to sync Campsite and Phorum users."));
	exit;
}

$queryStr = "SELECT u.Id, pu.user_id "
            . "FROM liveuser_users AS u LEFT JOIN phorum_users AS pu "
            . "ON u.UName = pu.username "
            . "WHERE fk_campsite_user_id IS NULL";
$nullUsers = $g_ado_db->GetAll($queryStr);
if (sizeof($nullUsers) > 0) {
    foreach ($nullUsers as $nullUser) {
        $queryStr = "SELECT user_id FROM phorum_users WHERE fk_campsite_user_id = '".$nullUser['Id']."'";
        $phorumUser = $g_ado_db->GetRow($queryStr);
        if (sizeof($phorumUser) < 1) {
            // insert the corresponding phorum user
            $sql = "UPDATE phorum_users SET fk_campsite_user_id = ".$nullUser['Id']
                 . " WHERE user_id = ".$nullUser['user_id'];
            $g_ado_db->Execute($sql);
        }
    }
}

?>