<?php
// Include fungsi
require_once 'includes/functions.php';

// Proses form jika di-submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $priority = isset($_POST['priority']) ? $_POST['priority'] : 'Sedang';
    $deadline = isset($_POST['deadline']) ? trim($_POST['deadline']) : null;
    
    // Validasi input
    if (empty($title)) {
        // Redirect dengan pesan error
        header('Location: index.php?error=Judul tugas tidak boleh kosong');
        exit;
    }
    
    // Tambahkan tugas baru
    $result = addTask($title, $description, $priority, $deadline);
    
    if ($result) {
        // Redirect ke halaman utama dengan pesan sukses
        header('Location: index.php?success=Tugas berhasil ditambahkan');
        exit;
    } else {
        // Redirect dengan pesan error
        header('Location: index.php?error=Gagal menambahkan tugas');
        exit;
    }
} else {
    // Jika bukan POST, redirect ke halaman utama
    header('Location: index.php');
    exit;
}
?>