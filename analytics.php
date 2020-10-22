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
 * This file contains the Aws Email Analytics block.
 *
 * @package    block_aws_email_analytics
 * @copyright  2018 onwards Nasmak Technologies
 (https://www.nasmak.com.au/)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
require_once('../../config.php');
require_once($CFG->libdir.'/coursecatlib.php');
require_once($CFG->dirroot.'/blocks/moodleblock.class.php');
require_once($CFG->dirroot.'/blocks/aws_email_analytics/block_aws_email_analytics.php');
require_once($CFG->dirroot.'/blocks/aws_email_analytics/analytics_form.php');
require_once($CFG->dirroot.'/blocks/aws_email_analytics/lib.php');

require_login();

$systemcontext = context_system::instance();

 require_capability('block/aws_email_analytics:addinstance', $systemcontext);

$PAGE->set_pagelayout('admin');
$PAGE->set_url('/blocks/aws_email_analytics/analytics.php');
$PAGE->set_context($systemcontext);

// $args = array(
    // 'availablecourses' => coursecat::get(0)->get_courses(array('recursive' => true)),
    // 'analytics' => block_analytics::get_featured_courses()
// );

$exist = $DB->get_record('blocks_analytics',array());


$editform = new analytics_form(null, $exist);


if ($editform->is_cancelled()) {
    redirect($CFG->wwwroot.'/?redirect=0');
} else if ($data = $editform->get_data()) {



   

    if(!isset($exist) || empty($exist))
    {
		
		$DB->insert_record('blocks_analytics', $data);
		
		$result = subcriptionRequest($data->region,$data->access_key,$data->secret_key,$data->version,$data->protocle,$data->topic);
		

    }else{
		 if($data->removelogs == 1)
		 {
		  removeLogs();
		 }
        $DB->update_record('blocks_analytics', $data);
		
		$result = subcriptionRequest($data->region,$data->access_key,$data->secret_key,$data->version,$data->protocle,$data->topic);
		
    }
    
    $url = $CFG->wwwroot .'/blocks/aws_email_analytics/analytics.php';
  redirect($url, 'Request sent successfully to aws.',5, \core\output\notification::NOTIFY_SUCCESS);
}

$site = get_site();

$PAGE->set_title(get_string('editpagetitle', 'block_aws_email_analytics'));
$PAGE->set_heading($site->fullname . ' - ' .  get_string('pluginname', 'block_aws_email_analytics'));

echo $OUTPUT->header(),

     $OUTPUT->heading(get_string('editpagedesc', 'block_aws_email_analytics')),

     $editform->render(),

     $OUTPUT->footer();
