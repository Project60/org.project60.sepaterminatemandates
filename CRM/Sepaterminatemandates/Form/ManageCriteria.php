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

class CRM_Sepaterminatemandates_Form_ManageCriteria extends CRM_Core_Form {

  public function preProcess() {
    parent::preProcess();
    $this->setTitle(E::ts('Manage Criteria for terminating mandates'));
    $criteria = Civi\Api4\SEPAMandateTerminationCriterion::get()->setLimit(0)->execute();
    $rows = [];
    foreach($criteria as $criterion) {
      $rows[] = $criterion;
    }
    $this->assign('rows', $rows);

    $session = CRM_Core_Session::singleton();
    $qfKey = CRM_Utils_Request::retrieve('qfKey', 'String', $this);
    $urlPath = CRM_Utils_System::currentPath();
    $urlParams = 'force=1';
    if ($qfKey) {
      $urlParams .= "&qfKey=$qfKey";
    }
    $session->replaceUserContext(CRM_Utils_System::url($urlPath, $urlParams));
  }

}
