<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$pet_id = isset($_GET['pet_id']) ? (int)$_GET['pet_id'] : 0;
$user_id = $_SESSION['user_id'];

// Fetch pet details
$pet_query = "SELECT p.*, u.id AS owner_id FROM pets p JOIN users u ON p.user_id = u.id WHERE p.id = ?";
$stmt = mysqli_prepare($conn, $pet_query);
mysqli_stmt_bind_param($stmt, "i", $pet_id);
mysqli_stmt_execute($stmt);
$pet = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$pet || $pet['owner_id'] == $user_id) {
    header('Location: ../index.php');
    exit();
}

// Check if the user has already applied
$app_query = "SELECT id FROM adoption_applications WHERE pet_id = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $app_query);
mysqli_stmt_bind_param($stmt, "ii", $pet_id, $user_id);
mysqli_stmt_execute($stmt);
$app_result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($app_result) > 0) {
    $error = "You have already applied to adopt this pet.";
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $query = "INSERT INTO adoption_applications (pet_id, user_id, status) VALUES (?, ?, 'pending')";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $pet_id, $user_id);

    if (mysqli_stmt_execute($stmt)) {
        header('Location: view_applications.php');
        exit();
    } else {
        $error = "Failed to submit application.";
    }
}
?>

<section class="auth">
    <h2>Apply to Adopt <?php echo htmlspecialchars($pet['name']); ?></h2>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <p>Are you sure you want to apply to adopt <?php echo htmlspecialchars($pet['name']); ?>?</p>
    <form method="POST" action="apply.php?pet_id=<?php echo $pet_id; ?>">
        <button type="submit" class="btn">Submit Application</button>
        <a href="../index.php" class="btn">Cancel</a>
    </form>
</section>

<?php require_once '../includes/footer.php'; ?>