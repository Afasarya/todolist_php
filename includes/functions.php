<?php
// Include koneksi database
require_once 'config.php';

// Fungsi untuk mendapatkan semua tugas dengan pagination, search, dan filtering
function getTasks($page = 1, $limit = 5, $search = '', $status = '', $priority = '') {
    global $conn;
    
    // Offset untuk pagination
    $offset = ($page - 1) * $limit;
    
    // Base query
    $sql = "SELECT * FROM tasks WHERE 1=1";
    
    // Tambahkan kondisi search jika ada
    if (!empty($search)) {
        $search = $conn->real_escape_string($search);
        $sql .= " AND (title LIKE '%$search%' OR description LIKE '%$search%')";
    }
    
    // Tambahkan filter status jika ada
    if (!empty($status)) {
        $status = $conn->real_escape_string($status);
        $sql .= " AND status = '$status'";
    }
    
    // Tambahkan filter priority jika ada
    if (!empty($priority)) {
        $priority = $conn->real_escape_string($priority);
        $sql .= " AND priority = '$priority'";
    }
    
    // Order by
    $sql .= " ORDER BY 
            CASE 
                WHEN status = 'Belum Selesai' THEN 0
                ELSE 1
            END, 
            CASE 
                WHEN priority = 'Tinggi' THEN 1
                WHEN priority = 'Sedang' THEN 2
                WHEN priority = 'Rendah' THEN 3
            END,
            CASE
                WHEN deadline IS NOT NULL AND deadline < CURDATE() AND status = 'Belum Selesai' THEN 0
                WHEN deadline IS NOT NULL THEN 1
                ELSE 2
            END, 
            deadline ASC, 
            created_at DESC";
    
    // Tambahkan LIMIT untuk pagination
    $sql .= " LIMIT $limit OFFSET $offset";
    
    $result = $conn->query($sql);
    
    $tasks = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $tasks[] = $row;
        }
    }
    
    return $tasks;
}

// Fungsi untuk mendapatkan total jumlah tugas (untuk pagination)
function getTotalTasksCount($search = '', $status = '', $priority = '') {
    global $conn;
    
    // Base query
    $sql = "SELECT COUNT(*) as total FROM tasks WHERE 1=1";
    
    // Tambahkan kondisi search jika ada
    if (!empty($search)) {
        $search = $conn->real_escape_string($search);
        $sql .= " AND (title LIKE '%$search%' OR description LIKE '%$search%')";
    }
    
    // Tambahkan filter status jika ada
    if (!empty($status)) {
        $status = $conn->real_escape_string($status);
        $sql .= " AND status = '$status'";
    }
    
    // Tambahkan filter priority jika ada
    if (!empty($priority)) {
        $priority = $conn->real_escape_string($priority);
        $sql .= " AND priority = '$priority'";
    }
    
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['total'];
    }
    
    return 0;
}

// Fungsi untuk mendapatkan tugas berdasarkan ID
function getTaskById($id) {
    global $conn;
    $id = $conn->real_escape_string($id);
    $sql = "SELECT * FROM tasks WHERE id = '$id'";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

// Fungsi untuk menambahkan tugas baru
function addTask($title, $description, $priority, $deadline = null) {
    global $conn;
    $title = $conn->real_escape_string($title);
    $description = $conn->real_escape_string($description);
    $priority = $conn->real_escape_string($priority);
    
    // Validasi dan format deadline
    $deadlineStr = "NULL";
    if (!empty($deadline)) {
        $deadline = $conn->real_escape_string($deadline);
        $deadlineStr = "'$deadline'";
    }
    
    $sql = "INSERT INTO tasks (title, description, priority, deadline) VALUES ('$title', '$description', '$priority', $deadlineStr)";
    
    if ($conn->query($sql) === TRUE) {
        return $conn->insert_id;
    }
    
    return false;
}

// Fungsi untuk memperbarui tugas
function updateTask($id, $title, $description, $priority, $deadline = null) {
    global $conn;
    $id = $conn->real_escape_string($id);
    $title = $conn->real_escape_string($title);
    $description = $conn->real_escape_string($description);
    $priority = $conn->real_escape_string($priority);
    
    // Validasi dan format deadline
    $deadlineStr = "NULL";
    if (!empty($deadline)) {
        $deadline = $conn->real_escape_string($deadline);
        $deadlineStr = "'$deadline'";
    }
    
    $sql = "UPDATE tasks SET title = '$title', description = '$description', priority = '$priority', deadline = $deadlineStr WHERE id = '$id'";
    
    return $conn->query($sql) === TRUE;
}

// Fungsi untuk menghapus tugas
function deleteTask($id) {
    global $conn;
    $id = $conn->real_escape_string($id);
    $sql = "DELETE FROM tasks WHERE id = '$id'";
    
    return $conn->query($sql) === TRUE;
}

// Fungsi untuk mengubah status tugas
function toggleTaskStatus($id) {
    global $conn;
    $id = $conn->real_escape_string($id);
    
    // Dapatkan status saat ini
    $task = getTaskById($id);
    if (!$task) {
        return false;
    }
    
    $newStatus = $task['status'] === 'Belum Selesai' ? 'Selesai' : 'Belum Selesai';
    $sql = "UPDATE tasks SET status = '$newStatus' WHERE id = '$id'";
    
    return $conn->query($sql) === TRUE;
}

// Fungsi untuk mendapatkan jumlah tugas berdasarkan status
function getTaskCounts() {
    global $conn;
    
    $counts = [
        'total' => 0,
        'completed' => 0,
        'pending' => 0,
        'overdue' => 0,
        'upcoming' => 0
    ];
    
    // Total dan berdasarkan status
    $sql = "SELECT status, COUNT(*) as count FROM tasks GROUP BY status";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ($row['status'] === 'Selesai') {
                $counts['completed'] = $row['count'];
            } else {
                $counts['pending'] = $row['count'];
            }
        }
    }
    
    // Tugas yang lewat deadline
    $sql = "SELECT COUNT(*) as count FROM tasks WHERE status = 'Belum Selesai' AND deadline < CURDATE() AND deadline IS NOT NULL";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $counts['overdue'] = $row['count'];
    }
    
    // Tugas yang deadlinenya hari ini atau dalam 3 hari ke depan
    $sql = "SELECT COUNT(*) as count FROM tasks WHERE status = 'Belum Selesai' AND deadline IS NOT NULL AND deadline BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 3 DAY)";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $counts['upcoming'] = $row['count'];
    }
    
    $counts['total'] = $counts['completed'] + $counts['pending'];
    
    return $counts;
}

// Fungsi untuk mengecek apakah tugas melewati deadline
function isTaskOverdue($deadline) {
    if (empty($deadline)) {
        return false;
    }
    
    $deadlineDate = new DateTime($deadline);
    $today = new DateTime(date('Y-m-d'));
    
    return $deadlineDate < $today;
}

// Fungsi untuk mengecek apakah tugas mendekati deadline (dalam 3 hari)
function isTaskUpcoming($deadline) {
    if (empty($deadline)) {
        return false;
    }
    
    $deadlineDate = new DateTime($deadline);
    $today = new DateTime(date('Y-m-d'));
    $interval = $today->diff($deadlineDate);
    
    // Deadline hari ini atau dalam 3 hari ke depan
    return $deadlineDate >= $today && $interval->days <= 3;
}

// Fungsi untuk format tanggal Indonesia
function formatDate($date) {
    if (empty($date)) {
        return '-';
    }
    
    $timestamp = strtotime($date);
    $months = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    $day = date('d', $timestamp);
    $month = $months[date('n', $timestamp) - 1];
    $year = date('Y', $timestamp);
    
    return "$day $month $year";
}

// Fungsi untuk menghasilkan pagination HTML
function generatePagination($currentPage, $totalPages, $search, $status, $priority) {
    $html = '<div class="pagination">';
    
    // Parameter query untuk filter dan search
    $queryParams = [];
    if (!empty($search)) $queryParams[] = "search=" . urlencode($search);
    if (!empty($status)) $queryParams[] = "status=" . urlencode($status);
    if (!empty($priority)) $queryParams[] = "priority=" . urlencode($priority);
    $queryString = !empty($queryParams) ? '&' . implode('&', $queryParams) : '';
    
    // Previous button
    $prevDisabled = ($currentPage <= 1) ? 'disabled' : '';
    $prevPage = $currentPage - 1;
    $html .= "<a href=\"?page=$prevPage$queryString\" class=\"page-link $prevDisabled\"><i class=\"fas fa-chevron-left\"></i></a>";
    
    // Pagination numbers
    $startPage = max(1, $currentPage - 2);
    $endPage = min($totalPages, $currentPage + 2);
    
    // Always show first page
    if ($startPage > 1) {
        $html .= "<a href=\"?page=1$queryString\" class=\"page-link\">1</a>";
        if ($startPage > 2) {
            $html .= "<span class=\"page-ellipsis\">...</span>";
        }
    }
    
    for ($i = $startPage; $i <= $endPage; $i++) {
        $active = ($i == $currentPage) ? 'active' : '';
        $html .= "<a href=\"?page=$i$queryString\" class=\"page-link $active\">$i</a>";
    }
    
    // Always show last page
    if ($endPage < $totalPages) {
        if ($endPage < $totalPages - 1) {
            $html .= "<span class=\"page-ellipsis\">...</span>";
        }
        $html .= "<a href=\"?page=$totalPages$queryString\" class=\"page-link\">$totalPages</a>";
    }
    
    // Next button
    $nextDisabled = ($currentPage >= $totalPages) ? 'disabled' : '';
    $nextPage = $currentPage + 1;
    $html .= "<a href=\"?page=$nextPage$queryString\" class=\"page-link $nextDisabled\"><i class=\"fas fa-chevron-right\"></i></a>";
    
    $html .= '</div>';
    
    return $html;
}
?>