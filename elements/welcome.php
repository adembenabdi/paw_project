<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">
                <i class="fas fa-tachometer-alt me-2"></i>Dashboard
            </h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item active">Welcome, <?= $_SESSION['first_name'] ?>!</li>
            </ol>

            <!-- Statistics Cards -->
            <div class="row">
                <!-- Total Users Card -->
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-primary text-white mb-4 shadow">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <div class="display-6" id="totalUsers">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </div>
                                <div>Total Users</div>
                            </div>
                            <i class="fas fa-users fa-3x opacity-50"></i>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <a class="small text-white stretched-link" href="/users/user_list.php">View Details</a>
                            <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                        </div>
                    </div>
                </div>

                <!-- Today's Access Count -->
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-success text-white mb-4 shadow">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <div class="display-6" id="todayAccess">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </div>
                                <div>Today's Access</div>
                            </div>
                            <i class="fas fa-door-open fa-3x opacity-50"></i>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <a class="small text-white stretched-link" href="/users/all_logs.php">View Logs</a>
                            <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                        </div>
                    </div>
                </div>

                <!-- Access Granted Today -->
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-info text-white mb-4 shadow">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <div class="display-6" id="grantedToday">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </div>
                                <div>Granted Today</div>
                            </div>
                            <i class="fas fa-check-circle fa-3x opacity-50"></i>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <a class="small text-white stretched-link" href="/users/all_logs.php">View Details</a>
                            <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                        </div>
                    </div>
                </div>

                <!-- Access Denied Today -->
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-danger text-white mb-4 shadow">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <div class="display-6" id="deniedToday">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </div>
                                <div>Denied Today</div>
                            </div>
                            <i class="fas fa-times-circle fa-3x opacity-50"></i>
                        </div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <a class="small text-white stretched-link" href="/security_alerts.php">View Alerts</a>
                            <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Alerts Section -->
            <div class="row mb-4" id="securityAlertsSection" style="display: none;">
                <div class="col-12">
                    <div class="card border-danger shadow">
                        <div class="card-header bg-danger text-white">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Security Alerts</strong>
                        </div>
                        <div class="card-body" id="securityAlertsList">
                            <!-- Alerts will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row">
                <div class="col-xl-6">
                    <div class="card mb-4 shadow">
                        <div class="card-header">
                            <i class="fas fa-chart-area me-1"></i>
                            Today's Door Usage Over Time
                        </div>
                        <div class="card-body">
                            <canvas id="todayLineChart" width="100%" height="40"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6">
                    <div class="card mb-4 shadow">
                        <div class="card-header">
                            <i class="fas fa-chart-bar me-1"></i>
                            Weekly Door Usage
                        </div>
                        <div class="card-body">
                            <canvas id="weeklyBarChart" width="100%" height="40"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Access Rate Pie Chart & Recent Logs -->
            <div class="row">
                <div class="col-xl-4">
                    <div class="card mb-4 shadow">
                        <div class="card-header">
                            <i class="fas fa-chart-pie me-1"></i>
                            Access Rate (This Week)
                        </div>
                        <div class="card-body">
                            <canvas id="accessPieChart" width="100%" height="50"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-xl-8">
                    <div class="card mb-4 shadow">
                        <div class="card-header">
                            <i class="fas fa-clock me-1"></i>
                            Recent Access Logs
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="recentLogsTable">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>User</th>
                                            <th>Time</th>
                                            <th>Status</th>
                                            <th>RFID</th>
                                        </tr>
                                    </thead>
                                    <tbody id="recentLogsBody">
                                        <!-- Recent logs will be loaded here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4 shadow">
                        <div class="card-header">
                            <i class="fas fa-bolt me-1"></i>
                            Quick Actions
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-wrap gap-3 justify-content-center">
                                <a href="/users/add_user.php" class="btn btn-primary btn-lg">
                                    <i class="fas fa-user-plus me-2"></i>Add New User
                                </a>
                                <a href="/users/user_list.php" class="btn btn-info btn-lg">
                                    <i class="fas fa-users me-2"></i>Manage Users
                                </a>
                                <a href="/users/all_logs.php" class="btn btn-success btn-lg">
                                    <i class="fas fa-list me-2"></i>View All Logs
                                </a>
                                <a href="/admin_info.php" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-user-cog me-2"></i>Admin Profile
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/elements/foot.php'; ?>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Fetch Dashboard Statistics
async function loadDashboardStats() {
    try {
        const response = await fetch('/api/dashboard_stats.php');
        const data = await response.json();
        
        document.getElementById('totalUsers').textContent = data.total_users || 0;
        document.getElementById('todayAccess').textContent = data.today_access || 0;
        document.getElementById('grantedToday').textContent = data.granted_today || 0;
        document.getElementById('deniedToday').textContent = data.denied_today || 0;
        
        // Show security alerts if there are any
        if (data.security_alerts && data.security_alerts.length > 0) {
            document.getElementById('securityAlertsSection').style.display = 'block';
            const alertsList = document.getElementById('securityAlertsList');
            alertsList.innerHTML = data.security_alerts.map(alert => `
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <div>
                        <strong>Warning:</strong> ${alert.message}
                        <small class="text-muted ms-2">(${alert.time})</small>
                    </div>
                </div>
            `).join('');
        }
        
        // Load charts with data
        loadTodayChart(data.hourly_data || []);
        loadWeeklyChart(data.weekly_data || []);
        loadPieChart(data.granted_week || 0, data.denied_week || 0);
        loadRecentLogs(data.recent_logs || []);
        
    } catch (error) {
        console.error('Error loading dashboard stats:', error);
    }
}

// Today's Line Chart
function loadTodayChart(hourlyData) {
    const ctx = document.getElementById('todayLineChart').getContext('2d');
    const labels = hourlyData.map(d => d.hour + ':00');
    const granted = hourlyData.map(d => d.granted);
    const denied = hourlyData.map(d => d.denied);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels.length ? labels : ['00:00', '06:00', '12:00', '18:00', '23:00'],
            datasets: [{
                label: 'Access Granted',
                data: granted.length ? granted : [0, 0, 0, 0, 0],
                borderColor: 'rgba(40, 167, 69, 1)',
                backgroundColor: 'rgba(40, 167, 69, 0.2)',
                fill: true,
                tension: 0.4
            }, {
                label: 'Access Denied',
                data: denied.length ? denied : [0, 0, 0, 0, 0],
                borderColor: 'rgba(220, 53, 69, 1)',
                backgroundColor: 'rgba(220, 53, 69, 0.2)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' }
            }
        }
    });
}

// Weekly Bar Chart
function loadWeeklyChart(weeklyData) {
    const ctx = document.getElementById('weeklyBarChart').getContext('2d');
    const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    const labels = weeklyData.map(d => days[new Date(d.date).getDay()]);
    const totals = weeklyData.map(d => d.total);
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels.length ? labels : days,
            datasets: [{
                label: 'Total Access',
                data: totals.length ? totals : [0, 0, 0, 0, 0, 0, 0],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)',
                    'rgba(255, 159, 64, 0.7)',
                    'rgba(199, 199, 199, 0.7)'
                ],
                borderWidth: 1
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

// Access Pie Chart
function loadPieChart(granted, denied) {
    const ctx = document.getElementById('accessPieChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Granted', 'Denied'],
            datasets: [{
                data: [granted || 1, denied || 0],
                backgroundColor: [
                    'rgba(40, 167, 69, 0.8)',
                    'rgba(220, 53, 69, 0.8)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
}

// Recent Logs Table
function loadRecentLogs(logs) {
    const tbody = document.getElementById('recentLogsBody');
    if (logs.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center">No recent logs</td></tr>';
        return;
    }
    
    tbody.innerHTML = logs.map(log => {
        const statusClass = log.status.includes('Granted') ? 'bg-success' : 'bg-danger';
        return `
            <tr>
                <td><strong>${log.first_name} ${log.last_name}</strong></td>
                <td>${log.log_date}</td>
                <td><span class="badge ${statusClass}">${log.status}</span></td>
                <td><code>${log.used_rfid_code || 'N/A'}</code></td>
            </tr>
        `;
    }).join('');
}

// Load dashboard on page load
document.addEventListener('DOMContentLoaded', loadDashboardStats);

// Auto-refresh every 30 seconds
setInterval(loadDashboardStats, 30000);
</script>
