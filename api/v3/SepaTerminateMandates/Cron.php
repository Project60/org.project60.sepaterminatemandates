<?php
use CRM_Sepaterminatemandates_ExtensionUtil as E;

/**
 * SepaTerminateMandates.Cron API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/api-architecture/
 */
function _civicrm_api3_sepa_terminate_mandates_Cron_spec(&$spec) {
  $spec['limit'] = [
    'name' => 'limit',
    'type' => CRM_Utils_Type::T_INT,
    'api.default' => 100,
  ];
}

/**
 * SepaTerminateMandates.Cron API
 *
 * @param array $params
 *
 * @return array
 *   API result descriptor
 *
 * @see civicrm_api3_create_success
 *
 * @throws API_Exception
 */
function civicrm_api3_sepa_terminate_mandates_Cron($params) {
  $limit = 25;
  if (isset($params['limit'])) {
    $limit = $params['limit'];
  }
  $SEPAMandateTerminationCriterions = \Civi\Api4\SEPAMandateTerminationCriterion::get()
    ->addWhere('is_active', '=', TRUE)
    ->addClause('OR', ['next_check_date', 'IS NULL'], ['next_check_date', '<=', '2023-01-11'])
    ->setLimit(0)
    ->execute();
  $terminatedMandateCount = 0;
  foreach($SEPAMandateTerminationCriterions as $SEPAMandateTerminationCriterion) {
    $nextCheckDate = new \DateTime();
    $nextCheckDate->modify('+1 day');
    $query = new CRM_Sepaterminatemandates_Query($SEPAMandateTerminationCriterion['search_criteria'], 0, $limit);
    $total = $query->count();
    $terminatedMandateCount += $query->terminateAllFoundMandates($SEPAMandateTerminationCriterion['terminate_configuration']);
    if ($total < $limit) {
      \Civi\Api4\SEPAMandateTerminationCriterion::update()
        ->addWhere('id', '=', $SEPAMandateTerminationCriterion['id'])
        ->addValue('next_check_date', $nextCheckDate->format('Y-m-d'))
        ->execute();
    }
  }
  return ['count' => $terminatedMandateCount];
}
