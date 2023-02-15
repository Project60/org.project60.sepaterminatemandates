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

class CRM_Sepaterminatemandates_Form_Task_AddToGroup extends CRM_Core_Form_Task {

  protected function setEntityShortName() {
    self::$entityShortname = 'sepaterminatemandates';
  }

  public function preProcess() {
    $this->setEntityShortName();
    $session = CRM_Core_Session::singleton();
    $url = $session->readUserContext();
    $session->replaceUserContext($url);

    $searchFormValues = $this->controller->exportValues($this->get('searchFormName'));
    $this->_task = $searchFormValues['task'];
    $entityTasks = CRM_Sepaterminatemandates_Task::tasks();
    $this->assign('taskName', $entityTasks[$this->_task]);

    $entityIds = [];
    if ($searchFormValues['radio_ts'] == 'ts_sel') {
      foreach ($searchFormValues as $name => $value) {
        if (substr($name, 0, CRM_Core_Form::CB_PREFIX_LEN) == CRM_Core_Form::CB_PREFIX) {
          $entityIds[] = substr($name, CRM_Core_Form::CB_PREFIX_LEN);
        }
      }
    } else {
      $entityIds = $this->get('entityIds');
    }
    $this->_entityIds = $this->_componentIds = $entityIds;
    $this->assign('status', E::ts("Number of selected records: %1", array(1=>count($this->_entityIds))));
  }

  public function buildQuickForm() {
    $options = [E::ts('Add Contact To Existing Group'), E::ts('Create New Group')];
    $this->addRadio('group_option', E::ts('Group Options'), $options, ['onclick' => "return showElements();"]);

    $this->add('text', 'title', E::ts('Group Name:') . ' ',
      CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Group', 'title')
    );
    $this->addRule('title', E::ts('Name already exists in Database.'),
      'objectExists', ['CRM_Contact_DAO_Group', null, 'title']
    );

    $this->add('textarea', 'description', E::ts('Description:') . ' ',
      CRM_Core_DAO::getAttribute('CRM_Contact_DAO_Group', 'description')
    );

    $groupTypes = CRM_Core_OptionGroup::values('group_type', TRUE);
    if (!CRM_Core_Permission::access('CiviMail')) {
      $isWorkFlowEnabled = CRM_Mailing_Info::workflowEnabled();
      if ($isWorkFlowEnabled &&
        !CRM_Core_Permission::check('create mailings') &&
        !CRM_Core_Permission::check('schedule mailings') &&
        !CRM_Core_Permission::check('approve mailings')
      ) {
        unset($groupTypes['Mailing List']);
      }
    }

    if (!empty($groupTypes)) {
      $this->addCheckBox('group_type',
        E::ts('Group Type'),
        $groupTypes,
        NULL, NULL, NULL, NULL, '&nbsp;&nbsp;&nbsp;'
      );
    }

    // add select for groups
    $group = ['' => E::ts('- select group -')] + CRM_Core_PseudoConstant::nestedGroup();
    $this->add('select', 'group_id', E::ts('Select Group'), $group, FALSE, ['class' => 'crm-select2 huge']);
    CRM_Utils_System::setTitle(E::ts('Add Contacts to A Group'));
    $this->addDefaultButtons(E::ts('Add to Group'));
  }

  /**
   * Set the default form values.
   *
   *
   * @return array
   *   the default array reference
   */
  public function setDefaultValues() {
    $defaults = [];
    $defaults['group_option'] = 0;
    return $defaults;
  }

  /**
   * Add local and global form rules.
   */
  public function addRules() {
    $this->addFormRule(['CRM_Sepaterminatemandates_Form_Task_AddToGroup', 'formRule']);
  }

  /**
   * Global validation rules for the form.
   *
   * @param array $params
   *
   * @return array
   *   list of errors to be posted back to the form
   */
  public static function formRule($params) {
    $errors = [];

    if (!empty($params['group_option']) && empty($params['title'])) {
      $errors['title'] = E::ts("Group Name is a required field");
    }
    elseif (empty($params['group_option']) && empty($params['group_id'])) {
      $errors['group_id'] = E::ts("Select Group is a required field.");
    }

    return empty($errors) ? TRUE : $errors;
  }

  public function postProcess() {
    $submittedValues = $this->controller->exportValues();

    $groupOption = $submittedValues['group_option'] ?? NULL;
    if ($groupOption) {
      $groupParams = [];
      $groupParams['title'] = $submittedValues['title'];
      $groupParams['description'] = $submittedValues['description'];
      $groupParams['visibility'] = "User and User Admin Only";
      if (array_key_exists('group_type', $submittedValues) && is_array($submittedValues['group_type'])) {
        $groupParams['group_type'] = CRM_Core_DAO::VALUE_SEPARATOR . implode(CRM_Core_DAO::VALUE_SEPARATOR,
            array_keys($submittedValues['group_type'])
          ) . CRM_Core_DAO::VALUE_SEPARATOR;
      }
      else {
        $groupParams['group_type'] = '';
      }
      $groupParams['is_active'] = 1;

      $createdGroup = CRM_Contact_BAO_Group::create($groupParams);
      $submittedValues['group_id'] = $createdGroup->id;
    }

    $session = CRM_Core_Session::singleton();

    $name = 'CRM_Sepaterminatemandates_Form_Task_AddToGroup';
    $queue = CRM_Queue_Service::singleton()->create(array(
      'type' => 'Sql',
      'name' => $name,
      'reset' => TRUE, //do flush queue upon creation
    ));

    $total = count($this->_entityIds);
    $current = 0;
    foreach($this->_entityIds as $entityId) {
      $current++;
      if ($current > $total) {
        $current = $total;
      }
      $title = E::ts('Add to group %1', [1 => $current .'/'.$total]);
      //create a task without parameters
      $task = new CRM_Queue_Task(
        array(
          'CRM_Sepaterminatemandates_Form_Task_AddToGroup',
          'addToGroup'
        ), //call back method
        array($entityId, $submittedValues), //parameters,
        $title
      );
      //now add this task to the queue
      $queue->createItem($task);
    }

    $url = str_replace("&amp;", "&", $session->readUserContext());

    $runner = new CRM_Queue_Runner(array(
      'title' => E::ts('Add to Group'),
      'queue' => $queue, //the queue object
      'errorMode'=> CRM_Queue_Runner::ERROR_ABORT, //abort upon error and keep task in queue
      'onEnd' => array('postProcess', 'onEnd'), //method which is called as soon as the queue is finished
      'onEndUrl' => $url,
    ));

    $runner->runAllViaWeb(); // does not return
  }

  public static function addToGroup(CRM_Queue_TaskContext $ctx, int $mandateId, $formValues) {
    $mandate = civicrm_api3('SepaMandate', 'getsingle', ['id' => $mandateId]);
    $contactId = $mandate['contact_id'];
    CRM_Contact_BAO_GroupContact::addContactsToGroup([$contactId], $formValues['group_id']);
    return TRUE;
  }


}
