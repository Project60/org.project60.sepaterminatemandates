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

/**
 * This class is used by the Search functionality.
 *
 *  - the search controller is used for building/processing multiform
 *    searches.
 *
 * Typically the first form will display the search criteria and it's results
 *
 * The second form is used to process search results with the associated actions.
 */
class CRM_Sepaterminatemandates_Controller_SearchMandates extends CRM_Core_Controller {

  /**
   * Class constructor.
   *
   * @param string $title
   * @param bool $modal
   * @param int|mixed|null $action
   */
  public function __construct($title = NULL, $modal = TRUE, $action = CRM_Core_Action::NONE) {
    parent::__construct($title, $modal);

    //$this->set('component_mode', CRM_Contact_BAO_Query::MODE_ALL);
    $this->_stateMachine = new CRM_Sepaterminatemandates_StateMachine_SearchMandates($this, $action);

    // create and instantiate the pages
    $this->addPages($this->_stateMachine, $action);

    // add all the actions
    $this->addActions();
  }

  /**
   * Process the request, overrides the default QFC run method
   * This routine actually checks if the QFC is modal and if it
   * is the first invalid page, if so it call the requested action
   * if not, it calls the display action on the first invalid page
   * avoids the issue of users hitting the back button and getting
   * a broken page
   *
   * This run is basically a composition of the original run and the
   * jump action
   *
   * @return mixed
   */
  public function run() {

    $actionName = $this->getActionName();
    list($pageName, $action) = $actionName;
    // Hack to replace to userContext for redirecting after a Task has been completed.
    // We want the redirect
    if (!$this->_pages[$pageName] instanceof CRM_Sepaterminatemandates_Form_SearchMandates) {
      $session = CRM_Core_Session::singleton();
      $qfKey = CRM_Utils_Request::retrieve('qfKey', 'String', $this);
      $urlPath = CRM_Utils_System::currentPath();
      $urlParams = 'force=1';
      if ($qfKey) {
        $urlParams .= "&qfKey=$qfKey";
      }

      $this->setDestination(CRM_Utils_System::url($urlPath, $urlParams));
    }

    return parent::run();
  }

  /**
   * @return mixed
   */
  public function selectorName() {
    return $this->get('selectorName');
  }

}
