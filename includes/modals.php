<!-- Modal Tambah Tugas -->
<div id="addTaskModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Tambah Tugas Baru</h2>
            <button type="button" class="modal-close" id="closeAddTaskModal">&times;</button>
        </div>
        <div class="modal-body">
            <form action="add_task.php" method="POST" id="addTaskForm">
                <div class="form-group">
                    <label for="title">Judul:</label>
                    <input type="text" id="title" name="title" required placeholder="Masukkan judul tugas">
                </div>
                <div class="form-group">
                    <label for="description">Deskripsi:</label>
                    <textarea id="description" name="description" rows="3" placeholder="Masukkan deskripsi tugas (opsional)"></textarea>
                </div>
                <div class="form-group">
                    <label for="priority">Prioritas:</label>
                    <select id="priority" name="priority">
                        <option value="Rendah">Rendah</option>
                        <option value="Sedang" selected>Sedang</option>
                        <option value="Tinggi">Tinggi</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="deadline">Deadline:</label>
                    <input type="date" id="deadline" name="deadline" min="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="form-buttons">
                    <button type="submit" class="btn btn-success">Simpan</button>
                    <button type="button" id="cancelAdd" class="btn btn-secondary">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Tugas (Template, akan diubah secara dinamis) -->
<div id="editTaskModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Edit Tugas</h2>
            <button type="button" class="modal-close" id="closeEditTaskModal">&times;</button>
        </div>
        <div class="modal-body">
            <form action="edit_task.php" method="POST" id="editTaskForm">
                <input type="hidden" id="edit_task_id" name="task_id" value="">
                <div class="form-group">
                    <label for="edit_title">Judul:</label>
                    <input type="text" id="edit_title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="edit_description">Deskripsi:</label>
                    <textarea id="edit_description" name="description" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label for="edit_priority">Prioritas:</label>
                    <select id="edit_priority" name="priority">
                        <option value="Rendah">Rendah</option>
                        <option value="Sedang">Sedang</option>
                        <option value="Tinggi">Tinggi</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_deadline">Deadline:</label>
                    <input type="date" id="edit_deadline" name="deadline">
                </div>
                <div class="form-buttons">
                    <button type="submit" class="btn btn-success">Perbarui</button>
                    <button type="button" id="cancelEdit" class="btn btn-secondary">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>