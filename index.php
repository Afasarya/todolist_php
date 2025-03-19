<?php
// Include fungsi
require_once 'includes/functions.php';

// Dapatkan semua tugas
$tasks = getAllTasks();

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
    </div>

    <div class="task-controls">
        <button id="showAddTaskForm" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Tugas
        </button>
    </div>

    <!-- Form Tambah Tugas (Tersembunyi awalnya) -->
    <div id="addTaskForm" class="task-form" style="display: none;">
        <h2>Tambah Tugas Baru</h2>
        <form action="add_task.php" method="POST">
            <div class="form-group">
                <label for="title">Judul:</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="description">Deskripsi:</label>
                <textarea id="description" name="description" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label for="priority">Prioritas:</label>
                <select id="priority" name="priority">
                    <option value="Rendah">Rendah</option>
                    <option value="Sedang" selected>Sedang</option>
                    <option value="Tinggi">Tinggi</option>
                </select>
            </div>
            <div class="form-buttons">
                <button type="submit" class="btn btn-success">Simpan</button>
                <button type="button" id="cancelAdd" class="btn btn-secondary">Batal</button>
            </div>
        </form>
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
            <?php foreach ($tasks as $task): ?>
                <div class="task-item <?php echo $task['status'] === 'Selesai' ? 'completed' : ''; ?>">
                    <div class="task-header">
                        <div class="task-status">
                            <form action="complete_task.php" method="POST">
                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                <button type="submit" class="status-toggle">
                                    <i class="fas <?php echo $task['status'] === 'Selesai' ? 'fa-check-circle' : 'fa-circle'; ?>"></i>
                                </button>
                            </form>
                        </div>
                        <div class="task-info">
                            <h3><?php echo htmlspecialchars($task['title']); ?></h3>
                            <span class="priority priority-<?php echo strtolower($task['priority']); ?>">
                                <?php echo $task['priority']; ?>
                            </span>
                        </div>
                        <div class="task-actions">
                            <button class="btn-icon btn-edit" data-id="<?php echo $task['id']; ?>">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="delete_task.php" method="POST" class="delete-form">
                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
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
                            <i class="fas fa-calendar"></i> 
                            <?php echo date('d M Y', strtotime($task['created_at'])); ?>
                        </span>
                    </div>
                </div>
                
                <!-- Form Edit untuk Tugas Ini (Tersembunyi awalnya) -->
                <div id="editForm-<?php echo $task['id']; ?>" class="task-form edit-form" style="display: none;">
                    <h2>Edit Tugas</h2>
                    <form action="edit_task.php" method="POST">
                        <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                        <div class="form-group">
                            <label for="edit-title-<?php echo $task['id']; ?>">Judul:</label>
                            <input type="text" id="edit-title-<?php echo $task['id']; ?>" name="title" value="<?php echo htmlspecialchars($task['title']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-description-<?php echo $task['id']; ?>">Deskripsi:</label>
                            <textarea id="edit-description-<?php echo $task['id']; ?>" name="description" rows="3"><?php echo htmlspecialchars($task['description']); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="edit-priority-<?php echo $task['id']; ?>">Prioritas:</label>
                            <select id="edit-priority-<?php echo $task['id']; ?>" name="priority">
                                <option value="Rendah" <?php echo $task['priority'] === 'Rendah' ? 'selected' : ''; ?>>Rendah</option>
                                <option value="Sedang" <?php echo $task['priority'] === 'Sedang' ? 'selected' : ''; ?>>Sedang</option>
                                <option value="Tinggi" <?php echo $task['priority'] === 'Tinggi' ? 'selected' : ''; ?>>Tinggi</option>
                            </select>
                        </div>
                        <div class="form-buttons">
                            <button type="submit" class="btn btn-success">Perbarui</button>
                            <button type="button" class="btn btn-secondary cancel-edit" data-id="<?php echo $task['id']; ?>">Batal</button>
                        </div>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<footer>
    <div class="container">
        <p>&copy; <?php echo date('Y'); ?> - To Do List Interaktif</p>
    </div>
</footer>

<script src="assets/js/script.js"></script>
</body>
</html>