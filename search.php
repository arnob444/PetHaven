<?php
session_start();
require_once 'includes/config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PetHaven - Search</title>
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

    <section class="search-filter">
        <h3>Find Your Perfect Pet</h3>
        <form method="GET" action="search.php" class="filter-form">
            <select name="category">
                <option value="">Select Category</option>
                <option value="dog" <?php echo isset($_GET['category']) && $_GET['category'] == 'dog' ? 'selected' : ''; ?>>Dog</option>
                <option value="cat" <?php echo isset($_GET['category']) && $_GET['category'] == 'cat' ? 'selected' : ''; ?>>Cat</option>
                <option value="other" <?php echo isset($_GET['category']) && $_GET['category'] == 'other' ? 'selected' : ''; ?>>Other</option>
            </select>
            <input type="number" name="age" placeholder="Age" min="0" value="<?php echo isset($_GET['age']) ? htmlspecialchars($_GET['age']) : ''; ?>">
            <input type="text" name="breed" placeholder="Breed" value="<?php echo isset($_GET['breed']) ? htmlspecialchars($_GET['breed']) : ''; ?>">
            <input type="text" name="location" placeholder="Location" value="<?php echo isset($_GET['location']) ? htmlspecialchars($_GET['location']) : ''; ?>">
            <button type="submit" class="btn">Search</button>
        </form>
    </section>

    <section class="featured-pets">
        <h2>Search Results</h2>
        <div class="pet-grid">
            <?php
            $where = "WHERE 1=1";
            $params = [];
            if (isset($_GET['category']) && !empty($_GET['category'])) {
                $where .= " AND category = ?";
                $params[] = $_GET['category'];
            }
            if (isset($_GET['age']) && !empty($_GET['age'])) {
                $where .= " AND age = ?";
                $params[] = (int)$_GET['age'];
            }
            if (isset($_GET['breed']) && !empty($_GET['breed'])) {
                $where .= " AND breed LIKE ?";
                $params[] = "%" . $_GET['breed'] . "%";
            }
            if (isset($_GET['location']) && !empty($_GET['location'])) {
                $where .= " AND location LIKE ?";
                $params[] = "%" . $_GET['location'] . "%";
            }

            $query = "SELECT * FROM pets $where ORDER BY created_at DESC";
            $stmt = mysqli_prepare($conn, $query);
            if (!empty($params)) {
                $types = str_repeat('s', count($params));
                mysqli_stmt_bind_param($stmt, $types, ...$params);
            }
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            while ($pet = mysqli_fetch_assoc($result)): ?>
                <div class="pet-card">
                    <?php if ($pet['photo']): ?>
                        <img src="assets/images/uploads/<?php echo htmlspecialchars($pet['photo']); ?>" alt="<?php echo htmlspecialchars($pet['name']); ?>">
                    <?php else: ?>
                        <img src="assets/images/placeholder.jpg" alt="Pet Placeholder">
                    <?php endif; ?>
                    <h3><?php echo htmlspecialchars($pet['name']); ?></h3>
                    <p><strong>Breed:</strong> <?php echo htmlspecialchars($pet['breed']); ?></p>
                    <p><strong>Age:</strong> <?php echo htmlspecialchars($pet['age']); ?> years</p>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($pet['location']); ?></p>
                    <a href="pet/view_pet.php?id=<?php echo $pet['id']; ?>" class="btn">Meet <?php echo htmlspecialchars($pet['name']); ?></a>
                </div>
            <?php endwhile; ?>
            <?php if (mysqli_num_rows($result) == 0): ?>
                <p>No pets found matching your criteria.</p>
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