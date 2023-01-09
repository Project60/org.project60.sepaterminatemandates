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
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;

class ConfigContainer {

  /**
   * @var Config
   */
  public static $configContainer;

  private function __construct() {
  }

  /**
   * @return \Civi\SepaTerminateMandates\Config
   */
  public static function getInstance() {
    if (!self::$configContainer) {
      $file = self::getCacheFile();
      if (!file_exists($file)) {
        $containerBuilder = new ContainerBuilder();
        Config::buildConfigContainer($containerBuilder);
        $containerBuilder->compile();
        $dumper = new PhpDumper($containerBuilder);
        file_put_contents($file, $dumper->dump([
          'class' => 'CachedSepaTerminateMandatesConfigContainer',
          'base_class' => '\Civi\SepaTerminateMandates\Config',
        ]));
      }
      require_once $file;
      self::$configContainer = new \CachedSepaTerminateMandatesConfigContainer();
    }
    return self::$configContainer;
  }

  /**
   * Clear the cache.
   */
  public static function clearCache() {
    $file = self::getCacheFile();
    if (file_exists($file)) {
      unlink($file);
    }
  }

  /**
   * Clears the cached configuration file ony when custom field or custom group has been saved.
   *
   * @param $op
   * @param $objectName
   * @param $objectId
   * @param $objectRef
   */
  public static function postHook($op, $objectName, $id, &$objectRef) {
    $clearCacheObjects = ['Contribution'];
    if (in_array($objectName, $clearCacheObjects)) {
      self::clearCache();
    }
  }

  /**
   * The name of the cache file.
   *
   * @return string
   */
  public static function getCacheFile() {
    // The envId is build based on the domain and database settings.
    // So we cater for multisite installations and installations with one code base
    // and multiple databases.
    $envId = \CRM_Core_Config_Runtime::getId();
    return CIVICRM_TEMPLATE_COMPILEDIR.DIRECTORY_SEPARATOR."CachedSepaTerminateMandateConfigContainer.{$envId}.php";
  }

}
