// Menunggu dokumen HTML selesai dimuat
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const blurOverlay = document.getElementById('blurOverlay');
    const showAddTaskModalBtn = document.getElementById('showAddTaskModal');
    const addTaskModal = document.getElementById('addTaskModal');
    const editTaskModal = document.getElementById('editTaskModal');
    const closeAddTaskModalBtn = document.getElementById('closeAddTaskModal');
    const closeEditTaskModalBtn = document.getElementById('closeEditTaskModal');
    const cancelAddBtn = document.getElementById('cancelAdd');
    const cancelEditBtn = document.getElementById('cancelEdit');
    
    // Fungsi untuk tampilkan modal dengan blur overlay
    function showModal(modal) {
        if (modal && blurOverlay) {
            blurOverlay.style.display = 'block';
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden'; // Prevent scrolling
            
            // Animation
            setTimeout(() => {
                modal.classList.add('show');
            }, 10);
        }
    }
    
    // Fungsi untuk sembunyikan modal
    function hideModal(modal) {
        if (modal && blurOverlay) {
            modal.classList.remove('show');
            
            // Wait for animation to finish
            setTimeout(() => {
                modal.style.display = 'none';
                blurOverlay.style.display = 'none';
                document.body.style.overflow = ''; // Re-enable scrolling
            }, 300);
        }
    }
    
    // Event untuk tampilkan modal tambah tugas
    if (showAddTaskModalBtn && addTaskModal) {
        showAddTaskModalBtn.addEventListener('click', function() {
            showModal(addTaskModal);
        });
    }
    
    // Event untuk sembunyikan modal tambah tugas
    if (closeAddTaskModalBtn && addTaskModal) {
        closeAddTaskModalBtn.addEventListener('click', function() {
            hideModal(addTaskModal);
        });
    }
    
    if (cancelAddBtn && addTaskModal) {
        cancelAddBtn.addEventListener('click', function() {
            hideModal(addTaskModal);
        });
    }
    
    // Event untuk sembunyikan modal edit tugas
    if (closeEditTaskModalBtn && editTaskModal) {
        closeEditTaskModalBtn.addEventListener('click', function() {
            hideModal(editTaskModal);
        });
    }
    
    if (cancelEditBtn && editTaskModal) {
        cancelEditBtn.addEventListener('click', function() {
            hideModal(editTaskModal);
        });
    }
    
    // Event untuk tombol edit tugas
    const editButtons = document.querySelectorAll('.btn-edit');
    
    editButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            const taskId = this.getAttribute('data-id');
            const taskDataElement = document.getElementById(`task-data-${taskId}`);
            
            if (taskDataElement && editTaskModal) {
                try {
                    // Parse data JSON
                    const taskData = JSON.parse(taskDataElement.textContent);
                    
                    // Isi form edit dengan data tugas
                    document.getElementById('edit_task_id').value = taskData.id;
                    document.getElementById('edit_title').value = taskData.title;
                    document.getElementById('edit_description').value = taskData.description;
                    document.getElementById('edit_priority').value = taskData.priority;
                    document.getElementById('edit_deadline').value = taskData.deadline || '';
                    
                    // Tampilkan modal edit
                    showModal(editTaskModal);
                } catch (error) {
                    console.error('Error parsing task data:', error);
                }
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
    
    // Tampilkan notifikasi
    function showNotification(message, type) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        
        // Add icon based on type
        const icon = document.createElement('i');
        icon.className = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
        notification.appendChild(icon);
        
        // Add message
        const messageSpan = document.createElement('span');
        messageSpan.textContent = message;
        notification.appendChild(messageSpan);
        
        // Add to body
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
            notification.style.opacity = '1';
        }, 10);
        
        // Remove after delay
        setTimeout(() => {
            notification.style.transform = 'translateX(20px)';
            notification.style.opacity = '0';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 500);
        }, 5000);
    }
    
    // Check for URL parameters for notifications
    const urlParams = new URLSearchParams(window.location.search);
    const successMessage = urlParams.get('success');
    const errorMessage = urlParams.get('error');
    
    if (successMessage) {
        showNotification(successMessage, 'success');
    } else if (errorMessage) {
        showNotification(errorMessage, 'error');
    }
    
    // Tambahkan CSS untuk notifikasi
    const style = document.createElement('style');
    style.textContent = `
        .notification {
            transform: translateX(20px);
            opacity: 0;
            transition: transform 0.5s ease, opacity 0.5s ease;
        }
    `;
    document.head.appendChild(style);
    
    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === addTaskModal) {
            hideModal(addTaskModal);
        } else if (event.target === editTaskModal) {
            hideModal(editTaskModal);
        }
    });
});