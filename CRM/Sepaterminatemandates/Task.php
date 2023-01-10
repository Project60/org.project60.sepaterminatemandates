<?php
/**
 * @author Jaap Jansma <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

use CRM_Sepaterminatemandates_ExtensionUtil as E;

class CRM_Sepaterminatemandates_Task extends CRM_Core_Task {

  static $objectType = null;

  /**
   * These tasks are the core set of tasks that the user can perform
   * on a contact / group of contacts.
   *
   * @return array
   *   the set of tasks for a group of contacts
   */
  public static function tasks() {
    if (!(self::$_tasks)) {
      self::$objectType = 'sepaterminatemandates';

      self::$_tasks = [
        'CRM_Sepaterminatemandates_Form_Task_TerminateMandate' => [
          'class' => 'CRM_Sepaterminatemandates_Form_Task_TerminateMandate',
          'title' => E::ts('Terminate Mandates'),
          'result' => true,
        ]
      ];
      parent::tasks();
    }

    return self::$_tasks;
  }

  /**
   * These tasks are the core set of tasks that the user can perform
   * on participants
   *
   * @param int $value
   *
   * @return array
   *   the set of tasks for a group of participants
   */
  public static function getTask($value) {
    static::tasks();
    if (!CRM_Utils_Array::value($value, self::$_tasks)) {
      // Children can specify a default task (eg. print), pick another if it is not valid.
      $value = key(self::$_tasks);
    }
    if ($value && isset(self::$_tasks[$value])) {
      return array(
        CRM_Utils_Array::value('class', self::$_tasks[$value]),
        CRM_Utils_Array::value('result', self::$_tasks[$value]),
      );
    }
    return array(null, null);
  }

  /**
   * Add data processor searches to the search action designer list
   *
   * @param $types
   */
  public static function searchActionDesignerTypes(&$types) {
    $types['sepaterminatemandates'] = array(
      'title' => E::ts('Sepa Terminate Mandates'),
      'class' => 'CRM_Sepaterminatemandates_Form_Task_SearchActionDesigner',
      'id_field_title' => E::ts('Mandate ID'),
    );
  }

}
