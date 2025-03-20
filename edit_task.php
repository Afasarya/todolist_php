<?php
// Include fungsi
require_once 'includes/functions.php';

// Proses form jika di-submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $taskId = isset($_POST['task_id']) ? $_POST['task_id'] : '';
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $priority = isset($_POST['priority']) ? $_POST['priority'] : 'Sedang';
    $deadline = isset($_POST['deadline']) ? trim($_POST['deadline']) : null;
    
    // Validasi input
    if (empty($taskId) || empty($title)) {
        // Redirect dengan pesan error
        header('Location: index.php?error=Data tidak valid');
        exit;
    }
    
    // Perbarui tugas
    $result = updateTask($taskId, $title, $description, $priority, $deadline);
    
    if ($result) {
        // Redirect ke halaman utama dengan pesan sukses
        header('Location: index.php?success=Tugas berhasil diperbarui');
        exit;
    } else {
        // Redirect dengan pesan error
        header('Location: index.php?error=Gagal memperbarui tugas');
        exit;
    }
} else {
    // Jika bukan POST, redirect ke halaman utama
    header('Location: index.php');
    exit;
}
?>