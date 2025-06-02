<?php
session_start();
require_once '../includes/config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PetHaven - Edit Medical Record</title>
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

    $query = "SELECT m.*, p.user_id FROM medical_records m JOIN pets p ON m.pet_id = p.id WHERE m.id = ? AND p.user_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $record_id, $user_id);
    mysqli_stmt_execute($stmt);
    $record = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    $pets_query = "SELECT id, name FROM pets WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $pets_query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $pets_result = mysqli_stmt_get_result($stmt);

    if (!$record) {
        header('Location: ../dashboard.php');
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $pet_id = (int)$_POST['pet_id'];
        $vaccine_name = mysqli_real_escape_string($conn, $_POST['vaccine_name']);
        $vaccine_date = mysqli_real_escape_string($conn, $_POST['vaccine_date']);

        $verify_query = "SELECT id FROM pets WHERE id = ? AND user_id = ?";
        $stmt = mysqli_prepare($conn, $verify_query);
        mysqli_stmt_bind_param($stmt, "ii", $pet_id, $user_id);
        mysqli_stmt_execute($stmt);
        $pet = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if (!$pet) {
            $error = "Invalid pet selected.";
        } else {
            $update_query = "UPDATE medical_records SET pet_id = ?, vaccine_name = ?, vaccine_date = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $update_query);
            mysqli_stmt_bind_param($stmt, "issi", $pet_id, $vaccine_name, $vaccine_date, $record_id);

            if (mysqli_stmt_execute($stmt)) {
                header('Location: ../dashboard.php');
                exit();
            } else {
                $error = "Failed to update medical record.";
            }
        }
    }
    ?>

    <section class="auth">
        <h2>Edit Medical Record</h2>
        <?php if (isset($error)): ?><p style="color: red;"><?php echo $error; ?></p><?php endif; ?>
        <form method="POST" action="edit_medical.php?id=<?php echo $record_id; ?>">
            <div>
                <label for="pet_id">Pet:</label>
                <select id="pet_id" name="pet_id" required>
                    <option value="">Select a pet</option>
                    <?php while ($pet = mysqli_fetch_assoc($pets_result)): ?>
                        <option value="<?php echo $pet['id']; ?>" <?php echo $record['pet_id'] == $pet['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($pet['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>
                <label for="vaccine_name">Vaccine Name:</label>
                <input type="text" id="vaccine_name" name="vaccine_name" value="<?php echo htmlspecialchars($record['vaccine_name']); ?>" required>
            </div>
            <div>
                <label for="vaccine_date">Vaccine Date:</label>
                <input type="date" id="vaccine_date" name="vaccine_date" value="<?php echo htmlspecialchars($record['vaccine_date']); ?>" required>
            </div>
            <button type="submit" class="btn">Update Record</button>
            <a href="../dashboard.php" class="btn">Back to Dashboard</a>
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