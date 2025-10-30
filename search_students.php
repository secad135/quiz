<?php
require 'db.php';

$term = $_GET['term'] ?? '';
$year = $_GET['year'] ?? '';

$sql = "SELECT id, full_name, academic_year 
        FROM students 
        WHERE full_name LIKE ?";

$params = ["%$term%"];

if (!empty($year)) {
    $sql .= " AND academic_year = ?";
    $params[] = $year;
}

$stmt = $conn->prepare($sql);

$types = str_repeat('s', count($params));
if (!empty($year)) $types = "si"; // رشته + عدد

$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while($row = $result->fetch_assoc()) {
    $data[] = ['id' => $row['id'], 'text' => $row['full_name'] . " - سال " . $row['academic_year']];
}

echo json_encode($data);
