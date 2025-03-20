<?php
// Include fungsi
require_once 'includes/functions.php';

// Proses form jika di-submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $taskId = isset($_POST['task_id']) ? $_POST['task_id'] : '';
    
    // Get pagination and filter parameters
    $currentPage = isset($_POST['current_page']) ? (int)$_POST['current_page'] : 1;
    $search = isset($_POST['search']) ? trim($_POST['search']) : '';
    $status = isset($_POST['status']) ? trim($_POST['status']) : '';
    $priority = isset($_POST['priority']) ? trim($_POST['priority']) : '';
    
    // Build redirect URL
    $redirectUrl = "index.php?page=$currentPage";
    if (!empty($search)) $redirectUrl .= "&search=" . urlencode($search);
    if (!empty($status)) $redirectUrl .= "&status=" . urlencode($status);
    if (!empty($priority)) $redirectUrl .= "&priority=" . urlencode($priority);
    
    // Validasi input
    if (empty($taskId)) {
        // Redirect dengan pesan error
        header("Location: $redirectUrl&error=ID tugas tidak valid");
        exit;
    }
    
    // Hapus tugas
    $result = deleteTask($taskId);
    
    if ($result) {
        // Redirect ke halaman utama dengan pesan sukses
        header("Location: $redirectUrl&success=Tugas berhasil dihapus");
        exit;
    } else {
        // Redirect dengan pesan error
        header("Location: $redirectUrl&error=Gagal menghapus tugas");
        exit;
    }
} else {
    // Jika bukan POST, redirect ke halaman utama
    header('Location: index.php');
    exit;
}
?>