<?php
/**
 * @author Jaap Jansma <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

use CRM_Sepaterminatemandates_ExtensionUtil as E;

class CRM_Sepaterminatemandates_Form_Task_SearchActionDesigner extends CRM_Searchactiondesigner_Form_Task_Task {

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

    if (strpos($this->_task,'searchactiondesigner_') !== 0) {
      throw new \Exception(E::ts('Invalid search task'));
    }
    $this->searchTaskId = substr($this->_task, 21);

    $this->searchTask = civicrm_api3('SearchTask', 'getsingle', array('id' => $this->searchTaskId));
    $this->assign('searchTask', $this->searchTask);
    $this->assign('status', E::ts("Number of selected records: %1", array(1=>count($this->_entityIds))));
  }


}
