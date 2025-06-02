<?php
session_start();
require_once 'includes/config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PetHaven - Dashboard</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <header>
        <div class="logo">PetHaven</div>
        <nav>
            <a href="index.php">Home</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="dashboard.php">Dashboard</a>
                <a href="auth/logout.php">Logout</a>
            <?php else: ?>
                <a href="auth/login.php">Login</a>
                <a href="auth/register.php">Register</a>
            <?php endif; ?>
        </nav>
    </header>

    <?php
    if (!isset($_SESSION['user_id'])) {
        header('Location: auth/login.php');
        exit();
    }

    $user_id = $_SESSION['user_id'];

    // Fetch user's pets
    $pets_query = "SELECT * FROM pets WHERE user_id = ? ORDER BY created_at DESC";
    $stmt = mysqli_prepare($conn, $pets_query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $pets_result = mysqli_stmt_get_result($stmt);

    // Fetch adoption requests (received)
    $applications_query = "SELECT a.*, p.name AS pet_name FROM adoption_applications a JOIN pets p ON a.pet_id = p.id WHERE p.user_id = ? ORDER BY a.created_at DESC";
    $stmt = mysqli_prepare($conn, $applications_query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $applications_result = mysqli_stmt_get_result($stmt);

    // Fetch medical logs
    $medical_query = "SELECT m.*, p.name AS pet_name FROM medical_records m JOIN pets p ON m.pet_id = p.id WHERE p.user_id = ? ORDER BY m.created_at DESC";
    $stmt = mysqli_prepare($conn, $medical_query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $medical_result = mysqli_stmt_get_result($stmt);
    ?>

    <section class="dashboard">
        <h2>Dashboard</h2>

        <h3>My Pets</h3>
        <div class="pet-grid">
            <?php while ($pet = mysqli_fetch_assoc($pets_result)): ?>
                <div class="pet-card">
                    <h3><?php echo htmlspecialchars($pet['name']); ?></h3>
                    <p><strong>Breed:</strong> <?php echo htmlspecialchars($pet['breed']); ?></p>
                    <a href="pet/edit_pet.php?id=<?php echo $pet['id']; ?>" class="btn">Edit</a>
                    <a href="pet/delete_pet.php?id=<?php echo $pet['id']; ?>" class="btn" onclick="return confirm('Are you sure?')">Delete</a>
                </div>
            <?php endwhile; ?>
            <?php if (mysqli_num_rows($pets_result) == 0): ?>
                <p>No pets posted yet. <a href="pet/add_pet.php">Add a pet</a></p>
            <?php endif; ?>
        </div>

        <h3>Adoption Requests</h3>
        <div class="pet-grid">
            <?php while ($app = mysqli_fetch_assoc($applications_result)): ?>
                <div class="pet-card">
                    <h3>Pet: <?php echo htmlspecialchars($app['pet_name']); ?></h3>
                    <p><strong>Status:</strong> <?php echo htmlspecialchars($app['status']); ?></p>
                    <?php if ($app['status'] == 'pending'): ?>
                        <a href="adoption/approve_application.php?id=<?php echo $app['id']; ?>" class="btn">Approve</a>
                        <a href="adoption/delete_application.php?id=<?php echo $app['id']; ?>" class="btn" style="background: #DC3545;">Reject</a>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
            <?php if (mysqli_num_rows($applications_result) == 0): ?>
                <p>No adoption requests yet.</p>
            <?php endif; ?>
        </div>

        <h3>Medical Logs</h3>
        <div class="pet-grid">
            <?php while ($medical = mysqli_fetch_assoc($medical_result)): ?>
                <div class="pet-card">
                    <h3>Pet: <?php echo htmlspecialchars($medical['pet_name']); ?></h3>
                    <p><strong>Vaccine:</strong> <?php echo htmlspecialchars($medical['vaccine_name']); ?></p>
                    <p><strong>Date:</strong> <?php echo htmlspecialchars($medical['vaccine_date']); ?></p>
                    <a href="medical/edit_medical.php?id=<?php echo $medical['id']; ?>" class="btn">Edit</a>
                    <a href="medical/delete_medical.php?id=<?php echo $medical['id']; ?>" class="btn" onclick="return confirm('Are you sure?')">Delete</a>
                </div>
            <?php endwhile; ?>
            <?php if (mysqli_num_rows($medical_result) == 0): ?>
                <p>No medical records yet. <a href="medical/add_medical.php">Add a record</a></p>
            <?php endif; ?>
        </div>
    </section>

    <footer>
        <div class="links">
            <div>
                <h4>Connect with Us</h4>
                <p>Email: <a href="mailto:pethaven@gmail.com">pethaven@gmail.com</a></p>
            </div>
            <div>
                <h4>Quick Links</h4>
                <p><a href="index.php">Home</a></p>
                <p><a href="search.php">Adoption</a></p>
            </div>
            <div>
                <h4>Follow Us</h4>
                <p><a href="#">Instagram</a></p>
            </div>
        </div>
    </footer>
</body>
</html>