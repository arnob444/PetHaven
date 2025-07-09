<?php
session_start();
require_once '../includes/config.php';

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

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="" />
    <link
      rel="stylesheet"
      as="style"
      onload="this.rel='stylesheet'"
      href="https://fonts.googleapis.com/css2?display=swap&family=Noto+Sans%3Awght%40400%3B500%3B700%3B900&family=Plus+Jakarta+Sans%3Awght%40400%3B500%3B700%3B800"
    />

    <title>PetHaven - View Pet</title>
    <link rel="icon" type="image/x-icon" href="data:image/x-icon;base64," />

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        function toggleSellerDetails() {
            const card = document.getElementById('sellerDetailsCard');
            card.classList.toggle('hidden');
        }
    </script>
</head>

<body>
    <div class="relative flex size-full min-h-screen flex-col bg-white group/design-root overflow-x-hidden" style='font-family: "Plus Jakarta Sans", "Noto Sans", sans-serif;'>
      <div class="layout-container flex h-full grow flex-col">
        <header class="flex items-center justify-between whitespace-nowrap border-b border-solid border-b-[#f5f2f0] px-10 py-3">
          <div class="flex items-center gap-4 text-[#181511]">
            <div class="size-4">
              <img src="../assets/images/icons/logo.png" alt="PetHaven Logo" class="w-5 h-4" />
            </div>
            <h2 class="text-[#181511] text-lg font-bold leading-tight tracking-[-0.015em]">PetHaven</h2>
          </div>
          <div class="flex flex-1 justify-end gap-8">
            <div class="flex items-center gap-9">
              <a class="text-[#181511] text-sm font-medium leading-normal" href="../index.php">Home</a>
              <a class="text-[#181511] text-sm font-medium leading-normal" href="../search.php?listing_type=adoption">Adopt</a>
              <a class="text-[#181511] text-sm font-medium leading-normal" href="../search.php?listing_type=buy_sell">Buy/Sell</a>
            </div>
            <div class="flex gap-2">
              <?php if (isset($_SESSION['user_id'])): ?>
                <a href="../dashboard.php" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#f39224] text-[#181511] text-sm font-bold leading-normal tracking-[0.015em]">
                  <span class="truncate">Dashboard</span>
                </a>
                <a href="../auth/logout.php" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#f5f2f0] text-[#181511] text-sm font-bold leading-normal tracking-[0.015em]">
                  <span class="truncate">Logout</span>
                </a>
              <?php else: ?>
                <a href="../auth/login.php" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#f39224] text-[#181511] text-sm font-bold leading-normal tracking-[0.015em]">
                  <span class="truncate">Log in</span>
                </a>
                <a href="../auth/register.php" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#f5f2f0] text-[#181511] text-sm font-bold leading-normal tracking-[0.015em]">
                  <span class="truncate">Sign up</span>
                </a>
              <?php endif; ?>
            </div>
          </div>
        </header>
        <div class="px-40 flex flex-1 justify-center py-5">
          <div class="layout-content-container flex flex-col w-[512px] max-w-[512px] py-5 max-w-[960px] flex-1">
            <h2 class="text-[#181511] tracking-light text-[28px] font-bold leading-tight px-4 text-center pb-3 pt-5"><?php echo htmlspecialchars($pet['name']); ?></h2>
            <div class="flex flex-col gap-6 px-4 py-3">
              <div
                class="w-full bg-center bg-no-repeat aspect-[3/2] bg-cover rounded-xl"
                style='background-image: url("<?php echo $pet['photo'] ? "../assets/images/uploads/" . htmlspecialchars(basename($pet['photo'])) : "../assets/images/placeholder.jpg"; ?>");'
              ></div>
              <div class="flex flex-col gap-2">
                <p class="text-[#181511] text-base font-medium leading-normal"><strong>Type:</strong> <?php echo ucfirst(htmlspecialchars($pet['listing_type'])); ?></p>
                <?php if ($pet['listing_type'] == 'buy_sell' && $pet['price']): ?>
                  <p class="text-[#181511] text-base font-medium leading-normal"><strong>Price:</strong> $<?php echo number_format($pet['price'], 2); ?></p>
                <?php endif; ?>
                <p class="text-[#181511] text-base font-medium leading-normal"><strong>Breed:</strong> <?php echo htmlspecialchars($pet['breed'] ? $pet['breed'] : "Unknown"); ?></p>
                <p class="text-[#181511] text-base font-medium leading-normal"><strong>Age:</strong> <?php echo htmlspecialchars($pet['age'] ? $pet['age'] . " years" : "Unknown"); ?></p>
                <p class="text-[#181511] text-base font-medium leading-normal"><strong>Category:</strong> <?php echo htmlspecialchars($pet['category'] ? $pet['category'] : "Unknown"); ?></p>
                <p class="text-[#181511] text-base font-medium leading-normal"><strong>Location:</strong> <?php echo htmlspecialchars($pet['location'] ? $pet['location'] : "Unknown"); ?></p>
                <p class="text-[#181511] text-base font-medium leading-normal"><strong>Posted by:</strong> <?php echo htmlspecialchars($pet['username']); ?></p>
                <p class="text-[#181511] text-base font-medium leading-normal"><strong>Posted on:</strong> <?php echo htmlspecialchars(date('Y-m-d', strtotime($pet['created_at']))); ?></p>
              </div>
              <div class="flex flex-wrap gap-3">
                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $pet['user_id']): ?>
                  <?php if ($pet['listing_type'] == 'adoption'): ?>
                    <?php if (!$has_applied): ?>
                      <a href="../adoption/apply.php?pet_id=<?php echo $pet['id']; ?>" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#f39224] text-[#181511] text-sm font-bold leading-normal tracking-[0.015em]">
                        <span class="truncate">Apply to Adopt</span>
                      </a>
                    <?php else: ?>
                      <p class="text-[#181511] text-sm font-normal leading-normal px-4 py-2 bg-[#f5f2f0] rounded-xl">You have already applied to adopt this pet.</p>
                    <?php endif; ?>
                  <?php elseif ($pet['listing_type'] == 'buy_sell'): ?>
                    <button onclick="toggleSellerDetails()" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#f39224] text-[#181511] text-sm font-bold leading-normal tracking-[0.015em]">
                      <span class="truncate">Contact Seller</span>
                    </button>
                  <?php endif; ?>
                <?php endif; ?>
                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $pet['user_id']): ?>
                  <a href="edit_pet.php?id=<?php echo $pet['id']; ?>" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#f39224] text-[#181511] text-sm font-bold leading-normal tracking-[0.015em]">
                    <span class="truncate">Edit</span>
                  </a>
                  <a href="delete_pet.php?id=<?php echo $pet['id']; ?>" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#f5f2f0] text-[#181511] text-sm font-bold leading-normal tracking-[0.015em]" onclick="return confirm('Are you sure?')">
                    <span class="truncate">Delete</span>
                  </a>
                <?php endif; ?>
                <a href="../index.php" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#f5f2f0] text-[#181511] text-sm font-bold leading-normal tracking-[0.015em]">
                  <span class="truncate">Back to Home</span>
                </a>
              </div>
            </div>
          </div>
        </div>
        <?php include '../includes/footer.php'; ?>
      </div>

      <!-- Seller Details Card -->
      <div id="sellerDetailsCard" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white p-6 rounded-xl shadow-lg w-[400px] max-w-[90%]">
          <h3 class="text-[#181511] text-lg font-bold leading-tight mb-4">Seller Details</h3>
          <div class="flex flex-col gap-2">
            <p class="text-[#181511] text-base font-medium leading-normal"><strong>Username:</strong> <?php echo htmlspecialchars($pet['username']); ?></p>
            <p class="text-[#181511] text-base font-medium leading-normal"><strong>Email:</strong> <?php echo htmlspecialchars($pet['email']); ?></p>
          </div>
          <button onclick="toggleSellerDetails()" class="mt-4 flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#f5f2f0] text-[#181511] text-sm font-bold leading-normal tracking-[0.015em]">
            <span class="truncate">Close</span>
          </button>
        </div>
      </div>
    </div>
</body>

</html>