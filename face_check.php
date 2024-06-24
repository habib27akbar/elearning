<?php
session_start();
include 'config/db.php';

$data = json_decode(file_get_contents('php://input'), true);
$faceData = $data['faceData'];
$level = $data['level'];
$userId = $data['userId'];

// Insert face data into the face_checking table
$sql = "INSERT INTO face_checking (data_id, level, face_data, check_date)
        VALUES ('$userId', '$level', '$faceData', NOW())";

if ($con->query($sql) === TRUE) {
    echo json_encode(['success' => true, 'message' => 'Face check successful']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $con->error]);
}

$con->close();
?>
