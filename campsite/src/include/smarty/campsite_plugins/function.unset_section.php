<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */

/**
 * Campsite unset_section function plugin
 *
 * Type:     function
 * Name:     unset_section
 * Purpose:  
 *
 * @param empty
 *     $p_params
 * @param object
 *     $p_smarty The Smarty object
 */
function smarty_function_unset_section($p_params, &$p_smarty)
{
    // gets the context variable
    $campsite = $p_smarty->get_template_vars('sf');
    if (!is_object($campsite->section) || !$campsite->section->defined) {
        return;
    }

    $campsite->section = new MetaSection();

} // fn smarty_function_unset_section

?>