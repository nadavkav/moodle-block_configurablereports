<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/** Configurable Reports
 * A Moodle block for creating customizable reports
 * @package blocks
 * @author: Juan leyva <http://www.twitter.com/jleyvadelgado>
 * @date: 2009
 */

require_once($CFG->dirroot.'/blocks/configurable_reports/plugin.class.php');

class plugin_firstlevelcategories extends plugin_base{

    function init(){
        $this->form = false;
        $this->unique = true;
        $this->fullname = get_string('filterfirstlevelcategories','block_configurable_reports');
        $this->reporttypes = array('categories','sql');
    }

    function summary($data){
        return get_string('filterfirstlevelcategories_summary','block_configurable_reports');
    }

    function execute($finalelements, $data){

        $filter_firstlevelcategories = optional_param('filter_firstlevelcategories',0,PARAM_INT);
        if(!$filter_firstlevelcategories)
            return $finalelements;

        if ($this->report->type != 'sql') {
            return array($filter_firstlevelcategories);
        } else {
            if (preg_match("/%%FILTER_FLSUBCATEGORIES:([^%]+)%%/i", $finalelements, $output)) {
                $replace = ' AND ('.$output[1].' LIKE CONCAT( \'%/\', '.$filter_firstlevelcategories.') OR '.$output[1].' LIKE CONCAT( \'%/\', '.$filter_firstlevelcategories.', \'/%\') ) ';
                $finalelements = str_replace('%%FILTER_FLSUBCATEGORIES:'.$output[1].'%%', $replace, $finalelements);
            }
            // Once more... In case we have a different synatx
            if (preg_match("/%%FILTER_FLSUBCATEGORIES:([^%]+)%%/i", $finalelements, $output)) {
                $replace = ' AND ('.$output[1].' LIKE CONCAT( \'%/\', '.$filter_firstlevelcategories.') OR '.$output[1].' LIKE CONCAT( \'%/\', '.$filter_firstlevelcategories.', \'/%\') ) ';
                $finalelements = str_replace('%%FILTER_FLSUBCATEGORIES:'.$output[1].'%%', $replace, $finalelements);
            }
        }
        return $finalelements;
    }

    function print_filter(&$mform){
        global $remoteDB;

        $filter_firstlevelcategories = optional_param('filter_firstlevelcategories',0,PARAM_INT);

        $reportclassname = 'report_'.$this->report->type;
        $reportclass = new $reportclassname($this->report);

        if($this->report->type != 'sql'){
            $components = cr_unserialize($this->report->components);
            $conditions = $components['conditions'];

            $firstlevelcategorieslist = $reportclass->elements_by_conditions($conditions);
        } else {
            $firstlevelcategorieslist = array_keys($remoteDB->get_records('course_categories', null, 'path'));
        }

        $courseoptions = array();
        $courseoptions[0] = get_string('filter_all', 'block_configurable_reports');

        if(!empty($firstlevelcategorieslist)){
            list($usql, $params) = $remoteDB->get_in_or_equal($firstlevelcategorieslist);
            $firstlevelcategories = $remoteDB->get_records_select('course_categories', "depth = '1' AND id $usql", $params, 'path');

            foreach($firstlevelcategories as $c){
                $courseoptions[$c->id] = str_repeat('&nbsp&nbsp&nbsp', $c->depth).' '.format_string($c->name);
            }
        }

        $mform->addElement('select', 'filter_firstlevelcategories', get_string('category'), $courseoptions);
        $mform->setType('filter_firstlevelcategories', PARAM_INT);

    }

}

