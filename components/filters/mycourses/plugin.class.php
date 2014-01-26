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

class plugin_mycourses extends plugin_base {

	function init() {
		$this->form = false;
		$this->unique = true;
		$this->fullname = get_string('filtermycourses','block_configurable_reports');
		$this->reporttypes = array('courses','sql');
	}

	function summary($data) {
		return get_string('filtermycourses_summary','block_configurable_reports');
	}

	function execute($finalelements, $data) {

		$filter_mycourses = optional_param('filter_mycourses', 0, PARAM_INT);
		if (!$filter_mycourses)
			return $finalelements;

		if ($this->report->type != 'sql') {
            return array($filter_mycourses);
		} else {
			if(preg_match("/%%FILTER_MYCOURSES:([^%]+)%%/i", $finalelements, $output)){
				$replace = ' AND '.$output[1].' = '.$filter_mycourses;
				return str_replace('%%FILTER_MYCOURSES:'.$output[1].'%%', $replace, $finalelements);
			}
		}
		return $finalelements;
	}

	function print_filter(&$mform) {
		global $remoteDB, $USER;

		$filter_mycourses = optional_param('filter_mycourses', 0, PARAM_INT);

		$reportclassname = 'report_'.$this->report->type;
		$reportclass = new $reportclassname($this->report);

		if ($this->report->type != 'sql') {
			$components = cr_unserialize($this->report->components);
			$conditions = $components['conditions'];

			$courselist = $reportclass->elements_by_conditions($conditions);
		} else {
            $sql_mycourses = "SELECT context.instanceid AS courseid
                    FROM mdl_role_assignments AS ra
                    JOIN mdl_context AS context ON ra.contextid = context.id
                    AND context.contextlevel = 50
                    WHERE ra.userid = {$USER->id}";
			$mycourselist = array_keys($remoteDB->get_records_sql($sql_mycourses));
		}

		$courseoptions = array();
		$courseoptions[0] = get_string('filter_all', 'block_configurable_reports');

		if (!empty($mycourselist)) {
			list($usql, $params) = $remoteDB->get_in_or_equal($mycourselist);
			$mycourses = $remoteDB->get_records_select('course', "id $usql", $params);

			foreach($mycourses as $c) {
				$mycourseoptions[$c->id] = format_string($c->fullname);
			}
		}

		$mform->addElement('select', 'filter_mycourses', get_string('course'), $mycourseoptions);
		$mform->setType('filter_mycourses', PARAM_INT);

	}

}

