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

class plugin_coursemodule extends plugin_base{

	function init(){
		$this->form = false;
		$this->unique = true;
		$this->fullname = get_string('filtercoursemodule', 'block_configurable_reports');
		$this->reporttypes = array('courses','sql');
	}

	function summary($data){
		return get_string('filtercoursemodule_summary', 'block_configurable_reports');
	}

	function execute($finalelements, $data){

		$filter_coursemodule = optional_param('filter_coursemodule', 0 ,PARAM_INT);
		if(!$filter_coursemodule)
			return $finalelements;

		if($this->report->type != 'sql'){
				return array($filter_coursemodule);
		} else {
			if(preg_match("/%%FILTER_COURSEMODULE:([^%]+)%%/i", $finalelements, $output)){
				$replace = ' AND '.$output[1].' = '.$filter_coursemodule;
				return str_replace('%%FILTER_COURSEMODULE:'.$output[1].'%%', $replace, $finalelements);
			}
		}
		return $finalelements;
	}

	function print_filter(&$mform){
		global $remoteDB, $CFG;

		//$filter_coursemodule = optional_param('filter_coursemodule', 0, PARAM_INT);

		$reportclassname = 'report_'.$this->report->type;
		$reportclass = new $reportclassname($this->report);

		if($this->report->type != 'sql'){
			$components = cr_unserialize($this->report->components);
			$conditions = $components['conditions'];

            $coursemodulelist = $reportclass->elements_by_conditions($conditions);
		} else {
            $coursemodulelist = array_keys($remoteDB->get_records('modules'));
		}

		$coursemoduleoptions = array();
		$coursemoduleoptions[0] = get_string('filter_all', 'block_configurable_reports');

		if(!empty($coursemodulelist)){
			list($usql, $params) = $remoteDB->get_in_or_equal($coursemodulelist);
			$coursemodules = $remoteDB->get_records_select('modules', "id $usql", $params);

			foreach($coursemodules as $m){
				$coursemodulesoptions[$m->id] = format_string($m->name).' - '.get_string('pluginname', $m->name);
			}
		}

		$mform->addElement('select', 'filter_coursemodule', get_string('coursemodule', 'block_configurable_reports'),
            $coursemodulesoptions);
		$mform->setType('filter_coursemodule', PARAM_INT);

	}

}

