<?php
// Ensure session_start() is called before any output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/elements/head.php' ?>
    </head>
    <body class="sb-nav-fixed">
        <?php
        if (isset($_SESSION['id_admin'])) {
            include $_SERVER['DOCUMENT_ROOT'] . '/elements/nav.php';
            include $_SERVER['DOCUMENT_ROOT'] . '/elements/sidebar.php';
        ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">
                        <i class="fas fa-exclamation-triangle text-danger me-2"></i>Security Alerts
                    </h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item"><a href="/index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Security Alerts</li>
                    </ol>

                    <!-- Current Alerts -->
                    <div class="card mb-4 shadow">
                        <div class="card-header bg-danger text-white">
                            <i class="fas fa-bell me-1"></i>
                            Active Security Alerts
                        </div>
                        <div class="card-body" id="activeAlerts">
                            <div class="text-center">
                                <i class="fas fa-spinner fa-spin fa-2x"></i>
                                <p>Loading alerts...</p>
                            </div>
                        </div>
                    </div>

                    <!-- Failed Attempts Summary -->
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="card mb-4 shadow">
                                <div class="card-header">
                                    <i class="fas fa-user-times me-1"></i>
                                    Failed Access Attempts (Last 24 Hours)
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>User</th>
                                                    <th>Failed Attempts</th>
                                                    <th>Last Attempt</th>
                                                </tr>
                                            </thead>
                                            <tbody id="failedAttemptsBody">
                                                <!-- Data loaded via JS -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="card mb-4 shadow">
                                <div class="card-header">
                                    <i class="fas fa-id-card me-1"></i>
                                    Unknown RFID Card Attempts
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>RFID Code</th>
                                                    <th>Time</th>
                                                    <th>Attempts</th>
                                                </tr>
                                            </thead>
                                            <tbody id="unknownRfidBody">
                                                <!-- Data loaded via JS -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Security Statistics -->
                    <div class="row">
                        <div class="col-xl-8">
                            <div class="card mb-4 shadow">
                                <div class="card-header">
                                    <i class="fas fa-chart-line me-1"></i>
                                    Failed Attempts Trend (Last 7 Days)
                                </div>
                                <div class="card-body">
                                    <canvas id="failedTrendChart" width="100%" height="40"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4">
                            <div class="card mb-4 shadow">
                                <div class="card-header">
                                    <i class="fas fa-shield-alt me-1"></i>
                                    Security Status
                                </div>
                                <div class="card-body text-center" id="securityStatus">
                                    <i class="fas fa-spinner fa-spin fa-3x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            <?php include $_SERVER['DOCUMENT_ROOT'] . '/elements/foot.php'; ?>
        </div>
        <?php
        } else {
            include $_SERVER['DOCUMENT_ROOT'] . '/elements/authentification.php';
        }
        ?>
        <?php include $_SERVER['DOCUMENT_ROOT'] . '/elements/scripts.php'; ?>
        
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
        async function loadSecurityData() {
            try {
                const response = await fetch('/api/security_alerts.php');
                const data = await response.json();
                
                // Active Alerts
                const alertsDiv = document.getElementById('activeAlerts');
                if (data.active_alerts && data.active_alerts.length > 0) {
                    alertsDiv.innerHTML = data.active_alerts.map(alert => `
                        <div class="alert alert-danger d-flex align-items-center" role="alert">
                            <i class="fas fa-exclamation-triangle me-2 fa-lg"></i>
                            <div>
                                <strong>${alert.type}:</strong> ${alert.message}
                                <br><small class="text-muted">${alert.time}</small>
                            </div>
                        </div>
                    `).join('');
                } else {
                    alertsDiv.innerHTML = `
                        <div class="alert alert-success d-flex align-items-center" role="alert">
                            <i class="fas fa-check-circle me-2 fa-lg"></i>
                            <div>No active security alerts. System is secure.</div>
                        </div>
                    `;
                }
                
                // Failed Attempts Table
                const failedBody = document.getElementById('failedAttemptsBody');
                if (data.failed_attempts && data.failed_attempts.length > 0) {
                    failedBody.innerHTML = data.failed_attempts.map(row => `
                        <tr>
                            <td>${row.first_name} ${row.last_name}</td>
                            <td><span class="badge bg-danger">${row.count}</span></td>
                            <td>${row.last_attempt}</td>
                        </tr>
                    `).join('');
                } else {
                    failedBody.innerHTML = '<tr><td colspan="3" class="text-center">No failed attempts</td></tr>';
                }
                
                // Unknown RFID Table
                const unknownBody = document.getElementById('unknownRfidBody');
                if (data.unknown_rfid && data.unknown_rfid.length > 0) {
                    unknownBody.innerHTML = data.unknown_rfid.map(row => `
                        <tr>
                            <td><code>${row.rfid_code}</code></td>
                            <td>${row.time}</td>
                            <td><span class="badge bg-warning text-dark">${row.count}</span></td>
                        </tr>
                    `).join('');
                } else {
                    unknownBody.innerHTML = '<tr><td colspan="3" class="text-center">No unknown RFID attempts</td></tr>';
                }
                
                // Security Status
                const statusDiv = document.getElementById('securityStatus');
                const alertCount = (data.active_alerts || []).length;
                if (alertCount === 0) {
                    statusDiv.innerHTML = `
                        <i class="fas fa-shield-alt text-success fa-5x mb-3"></i>
                        <h3 class="text-success">SECURE</h3>
                        <p>No security threats detected</p>
                    `;
                } else {
                    statusDiv.innerHTML = `
                        <i class="fas fa-exclamation-triangle text-danger fa-5x mb-3"></i>
                        <h3 class="text-danger">ALERT</h3>
                        <p>${alertCount} active alert(s)</p>
                    `;
                }
                
                // Failed Trend Chart
                loadFailedTrendChart(data.failed_trend || []);
                
            } catch (error) {
                console.error('Error loading security data:', error);
            }
        }
        
        function loadFailedTrendChart(trendData) {
            const ctx = document.getElementById('failedTrendChart').getContext('2d');
            const labels = trendData.map(d => d.date);
            const counts = trendData.map(d => d.count);
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels.length ? labels : ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7'],
                    datasets: [{
                        label: 'Failed Attempts',
                        data: counts.length ? counts : [0, 0, 0, 0, 0, 0, 0],
                        borderColor: 'rgba(220, 53, 69, 1)',
                        backgroundColor: 'rgba(220, 53, 69, 0.2)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }
        
        document.addEventListener('DOMContentLoaded', loadSecurityData);
        </script>
    </body>
</html>
