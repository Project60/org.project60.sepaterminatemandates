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

class CRM_Sepaterminatemandates_Query {

  protected $values = [];

  protected $count = 0;

  protected $offset;

  protected $limit;

  /**
   * @var \CRM_Core_DAO|\DB_Error|object
   */
  protected $dao;

  const STATUS_CANCELLED = 3;

  const STATUS_FAILED = 4;

  protected $contributionStatus = [];

  public function __construct($values, $offset, $limit) {
    $this->loadContributionStatusLabels();
    $this->offset = $offset;
    $this->limit = $limit;
    $this->values = $values;
    $this->query();
  }

  public function count() {
    return $this->count;
  }

  public function rows() {
    $rows = [];
    while($this->dao->fetch()) {
      $row = [
        'id' => $this->dao->id,
        'reference' => $this->dao->reference,
        'contact' => $this->dao->contact,
        'contact_id' => $this->dao->contact_id,
        'start_date' => $this->dao->start_date,
        'campaign_id' => $this->dao->campaign_id,
        'campaign' => $this->dao->campaign,
        'contributions' => $this->getContributions($this->dao->recur_id),
      ];
      $rows[] = $row;
    }
    return $rows;
  }

  protected function getContributions($contribution_recur_id) {
    $sql = "
      SELECT
        `c`.`receive_date` as `receive_date`,
        `c`.`contribution_status_id` as `status_id`,
        `c`.`cancel_reason` as `cancel_reason`
      FROM `civicrm_contribution` `c`
      WHERE DATE(`receive_date`) >= DATE(%1) AND `c`.`contribution_recur_id` = %2
      ORDER BY `receive_date` DESC
    ";
    $date = new \DateTime();
    $date->modify('-' . $this->values['cancelled_contributions_months'].' months');
    $sqlParams[1] = [$date->format('Y-m-d'), 'String'];
    $sqlParams[2] = [$contribution_recur_id, 'Integer'];
    $dao = CRM_Core_DAO::executeQuery($sql, $sqlParams);
    $result = [];
    while($dao->fetch()) {
      $row = [
        'receive_date' => $dao->receive_date,
        'status_id' => $dao->status_id,
        'status' => $this->contributionStatus[$dao->status_id],
        'cancel_reason' => $dao->cancel_reason,
        'is_cancelled' => 0,
      ];
      if ($dao->status_id == self::STATUS_FAILED || $dao->status_id == self::STATUS_CANCELLED) {
        $row['is_cancelled'] = 1;
      }
      $result[] = $row;
    }
    return $result;
  }

  protected function query() {
    $date = new \DateTime();
    $date->modify('-' . $this->values['cancelled_contributions_months'].' months');
    $cancelReasonConditions = [];
    if (!empty($this->values['cancel_reasons'])) {
      $cancel_reasons = $this->values['cancel_reasons'];
      if (!is_array($cancel_reasons)) {
        $cancel_reasons = [$cancel_reasons];
      }
      foreach($cancel_reasons as $cancel_reason) {
        $cancelReasonConditions[] = "`cancel_reason` = '".CRM_Utils_Type::escape($cancel_reason, 'String')."'";
      }
    }
    $cancelReasonCondition = "";
    if (count($cancelReasonConditions)) {
      $cancelReasonCondition = " AND (" . implode(" OR ", $cancelReasonConditions) . ")";
    }
    $failureCountStatement = "IF ((`contribution_status_id` = %1 OR `contribution_status_id` = %2) {$cancelReasonCondition}, @_failureCount := @_failureCount +1, @_failureCount) AS `failureCount`,";
    if (!empty($this->values['cancelled_contribution_successive'])) {
      $failureCountStatement = "IF ((`contribution_status_id` = %1 OR `contribution_status_id` = %2) {$cancelReasonCondition}, @_failureCount := @_failureCount +1, @_failureCount := 0) AS `failureCount`,";
    }
    $contributionSql = "
      SELECT
        `contribution_recur_id`,
        IF (@previousRecurId = `contribution_recur_id`, @_failureCount, @_failureCount := 0) as `_resetFailureCount`,
        {$failureCountStatement}
        @previousRecurId := contribution_recur_id
      FROM `civicrm_contribution`
      JOIN (SELECT @_failureCount := 0) AS t
      JOIN (SELECT @previousRecurId := 0) AS t1
      WHERE DATE(`receive_date`) >= DATE(%3)
      ORDER BY `contribution_recur_id`, `receive_date` DESC
    ";
    $sqlParams[1] = [self::STATUS_CANCELLED, 'Integer'];
    $sqlParams[2] = [self::STATUS_FAILED, 'Integer'];
    $sqlParams[3] = [$date->format('Y-m-d'), 'String'];

    $sql = "
      SELECT SQL_CALC_FOUND_ROWS
        `mandate`.`id` as `id`,
        `mandate`.`reference` as `reference`,
        `contact`.`id` as `contact_id`,
        `contact`.`display_name` as `contact`,
        `rcur`.`start_date` as `start_date`,
        `rcur`.`campaign_id` as `campaign_id`,
        `rcur`.`id` as `recur_id`,
        `campaign`.`title` as `campaign`
      FROM `civicrm_sdd_mandate` `mandate`
      INNER JOIN `civicrm_contact` `contact` ON `contact`.`id` = `mandate`.`contact_id`
      INNER JOIN `civicrm_contribution_recur` `rcur` ON `rcur`.`id` = `mandate`.`entity_id` AND `mandate`.`entity_table` = 'civicrm_contribution_recur'
      INNER JOIN (" . $contributionSql . ") `contributionTemp` ON `contributionTemp`.`contribution_recur_id` = `rcur`.`id`
      LEFT JOIN `civicrm_campaign` `campaign` ON `campaign`.`id` = `rcur`.`campaign_id`
      WHERE (`mandate`.`status` = 'RCUR' OR `mandate`.`status` = 'FRST')
      AND `contributionTemp`.`failureCount` >= %4
    ";
    $sqlParams[4] = [$this->values['cancelled_contributions_qty'], 'Integer'];
    if (!empty($this->values['campaign_ids'])) {
      $campaign_ids = $this->values['campaign_ids'];
      if (!is_array($campaign_ids)) {
        $campaign_ids = explode(",", $campaign_ids);
      }
      $sql .= " AND `rcur`.`campaign_id` IN (". implode(',', $campaign_ids) . ")";
    }
    $sql .= " GROUP BY `contributionTemp`.`contribution_recur_id`";
    $sql .= " LIMIT %5, %6";
    $sqlParams[5] = [$this->offset, 'Integer'];
    $sqlParams[6] = [$this->limit, 'Integer'];
    $this->dao = CRM_Core_DAO::executeQuery($sql, $sqlParams);

    $this->count = CRM_Core_DAO::singleValueQuery("SELECT FOUND_ROWS();");
  }

  private function loadContributionStatusLabels() {
    $result = civicrm_api3('OptionValue', 'get', ['option_group_id' => 'contribution_status', 'options' => ['limit' => 0]]);
    foreach($result['values'] as $val) {
      $this->contributionStatus[$val['value']] = $val['label'];
    }
  }

}
