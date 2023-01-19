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

class CRM_Sepaterminatemandates_Form_EditCriteria extends CRM_Core_Form {

  private $id;

  private $criteria;

  public function preProcess() {
    $this->id = CRM_Utils_Request::retrieve('id', 'Integer');
    $this->assign('criteria_id', $this->id);

    $session = CRM_Core_Session::singleton();
    switch($this->_action) {
      case CRM_Core_Action::DISABLE:
        Civi\Api4\SEPAMandateTerminationCriterion::update()
          ->addValue('is_active', '0')
          ->addWhere('id', '=', $this->id)
          ->execute();
        $session->setStatus(E::ts('Criteria disabled'), E::ts('Disable'), 'success');
        CRM_Utils_System::redirect($session->readUserContext());
        break;
      case CRM_Core_Action::ENABLE:
        Civi\Api4\SEPAMandateTerminationCriterion::update()
          ->addValue('is_active', '1')
          ->addWhere('id', '=', $this->id)
          ->execute();
        $session->setStatus(E::ts('Criteria enabled'), E::ts('Enable'), 'success');
        CRM_Utils_System::redirect($session->readUserContext());
        break;
    }

    if ($this->id) {
      $this->criteria = Civi\Api4\SEPAMandateTerminationCriterion::get()
        ->addWhere('id', '=', $this->id)
        ->execute()
        ->first();
      $this->assign('criteria', $this->criteria);
    }
  }

  public function buildQuickForm() {
    $this->add('hidden', 'id');
    if ($this->_action != CRM_Core_Action::DELETE) {
      $this->add('textarea', 'description', E::ts('Description'), [
        'rows' => 3,
        'cols' => 100,
        'class' => '',
      ], true);
      $this->add('checkbox', 'is_active', E::ts('Enabled'));

      $config = \Civi\SepaTerminateMandates\ConfigContainer::getInstance();
      $this->add('text', 'cancelled_contributions_qty', E::ts('Cancelled contributions Quantity'), [
        'class' => 'six'
      ], TRUE);
      $this->add('text', 'cancelled_contributions_months', E::ts('Cancelled contributions last months'), [
        'class' => 'six'
      ], TRUE);
      $this->addYesNo('cancelled_contribution_successive', E::ts('Successive'), FALSE, TRUE);
      $this->addEntityRef('campaign_ids', E::ts('Campaign'), [
        'entity' => 'Campaign',
        'select' => ['minimumInputLength' => 0],
        'placeholder' => E::ts('- Any campaign -'),
        'multiple' => TRUE,
      ], FALSE);
      $this->add('select', 'cancel_reasons', E::ts('Cancel Reason'), $config->getCancelReasons(), FALSE, [
        'style' => 'min-width:250px',
        'class' => 'crm-select2 huge',
        'placeholder' => E::ts('- Any reason -'),
        'multiple' => TRUE,
      ]);

      $this->add('text', 'reason', E::ts('Cancel Reason'), [
        'class' => 'huge',
      ], true);
      $this->add('select', 'activity_type_id', E::ts('Activity Type'), CRM_Sepaterminatemandates_Utils::getActivityTypes(), true, [
        'style' => 'min-width:250px',
        'class' => 'crm-select2 huge',
        'placeholder' => E::ts('- select -'),
      ]);
      $this->add('select', 'activity_status_id', E::ts('Activity Status'), CRM_Sepaterminatemandates_Utils::getActivityStatus(), true, [
        'style' => 'min-width:250px',
        'class' => 'crm-select2 huge',
        'placeholder' => E::ts('- select -'),
      ]);
      $this->addEntityRef('activity_assignee', E::ts('Activity Assignee'), [
        'style' => 'min-width:250px',
        'class' => 'huge',
        'placeholder' => E::ts('- select -'),
        'entity' => 'Contact',
        'api' => array('params' => ['contact_type' => ['IN' => ['Individual']]]),
        'create' => false,
        'multiple' => false,
      ], false);

      $this->addButtons(array(
        array('type' => 'next', 'name' => E::ts('Save'), 'isDefault' => TRUE,),
        array('type' => 'cancel', 'name' => E::ts('Cancel'))));
    } else {
      $this->addButtons(array(
        array('type' => 'next', 'name' => E::ts('Delete'), 'isDefault' => TRUE,),
        array('type' => 'cancel', 'name' => E::ts('Cancel'))));
    }
    parent::buildQuickForm();
  }

  public function postProcess() {
    $session = CRM_Core_Session::singleton();
    if ($this->_action == CRM_Core_Action::DELETE) {
      Civi\Api4\SEPAMandateTerminationCriterion::delete()
        ->addWhere('id', '=', $this->id)
        ->execute();

      $session->setStatus(E::ts('Criteria removed'), E::ts('Removed'), 'success');
    } else {
      $submittedValues = $this->exportValues();
      $values['description'] = $submittedValues['description'];
      $values['is_active'] = !empty($submittedValues['is_active']) ? 1 : 0;
      $values['search_criteria'] = [];
      $values['search_criteria']['cancelled_contributions_qty'] = $submittedValues['cancelled_contributions_qty'];
      $values['search_criteria']['cancelled_contributions_months'] = $submittedValues['cancelled_contributions_months'];
      $values['search_criteria']['cancelled_contribution_successive'] = $submittedValues['cancelled_contribution_successive'];
      $values['search_criteria']['campaign_ids'] = $submittedValues['campaign_ids'];
      $values['search_criteria']['cancel_reasons'] = $submittedValues['cancel_reasons'];
      $values['terminate_configuration'] = [];
      $values['terminate_configuration']['reason'] = $submittedValues['reason'];
      $values['terminate_configuration']['activity_type_id'] = $submittedValues['activity_type_id'];
      $values['terminate_configuration']['activity_status_id'] = $submittedValues['activity_status_id'];
      $values['terminate_configuration']['activity_assignee'] = $submittedValues['activity_assignee'];

      if ($this->id) {
        $values['id'] = $this->id;
        Civi\Api4\SEPAMandateTerminationCriterion::update()
          ->addWhere('id', '=', $this->id)
          ->setValues($values)
          ->execute();
      }
      else {
        Civi\Api4\SEPAMandateTerminationCriterion::create()
          ->setValues($values)
          ->execute();
      }
    }
    $redirectUrl = $session->popUserContext();
    CRM_Utils_System::redirect($redirectUrl);
  }

  /**
   * Function to set default values (overrides parent function)
   *
   * @return array $defaults
   * @access public
   */
  function setDefaultValues() {
    $defaults = array();
    $defaults['id'] = $this->id;
    switch ($this->_action) {
      case CRM_Core_Action::ADD:
        $this->setAddDefaults($defaults);
        break;
      case CRM_Core_Action::UPDATE:
        $this->setUpdateDefaults($defaults);
        break;
    }
    return $defaults;
  }

  /**
   * Function to set default values if action is add
   *
   * @param array $defaults
   * @access protected
   */
  protected function setAddDefaults(&$defaults) {
    $defaults['is_active'] = 1;;
  }

  /**
   * Function to set default values if action is update
   *
   * @param array $defaults
   * @access protected
   */
  protected function setUpdateDefaults(&$defaults) {
    if (!empty($this->criteria)) {
      if (isset($this->criteria['description'])) {
        $defaults['description'] = $this->criteria['description'];
      } else {
        $defaults['description'] = '';
      }
      $defaults['is_active'] = $this->criteria['is_active'];
      if (isset($this->criteria['search_criteria']) && is_array($this->criteria['search_criteria'])) {
        $defaults = array_merge($defaults, $this->criteria['search_criteria']);
      }
      if (isset($this->criteria['terminate_configuration']) && is_array($this->criteria['terminate_configuration'])) {
        $defaults = array_merge($defaults, $this->criteria['terminate_configuration']);
      }
    }
  }

}
