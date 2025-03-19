<?php
// Include koneksi database
require_once 'config.php';

// Fungsi untuk mendapatkan semua tugas
function getAllTasks() {
    global $conn;
    $sql = "SELECT * FROM tasks ORDER BY 
            CASE 
                WHEN status = 'Belum Selesai' THEN 0
                ELSE 1
            END, 
            CASE 
                WHEN priority = 'Tinggi' THEN 1
                WHEN priority = 'Sedang' THEN 2
                WHEN priority = 'Rendah' THEN 3
            END, 
            created_at DESC";
    $result = $conn->query($sql);
    
    $tasks = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $tasks[] = $row;
        }
    }
    
    return $tasks;
}

// Fungsi untuk mendapatkan tugas berdasarkan ID
function getTaskById($id) {
    global $conn;
    $id = $conn->real_escape_string($id);
    $sql = "SELECT * FROM tasks WHERE id = '$id'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

// Fungsi untuk menambahkan tugas baru
function addTask($title, $description, $priority) {
    global $conn;
    $title = $conn->real_escape_string($title);
    $description = $conn->real_escape_string($description);
    $priority = $conn->real_escape_string($priority);
    
    $sql = "INSERT INTO tasks (title, description, priority) VALUES ('$title', '$description', '$priority')";
    
    if ($conn->query($sql) === TRUE) {
        return $conn->insert_id;
    }
    
    return false;
}

// Fungsi untuk memperbarui tugas
function updateTask($id, $title, $description, $priority) {
    global $conn;
    $id = $conn->real_escape_string($id);
    $title = $conn->real_escape_string($title);
    $description = $conn->real_escape_string($description);
    $priority = $conn->real_escape_string($priority);
    
    $sql = "UPDATE tasks SET title = '$title', description = '$description', priority = '$priority' WHERE id = '$id'";
    
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
        'pending' => 0
    ];
    
    $sql = "SELECT status, COUNT(*) as count FROM tasks GROUP BY status";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            if ($row['status'] === 'Selesai') {
                $counts['completed'] = $row['count'];
            } else {
                $counts['pending'] = $row['count'];
            }
        }
    }
    
    $counts['total'] = $counts['completed'] + $counts['pending'];
    
    return $counts;
}
?>