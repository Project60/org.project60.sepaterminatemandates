<?php

require_once 'sepaterminatemandates.civix.php';
// phpcs:disable
use CRM_Sepaterminatemandates_ExtensionUtil as E;
// phpcs:enable

/**
 * Implements hook_civicrm_post().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_post/
 *
 * @param $op
 * @param $objectName
 * @param $objectId
 * @param $objectRef
 */
function sepaterminatemandates_civicrm_post($op, $objectName, $objectId, &$objectRef) {
  Civi\SepaTerminateMandates\ConfigContainer::postHook($op, $objectName, $objectId, $objectRef);
}

function sepaterminatemandates_search_action_designer_types(&$types) {
  CRM_Sepaterminatemandates_Task::searchActionDesignerTypes($types);
}

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function sepaterminatemandates_civicrm_config(&$config) {
  _sepaterminatemandates_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function sepaterminatemandates_civicrm_install(): void {
  _sepaterminatemandates_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function sepaterminatemandates_civicrm_enable(): void {
  _sepaterminatemandates_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function sepaterminatemandates_civicrm_entityTypes(&$entityTypes): void {
  _sepaterminatemandates_civix_civicrm_entityTypes($entityTypes);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_preProcess
 */
//function sepaterminatemandates_civicrm_preProcess($formName, &$form): void {
//
//}

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 */
//function sepaterminatemandates_civicrm_navigationMenu(&$menu): void {
//  _sepaterminatemandates_civix_insert_navigation_menu($menu, 'Mailings', [
//    'label' => E::ts('New subliminal message'),
//    'name' => 'mailing_subliminal_message',
//    'url' => 'civicrm/mailing/subliminal',
//    'permission' => 'access CiviMail',
//    'operator' => 'OR',
//    'separator' => 0,
//  ]);
//  _sepaterminatemandates_civix_navigationMenu($menu);
//}
