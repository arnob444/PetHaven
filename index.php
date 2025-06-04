<?php
session_start();
require_once 'includes/config.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PetHaven</title>
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

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Looking to Adopt a Pet?</h1>
            <p>Explore pets up for adoption and bring your new companion home!</p>
            <a href="#featured-pets" class="btn">Explore Now</a>
        </div>
    </section>

    <!-- Welcome Section -->
    <section class="welcome">
        <h2>Welcome to <span>PetHaven</span>.</h2>
        <ul>
            <li><strong>Connect</strong> - A dedicated pet-friendly platform to showcase pets in care, buy, adopt, sell, and more.</li>
            <li><strong>Support</strong> - Rescue, foster, and adoption charities for pets in need.</li>
            <li><strong>Individuals</strong> - Easily find, adopt, and manage your perfect pet match.</li>
        </ul>
    </section>

    <!-- Search/Filter Section -->
    <?php
    // include 'includes/header.php';
    ?>

    <section class="search-filter">
        <h3>Find Your Perfect Pet</h3>
        <form method="GET" action="search.php" class="filter-form">
            <select name="category">
                <option value="">Select Category</option>
                <option value="dog">Dog</option>
                <option value="cat">Cat</option>
                <option value="other">Other</option>
            </select>
            <input type="number" name="age" placeholder="Age" min="0">
            <input type="text" name="breed" placeholder="Breed">
            <input type="text" name="location" placeholder="Location">
            <select name="listing_type">
                <option value="">All Types</option>
                <option value="adoption">Adoption</option>
                <option value="buy_sell">Buy/Sell</option>
            </select>
            <button type="submit" class="btn">Search</button>
        </form>
    </section>

    <section id="featured-pets" class="featured-pets">
        <h2>Featured Pets</h2>
        <p>Meet adorable pets waiting for their forever homes or purchase</p>
        <a href="search.php" class="view-all">View All Pets →</a>
        <div class="pet-grid">
            <?php
            $query = "SELECT * FROM pets ORDER BY created_at DESC LIMIT 6";
            $result = mysqli_query($conn, $query);
            while ($pet = mysqli_fetch_assoc($result)): ?>
                <div class="pet-card">
                    <?php if ($pet['photo']): ?>
                        <img src="assets/images/uploads/<?php echo htmlspecialchars($pet['photo']); ?>" alt="<?php echo htmlspecialchars($pet['name']); ?>">
                    <?php else: ?>
                        <img src="assets/images/placeholder.jpg" alt="Pet Placeholder">
                    <?php endif; ?>
                    <h3><?php echo htmlspecialchars($pet['name']); ?></h3>
                    <p><strong>Type:</strong> <?php echo ucfirst(htmlspecialchars($pet['listing_type'])); ?></p>
                    <?php if ($pet['listing_type'] == 'buy_sell' && $pet['price']): ?>
                        <p><strong>Price:</strong> $<?php echo number_format($pet['price'], 2); ?></p>
                    <?php endif; ?>
                    <p><strong>Breed:</strong> <?php echo htmlspecialchars($pet['breed']); ?></p>
                    <p><strong>Age:</strong> <?php echo htmlspecialchars($pet['age']); ?> years</p>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($pet['location']); ?></p>
                    <a href="pet/view_pet.php?id=<?php echo $pet['id']; ?>" class="btn">View Details</a>
                </div>
            <?php endwhile; ?>
        </div>
    </section>

    <footer>
        <div class="links">
            <div>
                <h4>Connect with Us</h4>
                <p>Email: <a href="dasarnob@gmail.com">pethaven@gmail.com</a></p>
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