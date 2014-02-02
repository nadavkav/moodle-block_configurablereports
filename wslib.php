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
 * A Moodle block for creating Configurable Reports
 * @package blocks
 * @author: Juan leyva <http://www.twitter.com/jleyvadelgado>
 * @date: 2009
 */

require_once("../../config.php");

echo "Max time for currently running query = ".get_mysqlserver_longestrunningquerytime();

//echo "MySQL server load = ". get_mysqlserver_loaded();


function get_mysqlserver_loaded(){
    global $CFG, $COURSE, $PAGE;

    // Use a custom $remoteDB (and not current system's $DB)
    // todo: major security issue
    $remoteDBhost = get_config('block_configurable_reports', 'dbhost');
    if (empty($remoteDBhost)) {
        $remoteDBhost = $CFG->dbhost;
    }
    $remoteDBuser = get_config('block_configurable_reports', 'dbuser');
    if (empty($remoteDBuser)) {
        $remoteDBuser = $CFG->dbuser;
    }
    $remoteDBpass = get_config('block_configurable_reports', 'dbpass');
    if (empty($remoteDBpass)) {
        $remoteDBpass = $CFG->dbpass;
    }

    // Test for MYSQL server load before running query.
    // TODO: using direct php mysql calls, should be an issue with other DBs
    $link = mysqli_connect($remoteDBhost, $remoteDBuser, $remoteDBpass);
    //$status = explode('  ', mysql_stat($link));

    // Innodb_row_lock_current_waits
    //$sql_dbload = "SHOW GLOBAL STATUS WHERE Variable_name = 'Connections'";
    $sql_dbload = "SHOW GLOBAL STATUS WHERE Variable_name = 'Threads_running'";
    //$sql_dbload = "SHOW GLOBAL STATUS WHERE Variable_name = 'Threads_connected'";
    //$sql_dbload = "SHOW GLOBAL STATUS WHERE Variable_name = 'Innodb_row_lock_current_waits'";

    //$serverload = get_config('block_configurable_reports', 'serverload');

    $result = $link->query($sql_dbload);
    $row = mysqli_fetch_array($result);
    //echo $row['Variable_name'] . ' = ' . $row['Value'] . "\n";
    return $row['Value'];
    /*
        if ( $row['Value'] > $serverload ) {
            $PAGE->set_pagelayout('incourse');
            notice(get_string('serverloadnotice', 'block_configurable_reports', $row['Value']),
                "$CFG->wwwroot/course/view.php?id={$COURSE->id}");
        }

        return false;
    */
}

function get_mysqlserver_longestrunningquerytime(){
    global $CFG, $COURSE, $PAGE;

    // Use a custom $remoteDB (and not current system's $DB)
    // todo: major security issue
    $remoteDBhost = get_config('block_configurable_reports', 'dbhost');
    if (empty($remoteDBhost)) {
        $remoteDBhost = $CFG->dbhost;
    }
    $remoteDBuser = get_config('block_configurable_reports', 'dbuser');
    if (empty($remoteDBuser)) {
        $remoteDBuser = $CFG->dbuser;
    }
    $remoteDBpass = get_config('block_configurable_reports', 'dbpass');
    if (empty($remoteDBpass)) {
        $remoteDBpass = $CFG->dbpass;
    }

    // Test for MYSQL server load before running query.
    // TODO: using direct php mysql calls, should be an issue with other DBs
    $link = mysqli_connect($remoteDBhost, $remoteDBuser, $remoteDBpass);
    //$status = explode('  ', mysql_stat($link));

    //$sql_dbload = "SHOW PROCESSLIST WHERE Command = 'Query' ORDER BY Time DESC";
    $sql_dbload = "SHOW PROCESSLIST";

    //$serverload = get_config('block_configurable_reports', 'serverload');

    $result = $link->query($sql_dbload);
    $maxtime = 0;
    while ($row = mysqli_fetch_array($result)) {
        if ($row['Command'] == 'Query' AND $row['Time'] > 0)
            if ($maxtime < $row['Time']) $maxtime = $row['Time'];
        //print_object($row);
    }
    return $maxtime;
}