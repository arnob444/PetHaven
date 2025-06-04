<?php
session_start();
require_once '../includes/config.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PetHaven - Add Pet</title>
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
    // include '../includes/header.php';

    if (!isset($_SESSION['user_id'])) {
        header('Location: ../auth/login.php');
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $user_id = $_SESSION['user_id'];
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $breed = mysqli_real_escape_string($conn, $_POST['breed']);
        $age = (int)$_POST['age'];
        $category = mysqli_real_escape_string($conn, $_POST['category']);
        $location = mysqli_real_escape_string($conn, $_POST['location']);
        $listing_type = mysqli_real_escape_string($conn, $_POST['listing_type']);
        $price = $listing_type == 'buy_sell' ? mysqli_real_escape_string($conn, $_POST['price']) : null;
        $photo = $_FILES['photo']['name'];
        $photo_tmp = $_FILES['photo']['tmp_name'];

        if ($photo) {
            $photo_path = "../assets/images/uploads/" . basename($photo);
            move_uploaded_file($photo_tmp, $photo_path);
        } else {
            $photo_path = null;
        }

        $query = "INSERT INTO pets (user_id, name, breed, age, category, photo, location, listing_type, price) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "isssssssd", $user_id, $name, $breed, $age, $category, $photo_path, $location, $listing_type, $price);

        if (mysqli_stmt_execute($stmt)) {
            header('Location: ../dashboard.php');
            exit();
        } else {
            $error = "Failed to add pet.";
        }
    }
    ?>

    <section class="auth">
        <h2>Add a New Pet</h2>
        <?php if (isset($error)): ?><p style="color: red;"><?php echo $error; ?></p><?php endif; ?>
        <form method="POST" action="add_pet.php" enctype="multipart/form-data">
            <div>
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div>
                <label for="breed">Breed:</label>
                <input type="text" id="breed" name="breed">
            </div>
            <div>
                <label for="age">Age:</label>
                <input type="number" id="age" name="age" min="0" required>
            </div>
            <div>
                <label for="category">Category:</label>
                <select id="category" name="category" required>
                    <option value="dog">Dog</option>
                    <option value="cat">Cat</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div>
                <label for="location">Location:</label>
                <input type="text" id="location" name="location">
            </div>
            <div>
                <label for="listing_type">Listing Type:</label>
                <select id="listing_type" name="listing_type" required>
                    <option value="adoption">Adoption</option>
                    <option value="buy_sell">Buy/Sell</option>
                </select>
            </div>
            <div id="price_field" style="display: none;">
                <label for="price">Price (USD):</label>
                <input type="number" id="price" name="price" min="0" step="0.01" placeholder="Enter price">
            </div>
            <div>
                <label for="photo">Photo:</label>
                <input type="file" id="photo" name="photo" accept="image/*">
            </div>
            <button type="submit" class="btn">Add Pet</button>
            <a href="../dashboard.php" class="btn">Back to Dashboard</a>
        </form>
    </section>

    <script>
        document.getElementById('listing_type').addEventListener('change', function() {
            var priceField = document.getElementById('price_field');
            if (this.value === 'buy_sell') {
                priceField.style.display = 'block';
                document.getElementById('price').setAttribute('required', 'required');
            } else {
                priceField.style.display = 'none';
                document.getElementById('price').removeAttribute('required');
            }
        });
    </script>

    <?php include '../includes/footer.php'; ?>

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