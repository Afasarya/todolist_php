<?php
// Include fungsi
require_once 'includes/functions.php';

// Pagination: Dapatkan halaman saat ini
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 5; // Jumlah tugas per halaman

// Get search and filter parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status = isset($_GET['status']) ? trim($_GET['status']) : '';
$priority = isset($_GET['priority']) ? trim($_GET['priority']) : '';

// Dapatkan tugas sesuai pagination, search, dan filter
$tasks = getTasks($page, $limit, $search, $status, $priority);

// Hitung total tugas untuk pagination
$totalTasks = getTotalTasksCount($search, $status, $priority);
$totalPages = ceil($totalTasks / $limit);

// Jika halaman saat ini lebih besar dari total halaman, redirect ke halaman terakhir
if ($totalPages > 0 && $page > $totalPages) {
    header("Location: index.php?page=$totalPages");
    exit;
}

// Dapatkan statistik tugas
$taskCounts = getTaskCounts();

// Include header
include 'includes/header.php';
?>

<main>
    <div class="dashboard">
        <div class="stat-card">
            <div class="stat-value"><?php echo $taskCounts['total']; ?></div>
            <div class="stat-label">Total Tugas</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo $taskCounts['completed']; ?></div>
            <div class="stat-label">Selesai</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php echo $taskCounts['pending']; ?></div>
            <div class="stat-label">Belum Selesai</div>
        </div>
        <div class="stat-card">
            <div class="stat-value <?php echo $taskCounts['overdue'] > 0 ? 'stat-alert' : ''; ?>">
                <?php echo $taskCounts['overdue']; ?>
            </div>
            <div class="stat-label">Terlewat</div>
        </div>
        <div class="stat-card">
            <div class="stat-value <?php echo $taskCounts['upcoming'] > 0 ? 'stat-warning' : ''; ?>">
                <?php echo $taskCounts['upcoming']; ?>
            </div>
            <div class="stat-label">Mendekati Deadline</div>
        </div>
    </div>

    <!-- Search dan Filter -->
    <div class="search-filter-container">
        <form action="index.php" method="GET" class="search-filter-form">
            <div class="search-box">
                <input type="text" name="search" placeholder="Cari tugas..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit"><i class="fas fa-search"></i></button>
            </div>
            
            <div class="filter-container">
                <div class="filter-item">
                    <label for="status-filter">Status:</label>
                    <select name="status" id="status-filter" onchange="this.form.submit()">
                        <option value="">Semua</option>
                        <option value="Belum Selesai" <?php echo $status === 'Belum Selesai' ? 'selected' : ''; ?>>Belum Selesai</option>
                        <option value="Selesai" <?php echo $status === 'Selesai' ? 'selected' : ''; ?>>Selesai</option>
                    </select>
                </div>
                
                <div class="filter-item">
                    <label for="priority-filter">Prioritas:</label>
                    <select name="priority" id="priority-filter" onchange="this.form.submit()">
                        <option value="">Semua</option>
                        <option value="Tinggi" <?php echo $priority === 'Tinggi' ? 'selected' : ''; ?>>Tinggi</option>
                        <option value="Sedang" <?php echo $priority === 'Sedang' ? 'selected' : ''; ?>>Sedang</option>
                        <option value="Rendah" <?php echo $priority === 'Rendah' ? 'selected' : ''; ?>>Rendah</option>
                    </select>
                </div>
                
                <!-- Tombol Reset Filter -->
                <?php if (!empty($search) || !empty($status) || !empty($priority)): ?>
                <div class="filter-item">
                    <a href="index.php" class="btn btn-reset"><i class="fas fa-times"></i> Reset</a>
                </div>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="task-controls">
        <button id="showAddTaskModal" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Tugas
        </button>
    </div>

    <!-- Daftar Tugas -->
    <div class="task-list">
        <h2>Daftar Tugas</h2>
        
        <?php if (empty($tasks)): ?>
            <div class="empty-state">
                <i class="fas fa-clipboard-list"></i>
                <p>Belum ada tugas. Tambahkan tugas baru!</p>
            </div>
        <?php else: ?>
            <?php foreach ($tasks as $task): 
                // Periksa jika tugas terlewat deadline
                $isOverdue = isTaskOverdue($task['deadline']) && $task['status'] === 'Belum Selesai';
                $isUpcoming = isTaskUpcoming($task['deadline']) && $task['status'] === 'Belum Selesai';
                $taskClass = $task['status'] === 'Selesai' ? 'completed' : '';
                $taskClass .= $isOverdue ? ' overdue' : '';
                $taskClass .= $isUpcoming ? ' upcoming' : '';
            ?>
                <div class="task-item <?php echo $taskClass; ?>">
                    <?php if ($isOverdue): ?>
                        <div class="task-alert">
                            <i class="fas fa-exclamation-circle"></i> Terlewat Deadline
                        </div>
                    <?php elseif ($isUpcoming): ?>
                        <div class="task-warning">
                            <i class="fas fa-clock"></i> Mendekati Deadline
                        </div>
                    <?php endif; ?>
                    
                    <div class="task-header">
                        <div class="task-status">
                            <form action="complete_task.php" method="POST">
                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                <input type="hidden" name="current_page" value="<?php echo $page; ?>">
                                <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                                <input type="hidden" name="status" value="<?php echo htmlspecialchars($status); ?>">
                                <input type="hidden" name="priority" value="<?php echo htmlspecialchars($priority); ?>">
                                <button type="submit" class="status-toggle" <?php echo $isOverdue ? 'title="Tugas ini telah melewati deadline"' : ''; ?>>
                                    <i class="fas <?php echo $task['status'] === 'Selesai' ? 'fa-check-circle' : 'fa-circle'; ?>"></i>
                                </button>
                            </form>
                        </div>
                        <div class="task-info">
                            <h3><?php echo htmlspecialchars($task['title']); ?></h3>
                            <div class="task-meta-info">
                                <span class="priority priority-<?php echo strtolower($task['priority']); ?>">
                                    <?php echo $task['priority']; ?>
                                </span>
                                <?php if (!empty($task['deadline'])): ?>
                                <span class="deadline <?php echo $isOverdue ? 'overdue' : ($isUpcoming ? 'upcoming' : ''); ?>">
                                    <i class="fas fa-calendar-alt"></i> 
                                    <?php echo formatDate($task['deadline']); ?>
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="task-actions">
                            <button class="btn-icon btn-edit" data-id="<?php echo $task['id']; ?>" <?php echo $isOverdue ? 'disabled' : ''; ?>>
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="delete_task.php" method="POST" class="delete-form">
                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                <input type="hidden" name="current_page" value="<?php echo $page; ?>">
                                <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                                <input type="hidden" name="status" value="<?php echo htmlspecialchars($status); ?>">
                                <input type="hidden" name="priority" value="<?php echo htmlspecialchars($priority); ?>">
                                <button type="submit" class="btn-icon btn-delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php if (!empty($task['description'])): ?>
                        <div class="task-description">
                            <?php echo nl2br(htmlspecialchars($task['description'])); ?>
                        </div>
                    <?php endif; ?>
                    <div class="task-meta">
                        <span class="task-date">
                            <i class="fas fa-clock"></i> 
                            Dibuat: <?php echo date('d M Y', strtotime($task['created_at'])); ?>
                        </span>
                    </div>
                </div>
                
                <!-- Data JSON untuk Edit Modal -->
                <script type="application/json" id="task-data-<?php echo $task['id']; ?>">
                    {
                        "id": "<?php echo $task['id']; ?>",
                        "title": "<?php echo htmlspecialchars(addslashes($task['title'])); ?>",
                        "description": "<?php echo htmlspecialchars(addslashes($task['description'])); ?>",
                        "priority": "<?php echo $task['priority']; ?>",
                        "deadline": "<?php echo $task['deadline']; ?>"
                    }
                </script>
            <?php endforeach; ?>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <?php echo generatePagination($page, $totalPages, $search, $status, $priority); ?>
            <?php endif; ?>
            
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/modals.php'; ?>

<footer>
    <div class="container">
        <p>&copy; <?php echo date('Y'); ?> - To Do List Interaktif</p>
    </div>
</footer>

<script src="assets/js/script.js"></script>
</body>
</html>