<?php

use CRM_Sepaterminatemandates_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/quickform/
 */
class CRM_Sepaterminatemandates_Form_SearchMandates extends CRM_Core_Form_Search {

  /**
   * Prepare for search by loading options from the url, handling force
   * searches, retrieving form values.
   *
   * @throws \CRM_Core_Exception
   * @throws \CiviCRM_API3_Exception
   */
  public function preProcess() {
    parent::preProcess();
    $qfKey = CRM_Utils_Request::retrieveValue('qfKey', 'String');
    $urlPath = CRM_Utils_System::currentPath();
    $urlParams = 'force=1';
    if ($qfKey) {
      $urlParams .= "&qfKey=$qfKey";
    }
    $this->currentUrl = CRM_Utils_System::url($urlPath, $urlParams);
    $session = CRM_Core_Session::singleton();
    $session->replaceUserContext($this->currentUrl);
    $this->_searchButtonName = $this->getButtonName('refresh');
    $this->_actionButtonName = $this->getButtonName('next', 'action');
    $this->_done = FALSE;
    $this->defaults = [];
    $this->_reset = CRM_Utils_Request::retrieveValue('reset', 'Boolean');
    $this->_force = CRM_Utils_Request::retrieveValue('force', 'Boolean');
    $this->_context = CRM_Utils_Request::retrieveValue('context', 'String', 'search');
    $this->_formValues = $this->getSubmitValues();
    if (!empty($this->_formValues)) {
      $defaultLimit = 50;
      $this->assign('cancelledContributionsMonths', $this->_formValues['cancelled_contributions_months']);
      $limit = CRM_Utils_Request::retrieveValue('crmRowCount', 'Positive', $defaultLimit);
      $pageId = CRM_Utils_Request::retrieveValue('crmPID', 'Positive', 1);
      $offset = ($pageId - 1) * $limit;

      $query = new CRM_Sepaterminatemandates_Query($this->getSubmitValues(), $offset, $limit);
      $count = $query->count();
      $rows = $query->rows();
      $this->entityIDs = [];
      foreach($rows as $index => $row) {
        $rows[$index]['checkbox'] = CRM_Core_Form::CB_PREFIX . $row['id'];
        $this->addElement('checkbox', $rows[$index]['checkbox'], NULL, NULL, ['class' => 'select-row']);
        $this->entityIDs[] = $row['id'];
      }

      $pagerParams = $this->getPagerParams($defaultLimit);
      $pagerParams['total'] = $count;
      $pagerParams['pageID'] = $pageId;
      $this->pager = new CRM_Utils_Pager($pagerParams);
      $this->assign('pager', $this->pager);
      $this->controller->set('rowCount', $count);

      $this->addElement('checkbox', 'toggleSelect', NULL, NULL, ['class' => 'select-rows']);
      $this->assign('rows', $rows);
      $this->controller->set('entityIds', $this->entityIDs);
    }
  }


  public function buildQuickForm() {
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
      'multiple' => TRUE,
    ], FALSE);
    $this->add('select', 'cancel_reasons', E::ts('Cancel Reason'), $config->getCancelReasons(), FALSE, [
      'style' => 'min-width:250px',
      'class' => 'crm-select2 huge',
      'placeholder' => E::ts('- select -'),
      'multiple' => TRUE,
    ]);

    parent::buildQuickForm();
  }

  /**
   * This virtual function is used to set the default values of various form
   * elements.
   *
   * @return array|NULL
   *   reference to the array of default values
   */
  public function setDefaultValues() {
    $defaults = [];
    $defaults['cancelled_contributions_qty'] = 3;
    $defaults['cancelled_contributions_months'] = 12;
    $defaults['cancelled_contribution_successive'] = '1';
    return $defaults;
  }


  public function postProcess() {
    if (!empty($_POST)) {
      $this->_formValues = $this->controller->exportValues($this->_name);
    }
    $this->set('formValues', $this->_formValues);
    $buttonName = $this->controller->getButtonName();
    if ($buttonName && $buttonName == $this->_actionButtonName) {
      // check actionName and if next, then do not repeat a search, since we are going to the next page
      // hack, make sure we reset the task values
      $formName = $this->controller->getStateMachine()->getTaskFormName();
      $this->controller->resetPage($formName);
      return;
    }
  }

  /**
   * Builds the list of tasks or actions that a searcher can perform on a result set.
   *
   * @return array
   */
  public function buildTaskList() {
    if (!$this->_taskList) {
      $this->_taskList = CRM_Sepaterminatemandates_Task::taskTitles();
    }
    return $this->_taskList;
  }

  /**
   * @return array
   */
  protected function getPagerParams($defaultLimit) {
    $params = [];
    $params['total'] = 0;
    $params['status'] =E::ts('%%StatusMessage%%');
    $params['csvString'] = NULL;
    $params['rowCount'] =  $defaultLimit;
    $params['buttonTop'] = 'PagerTopButton';
    $params['buttonBottom'] = 'PagerBottomButton';
    return $params;
  }

}
