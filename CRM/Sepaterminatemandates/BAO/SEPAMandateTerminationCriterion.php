<?php
// phpcs:disable
use CRM_Sepaterminatemandates_ExtensionUtil as E;
// phpcs:enable

class CRM_Sepaterminatemandates_BAO_SEPAMandateTerminationCriterion extends CRM_Sepaterminatemandates_DAO_SEPAMandateTerminationCriterion {

  /**
   * Create a new SEPAMandateTerminationCriterion based on array-data
   *
   * @param array $params key-value pairs
   * @return CRM_Sepaterminatemandates_DAO_SEPAMandateTerminationCriterion|NULL
   */
  /*
  public static function create($params) {
    $className = 'CRM_Sepaterminatemandates_DAO_SEPAMandateTerminationCriterion';
    $entityName = 'SEPAMandateTerminationCriterion';
    $hook = empty($params['id']) ? 'create' : 'edit';

    CRM_Utils_Hook::pre($hook, $entityName, CRM_Utils_Array::value('id', $params), $params);
    $instance = new $className();
    $instance->copyValues($params);
    $instance->save();
    CRM_Utils_Hook::post($hook, $entityName, $instance->id, $instance);

    return $instance;
  }
  */

}
