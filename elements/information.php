<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">
                <i class="fas fa-user-cog me-2"></i>Admin Profile
            </h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="/index.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Admin Profile</li>
            </ol>

            <div class="row">
                <!-- Profile Card -->
                <div class="col-xl-4">
                    <div class="card mb-4 shadow">
                        <div class="card-header bg-primary text-white">
                            <i class="fas fa-user-circle me-1"></i> Profile Information
                        </div>
                        <div class="card-body text-center">
                            <div class="mb-4">
                                <i class="fas fa-user-circle fa-6x text-primary"></i>
                            </div>
                            <h4><?= $_SESSION['first_name'] . " " . $_SESSION['last_name'] ?></h4>
                            <p class="text-muted"><i class="fas fa-crown me-1"></i>Administrator</p>
                            <hr>
                            <div class="text-start">
                                <p><i class="fas fa-envelope me-2 text-primary"></i><strong>Email:</strong> <?= $_SESSION['email'] ?></p>
                                <p><i class="fas fa-birthday-cake me-2 text-primary"></i><strong>Birth Date:</strong> <?= $_SESSION['birth_date'] ?></p>
                                <p><i class="fas fa-id-badge me-2 text-primary"></i><strong>Admin ID:</strong> #<?= $_SESSION['id_admin'] ?></p>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-grid gap-2">
                                <button type="button" 
                                    onclick="window.location.href='/elements/modify_admin.php?id=<?= $_SESSION['id_admin'] ?>'" 
                                    class="btn btn-primary">
                                    <i class="fas fa-edit me-1"></i> Edit Profile
                                </button>
                                <button type="button" class="btn btn-dark" onclick="window.location.href='/config/logout.php'">
                                    <i class="fas fa-sign-out-alt me-1"></i> Logout
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activity Stats -->
                <div class="col-xl-8">
                    <div class="card mb-4 shadow">
                        <div class="card-header">
                            <i class="fas fa-chart-pie me-1"></i> System Overview
                        </div>
                        <div class="card-body">
                            <div class="row" id="systemStats">
                                <div class="col-md-4 mb-3">
                                    <div class="card bg-primary text-white h-100">
                                        <div class="card-body text-center">
                                            <h2 id="totalUsers">-</h2>
                                            <p class="mb-0"><i class="fas fa-users me-1"></i>Total Users</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card bg-success text-white h-100">
                                        <div class="card-body text-center">
                                            <h2 id="totalDoors">-</h2>
                                            <p class="mb-0"><i class="fas fa-door-closed me-1"></i>Total Doors</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="card bg-info text-white h-100">
                                        <div class="card-body text-center">
                                            <h2 id="totalLogs">-</h2>
                                            <p class="mb-0"><i class="fas fa-history me-1"></i>Total Logs</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4 shadow">
                        <div class="card-header">
                            <i class="fas fa-clock me-1"></i> Recent System Activity
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Time</th>
                                            <th>User</th>
                                            <th>Door</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="recentActivityBody">
                                        <tr>
                                            <td colspan="4" class="text-center">Loading...</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/elements/foot.php'; ?>
</div>

<script>
async function loadAdminStats() {
    try {
        const response = await fetch('/api/dashboard_stats.php');
        const data = await response.json();
        
        document.getElementById('totalUsers').textContent = data.total_users || 0;
        document.getElementById('totalDoors').textContent = data.total_doors || 0;
        document.getElementById('totalLogs').textContent = data.total_logs || 0;
        
        const tbody = document.getElementById('recentActivityBody');
        if (data.recent_logs && data.recent_logs.length > 0) {
            tbody.innerHTML = data.recent_logs.slice(0, 5).map(log => `
                <tr>
                    <td>${log.log_time}</td>
                    <td>${log.first_name} ${log.last_name}</td>
                    <td>${log.door_name}</td>
                    <td>${log.access == 1 ? 
                        '<span class="badge bg-success">Granted</span>' : 
                        '<span class="badge bg-danger">Denied</span>'}</td>
                </tr>
            `).join('');
        } else {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center">No recent activity</td></tr>';
        }
    } catch (error) {
        console.error('Error loading admin stats:', error);
    }
}

document.addEventListener('DOMContentLoaded', loadAdminStats);
</script>
