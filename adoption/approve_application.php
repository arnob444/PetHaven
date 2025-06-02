<?php
session_start();
require_once '../../includes/config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PetHaven - Approve Application</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <header>
        <div class="logo">PetHaven</div>
        <nav>
            <a href="../../index.php">Home</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="../../dashboard.php">Dashboard</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </nav>
    </header>

    <?php
    if (!isset($_SESSION['user_id'])) {
        header('Location: ../../auth/login.php');
        exit();
    }

    $app_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $user_id = $_SESSION['user_id'];

    $query = "SELECT a.*, p.user_id AS owner_id FROM adoption_applications a JOIN pets p ON a.pet_id = p.id WHERE a.id = ? AND p.user_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $app_id, $user_id);
    mysqli_stmt_execute($stmt);
    $app = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$app) {
        header('Location: ../../dashboard.php');
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
        $status = $_POST['action'] == 'approve' ? 'approved' : 'rejected';
        $query = "UPDATE adoption_applications SET status = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "si", $status, $app_id);

        if (mysqli_stmt_execute($stmt)) {
            header('Location: ../../dashboard.php');
            exit();
        } else {
            $error = "Failed to update application.";
        }
    }
    ?>

    <section class="auth">
        <h2>Manage Application</h2>
        <?php if (isset($error)): ?><p style="color: red;"><?php echo $error; ?></p><?php endif; ?>
        <p>Application for a pet by user ID <?php echo htmlspecialchars($app['user_id']); ?>.</p>
        <form method="POST" action="approve_application.php?id=<?php echo $app_id; ?>">
            <button type="submit" name="action" value="approve" class="btn">Approve</button>
            <button type="submit" name="action" value="reject" class="btn" style="background: #DC3545;">Reject</button>
            <a href="../../dashboard.php" class="btn">Back to Dashboard</a>
        </form>
    </section>

    <footer>
        <div class="links">
            <div>
                <h4>Connect with Us</h4>
                <p>Email: <a href="mailto:pethaven@gmail.com">pethaven@gmail.com</a></p>
            </div>
            <div>
                <h4>Quick Links</h4>
                <p><a href="../../index.php">Home</a></p>
                <p><a href="../../search.php">Adoption</a></p>
            </div>
            <div>
                <h4>Follow Us</h4>
                <p><a href="#">Instagram</a></p>
            </div>
        </div>
    </footer>
</body>
</html>