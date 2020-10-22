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
 
require_once($CFG->dirroot .'/config.php');
require 'vendor/autoload.php';
use Aws\Sns\SnsClient; 
use Aws\Exception\AwsException;


function getEmailList($type)
{
	global $DB;
	
	if($type =="total")
	{
		$sql = "select * from {blocks_email_logs}  ORDER BY id DESC";
	    $result =  $DB->get_records_sql($sql);
	
	}
	if($type =="bounce")
	{
		$sql = "select * from {blocks_email_logs} where notificationtype = 'Bounce' ORDER BY id DESC";
	    $result =  $DB->get_records_sql($sql);
	
	}
		if($type =="deliver")
	{
		$sql = "select * from {blocks_email_logs} where notificationtype = 'Delivery' ORDER BY id DESC";
	    $result =  $DB->get_records_sql($sql);
	}
		if($type =="reject")
	{
		$sql = "select * from {blocks_email_logs} where notificationtype = 'Complaint' ORDER BY id DESC";
	    $result =  $DB->get_records_sql($sql);
	}
	
	
	return $result;
}
function removeLogs()
{
	global $DB;
    $result =  $DB->delete_records('blocks_email_logs', []);
    return $result;
}
function removeOneMonthLogs()
{
	global $DB;
	$today = date('Y-m-d');
	$monthold = date('Y-m-d', strtotime('-30 days'));
	
	$result =  $DB->delete_records('blocks_email_logs', ['date' => $monthold]);
	
   
    return $result;
}

function get_mail_users_listing($sort='lastaccess', $dir='ASC', $page=0, $recordsperpage=0,
                           $search='', $firstinitial='', $lastinitial='', $extraselect='',
                           array $extraparams=null, $extracontext = null , $status) {
    global $DB, $CFG;

    $fullname  = $DB->sql_fullname();

    $select = "deleted <> 1 AND id <> :guestid";
    $params = array('guestid' => $CFG->siteguest);

    if (!empty($search)) {
        $search = trim($search);
        $select .= " AND (". $DB->sql_like($fullname, ':search1', false, false).
                   " OR ". $DB->sql_like('email', ':search2', false, false).
                   " OR username = :search3)";
        $params['search1'] = "%$search%";
        $params['search2'] = "%$search%";
        $params['search3'] = "$search";
    }

    if ($firstinitial) {
        $select .= " AND ". $DB->sql_like('firstname', ':fni', false, false);
        $params['fni'] = "$firstinitial%";
    }
    if ($lastinitial) {
        $select .= " AND ". $DB->sql_like('lastname', ':lni', false, false);
        $params['lni'] = "$lastinitial%";
    }

    if ($extraselect) {
        $select .= " AND $extraselect";
        $params = $params + (array)$extraparams;
    }

    if ($sort) {
        $sort = " ORDER BY $sort DESC";
    }
	
	// print_r($sort);
	// exit;

    // If a context is specified, get extra user fields that the current user
    // is supposed to see.
    $extrafields = '';
    if ($extracontext) {
        $extrafields = get_extra_user_fields_sql($extracontext, '', '',
                array('id', 'username', 'email', 'firstname', 'lastname', 'city', 'country',
                'lastaccess', 'confirmed', 'mnethostid'));
    }
    $namefields = get_all_user_name_fields(true);
    $extrafields = "$extrafields,$namefields";
	$extrafields = str_replace(",",",u.",$extrafields);
	
	$select = str_replace(" id"," u.id",$select);
	$select = str_replace("firstname","u.firstname",$select);
	$select = str_replace("lastname","u.lastname",$select);
	//$sort = str_replace("firstname","u.firstname",$sort);
	
	//$sort = "ORDER BY e.id DESC";
    if(!empty($status))
	{   
	$select .= " And notificationtype = '$status'";
	}
	//print_r($status);
	//exit;
	

	// $data = $DB->get_records_sql("SELECT e.id , e.notificationtype , e.destination , u.firstname FROM mdl_blocks_email_logs as e LEFT OUTER JOIN mdl_user as u ON e.user_id = u.id WHERE u.id = 3
// ");							  
    // warning: will return UNCONFIRMED USERS
    $data =  $DB->get_records_sql("SELECT e.id, u.username, u.email, u.city, u.country, u.lastaccess, u.confirmed, u.mnethostid, u.suspended $extrafields ,e.notificationtype,e.timestamp
				   FROM mdl_blocks_email_logs as e
				   LEFT OUTER JOIN  mdl_user as u    
				   ON e.user_id = u.id   
                               	 WHERE  $select
	  $sort", $params, $page, $recordsperpage );   
	
	// echo"<pre>";
	// print_r($data);
	// echo"</pre>";
	// exit;		
								  
		return $data;
		
		
}


 function subcriptionRequest($region,$access_key,$secret_key,$version,$protocol,$topic)
 {
	 global $CFG;
	 
         
			$SnSclient = new SnsClient([
				//'profile' => 'default',
				'region' => $region,
				'credentials' => [
					'key'    => $access_key,
					'secret' => $secret_key,
				],
				'version' => $version
			]);
			
			

			$endpoint = $CFG->wwwroot .'/blocks/aws_email_analytics/listen.php';
			
			
			try {
				$result = $SnSclient->subscribe([
					'Protocol' => $protocol,
					'Endpoint' => $endpoint,
					'ReturnSubscriptionArn' => true,
					'TopicArn' => $topic,
				]);
				
				return true;
			} catch (AwsException $e) {
                return false;
				//error_log($e->getMessage());
			} 

 }
 
 function checkSubscription($region,$access_key,$secret_key,$version)
 {
			$SnSclient = new SnsClient([
				//'profile' => 'default',
				'region' => $region,
				'credentials' => [
					'key'    => $access_key,
					'secret' => $secret_key,
				],
				'version' => $version
			]);
			
				

			try {
				$result = $SnSclient->listSubscriptions([
				]);
				// echo"<pre>";
				// var_dump($result);
				// echo"</pre>";
				// exit;
			} catch (AwsException $e) {
				// output error message if fails
				error_log($e->getMessage());
			}
 }


?>