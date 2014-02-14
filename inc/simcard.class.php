<?php
/*
 * @version $Id: simcard.class.php 36 2012-08-31 13:59:28Z walid $
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

/// Class Simcard
//class PluginSimcardSimcard extends CommonDBTM {
class PluginSimcardSimcard extends CommonDBTM {

   // From CommonDBTM
   //static $types = array('');
  public $dohistory = true;
  //~ static $types = array('Computer', 'Monitor', 'NetworkEquipment', 'Peripheral', 'Phone', 'Printer', 'Software', 'Entity');
  static $types = array('Phone' , 'Entity');
   /**
    * Name of the type
    *
    * @param $nb  integer  number of item in the type (default 0)
   **/
   static function getTypeName($nb=0) {
      global $LANG;
      return $LANG['plugin_simcard']['profile'][1];
   }

   static function canCreate() {
      return Session::haveRight('simcard', 'w');
   }

   static function canView() {
      return Session::haveRight('simcard', 'r');
   }

     function defineTabs($options=array()) {
      global $LANG;
      $ong     = array();
      if ($this->fields['id'] > 0) {
         if (!isset($options['withtemplate']) || empty($options['withtemplate'])) {
            $this->addStandardTab('PluginSimcardSimcard_Item', $ong, $options);
            $this->addStandardTab('NetworkPort', $ong, $options);
            $this->addStandardTab('Document',$ong,$options);
            $this->addStandardTab('Infocom',$ong,$options);
            $this->addStandardTab('Contract_Item', $ong, $options);
            $this->addStandardTab('Ticket',$ong,$options);
            $this->addStandardTab('Note',$ong,$options);
            $this->addStandardTab('Log',$ong,$options);
            $this->addStandardTab('Event',$ong,$options);
         } else {
            $this->addStandardTab('Document',$ong,$options);
            $this->addStandardTab('Log',$ong,$options);
            $this->addStandardTab('Event',$ong,$options);
         }
      } else {
         $ong[1] = $LANG['title'][26];
      }

      return $ong;
   }

   /**
    * Print the simcard form
    *
    * @param $ID        integer ID of the item
    * @param $options   array
    *     - target for the Form
    *     - withtemplate template or basic simcard
    *
    *@return Nothing (display)
   **/
    function showForm($ID, $options=array()) {
      global $CFG_GLPI, $DB, $LANG;

      $target       = $this->getFormURL();
      $withtemplate = '';

      if (isset($options['target'])) {
        $target = $options['target'];
      }

      if (isset($options['withtemplate'])) {
         $withtemplate = $options['withtemplate'];
      }
      
      if ($ID > 0) {
         $this->check($ID,'r');
      } else {
         // Create item
         $this->check(-1,'w');
      }

      $this->showTabs($options);
      $this->showFormHeader($options);

      if (isset($options['itemtype']) && isset($options['items_id'])) {
         echo "<tr class='tab_bg_1'>";
         echo "<td>".$LANG['document'][14]."</td>";
         echo "<td>";
         $item = new $options['itemtype'];
         $item->getFromDB($options['items_id']);
         echo $item->getLink(1);
         echo "</td>";
         echo "<td colspan='2'></td></tr>\n";
         echo "<input type='hidden' name='_itemtype' value='".$options['itemtype']."'>";
         echo "<input type='hidden' name='_items_id' value='".$options['items_id']."'>";
      }
      
      
      echo "<tr class='tab_bg_1'>";
      echo "<td>".__('Nom').
                          (isset($options['withtemplate']) && $options['withtemplate']?"*":"").
           "</td>";
      echo "<td>";
      $objectName = autoName($this->fields["name"], "name",
                             (isset($options['withtemplate']) && $options['withtemplate']==2),
                             $this->getType(), $this->fields["entities_id"]);
      Html::autocompletionTextField($this, 'name', array('value' => $objectName));
      echo "</td>";
      echo "<td>".__('statut')."</td>";
      echo "<td>";
      Dropdown::show('State', array('value' => $this->fields["states_id"]));
      echo "</td></tr>\n";

      echo "<tr class='tab_bg_1'>";
      echo "<td>".('Lieu')."</td>";
      echo "<td>";
      Dropdown::show('Location', array('value'  => $this->fields["locations_id"],
                                       'entity' => $this->fields["entities_id"]));
      echo "</td>";
      echo "<td>".$LANG['plugin_simcard'][6]."</td>";
      echo "<td>";
      Dropdown::show('PluginSimcardSimcardSize',
                     array('value' => $this->fields["plugin_simcard_simcardsizes_id"]));
      echo "</td></tr>\n";

      echo "<tr class='tab_bg_1'>";
      echo "<td>".('Responsable Technique')."</td>";
      echo "<td>";
      User::dropdown(array('name'   => 'users_id_tech',
                           'value'  => $this->fields["users_id_tech"],
                           'right'  => 'interface',
                           'entity' => $this->fields["entities_id"]));
      echo "</td>";
      echo "<td>".$LANG['plugin_simcard'][9]."</td>";
      echo "<td>";
      Dropdown::show('PluginSimcardSimcardVoltage',
                     array('value' => $this->fields["plugin_simcard_simcardvoltages_id"]));
      echo "</td></tr>\n";
      
      echo "<tr class='tab_bg_1'>";
      echo "<td>".$LANG['plugin_simcard'][7]."</td>";
      echo "<td>";
      Dropdown::show('PluginSimcardPhoneOperator',
                     array('value' => $this->fields["plugin_simcard_phoneoperators_id"]));
      echo "</td>";
      echo "<td>" . ('Associable à un ticket') . "&nbsp;:</td><td>";
      Dropdown::showYesNo('is_helpdesk_visible',$this->fields['is_helpdesk_visible']);
      echo "</td></tr>\n";
   
      
      echo "<tr class='tab_bg_1'>";
      echo "<td>".('Utilisateur')."</td>";
      echo "<td>";
      User::dropdown(array('value'  => $this->fields["users_id"],
                           'entity' => $this->fields["entities_id"],
                           'right'  => 'all'));
      echo "</td>";

      echo "<input type='hidden' name='is_global' value='1'>";
      
      echo "<td></td></tr>\n";
      echo "<tr class='tab_bg_1'>";
      echo "<td>".('Groupe')."</td>";
      echo "<td>";
      Dropdown::show('Group', array('value'     => $this->fields["groups_id"],
                                    'entity'    => $this->fields["entities_id"]));

      echo "</td>";
      echo "<td rowspan='7'>".('common')."</td>";
      echo "<td rowspan='7' class='middle'>";
      echo "<textarea cols='45' rows='15' name='comment' >".$this->fields["comment"]."</textarea>";
      echo "</td></tr>\n";
      
      echo "<tr class='tab_bg_1'>";
      echo "<td>".$LANG['plugin_simcard'][1]."</td>";
      echo "<td>";
      Html::autocompletionTextField($this,'phonenumber');
      echo "</td></tr>\n";

      echo "<tr class='tab_bg_1'>";
      echo "<td>".$LANG['plugin_simcard'][8]."</td>";
      echo "<td>";
      Html::autocompletionTextField($this,'serial');
      echo "</td></tr>\n";
      
      echo "<tr class='tab_bg_1'>";
      echo "<td>".("Numéro d'inventaire").
                          (isset($options['withtemplate']) && $options['withtemplate']?"*":"").
           "</td>";
      echo "<td>";
      $objectName = autoName($this->fields["otherserial"], "otherserial",
                             (isset($options['withtemplate']) && $options['withtemplate']==2),
                             $this->getType(), $this->fields["entities_id"]);
      Html::autocompletionTextField($this, 'otherserial', array('value' => $objectName));
      echo "</td></tr>\n";
      
      //Only show PIN and PUK code to users who can write (theses informations are highly sensible)
      if (Session::haveRight('simcard', 'w')) {
         echo "<tr class='tab_bg_1'>";
         echo "<td>".$LANG['plugin_simcard'][3]."</td>";
         echo "<td>";
         Html::autocompletionTextField($this,'pin');
         echo "</td></tr>\n";
         
         echo "<tr class='tab_bg_1'>";
         echo "<td>".$LANG['plugin_simcard'][5]."</td>";
         echo "<td>";
         Html::autocompletionTextField($this,'pin2');
         echo "</td></tr>\n";
         
         echo "<tr class='tab_bg_1'>";
         echo "<td>".$LANG['plugin_simcard'][4]."</td>";
         echo "<td>";
         Html::autocompletionTextField($this,'puk');
         echo "</td></tr>\n";

         echo "<tr class='tab_bg_1'>";
         echo "<td>".$LANG['plugin_simcard'][2]."</td>";
         echo "<td>";
         Html::autocompletionTextField($this,'puk2');
         echo "</td></tr>\n";
      }

      
      $this->showFormButtons($options);
      $this->addDivForTabs();

      return true;
   }

     function prepareInputForAdd($input) {

      if (isset($input["id"]) && $input["id"]>0) {
         $input["_oldID"] = $input["id"];
      }
      unset($input['id']);
      unset($input['withtemplate']);

       return $input;
   }
   
    function post_addItem() {
      global $DB, $CFG_GLPI;

      // Manage add from template
      if (isset($this->input["_oldID"])) {
         // ADD Infocoms
      //   $ic = new Infocom();
        // $ic->cloneItem($this->getType(), $this->input["_oldID"], $this->fields['id']);
Infocom::cloneItem($this->getType(), $this->input["_oldID"], $this->fields['id']);
		//test 0.84.3
			Contract_Item::cloneItem($this->getType(), $this->input["_oldID"], $this->fields['id']);
         // ADD Contract
     //    $query = "SELECT `contracts_id`
              //     FROM `glpi_contracts_items`
            //       WHERE `items_id` = '".$this->input["_oldID"]."'
          //               AND `itemtype` = '".$this->getType()."'";
        // $result = $DB->query($query);

        // if ($DB->numrows($result)>0) {
          //  $contractitem = new Contract_Item();

           // while ($data=$DB->fetch_array($result)) {
             //  $contractitem->add(array('contracts_id' => $data["contracts_id"],
               //                         'itemtype'     => $this->getType(),
                 //                       'items_id'     => $this->fields['id']));
          //  }
         //}

         // ADD Documents
       //  $query = "SELECT `documents_id`
         //          FROM `glpi_documents_items`
           //        WHERE `items_id` = '".$this->input["_oldID"]."'
             //            AND `itemtype` = '".$this->getType()."'";
        // $result = $DB->query($query);
//
  //       if ($DB->numrows($result)>0) {
    //        $docitem = new Document_Item();
//
  //          while ($data=$DB->fetch_array($result)) {
    //           $docitem->add(array('documents_id' => $data["documents_id"],
      //                             'itemtype'     => $this->getType(),
        //                           'items_id'     => $this->fields['id']));
Document_Item::cloneItem($this->getType(), $this->input["_oldID"], $this->fields['id']);          
 // }
   //      }
     }

      if (isset($this->input['_itemtype']) && isset($this->input['_items_id'])) {
         $simcard_item = new PluginSimcardSimcard_Item();
         $tmp['plugin_simcard_simcards_id'] = $this->getID();
         $tmp['itemtype'] = $this->input['_itemtype'];
         $tmp['items_id'] = $this->input['_items_id'];
         $simcard_item->add($tmp);
      }
      
   }
   
    function getSearchOptions() {
      global $CFG_GLPI, $LANG;

      $tab = array();
      $tab['common']             = $LANG['plugin_simcard']['profile'][1];

      $tab[1]['table']           = $this->getTable();
      $tab[1]['field']           = 'name';
    //  $tab[1]['name']            = $LANG['common'][16];
      $tab[1]['name']            = __('Name');
      $tab[1]['datatype']        = 'itemlink';
      $tab[1]['itemlink_type']   = $this->getType();
      $tab[1]['massiveaction']   = false; // implicit key==1
      $tab[1]['injectable']      = true;
      $tab[1]['checktype']       = 'text';
      $tab[1]['displaytype']     = 'text';
      
      $tab[2]['table']           = $this->getTable();
      $tab[2]['field']           = 'id';
      $tab[2]['name']            = __('Type');
      $tab[2]['massiveaction']   = false; // implicit field is id
      $tab[2]['injectable']      = false;
      
      $tab[5]['table']           = $this->getTable();
      $tab[5]['field']           = 'serial';
      $tab[5]['name']            = $LANG['plugin_simcard'][8];
      $tab[5]['datatype']        = 'string';
      $tab[5]['checktype']       = 'text';
      $tab[5]['displaytype']     = 'text';
      $tab[5]['injectable']      = true;
      
      $tab[6]['table']           = $this->getTable();
      $tab[6]['field']           = 'otherserial';
      $tab[6]['name']            = __('common');
      $tab[6]['datatype']        = 'string';
      $tab[6]['checktype']       = 'text';
      $tab[6]['displaytype']     = 'text';
      $tab[6]['injectable']      = true;
      
      $tab[16]['table']          = $this->getTable();
      $tab[16]['field']          = 'comment';
      $tab[16]['name']           = __('common');
      $tab[16]['datatype']       = 'text';
      $tab[16]['linkfield']      = 'comment';
      $tab[16]['checktype']      = 'text';
      $tab[16]['displaytype']    = 'multiline_text';
      $tab[16]['injectable']      = true;
      
      $tab += Location::getSearchOptionsToAdd();

      $tab[19]['table']          = $this->getTable();
      $tab[19]['field']          = 'date_mod';
      $tab[19]['name']           = __('login');
      $tab[19]['datatype']       = 'datetime';
      $tab[19]['massiveaction']  = false;
      $tab[19]['injectable']      = false;
      
      $tab[24]['table']          = 'glpi_users';
      $tab[24]['field']          = 'name';
      $tab[24]['linkfield']      = 'users_id_tech';
      $tab[24]['name']           = __('common');
      $tab[24]['checktype']      = 'text';
      $tab[24]['displaytype']    = 'user';
      $tab[24]['injectable']      = true;

      $tab[23]['table']          = 'glpi_plugin_simcard_simcardvoltages';
      $tab[23]['field']          = 'name';
      $tab[23]['name']           = $LANG['plugin_simcard'][9];
      $tab[23]['checktype']      = 'text';
      $tab[23]['displaytype']    = 'dropdown';
      $tab[23]['injectable']      = true;
      
      $tab[25]['table']          = 'glpi_plugin_simcard_simcardsizes';
      $tab[25]['field']          = 'name';
      $tab[25]['name']           = $LANG['plugin_simcard'][6];
      $tab[25]['checktype']      = 'text';
      $tab[25]['displaytype']    = 'dropdown';
      $tab[25]['injectable']      = true;
      
      $tab[26]['table']          = 'glpi_plugin_simcard_phoneoperators';
      $tab[26]['field']          = 'name';
      $tab[26]['name']           = $LANG['plugin_simcard'][7];
      $tab[26]['checktype']      = 'text';
      $tab[26]['displaytype']    = 'dropdown';
      $tab[26]['injectable']      = true;
      
      $tab[27]['table']          = $this->getTable();
      $tab[27]['field']          = 'phonenumber';
      $tab[27]['name']           = $LANG['plugin_simcard'][1];
      $tab[27]['checktype']       = 'text';
      $tab[27]['displaytype']     = 'text';
      $tab[27]['injectable']      = true;
      
      if (Session::haveRight('simcard', 'w')) {
         $tab[28]['table']          = $this->getTable();
         $tab[28]['field']          = 'pin';
         $tab[28]['name']           = $LANG['plugin_simcard'][3];
         $tab[28]['checktype']       = 'text';
         $tab[28]['displaytype']     = 'text';
         $tab[28]['injectable']      = true;
         
         $tab[29]['table']          = $this->getTable();
         $tab[29]['field']          = 'puk';
         $tab[29]['name']           = $LANG['plugin_simcard'][4];
         $tab[29]['checktype']       = 'text';
         $tab[29]['displaytype']     = 'text';
         $tab[29]['injectable']      = true;

         $tab[30]['table']          = $this->getTable();
         $tab[30]['field']          = 'pin2';
         $tab[30]['name']           = $LANG['plugin_simcard'][5];
         $tab[30]['checktype']       = 'text';
         $tab[30]['displaytype']     = 'text';
         $tab[30]['injectable']      = true;
         
         $tab[32]['table']          = $this->getTable();
         $tab[32]['field']          = 'puk2';
         $tab[32]['name']           = $LANG['plugin_simcard'][2];
         $tab[32]['checktype']       = 'text';
         $tab[32]['displaytype']     = 'text';
         $tab[32]['injectable']      = true;
      }

      $tab[31]['table']          = 'glpi_states';
      $tab[31]['field']          = 'name';
      $tab[31]['name']           = __('Statut');
      $tab[31]['checktype']       = 'text';
      $tab[31]['displaytype']     = 'dropdown';
      $tab[31]['injectable']      = true;
      
      $tab[49]['table']          = 'glpi_groups';
      $tab[49]['field']          = 'name';
      $tab[49]['linkfield']      = 'groups_id';
      $tab[49]['name']           = __('Group in charge of the hardware');
      $tab[49]['checktype']       = 'text';
      $tab[49]['displaytype']     = 'dropdown';
      $tab[49]['injectable']      = true;
      
      $tab[70]['table']          = 'glpi_users';
      $tab[70]['field']          = 'name';
      $tab[70]['name']           = __('Technician in charge of the hardware');
      $tab[70]['checktype']       = 'text';
      $tab[70]['displaytype']     = 'user';
      $tab[70]['injectable']      = true;
      
      $tab[80]['table']          = 'glpi_entities';
      $tab[80]['field']          = 'completename';
      $tab[80]['name']           = __('Entity');
      $tab[80]['injectable']     = false;
      
      $tab[90]['table']          = $this->getTable();
      $tab[90]['field']          = 'notepad';
      $tab[90]['name']           = __('title');
      $tab[90]['massiveaction']  = false;
      $tab[90]['linkfield']      = 'notepad';
      $tab[90]['checktype']       = 'text';
      $tab[90]['displaytype']     = 'multiline_text';
      $tab[90]['injectable']      = true;
      
      $tab[91]['injectable']      = false;
      $tab[93]['injectable']      = false;

      $tab[3]['checktype']       = 'text';
      $tab[3]['displaytype']     = 'dropdown';
      $tab[3]['injectable']      = true;

      return $tab;
   }
   
  function install(Migration $migration) {
      global $DB;
      $table = getTableForItemType(__CLASS__);
      if (!TableExists($table)) {
         $query = "CREATE TABLE IF NOT EXISTS `$table` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `entities_id` int(11) NOT NULL DEFAULT '0',
              `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
              `phonenumber` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
              `serial` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
              `pin` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
              `pin2` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
              `puk` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
              `puk2` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
              `otherserial` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
              `states_id` int(11) NOT NULL DEFAULT '0',
              `locations_id` int(11) NOT NULL DEFAULT '0',
              `users_id` int(11) NOT NULL DEFAULT '0',
              `users_id_tech` int(11) NOT NULL DEFAULT '0',
              `groups_id` int(11) NOT NULL DEFAULT '0',
              `plugin_simcard_phoneoperators_id` int(11) NOT NULL DEFAULT '0',
              `manufacturers_id` int(11) NOT NULL DEFAULT '0',
              `plugin_simcard_simcardsizes_id` int(11) NOT NULL DEFAULT '0',
              `plugin_simcard_simcardvoltages_id` int(11) NOT NULL DEFAULT '0',
              `comment` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL,
              `date_mod` datetime DEFAULT NULL,
              `is_template` tinyint(1) NOT NULL DEFAULT '0',
              `is_global` tinyint(1) NOT NULL DEFAULT '0',
              `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
              `template_name` varchar(255) COLLATE utf8_unicode_ci NULL,
              `notepad` longtext COLLATE utf8_unicode_ci NULL,
              `ticket_tco` decimal(20,4) DEFAULT '0.0000',
              `is_helpdesk_visible` tinyint(1) NOT NULL DEFAULT '1',
              PRIMARY KEY (`id`),
              KEY `name` (`name`),
              KEY `entities_id` (`entities_id`),
              KEY `states_id` (`states_id`),
              KEY `plugin_simcard_phoneoperators_id` (`plugin_simcard_phoneoperators_id`),
              KEY `plugin_simcard_simcardsizes_id` (`plugin_simcard_simcardsizes_id`),
              KEY `plugin_simcard_simcardvoltages_id` (`plugin_simcard_simcardvoltages_id`),
              KEY `manufacturers_id` (`manufacturers_id`),
              KEY `pin` (`pin`),
              KEY `pin2` (`pin2`),
              KEY `puk` (`puk`),
              KEY `puk2` (`puk2`),
              KEY `serial` (`serial`),
              KEY `users_id` (`users_id`),
              KEY `users_id_tech` (`users_id_tech`),
              KEY `groups_id` (`groups_id`),
              KEY `is_template` (`is_template`),
              KEY `is_deleted` (`is_deleted`),
              KEY `is_helpdesk_visible` (`is_helpdesk_visible`),
              KEY `is_global` (`is_global`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;";
         $DB->query($query) or die("Error adding table $table");
      }
   }
   
    function uninstall() {
      global $DB;

      foreach (array('DisplayPreference', 'Document_Item', 'Bookmark', 'Log') as $itemtype) {
         $item = new $itemtype();
         $item->deleteByCriteria(array('itemtype' => __CLASS__));
      }

      $plugin = new Plugin();
      if ($plugin->isActivated('datainjection') && class_exists('PluginDatainjectionModel')) {
         PluginDatainjectionModel::clean(array('itemtype' => __CLASS__));
      }

      if ($plugin->isInstalled('customfields') && $plugin->isActivated('customfields')) {
         PluginCustomfieldsItemtype::unregisterItemtype('PluginSimcardSimcard');
      }
      
      $table = getTableForItemType(__CLASS__);
      $DB->query("DROP TABLE IF EXISTS `$table`");
   }

     function getTabNameForItem(CommonGLPI $item, $withtemplate=0) {
      global $LANG;

      if (in_array(get_class($item), PluginSimcardSimcard_Item::getClasses())
         || get_class($item) == 'Profile') {
         return array(1 => $LANG['plugin_simcard']['profile'][1]);
      } elseif (get_class($item) == __CLASS__) {
         return $LANG['plugin_simcard']['profile'][1];
      }
      return '';
  }


 static function displayTabContentForItem(CommonGLPI $item, $tabnum=1, $withtemplate=0) {
      
  //    switch (get_class($item)) {
    //     case 'Profile':
      //      $profile      = new PluginSimcardProfile();
        //    if (!$profile->getFromDBByProfile($item->getField('id'))) {
          //     $profile->createAccess($item->getField('id'));
            //}
        //$profile->showForm($item->getField('id'));
         //break;
         //default:
           // PluginSimcardSimcard_Item::showForItem($item);
            //break;
      		$self=new self();
		if($item->getType()=='PluginSimcardSimcard') {
		 $self->showtotal($item->getField('id'));
	}
      return true;
   }

  /**
    * Type than could be linked to a Rack
    *
    * @param $all boolean, all type, or only allowed ones
    *
    * @return array of types
   **/
   static function getTypes($all=false) {

      if ($all) {
         return self::$types;
      }

      // Only allowed types
      $types = self::$types;

      foreach ($types as $key => $type) {
         if (!class_exists($type)) {
            continue;
         }

         $item = new $type();
         if (!$item->canView()) {
            unset($types[$key]);
         }
      }
      return $types;
   }
}

?>
