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
 
defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir.'/formslib.php');

/**
 * The form for handling featured courses.
 */
class analytics_form extends moodleform {

    /**
     * Form definition.
     */
    public function definition() {
        global $CFG,$DB;

		$isData = $DB->count_records('blocks_email_logs',[]);
         
		 
		 
		 
         $mform = $this->_form;
         $tempData = $this->_customdata;
       
         $mform->addElement('hidden', 'id', ($tempData->id) ? $tempData->id : '');  

        $mform->addElement('text', 'access_key', get_string('access_key', 'block_aws_email_analytics'))->setValue(($tempData->access_key) ? $tempData->access_key : '');
		$mform->addRule('access_key', null, 'required', null, 'client');
		
        $mform->addElement('text', 'secret_key', get_string('secret_key', 'block_aws_email_analytics'))->setValue(($tempData->secret_key) ? $tempData->secret_key : '');
		$mform->addRule('secret_key', null, 'required', null, 'client');
		
        $mform->addElement('text', 'region', get_string('region', 'block_aws_email_analytics'))->setValue(($tempData->region) ? $tempData->region : '');
		$mform->addRule('region', null, 'required', null, 'client');
		
        $mform->addElement('text', 'version', get_string('version', 'block_aws_email_analytics'))->setValue(($tempData->version) ? $tempData->version : '');
		$mform->addRule('version', null, 'required', null, 'client');
		
        $mform->addElement('text', 'protocle', get_string('protocole', 'block_aws_email_analytics'))->setValue(($tempData->protocle) ? $tempData->protocle : '');
		$mform->addRule('protocle', null, 'required', null, 'client');
        $mform->addElement('text', 'topic', get_string('topic', 'block_aws_email_analytics'))->setValue(($tempData->topic) ? $tempData->topic : '');
		$mform->addRule('topic', null, 'required', null, 'client');
		
		if($isData > 0)
		{
		$mform->addElement('checkbox', 'removelogs', get_string('removelogs', 'block_aws_email_analytics'));
		}



        $mform->addElement('submit', 'save', get_string('savechanges'));

        $mform->closeHeaderBefore('save');
    }
}
