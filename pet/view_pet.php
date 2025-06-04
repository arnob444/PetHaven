<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/header.php';

$pet_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$query = "SELECT p.*, u.username, u.email FROM pets p JOIN users u ON p.user_id = u.id WHERE p.id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $pet_id);
mysqli_stmt_execute($stmt);
$pet = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$pet) {
    header('Location: ../index.php');
    exit();
}

$has_applied = false;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $app_query = "SELECT id FROM adoption_applications WHERE pet_id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $app_query);
    mysqli_stmt_bind_param($stmt, "ii", $pet_id, $user_id);
    mysqli_stmt_execute($stmt);
    $app_result = mysqli_stmt_get_result($stmt);
    $has_applied = mysqli_num_rows($app_result) > 0;
}
?>

<section class="dashboard">
    <h2><?php echo htmlspecialchars($pet['name']); ?></h2>
    <div class="pet-card">
        <?php if ($pet['photo']): ?>
            <img src="../assets/images/uploads/<?php echo htmlspecialchars($pet['photo']); ?>" alt="<?php echo htmlspecialchars($pet['name']); ?>">
        <?php else: ?>
            <img src="../assets/images/placeholder.jpg" alt="Pet Placeholder">
        <?php endif; ?>
        <p><strong>Type:</strong> <?php echo ucfirst(htmlspecialchars($pet['listing_type'])); ?></p>
        <?php if ($pet['listing_type'] == 'buy_sell' && $pet['price']): ?>
            <p><strong>Price:</strong> $<?php echo number_format($pet['price'], 2); ?></p>
        <?php endif; ?>
        <p><strong>Breed:</strong> <?php echo htmlspecialchars($pet['breed']); ?></p>
        <p><strong>Age:</strong> <?php echo htmlspecialchars($pet['age']); ?> years</p>
        <p><strong>Category:</strong> <?php echo htmlspecialchars($pet['category']); ?></p>
        <p><strong>Location:</strong> <?php echo htmlspecialchars($pet['location']); ?></p>
        <p><strong>Posted by:</strong> <?php echo htmlspecialchars($pet['username']); ?></p>
        <p><strong>Posted on:</strong> <?php echo htmlspecialchars($pet['created_at']); ?></p>
        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $pet['user_id']): ?>
            <?php if ($pet['listing_type'] == 'adoption'): ?>
                <?php if (!$has_applied): ?>
                    <a href="../adoption/apply.php?pet_id=<?php echo $pet['id']; ?>" class="btn">Apply to Adopt</a>
                <?php else: ?>
                    <p style="color: green;">You have already applied to adopt this pet.</p>
                <?php endif; ?>
            <?php elseif ($pet['listing_type'] == 'buy_sell'): ?>
                <a href="mailto:<?php echo htmlspecialchars($pet['email']); ?>?subject=Inquiry about <?php echo htmlspecialchars($pet['name']); ?>" class="btn">Contact Seller</a>
            <?php endif; ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $pet['user_id']): ?>
            <a href="edit_pet.php?id=<?php echo $pet['id']; ?>" class="btn">Edit</a>
            <a href="delete_pet.php?id=<?php echo $pet['id']; ?>" class="btn" onclick="return confirm('Are you sure?')">Delete</a>
        <?php endif; ?>
        <a href="../index.php" class="btn">Back to Home</a>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>