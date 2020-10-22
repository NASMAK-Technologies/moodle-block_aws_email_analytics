<?php

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
