<?php
/*
 * @version $Id: simcardvoltage.class.php 36 2012-08-31 13:59:28Z walid $
 LICENSE

  This file is part of the simcard plugin.

 Order plugin is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 Order plugin is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with GLPI; along with Simcard. If not, see <http://www.gnu.org/licenses/>.
 --------------------------------------------------------------------------
 @package   simcard
 @author    the simcard plugin team
 @copyright Copyright (c) 2010-2011 Simcard plugin team
 @license   GPLv2+
            http://www.gnu.org/licenses/gpl.txt
 @link      https://forge.indepnet.net/projects/simcard
 @link      http://www.glpi-project.org/
 @since     2009
 ---------------------------------------------------------------------- */

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

/// Class Vlan
class PluginSimcardSimcardVoltage extends CommonDropdown {


   static function getTypeName($nb=0) {
      global $LANG;
      return $LANG['plugin_simcard'][9];
   }

   static function install(Migration $migration) {
      global $DB;
      $table = getTableForItemType(__CLASS__);
      if (!TableExists($table)) {
         $query = "CREATE TABLE IF NOT EXISTS `$table` (
           `id` int(11) NOT NULL AUTO_INCREMENT,
           `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
           `comment` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
           PRIMARY KEY (`id`),
           KEY `name` (`name`)
         ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
         $DB->query($query) or die ("Error adding table $table");
         
         $query = "INSERT INTO `$table` (`id`, `name`, `comment`) VALUES
                     (1, '3V', ''),
                     (2, '5V', '');";
         $DB->query($query) or die("Error adding simcard voltages");
      }
   }
   
   static function uninstall() {
      global $DB;

      $displayPreference = new DisplayPreference();
      $displayPreference->deleteByCriteria(array('itemtype' => __CLASS__));
      
      $table = getTableForItemType(__CLASS__);
      $DB->query("DROP TABLE IF EXISTS `$table`");
   }
}
?>