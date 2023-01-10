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

class CRM_Sepaterminatemandates_Form_Task_TerminateMandate extends CRM_Core_Form_Task {

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
    $this->addDefaultButtons(E::ts('Next'), 'upload');
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
    ], true);
  }

  public function postProcess() {
    $submittedValues = $this->controller->exportValues();

    $session = CRM_Core_Session::singleton();

    $name = 'CRM_Sepaterminatemandates_Form_Task_TerminateMandate';
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
      $title = E::ts('Terminate mandate %1', [1 => $current .'/'.$total]);
      //create a task without parameters
      $task = new CRM_Queue_Task(
        array(
          'CRM_Sepaterminatemandates_Form_Task_TerminateMandate',
          'terminateMandate'
        ), //call back method
        array($entityId, $submittedValues), //parameters,
        $title
      );
      //now add this task to the queue
      $queue->createItem($task);
    }

    $url = str_replace("&amp;", "&", $session->readUserContext());

    $runner = new CRM_Queue_Runner(array(
      'title' => E::ts('Terminate mandates'),
      'queue' => $queue, //the queue object
      'errorMode'=> CRM_Queue_Runner::ERROR_ABORT, //abort upon error and keep task in queue
      'onEnd' => array('postProcess', 'onEnd'), //method which is called as soon as the queue is finished
      'onEndUrl' => $url,
    ));

    $runner->runAllViaWeb(); // does not return
  }

  public static function terminateMandate(CRM_Queue_TaskContext $ctx, int $entityId, $values) {
    $mandate = civicrm_api3('SepaMandate', 'getsingle', ['id' => $entityId]);
    $contactId = $mandate['contact_id'];
    civicrm_api3('SepaMandate', 'terminate', ['mandate_id' => $entityId, 'cancel_reason' => $values['reason']]);
    civicrm_api3('Activity', 'create', [
      //'source_contact_id' => $contactId,
      'activity_type_id' => $values['activity_type_id'],
      'status_id' => $values['activity_status_id'],
      'target_id' => $contactId,
      'assignee_id' => $values['activity_assignee'],
      'subject' => E::ts('Sepa mandate cancelled because: %1', [1=>$values['reason']]),
    ]);
    return TRUE;
  }


}
