// Menunggu dokumen HTML selesai dimuat
document.addEventListener('DOMContentLoaded', function() {
    // Toggle form tambah tugas
    const showAddTaskFormBtn = document.getElementById('showAddTaskForm');
    const addTaskForm = document.getElementById('addTaskForm');
    const cancelAddBtn = document.getElementById('cancelAdd');
    
    if (showAddTaskFormBtn && addTaskForm && cancelAddBtn) {
        showAddTaskFormBtn.addEventListener('click', function() {
            addTaskForm.style.display = 'block';
            showAddTaskFormBtn.style.display = 'none';
        });
        
        cancelAddBtn.addEventListener('click', function() {
            addTaskForm.style.display = 'none';
            showAddTaskFormBtn.style.display = 'block';
        });
    }
    
    // Toggle form edit tugas
    const editButtons = document.querySelectorAll('.btn-edit');
    const cancelEditButtons = document.querySelectorAll('.cancel-edit');
    
    editButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const taskId = this.getAttribute('data-id');
            const editForm = document.getElementById(`editForm-${taskId}`);
            
            if (editForm) {
                // Sembunyikan task item
                const taskItem = this.closest('.task-item');
                if (taskItem) taskItem.style.display = 'none';
                
                // Tampilkan form edit
                editForm.style.display = 'block';
            }
        });
    });
    
    cancelEditButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const taskId = this.getAttribute('data-id');
            const editForm = document.getElementById(`editForm-${taskId}`);
            
            if (editForm) {
                // Sembunyikan form edit
                editForm.style.display = 'none';
                
                // Tampilkan task item
                const taskItems = document.querySelectorAll('.task-item');
                taskItems.forEach(function(item) {
                    if (item.querySelector(`[data-id="${taskId}"]`)) {
                        item.style.display = 'block';
                    }
                });
            }
        });
    });
    
    // Konfirmasi sebelum menghapus tugas
    const deleteForms = document.querySelectorAll('.delete-form');
    
    deleteForms.forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!confirm('Apakah Anda yakin ingin menghapus tugas ini?')) {
                event.preventDefault();
            }
        });
    });
    
    // Menampilkan notifikasi
    const urlParams = new URLSearchParams(window.location.search);
    const successMessage = urlParams.get('success');
    const errorMessage = urlParams.get('error');
    
    if (successMessage || errorMessage) {
        const notification = document.createElement('div');
        notification.className = `notification ${successMessage ? 'success' : 'error'}`;
        notification.textContent = successMessage || errorMessage;
        
        document.body.appendChild(notification);
        
        // Hapus notifikasi setelah 5 detik
        setTimeout(function() {
            notification.style.opacity = '0';
            setTimeout(function() {
                document.body.removeChild(notification);
            }, 500);
        }, 5000);
    }
});

// Tambahkan CSS untuk notifikasi
document.addEventListener('DOMContentLoaded', function() {
    const style = document.createElement('style');
    style.textContent = `
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 5px;
            color: white;
            font-weight: 500;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            transition: opacity 0.5s ease;
        }
        
        .notification.success {
            background-color: var(--success-color);
        }
        
        .notification.error {
            background-color: var(--danger-color);
        }
    `;
    document.head.appendChild(style);
});