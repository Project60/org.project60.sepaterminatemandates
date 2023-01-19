<?php
/**
 * Copyright (C) 2023  Jaap Jansma (jaap.jansma@civicoop.org)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

use CRM_Sepaterminatemandates_ExtensionUtil as E;

class CRM_Sepaterminatemandates_Utils {

  /**
   * Get a list of activity types
   *
   * @return array option values
   */
  public static function getActivityTypes(): array {
    static $options = [];
    if (count($options)) {
      return $options;
    }
    try {
      $optionApi = civicrm_api3('OptionValue', 'get', [
        'option_group_id' => 'activity_type',
        'is_active' => 1,
        'options' => ['limit' => 0],
      ]);
      foreach($optionApi['values'] as $option) {
        $options[$option['value']] = $option['label'];
      }
    } catch (CiviCRM_API3_Exception $e) {
    }
    return $options;
  }

  /**
   * Get a list of activity types
   *
   * @return array option values
   */
  public static function getActivityStatus(): array {
    static $options = [];
    if (count($options)) {
      return $options;
    }
    try {
      $optionApi = civicrm_api3('OptionValue', 'get', [
        'option_group_id' => 'activity_status',
        'is_active' => 1,
        'options' => ['limit' => 0],
      ]);
      foreach($optionApi['values'] as $option) {
        $options[$option['value']] = $option['label'];
      }
    } catch (CiviCRM_API3_Exception $e) {
    }
    return $options;
  }

  /**
   * Terminate a mandate
   *
   * @param int $entityId
   * @param $terminateConfiguration
   *
   * @return bool
   * @throws \CiviCRM_API3_Exception
   */
  public static function terminateMandate(int $mandateId, $terminateConfiguration) {
    $mandate = civicrm_api3('SepaMandate', 'getsingle', ['id' => $mandateId]);
    $contactId = $mandate['contact_id'];
    civicrm_api3('SepaMandate', 'terminate', ['mandate_id' => $mandateId, 'cancel_reason' => $terminateConfiguration['reason']]);
    $activityApiParams = [
      //'source_contact_id' => $contactId,
      'activity_type_id' => $terminateConfiguration['activity_type_id'],
      'status_id' => $terminateConfiguration['activity_status_id'],
      'target_id' => $contactId,
      'subject' => E::ts('Sepa mandate %1 cancelled because: %2', [1=>$mandate['reference'], 2=>$terminateConfiguration['reason']]),
    ];
    if (isset($terminateConfiguration['activity_assignee']) && !empty($terminateConfiguration['activity_assignee'])) {
      $activityApiParams['assignee_id'] = $terminateConfiguration['activity_assignee'];
    }
    civicrm_api3('Activity', 'create', $activityApiParams);
    return TRUE;
  }

}
