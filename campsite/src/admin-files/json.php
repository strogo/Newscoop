<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

header('Content-type: application/json');

if (!SecurityToken::isValid()) {
    echo json_encode(array(
        'status' => FALSE,
        'message' => getGS('Invalid security token.'),
    ));
    exit;
}

// include valid callbacks
// TODO replace with autoloading
require_once $GLOBALS['g_campsiteDir'] . '/classes/Extension/WidgetManager.php';

$callback = $_REQUEST['callback'];
$params = (array) $_REQUEST['params'];

try {
    $result = call_user_func_array($callback, $params);
    if ($result === FALSE) {
        throw new Exception('Unknown');
    }

    echo json_encode(array(
        'status' => TRUE,
        'result' => $result,
    ));
} catch (Exception $e) {
    echo json_encode(array(
        'status' => FALSE,
        'message' => $e->getMessage(),
    ));
}

exit;
