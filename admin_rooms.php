<?php
session_start();
require_once 'config/database.php';

// Redirect if not admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

// Handle room status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['room_id'], $_POST['new_status'])) {
    $room_id = intval($_POST['room_id']);
    $new_status = $_POST['new_status'];

    $stmt = $conn->prepare("UPDATE rooms SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $room_id);
    $stmt->execute();
    $stmt->close();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle new room addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_room'])) {
    $room_number = $_POST['room_number'];
    $type = $_POST['type'];
    $price = $_POST['price'];
    $status = $_POST['status'];

    // Image upload handling
    $image_url = null;
    if (isset($_FILES['room_image']) && $_FILES['room_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['room_image']['tmp_name'];
        $fileName = basename($_FILES['room_image']['name']);
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileExtension, $allowedExtensions)) {
            if (!is_dir('uploads')) {
                mkdir('uploads', 0755, true);
            }
            $newFileName = 'uploads/room_' . time() . '.' . $fileExtension;
            if (move_uploaded_file($fileTmpPath, $newFileName)) {
                $image_url = $newFileName;
            }
        }
    }

    $stmt = $conn->prepare("INSERT INTO rooms (room_number, type, price, status, image_url) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdss", $room_number, $type, $price, $status, $image_url);
    $stmt->execute();
    $stmt->close();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle room edit submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_room'])) {
    $edit_room_id = intval($_POST['edit_room_id']);
    $edit_room_number = $_POST['edit_room_number'];
    $edit_type = $_POST['edit_type'];
    $edit_price = $_POST['edit_price'];
    $edit_status = $_POST['edit_status'];

    $edit_image_url = null;
    if (isset($_FILES['edit_room_image']) && $_FILES['edit_room_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['edit_room_image']['tmp_name'];
        $fileName = basename($_FILES['edit_room_image']['name']);
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileExtension, $allowedExtensions)) {
            if (!is_dir('uploads')) {
                mkdir('uploads', 0755, true);
            }
            $newFileName = 'uploads/room_' . time() . '.' . $fileExtension;
            if (move_uploaded_file($fileTmpPath, $newFileName)) {
                $edit_image_url = $newFileName;
            }
        }
    }

    if ($edit_image_url) {
        $stmt = $conn->prepare("UPDATE rooms SET room_number = ?, type = ?, price = ?, status = ?, image_url = ? WHERE id = ?");
        $stmt->bind_param("ssdssi", $edit_room_number, $edit_type, $edit_price, $edit_status, $edit_image_url, $edit_room_id);
    } else {
        $stmt = $conn->prepare("UPDATE rooms SET room_number = ?, type = ?, price = ?, status = ? WHERE id = ?");
        $stmt->bind_param("ssdsi", $edit_room_number, $edit_type, $edit_price, $edit_status, $edit_room_id);
    }

    $stmt->execute();
    $stmt->close();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}


// Fetch all rooms
$result = $conn->query("SELECT * FROM rooms ORDER BY room_number");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin - Manage Rooms</title>
<link rel="stylesheet" href="css/style.css">
<style>
    body {
        font-family: Arial, sans-serif;
        background: #f4f6f9;
        padding: 2rem;
    }
    h2 {
        color: #1a237e;
        text-align: center;
        margin-bottom: 2rem;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
    }
    th, td {
        padding: 1rem;
        text-align: center;
        border-bottom: 1px solid #ddd;
    }
    th {
        background: #1a237e;
        color: white;
    }
    tr:hover {
        background: #f1f1f1;
    }
    img {
        width: 100px;
        height: auto;
        border-radius: 6px;
    }
    select, button, input, label {
        padding: 0.5rem 1rem;
        border-radius: 5px;
        border: 1px solid #ccc;
        font-size: 1rem;
    }
    button {
        background: #1a237e;
        color: white;
        cursor: pointer;
        transition: background 0.3s ease;
    }
    button:hover {
        background: #0f1a5a;
    }
    .update-form {
        display:flex;
        gap: 0.5rem;
        justify-content: center;
        margin-bottom: 0.3rem;
    }
    #addRoomBtn {
        display: inline-block;
        margin-bottom: 1rem;
        background: #4caf50;
        color: white;
        border: none;
        padding: 0.7rem 1.5rem;
        font-size: 1.1rem;
        border-radius: 50px;
        cursor: pointer;
        transition: background 0.3s ease;
    }
    #addRoomBtn:hover {
        background: #388e3c;
    }

    /* Modal styles */
    .modal {
        display: none; 
        position: fixed; 
        z-index: 1000; 
        left: 0;
        top: 0;
        width: 100%; 
        height: 100%; 
        overflow: auto;
        background-color: rgba(0,0,0,0.5); 
    }
    .modal-content {
        background-color: #fefefe;
        margin: 5% auto; 
        padding: 2rem;
        border: 1px solid #888;
        width: 400px; 
        border-radius: 10px;
        position: relative;
    }
    .close-btn {
        color: #aaa;
        position: absolute;
        right: 1rem;
        top: 1rem;
        font-size: 1.5rem;
        font-weight: bold;
        cursor: pointer;
    }
    .close-btn:hover,
    .close-btn:focus {
        color: black;
    }
    .modal-content label {
        display: block;
        margin-bottom: 0.5rem;
        color: #333;
        font-weight: 600;
    }
    .modal-content input[type="text"],
    .modal-content input[type="number"],
    .modal-content select,
    .modal-content input[type="file"] {
        width: 100%;
        margin-bottom: 1rem;
        padding: 0.5rem;
        font-size: 1rem;
        border-radius: 5px;
        border: 1px solid #ccc;
    }
    .modal-footer {
        display: flex;
        justify-content: space-between;
    }
    .btn-danger {
        background-color: #e53935;
    }
    .btn-danger:hover {
        background-color: #b71c1c;
    }
</style>
</head>
<body>
    <form action="admin_dashboard.php" method="get" style="margin-bottom: 1rem;">
    <button style="background-color: #1a237e; color: white; padding: 0.5rem 1rem; border: none; border-radius: 5px; cursor: pointer;">← Back to Dashboard</button>
</form>


<h2>Manage Rooms - The SunShine Living✨</h2>

<button id="addRoomBtn">+ Add New Room</button>

<table>
    <thead>
        <tr>
            <th>Room Number</th>
            <th>Type</th>
            <th>Status</th>
            <th>Price</th>
            <th>Image</th>
            <th>Update Status</th>
            <th>Edit</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $rooms = [];
        while ($room = $result->fetch_assoc()) {
            $rooms[$room['id']] = $room;
        }

        foreach ($rooms as $room):
        ?>
        <tr>
            <td><?=htmlspecialchars($room['room_number'])?></td>
            <td><?=htmlspecialchars($room['type'])?></td>
            <td><?=htmlspecialchars($room['status'])?></td>
            <td><?=htmlspecialchars(number_format($room['price'], 2))?></td>
            <td>
                <?php if ($room['image_url'] && file_exists($room['image_url'])): ?>
                    <img src="<?=htmlspecialchars($room['image_url'])?>" alt="Room Image">
                <?php else: ?>
                    No Image
                <?php endif; ?>
            </td>
            <td>
                <form class="update-form" method="POST" action="">
                    <input type="hidden" name="room_id" value="<?= $room['id'] ?>">
                    <select name="new_status" required>
                        <option value="available" <?= $room['status'] === 'available' ? 'selected' : '' ?>>Available</option>
                        <option value="booked" <?= $room['status'] === 'booked' ? 'selected' : '' ?>>Booked</option>
                        <option value="maintenance" <?= $room['status'] === 'maintenance' ? 'selected' : '' ?>>Maintenance</option>
                    </select>
                    <button type="submit">Update</button>
                </form>
            </td>
            <td>
                <button class="editBtn" data-roomid="<?= $room['id'] ?>">Edit</button>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Add Room Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" id="closeAddModal">&times;</span>
        <h3>Add New Room</h3>
        <form method="POST" action="" enctype="multipart/form-data">
            <input type="hidden" name="add_room" value="1">
            <label for="room_number">Room Number:</label>
            <input type="text" name="room_number" id="room_number" required>

            <label for="type">Type:</label>
            <input type="text" name="type" id="type" required>

            <label for="price">Price:</label>
            <input type="number" step="0.01" name="price" id="price" required>

            <label for="status">Status:</label>
            <select name="status" id="status" required>
                <option value="available" selected>Available</option>
                <option value="booked">Booked</option>
                <option value="maintenance">Maintenance</option>
            </select>

            <label for="room_image">Room Image (optional):</label>
            <input type="file" name="room_image" id="room_image" accept=".jpg,.jpeg,.png,.gif">

            <button type="submit">Add Room</button>
        </form>
    </div>
</div>

<!-- Edit Room Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" id="closeEditModal">&times;</span>
        <h3>Edit Room</h3>
        <form method="POST" action="" enctype="multipart/form-data" id="editRoomForm">
            <input type="hidden" name="edit_room" value="1">
            <input type="hidden" name="edit_room_id" id="edit_room_id">

            <label for="edit_room_number">Room Number:</label>
            <input type="text" name="edit_room_number" id="edit_room_number" required>

            <label for="edit_type">Type:</label>
            <input type="text" name="edit_type" id="edit_type" required>

            <label for="edit_price">Price:</label>
            <input type="number" step="0.01" name="edit_price" id="edit_price" required>

            <label for="edit_status">Status:</label>
            <select name="edit_status" id="edit_status" required>
                <option value="available">Available</option>
                <option value="booked">Booked</option>
                <option value="maintenance">Maintenance</option>
            </select>

            <label for="edit_room_image">Room Image (upload to replace):</label>
            <input type="file" name="edit_room_image" id="edit_room_image" accept=".jpg,.jpeg,.png,.gif">

            <div class="modal-footer">
                <button type="submit">Save Changes</button>

            </div>
        </form>
    </div>
</div>

<script>
// Rooms data from PHP
const rooms = <?= json_encode($rooms); ?>;

const addModal = document.getElementById('addModal');
const editModal = document.getElementById('editModal');
const addRoomBtn = document.getElementById('addRoomBtn');
const closeAddModalBtn = document.getElementById('closeAddModal');
const closeEditModalBtn = document.getElementById('closeEditModal');

addRoomBtn.addEventListener('click', () => {
    addModal.style.display = 'block';
});

closeAddModalBtn.addEventListener('click', () => {
    addModal.style.display = 'none';
});

closeEditModalBtn.addEventListener('click', () => {
    editModal.style.display = 'none';
});

// Close modals if user clicks outside modal content
window.addEventListener('click', (event) => {
    if (event.target === addModal) {
        addModal.style.display = 'none';
    }
    if (event.target === editModal) {
        editModal.style.display = 'none';
    }
});

// Close modals on ESC key
window.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        addModal.style.display = 'none';
        editModal.style.display = 'none';
    }
});

// Edit buttons logic
document.querySelectorAll('.editBtn').forEach(btn => {
    btn.addEventListener('click', () => {
        const roomId = btn.getAttribute('data-roomid');
        const room = rooms[roomId];

        if (!room) return;

        document.getElementById('edit_room_id').value = room.id;
        document.getElementById('edit_room_number').value = room.room_number;
        document.getElementById('edit_type').value = room.type;
        document.getElementById('edit_price').value = room.price;
        document.getElementById('edit_status').value = room.status;

        // Set delete form id
        //document.getElementById('delete_room_id').value = room.id;

        editModal.style.display = 'block';
    });
});
</script>

</body>
</html>
