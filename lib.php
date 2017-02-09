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

/**
 * Version details
 *
 * Configurable Reports - A Moodle block for creating customizable reports
 *
 * @package     block_configurable_reports
 * @author:     Juan leyva <http://www.twitter.com/jleyvadelgado>
 * @date:       2013-09-07
 *
 * @copyright  Juan leyva <http://www.twitter.com/jleyvadelgado>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * This function extends the course settings navigation block, if in a course
 * and have correct permissions a link to configurable_reports block page
 * will be added.
 */
function block_configurable_reports_extend_navigation_course($navigation, $course, $context) {
    if (!isloggedin()) {
        return;
    }

    if (is_null($navigation) or is_null($context)) {
        return;
    }

    if (has_capability('block/configurable_reports:viewreports', $context)) {
        if ($reports = $navigation->get('coursereports')) {
            $url = new moodle_url('/blocks/configurable_reports/report_list.php', array('id' => $course->id));
            $reports->add(get_string('pluginname', 'block_configurable_reports'), $url,
                navigation_node::TYPE_SETTING, null, null, new pix_icon('i/report', ''));
        }
    }

}
