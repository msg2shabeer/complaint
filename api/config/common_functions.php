<?php
include_once 'db.php';
// Cleaning Input
function cleanInput($input) {
    $search = array(
    '@<script[^>]*?>.*?</script>@si',   // Strip out javascript
    '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
    '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
    '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
    );

    $output = preg_replace($search, '', $input);
    return $output;
}

// Sanitizing data
function sanitize($input) {
    if (is_array($input)) {
        foreach($input as $var=>$val) {
            $output[$var] = sanitize($val);
        }
    }
    else {
        if (get_magic_quotes_gpc()) {
            $input = stripslashes($input);
        }
        $output  = cleanInput($input);
        // $output = mysql_real_escape_string($input);
    }
    return $output;
}

// Checking for any open complaints
function any_open_complaint($customer_id){
    $connection1 = new PDO("mysql:dbname=complaints;host=localhost","root","");
    $connection1->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    $connection1->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
    $complaint1 = new NotORM($connection1);
    $c_complaints = array();
    $matchingComplaint = $complaint1->complaints()->where("customer_id = ? AND (status_id = ? OR status_id = ?)", $customer_id,1,2);
    foreach ($matchingComplaint as $c_complaint) {
        $c_complaints[]       =   array(
                'id'        =>  $c_complaint['id'],
            );
    }
    if (sizeof($c_complaints) > 0) {
        return $c_complaints[0]['id'];
    }
    else{
        return False;
    }
}

// Increment no of calls
function increment_no_calls($complaint_id){
    $connection1 = new PDO("mysql:dbname=complaints;host=localhost","root","");
    $connection1->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    $connection1->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
    $complaint1 = new NotORM($connection1);
    $no_calls = $complaint1->complaints[$complaint_id]['no_calls'];
    if ($no_calls) {
        $no_calls = $no_calls[0] + 1;
        $val = array("no_calls"=>$no_calls);
        $complaint1->complaints[$complaint_id]->update($val);
        $result                 =   array('id' => $complaint_id,'job' => 'update');
    }
    else{
        $result                 =   array('id' => 0,'job' => 'cancelled');
    }
    return $result;
}

// Get complaint priority
function get_complaint_priority($no_of_calls,$complaint_reg_time){
    if ($no_of_calls > 5) {
        $no_of_calls = 5;
    }
    
    // get hour difference from complaint reg time and now
    $current_time = time();
    $hours = round(($current_time - strtotime($complaint_reg_time))/3600, 1);
    if ($hours > 5) {
        $hours = 5;
    }

    $priority = $no_of_calls + ($hours / 360 * 5);
    return $priority;
}
?>