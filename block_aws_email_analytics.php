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
 
defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir. '/coursecatlib.php');
require_once($CFG->libdir. '/moodlelib.php');
require_once($CFG->dirroot. '/course/renderer.php');
require 'vendor/autoload.php';

use Aws\Sns\SnsClient; 
use Aws\Exception\AwsException;

class block_aws_email_analytics extends block_base {
	

    public function init() {
		

        $this->title = get_string('pluginname', 'block_aws_email_analytics');
    }

    public function get_content() {
		global $CFG;
		require_once($CFG->dirroot. '/blocks/aws_email_analytics/lib.php');
		
		global $DB , $CFG;
    if ($this->content !== null) {
      return $this->content;
    }
	?>
    <style>
	.analytic-img-set {
		width:12%;
		} 
	.analytic-abox {
		color:#212121;
		text-decoration: none !important;
	}
	.analytic-counter-s {
		font-size:38px;
	} 
	.analytic-head-p {
		margin-bottom:-1px;
		font-size: 15px;
		   margin-top: -5%;
	}
	 .analytic-config-message {
		 text-align: center;
    font-size: 15px;
	 }
    </style>
 <?php
 
    $this->content  =  new stdClass;
    
	$check = $DB->get_record('blocks_analytics',[]);
	
	if(isset($check->subscription_status) && $check->subscription_status == "success")
	{
		  
      // removeOneMonthLogs();		  
    
    $total = $DB->count_records('blocks_email_logs',[]);
    $bounce = $DB->count_records('blocks_email_logs',['notificationtype'=>'Bounce']);
    $deliver= $DB->count_records('blocks_email_logs',['notificationtype'=>'Delivery']);
    $complaint = $DB->count_records('blocks_email_logs',['notificationtype'=>'Complaint']);
    
     $this->content->text   = '
	 <div class="row text-center" >
    <div class="col-md-3">   
	<p><img class="analytic-img-set" src="'.$CFG->wwwroot .'/blocks/aws_email_analytics/icons/email_plugin.svg" /> </p>
	<a class="analytic-abox"  href="/blocks/aws_email_analytics/list.php" >
	<span class="analytic-counter-s">'.$total.'</span>
	<p class="analytic-head-p">Total Attempts</p>  </a>
	</div>
	
    <div class="col-md-3">
	<p><img class="analytic-img-set" src="'.$CFG->wwwroot .'/blocks/aws_email_analytics/icons/delivered_plugin.svg" /> </p>
	<a class="analytic-abox" href="/blocks/aws_email_analytics/list.php?type=Delivery" > 
	<span  class="analytic-counter-s">'.$deliver.'</span>
	<p class="analytic-head-p"> Delivered</p>
	</a></div>
	
	<div class="col-md-3">
	<p><img class="analytic-img-set" src="'.$CFG->wwwroot .'/blocks/aws_email_analytics/icons/bounce_plugin.svg" /> </p>
	<a class="analytic-abox" href="/blocks/aws_email_analytics/list.php?type=Bounce" > 
	<span  class="analytic-counter-s">'.$bounce.'</span>
	<p class="analytic-head-p"> Bounced</p>
	</a></div>
	
	<div class="col-md-3">
	<p><img class="analytic-img-set" src="'.$CFG->wwwroot .'/blocks/aws_email_analytics/icons/rejected_plugin.svg" /> </p>
	<a class="analytic-abox" href="/blocks/aws_email_analytics/list.php?type=Complaint" > 
	<span  class="analytic-counter-s">'.$complaint.'</span>
	<p class="analytic-head-p"> Rejected</p>
	</a></div>
	
	
     </div>';
	}else{
		
		 $this->content->text = "<div class='analytic-config-message'> Please <a href='".$CFG->wwwroot."/blocks/aws_email_analytics/analytics.php'>Click Here </a> to complete  configration </div> ";
	}
	 
 
    return $this->content;
}

    public function applicable_formats() {
        return array('all' => true);
    }

    public function instance_allow_multiple() {
          return false;
    }

    public function has_config() {
        return false;
    }

    public function cron() {
        return true;
    }

   
}
?>
  
