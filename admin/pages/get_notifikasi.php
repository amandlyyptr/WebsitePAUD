<?php
include '../../includes/db.php';

$query = mysqli_query($conn, "
SELECT 
    admin, 
    aktivitas, 
    waktu 
FROM statistik 
ORDER BY waktu DESC 
LIMIT 5
");

$data = [];

while($row = mysqli_fetch_assoc($query)){
    $data[] = $row;
}

echo json_encode($data);
?>
