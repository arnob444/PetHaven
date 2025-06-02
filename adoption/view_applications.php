<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch adoption applications made by the user
$query = "SELECT a.*, p.name AS pet_name FROM adoption_applications a JOIN pets p ON a.pet_id = p.id WHERE a.user_id = ? ORDER BY a.created_at DESC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$applications_result = mysqli_stmt_get_result($stmt);
?>

<section class="dashboard">
    <h2>My Adoption Applications</h2>
    <div class="pet-grid">
        <?php while ($app = mysqli_fetch_assoc($applications_result)): ?>
            <div class="pet-card">
                <h3>Pet: <?php echo htmlspecialchars($app['pet_name']); ?></h3>
                <p><strong>Status:</strong> <?php echo htmlspecialchars($app['status']); ?></p>
                <p><strong>Applied on:</strong> <?php echo htmlspecialchars($app['created_at']); ?></p>
            </div>
        <?php endwhile; ?>
        <?php if (mysqli_num_rows($applications_result) == 0): ?>
            <p>You have not applied for any pets yet. <a href="../index.php">Browse pets</a></p>
        <?php endif; ?>
    </div>
    <a href="../dashboard.php" class="btn">Back to Dashboard</a>
</section>

<?php require_once '../includes/footer.php'; ?>