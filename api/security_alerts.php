<?php
// Security Alerts API
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';

try {
    $response = [
        'active_alerts' => [],
        'failed_attempts' => [],
        'unknown_rfid' => [],
        'failed_trend' => []
    ];

    // Get users with multiple failed attempts in last 24 hours
    $stmt = $conn->prepare("
        SELECT u.first_name, u.last_name, u.id_user,
               COUNT(*) as count,
               MAX(l.log_time) as last_attempt
        FROM logs l
        JOIN users u ON l.id_user = u.id_user
        WHERE l.access = 0 
        AND l.log_time >= NOW() - INTERVAL 24 HOUR
        GROUP BY u.id_user
        HAVING count >= 3
        ORDER BY count DESC
        LIMIT 10
    ");
    $stmt->execute();
    $failedAttempts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $response['failed_attempts'] = $failedAttempts;

    // Generate active alerts based on failed attempts
    foreach ($failedAttempts as $attempt) {
        if ($attempt['count'] >= 5) {
            $response['active_alerts'][] = [
                'type' => 'High Risk',
                'message' => "{$attempt['first_name']} {$attempt['last_name']} has {$attempt['count']} failed attempts. Consider reviewing access permissions.",
                'time' => $attempt['last_attempt']
            ];
        } elseif ($attempt['count'] >= 3) {
            $response['active_alerts'][] = [
                'type' => 'Warning',
                'message' => "{$attempt['first_name']} {$attempt['last_name']} has {$attempt['count']} failed attempts in last 24 hours.",
                'time' => $attempt['last_attempt']
            ];
        }
    }

    // Check for unusual activity (many denied accesses in short time for any door)
    $stmt = $conn->prepare("
        SELECT d.door_name, COUNT(*) as count
        FROM logs l
        JOIN doors d ON l.id_door = d.id_door
        WHERE l.access = 0 
        AND l.log_time >= NOW() - INTERVAL 1 HOUR
        GROUP BY l.id_door
        HAVING count >= 5
    ");
    $stmt->execute();
    $doorAlerts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($doorAlerts as $alert) {
        $response['active_alerts'][] = [
            'type' => 'Door Alert',
            'message' => "High number of denied accesses ({$alert['count']}) at {$alert['door_name']} in the last hour.",
            'time' => date('Y-m-d H:i:s')
        ];
    }

    // Unknown RFID attempts (logs without valid user - if tracked)
    // Since we don't have unknown RFIDs logged separately, we'll check for failed attempts pattern
    $stmt = $conn->prepare("
        SELECT l.id_log, l.log_time, u.rfid_code
        FROM logs l
        JOIN users u ON l.id_user = u.id_user
        WHERE l.access = 0
        AND l.log_time >= NOW() - INTERVAL 24 HOUR
        ORDER BY l.log_time DESC
        LIMIT 20
    ");
    $stmt->execute();
    $unknownAttempts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Group by RFID to show pattern
    $rfidGroups = [];
    foreach ($unknownAttempts as $attempt) {
        $rfid = $attempt['rfid_code'];
        if (!isset($rfidGroups[$rfid])) {
            $rfidGroups[$rfid] = [
                'rfid_code' => $rfid,
                'time' => $attempt['log_time'],
                'count' => 0
            ];
        }
        $rfidGroups[$rfid]['count']++;
    }
    $response['unknown_rfid'] = array_values($rfidGroups);

    // Failed attempts trend for last 7 days
    $stmt = $conn->prepare("
        SELECT DATE(log_time) as date, COUNT(*) as count
        FROM logs
        WHERE access = 0
        AND log_time >= NOW() - INTERVAL 7 DAY
        GROUP BY DATE(log_time)
        ORDER BY date ASC
    ");
    $stmt->execute();
    $response['failed_trend'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($response);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
