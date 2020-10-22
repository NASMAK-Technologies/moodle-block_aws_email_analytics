<?php

defined('MOODLE_INTERNAL') || die();

class block_aws_email_analytics_edit_form extends block_edit_form {

    protected function specific_definition($mform) {
        global $CFG;

        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));
        $mform->addElement('static', 'link',
                           get_string('editlink', 'block_aws_email_analytics',
                                      $CFG->wwwroot.'/blocks/aws_email_analytics/analytics.php'));
    }
}
