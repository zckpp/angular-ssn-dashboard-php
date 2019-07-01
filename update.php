<?php
require 'DB.php';

// Get the posted data.
$postData = file_get_contents("php://input");
date_default_timezone_set('UTC');

if(isset($postData) && !empty($postData)) {
    // Extract the data.
    $request = json_decode($postData);

    // Validate.
    if ((int)$request->id < 1) {
        return http_response_code(400);
    }
    $id = $request->id;

    if(isset($_GET["action"]) && !empty($_GET["action"])) {
        if ('status' == $_GET["action"]) {
            // Sanitize.
            $status = $request->status;
            $date = date('Y-m-d H:i:s');
            if(isset($_COOKIE['cookie_name'])) {
                $user = $_COOKIE['cookie_name'];
            }

            // Update request status
            $sql = "UPDATE `invalid_ssn` SET `status`='$status',`changeDate`='$date',`lastUser`='$user' WHERE `id` = '{$id}' LIMIT 1";
        } elseif ('note' == $_GET["action"]) {
            $note = htmlspecialchars($request->note);
            // Update request note
            $sql = "UPDATE `invalid_ssn` SET `note`='$note' WHERE `id` = '{$id}' LIMIT 1";
        }

        try {
            $db = DB::getInstance();
            $stm = $db->prepare($sql);
            $stm->execute();
            $column = [
                'status' => 'succeed',
            ];
            echo json_encode($column);
        } catch (Exception $e) {
            error_log("[" . date("Y-m-d h:i:sa") . "]" . $e->getMessage(), 3, "error.log");
            $column = [
                'status' => 'failed',
            ];
            echo json_encode($column);
        }
    }
}
?>