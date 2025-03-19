<?php
// Include fungsi
require_once 'includes/functions.php';

// Proses form jika di-submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $taskId = isset($_POST['task_id']) ? $_POST['task_id'] : '';
    
    // Validasi input
    if (empty($taskId)) {
        // Redirect dengan pesan error
        header('Location: index.php?error=ID tugas tidak valid');
        exit;
    }
    
    // Hapus tugas
    $result = deleteTask($taskId);
    
    if ($result) {
        // Redirect ke halaman utama dengan pesan sukses
        header('Location: index.php?success=Tugas berhasil dihapus');
        exit;
    } else {
        // Redirect dengan pesan error
        header('Location: index.php?error=Gagal menghapus tugas');
        exit;
    }
} else {
    // Jika bukan POST, redirect ke halaman utama
    header('Location: index.php');
    exit;
}
?>