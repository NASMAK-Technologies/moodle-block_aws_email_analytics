<?php
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
 

$entityBody = file_get_contents('php://input');
$data = json_decode($entityBody);
	 	    
if(isset($entityBody))
{
	
	//connection
   $conn = setConnection();
   

	//subscription confermation
	if(isset($data->Type)  && $data->Type == "SubscriptionConfirmation")
	{
	   $subsurl = $data->SubscribeURL;
	   
			$result = confirmSubscription($subsurl);
			if($result == "success")
			{
				$sql = "UPDATE mdl_blocks_analytics SET subscription_status = 'success' WHERE id=1";

				if ($conn->query($sql) === TRUE) {
				  echo "success";
				}
			}
             	


	} 

   //NOTIFICATION LOG'S

  
   if(isset($data->Type) && $data->Type =="Notification")
   {
     $subData = json_decode($data->Message);
    
	
	$type = $subData->notificationType;
	$timestamp =  $subData->mail->timestamp;
	$source =   $subData->mail->source;
	$sourceip =  $subData->mail->sourceIp;
	$destination =  $subData->mail->destination[0];
    $user_id = getUser($subData->mail->destination[0]);
	$today = date('Y-m-d');
	
	//$subject =   "dame message" ;
	  
	// $result = $DB->insert_record('block_analytics', $myData);
	$sql = "INSERT INTO mdl_blocks_email_logs (notificationtype, timestamp, source,sourceip,destination,user_id,date)
          VALUES ('$type', '$timestamp', '$source','$sourceip','$destination','$user_id','$today')";
		  
	if ($conn->query($sql) === TRUE) {
		echo"success";
	   }else{
		   print_r($conn->error);
		   exit;
	   }
    }
   
	

}
	 function confirmSubscription($sbsurl)
	 {
		 $curl = curl_init();

			curl_setopt_array($curl, array(
			  CURLOPT_URL => "$sbsurl",
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 0,
			  CURLOPT_FOLLOWLOCATION => true,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "POST",
			  CURLOPT_HTTPHEADER => array(
				"x-amz-sns-message-type: SubscriptionConfirmation"
			  ),
			));

			$response = curl_exec($curl);
		   
			curl_close($curl);
			  
			  if(!strpos($response,'ErrorResponse'))
			  {
				 return "success";
				  
			  }else{
				  return "error";
			  }
	 }

 function getUser($email)
 { 
 
   $data="";
   $conn = setConnection();
   $sql ="select id from mdl_user where email = '$email'";
   
   if($result = mysqli_query($conn,$sql))
   {
  $data = mysqli_fetch_assoc($result);
   return $data['id'];
   }else{
	   return 0;
   } 
   
 }


function setConnection()
{ 
    $file = file_get_contents('../../config.php');
	 $array = explode(";",$file);
	 $dbhost="";
	 $dbname="";
	 $dbuser="";
	 $dbpass="";
	 for($i = 1; $i< count($array); $i++)
		 {
			 $fullstring = $array[$i];
			 if(strpos($fullstring,'$CFG->dbhost') !== false)
				 {
			       $dbhost = get_string_between($fullstring, "'","'");
				 }
				 if(strpos($fullstring,'$CFG->dbname') !== false){
				 
			       $dbname = get_string_between($fullstring, "'","'");
				 }
				 if(strpos($fullstring,'$CFG->dbuser') !== false)
				 {
			       $dbuser = get_string_between($fullstring, "'","'");
				 }
				 if(strpos($fullstring,'$CFG->dbpass') !== false)
				 {
			       $dbpass = get_string_between($fullstring, "'","'");
				 }
		 }
	
	$servername = $dbhost;
	$username = $dbuser;
	$password = $dbpass;
	$dbname = $dbname;
	$conn = new mysqli($servername, $username, $password,$dbname);
	if ($conn->connect_error) {
	  die("Connection failed: " . $conn->connect_error);
	}
	
	return $conn;
}
function get_string_between($string, $start, $end){
		$string = $string;
		$ini = strpos($string, $start);
		if ($ini == 0) return '';
		$ini += strlen($start);
		$len = strpos($string, $end, $ini) - $ini;
		return substr($string, $ini, $len);
	}

?>