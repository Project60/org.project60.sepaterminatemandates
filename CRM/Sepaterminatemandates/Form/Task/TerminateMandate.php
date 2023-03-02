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

  protected $recordCount = 0;

  protected $searchFormValues = [];

  protected function setEntityShortName() {
    self::$entityShortname = 'sepaterminatemandates';
  }

  public function preProcess() {
    $this->setEntityShortName();
    $session = CRM_Core_Session::singleton();
    $url = $session->readUserContext();
    $session->replaceUserContext($url);

    $this->searchFormValues = $this->controller->exportValues($this->get('searchFormName'));
    $this->_task = $this->searchFormValues['task'];
    $entityTasks = CRM_Sepaterminatemandates_Task::tasks();
    $this->assign('taskName', $entityTasks[$this->_task]);

    $entityIds = [];
    $this->recordCount = CRM_Sepaterminatemandates_Utils::getSelectedEntityIdCount($this->searchFormValues);
    $this->_entityIds = $this->_componentIds = $entityIds;
    $this->assign('status', E::ts("Number of selected records: %1", array(1=>$this->recordCount)));
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
      'api' => array(),
      'create' => false,
      'multiple' => false,
    ], false);
    $this->add('text', 'subject', E::ts('Activity Subject'), [
      'class' => 'huge',
    ], true);
  }

  public function postProcess() {
    $submittedValues = $this->controller->exportValues();
    $submittedValues['is_manual'] = true;

    $session = CRM_Core_Session::singleton();

    $name = 'CRM_Sepaterminatemandates_Form_Task_TerminateMandate';
    $queue = CRM_Queue_Service::singleton()->create(array(
      'type' => 'Sql',
      'name' => $name,
      'reset' => TRUE, //do flush queue upon creation
    ));

    for($current=0; $current < $this->recordCount; $current++) {
      $title = E::ts('Terminate mandate %1', [1 => $current .'/'.$this->recordCount]);
      //create a task without parameters
      $task = new CRM_Queue_Task(
        array(
          'CRM_Sepaterminatemandates_Form_Task_TerminateMandate',
          'terminateMandate'
        ), //call back method
        array($current, $submittedValues, $this->searchFormValues), //parameters,
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

  public static function terminateMandate(CRM_Queue_TaskContext $ctx, int $offset, $terminateConfiguration, $searchFormValues) {
    $entityIds = CRM_Sepaterminatemandates_Utils::getSelectedEntityIds($searchFormValues, $offset, 1);
    foreach($entityIds as $entityId) {
      CRM_Sepaterminatemandates_Utils::terminateMandate($entityId, $terminateConfiguration);
    }
    return TRUE;
  }


}
