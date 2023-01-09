<?php
/**
 * Copyright (C) 2022  Jaap Jansma (jaap.jansma@civicoop.org)
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

namespace Civi\SepaTerminateMandates;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Container;

class Config extends Container {

  /**
   * @return array
   */
  public function getCancelReasons() {
    $return = $this->getParameter('cancel_reasons');
    if (!is_array($return)) {
      $return = [];
    }
    return $return;
  }

  /**
   * Build the container and load the different cancel reasons
   *
   * @param \Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder
   */
  public static function buildConfigContainer(ContainerBuilder $containerBuilder) {
    $cancelReasons = [];
    $dao = \CRM_Core_DAO::executeQuery("
        SELECT `c`.`cancel_reason`
        FROM `civicrm_contribution` `c`
        INNER JOIN `civicrm_sdd_mandate` `m` ON `c`.`contribution_recur_id` = `m`.`entity_id` AND `m`.`entity_table` = 'civicrm_contribution_recur'
        WHERE `c`.`contribution_recur_id` IS NOT NULL
        AND `c`.`cancel_reason` IS NOT NULL
        AND `c`.`cancel_reason` NOT LIKE 'Rebooked to CiviCRM ID%'
        AND `c`.`cancel_reason` NOT LIKE 'Terugbetaling%'
        AND `c`.`cancel_reason` NOT LIKE 'Terugstorting%'
        AND `c`.`cancel_reason` NOT LIKE 'Zie ac%'
        GROUP BY `c`.`cancel_reason`
        HAVING COUNT(`c`.`cancel_reason`) >= 1;");
    while ($dao->fetch()) {
      $cancelReasons[$dao->cancel_reason] = $dao->cancel_reason;
    }
    $containerBuilder->setParameter('cancel_reasons', $cancelReasons);
  }

}
