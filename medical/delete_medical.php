<?php
session_start();
require_once '../includes/config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PetHaven - Delete Medical Record</title>
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

    $record_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $user_id = $_SESSION['user_id'];

    $query = "SELECT m.id FROM medical_records m JOIN pets p ON m.pet_id = p.id WHERE m.id = ? AND p.user_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $record_id, $user_id);
    mysqli_stmt_execute($stmt);
    $record = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$record) {
        header('Location: ../dashboard.php');
        exit();
    }

    $delete_query = "DELETE FROM medical_records WHERE id = ?";
    $stmt = mysqli_prepare($conn, $delete_query);
    mysqli_stmt_bind_param($stmt, "i", $record_id);

    if (mysqli_stmt_execute($stmt)) {
        header('Location: ../dashboard.php');
        exit();
    } else {
        $error = "Failed to delete medical record.";
    }
    ?>

    <section class="auth">
        <h2>Delete Medical Record</h2>
        <?php if (isset($error)): ?><p style="color: red;"><?php echo $error; ?></p><?php endif; ?>
        <p>Medical record deleted successfully.</p>
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
            <div>
                <h4>Follow Us</h4>
                <p><a href="#">Instagram</a></p>
            </div>
        </div>
    </footer>
</body>
</html>