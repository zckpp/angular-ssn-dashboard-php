<?php

require 'DB.php';

$requests = [];
// Get the posted data.
$postData = file_get_contents("php://input");

if(isset($_GET["status"]) && !empty($_GET["status"])) {
    // getting status of the request
    $status = $_GET["status"];
    // getting search terms if exist
    if(isset($postData) && !empty($postData)) {
        $term = $postData;
        // if user try to search both first and last name
        if (strpos($term, ' ') !== false) {
            $array = explode(" ",$term);
            $sql = "SELECT * FROM `invalid_ssn` WHERE `status` LIKE '{$status}' AND (`firstName` LIKE '{$array[0]}%' AND `lastName` LIKE '{$array[1]}%')";
        }
        else {
            $sql = "SELECT * FROM `invalid_ssn` WHERE `status` LIKE '{$status}' AND (`firstName` LIKE '{$term}%' OR `lastName` LIKE '{$term}%')";
        }
    }
    else {
        $sql = "SELECT * FROM `invalid_ssn` WHERE `status` LIKE '{$status}'";
    }
}

try {
    $db = DB::getInstance();
    $stm = $db->prepare($sql);
    $stm->execute();
    $i = 0;
    while($row = $stm->fetch(PDO::FETCH_ASSOC)) {
        $requests[$i]['id']    = $row['id'];
        $requests[$i]['employee_id']    = $row['empID'];
        $requests[$i]['status'] = $row['status'];
        $requests[$i]['first_name'] = $row['firstName'];
        $requests[$i]['last_name'] = $row['lastName'];
        $requests[$i]['department']    = $row['dept'];
        $requests[$i]['last_user'] = $row['lastUser'];
        $requests[$i]['change_date'] = $row['changeDate'];
        $requests[$i]['created_date'] = $row['createdDate'];
        $requests[$i]['hire_date'] = $row['hireDate'];
        $i++;
    }
    echo json_encode($requests);
} catch (Exception $e) {
    error_log("[" . date("Y-m-d h:i:sa") . "]" . $e->getMessage(), 3, "error.log");
    http_response_code(404);
}