<?php
header('Content-Type: application/json');
session_start();
include_once $_SERVER['DOCUMENT_ROOT'] . "/config/config.php";

// Check if user is logged in
if (!isset($_SESSION['id_admin'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$response = [];

// Get total users count (excluding unknown user id=4)
$result = $con->query("SELECT COUNT(*) as total FROM user WHERE id_user != 4");
$response['total_users'] = $result->fetch_assoc()['total'];

// Get total doors count
$result = $con->query("SELECT COUNT(*) as total FROM door");
$response['total_doors'] = $result ? $result->fetch_assoc()['total'] : 0;

// Get total logs count
$result = $con->query("SELECT COUNT(*) as total FROM access_logs");
$response['total_logs'] = $result ? $result->fetch_assoc()['total'] : 0;

// Get today's date
$today = date('Y-m-d');

// Get today's access count
$result = $con->query("SELECT COUNT(*) as total FROM access_logs WHERE DATE(log_date) = '$today'");
$response['today_access'] = $result->fetch_assoc()['total'];

// Get granted today
$result = $con->query("SELECT COUNT(*) as total FROM access_logs WHERE DATE(log_date) = '$today' AND status = 'Access Granted'");
$response['granted_today'] = $result->fetch_assoc()['total'];

// Get denied today
$result = $con->query("SELECT COUNT(*) as total FROM access_logs WHERE DATE(log_date) = '$today' AND status = 'Access Denied'");
$response['denied_today'] = $result->fetch_assoc()['total'];

// Get this week's granted/denied counts
$weekAgo = date('Y-m-d', strtotime('-7 days'));
$result = $con->query("SELECT COUNT(*) as total FROM access_logs WHERE DATE(log_date) >= '$weekAgo' AND status = 'Access Granted'");
$response['granted_week'] = $result->fetch_assoc()['total'];

$result = $con->query("SELECT COUNT(*) as total FROM access_logs WHERE DATE(log_date) >= '$weekAgo' AND status = 'Access Denied'");
$response['denied_week'] = $result->fetch_assoc()['total'];

// Get hourly data for today
$hourlyData = [];
$result = $con->query("
    SELECT 
        HOUR(log_date) as hour,
        SUM(CASE WHEN status = 'Access Granted' THEN 1 ELSE 0 END) as granted,
        SUM(CASE WHEN status = 'Access Denied' THEN 1 ELSE 0 END) as denied
    FROM access_logs 
    WHERE DATE(log_date) = '$today'
    GROUP BY HOUR(log_date)
    ORDER BY hour
");
while ($row = $result->fetch_assoc()) {
    $hourlyData[] = $row;
}
$response['hourly_data'] = $hourlyData;

// Get weekly data (last 7 days)
$weeklyData = [];
$result = $con->query("
    SELECT 
        DATE(log_date) as date,
        COUNT(*) as total
    FROM access_logs 
    WHERE DATE(log_date) >= '$weekAgo'
    GROUP BY DATE(log_date)
    ORDER BY date
");
while ($row = $result->fetch_assoc()) {
    $weeklyData[] = $row;
}
$response['weekly_data'] = $weeklyData;

// Get recent logs (last 10)
$recentLogs = [];
$result = $con->query("
    SELECT 
        al.log_date,
        al.status,
        al.used_rfid_code,
        u.first_name,
        u.last_name
    FROM access_logs al
    JOIN user u ON al.user_id_log = u.id_user
    ORDER BY al.log_date DESC
    LIMIT 10
");
while ($row = $result->fetch_assoc()) {
    $row['door_name'] = 'Main Entrance'; // Default door name since schema doesn't include door in logs
    $row['access'] = ($row['status'] == 'Access Granted') ? 1 : 0;
    $row['log_time'] = $row['log_date'];
    $recentLogs[] = $row;
}
$response['recent_logs'] = $recentLogs;

// Security alerts - Check for multiple failed attempts
$securityAlerts = [];
$result = $con->query("
    SELECT 
        u.first_name,
        u.last_name,
        COUNT(*) as failed_attempts,
        MAX(al.log_date) as last_attempt
    FROM access_logs al
    JOIN user u ON al.user_id_log = u.id_user
    WHERE al.status = 'Access Denied' 
    AND al.log_date >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
    GROUP BY al.user_id_log
    HAVING failed_attempts >= 3
");
while ($row = $result->fetch_assoc()) {
    $securityAlerts[] = [
        'message' => $row['first_name'] . ' ' . $row['last_name'] . ' has ' . $row['failed_attempts'] . ' failed access attempts in the last hour!',
        'time' => $row['last_attempt']
    ];
}

// Check for unknown RFID attempts
$result = $con->query("
    SELECT COUNT(*) as unknown_attempts, MAX(log_date) as last_attempt
    FROM access_logs 
    WHERE user_id_log = 4 AND DATE(log_date) = '$today'
");
$unknownRow = $result->fetch_assoc();
if ($unknownRow['unknown_attempts'] > 0) {
    $securityAlerts[] = [
        'message' => $unknownRow['unknown_attempts'] . ' unknown RFID card attempt(s) detected today!',
        'time' => $unknownRow['last_attempt']
    ];
}

$response['security_alerts'] = $securityAlerts;

echo json_encode($response);
?>
