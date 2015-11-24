Configurable Reports Block

Installation, Documentation, Tutorials....
See http://docs.moodle.org/en/blocks/configurable_reports/
Also http://moodle.org/mod/data/view.php?d=13&rid=4283

Author: Juan Leyva
<http://moodle.org/user/profile.php?id=49568>
<http://twitter.com/jleyvadelgado>
<http://sites.google.com/site/mooconsole/>
<http://moodle-es.blogspot.com>
<http://openlearningtech.blogspot.com>

Thanks to:
Ivan Breziansky for translating the block to slovak language
Iñaki Arenaza for translating the block documentation to spanish
Luis de Vasconcelos for testing the block
Adam Olley and Netspot Moodle Partner for improving some parts of the Moodle2 version

Some parts of this plugin uses code of:

Admin Report: Custom SQL queries
http://moodle.org/mod/data/view.php?d=13&rid=2884
By Tim Hunt

Updates to the original plugin
Developed by Nadav Kavalerchik <nadavkav@gmail.com>
Funded by Meital (A Machba division) - Inter-University Center for e-Learning.
http://meital.iucc.ac.il/meital/English/English.htm

Major updates:

* Reports can run on a different DB then the current (production) DB.
* Reports can run on a CRON scheduler.
* Several filter plugins added (list).
* Several inline "SQL" special variable added (%%USERID%%, %%COURSEID%%, ...)
* Site level report can be shared in all courses.
* New Counterpart "report" plugin that enables a teacher to run reports from Administration->Reports block (no need to add CB block).
* Share reports between Moodle systems - using GITHUB as a repository to distribute and manage sharable SQL queries.
* Control report row limit
* Move report from course level to system level ( add "&adminmode=1" to report setting page's URL and change report's courseid at the bottom of the form)
* %%COURSEID%% can be overridden be using "&courseid=XXX" on the report's URL.
* Hide sub-reports from the list.
* Enable unique aliases to each report, so they can be invoked by other reports persistently across Moodle systems.
* Reports TAGs - enable better report management

New JavaScript libraries
========================
CodeMirror - Display highlighted SQL queries.
DataTables - Display paged reports, enable global search on any field, Sticky headers, control row count display, more...
pChart2 - Modern version of pChart (Which seems deprecated)

New Filters & SQL syntax
========================
%%COURSEID%% and %%USERID%% and %%FILTER_VAR%%

%%DEBUG%% (Add to the first line of the SQL) - Display the fully processed SQL query

And Special filters (if available!):
%%FILTER_SEARCHTEXT:table.field:('=', '<', '>', '<=', '>=', '~')%%
%%FILTER_SEMESTER:table.field%%
%%FILTER_YEARNUMERIC:table.field%%
%%FILTER_YEARHEBREW:table.field%%
%%FILTER_COURSES:mdl_course.id%%
%%FILTER_MYCOURSE:table.field%%
%%FILTER_CATEGORIES:mdl_course.category%%
%%FILTER_SUBCATEGORIES:mdl_course_category.path%%
%%FILTER_FLSUBCATEGORIES:mdl_course_category.path%%
%%FILTER_ROLE:table.field%%
%%FILTER_STARTTIME:l.time:>%% %%FILTER_ENDTIME:l.time:<%% ('<', '>', '<=', '>=', '~')
%%FILTER_COURSEMODULEID%% , %%FILTER_COURSEMODULEFIELDS%% , %%FILTER_COURSEMODULE%%
%%FILTER_USERS:table.field%%
%%FILTER_SYSTEMUSER:table.field%%
%%FILTER_COURSEUSER:table.field%%
%%FILTER_MODULE:mdl_moduels.id%%

== Performance ==
It is advisable to create a new user in the DB with special resource restrictions (http://dev.mysql.com/doc/refman/5.5/en/user-resources.html) to avoid load on the DB when using Moodle's $DB user's connections. Another alternative would be to run the reports on a different DB or an entire new DB server.
