<?php
session_start();
require_once '../includes/config.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PetHaven - View Medical Records</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>

<body>
    <header>
        <div class="logo">PetHaven</div>
        <nav>
            <a href="../index.php">Home</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="../dashboard.php">Dashboard</a>
                <a href="../auth/logout.php">Logout</a>
            <?php else: ?>
                <a href="../auth/login.php">Login</a>
                <a href="../auth/register.php">Register</a>
            <?php endif; ?>
        </nav>
    </header>

    <?php
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../auth/login.php');
        exit();
    }

    $pet_id = isset($_GET['pet_id']) ? (int)$_GET['pet_id'] : 0;
    $user_id = $_SESSION['user_id'];

    $pet_query = "SELECT name FROM pets WHERE id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $pet_query);
    mysqli_stmt_bind_param($stmt, "ii", $pet_id, $user_id);
    mysqli_stmt_execute($stmt);
    $pet = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$pet) {
        header('Location: ../dashboard.php');
        exit();
    }

    $records_query = "SELECT * FROM medical_records WHERE pet_id = ? ORDER BY created_at DESC";
    $stmt = mysqli_prepare($conn, $records_query);
    mysqli_stmt_bind_param($stmt, "i", $pet_id);
    mysqli_stmt_execute($stmt);
    $records_result = mysqli_stmt_get_result($stmt);
    ?>

    <section class="dashboard">
        <h2>Medical Records for <?php echo htmlspecialchars($pet['name']); ?></h2>
        <div class="pet-grid">
            <?php while ($record = mysqli_fetch_assoc($records_result)): ?>
                <div class="pet-card">
                    <h3><?php echo htmlspecialchars($record['vaccine_name']); ?></h3>
                    <p><strong>Date:</strong> <?php echo htmlspecialchars($record['vaccine_date']); ?></p>
                    <p><strong>Added:</strong> <?php echo htmlspecialchars($record['created_at']); ?></p>
                    <a href="edit_medical.php?id=<?php echo $record['id']; ?>" class="btn">Edit</a>
                    <a href="delete_medical.php?id=<?php echo $record['id']; ?>" class="btn" onclick="return confirm('Are you sure?')">Delete</a>
                </div>
            <?php endwhile; ?>
            <?php if (mysqli_num_rows($records_result) == 0): ?>
                <p>No medical records found for this pet. <a href="add_medical.php">Add a record</a></p>
            <?php endif; ?>
        </div>
        <a href="../dashboard.php" class="btn">Back to Dashboard</a>
    </section>

    <footer>
        <div class="links">
            <div>
                <h4>Connect with Us</h4>
                <p>Email: <a href="mailto:pethaven@gmail.com">pethaven@gmail.com</a></p>
            </div>
            <div>
                <h4>Quick Links</h4>
                <p><a href="../index.php">Home</a></p>
                <p><a href="../search.php">Adoption</a></p>
            </div>
            <div