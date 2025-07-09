<?php
session_start();
require_once 'includes/config.php';

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

    <title>PetHaven - Dashboard</title>
    <link rel="icon" type="image/x-icon" href="data:image/x-icon;base64," />

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
</head>

<body>
    <div class="relative flex size-full min-h-screen flex-col bg-white group/design-root overflow-x-hidden" style='font-family: "Plus Jakarta Sans", "Noto Sans", sans-serif;'>
      <div class="layout-container flex h-full grow flex-col">
        <header class="flex items-center justify-between whitespace-nowrap border-b border-solid border-b-[#f5f2f0] px-10 py-3">
          <div class="flex items-center gap-4 text-[#181511]">
            <div class="size-4">
              <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M24 4H6V17.3333V30.6667H24V44H42V30.6667V17.3333H24V4Z" fill="currentColor"></path>
              </svg>
            </div>
            <h2 class="text-[#181511] text-lg font-bold leading-tight tracking-[-0.015em]">PetHaven</h2>
          </div>
          <div class="flex flex-1 justify-end gap-8">
            <div class="flex items-center gap-9">
              <a class="text-[#181511] text-sm font-medium leading-normal" href="index.php">Home</a>
              <a class="text-[#181511] text-sm font-medium leading-normal" href="search.php?listing_type=adoption">Adopt</a>
              <a class="text-[#181511] text-sm font-medium leading-normal" href="search.php?listing_type=buy_sell">Buy/Sell</a>
            </div>
            <div class="flex gap-2">
              <?php if (isset($_SESSION['user_id'])): ?>
                <a href="dashboard.php" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#f39224] text-[#181511] text-sm font-bold leading-normal tracking-[0.015em]">
                  <span class="truncate">Dashboard</span>
                </a>
                <a href="auth/logout.php" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#f5f2f0] text-[#181511] text-sm font-bold leading-normal tracking-[0.015em]">
                  <span class="truncate">Logout</span>
                </a>
              <?php else: ?>
                <a href="auth/login.php" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#f39224] text-[#181511] text-sm font-bold leading-normal tracking-[0.015em]">
                  <span class="truncate">Log in</span>
                </a>
                <a href="auth/register.php" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#f5f2f0] text-[#181511] text-sm font-bold leading-normal tracking-[0.015em]">
                  <span class="truncate">Sign up</span>
                </a>
              <?php endif; ?>
            </div>
          </div>
        </header>
        <div class="px-40 flex flex-1 justify-center py-5">
          <div class="layout-content-container flex flex-col max-w-[960px] flex-1">
            <h2 class="text-[#181511] tracking-light text-[28px] font-bold leading-tight px-4 text-center pb-3 pt-5">Dashboard</h2>

            <!-- My Pets Section -->
            <div class="flex justify-between items-center px-4 py-3">
              <h3 class="text-[#181511] text-xl font-bold leading-tight">My Pets</h3>
              <a href="pet/add_pet.php" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#f39224] text-[#181511] text-sm font-bold leading-normal tracking-[0.015em]">
                <span class="truncate">Add a Pet</span>
              </a>
            </div>
            <p class="text-[#8a7760] text-sm font-normal leading-normal px-4 py-1">
              You can only add medical records for pets you’ve personally added.
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 px-4 py-3">
              <?php while ($pet = mysqli_fetch_assoc($pets_result)): ?>
                <div class="flex flex-col gap-3 rounded-xl border border-[#e4e1dd] bg-white p-4">
                  <?php if ($pet['photo']): ?>
                    <img src="<?php echo htmlspecialchars('assets/images/uploads/' . basename($pet['photo'])); ?>" alt="<?php echo htmlspecialchars($pet['name']); ?>" class="w-full h-48 object-cover rounded-lg">
                  <?php else: ?>
                    <img src="<?php echo htmlspecialchars('assets/images/placeholder.jpg'); ?>" alt="Pet Placeholder" class="w-full h-48 object-cover rounded-lg">
                  <?php endif; ?>
                  <h3 class="text-[#181511] text-lg font-bold leading-tight"><?php echo htmlspecialchars($pet['name']); ?></h3>
                  <p class="text-[#8a7760] text-sm font-normal leading-normal">
                    <strong>Type:</strong> <?php echo ucfirst(htmlspecialchars($pet['listing_type'])); ?>
                  </p>
                  <?php if ($pet['listing_type'] == 'buy_sell' && $pet['price']): ?>
                    <p class="text-[#8a7760] text-sm font-normal leading-normal">
                      <strong>Price:</strong> $<?php echo number_format($pet['price'], 2); ?>
                    </p>
                  <?php endif; ?>
                  <p class="text-[#8a7760] text-sm font-normal leading-normal">
                    <strong>Breed:</strong> <?php echo htmlspecialchars($pet['breed']); ?>
                  </p>
                  <p class="text-[#8a7760] text-sm font-normal leading-normal">
                    <strong>Age:</strong> <?php echo htmlspecialchars($pet['age']); ?> years
                  </p>
                  <p class="text-[#8a7760] text-sm font-normal leading-normal">
                    <strong>Category:</strong> <?php echo htmlspecialchars($pet['category']); ?>
                  </p>
                  <p class="text-[#8a7760] text-sm font-normal leading-normal">
                    <strong>Location:</strong> <?php echo htmlspecialchars($pet['location']); ?>
                  </p>
                  <div class="flex gap-2">
                    <a href="pet/edit_pet.php?id=<?php echo $pet['id']; ?>" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#f39224] text-[#181511] text-sm font-bold leading-normal tracking-[0.015em]">
                      <span class="truncate">Edit</span>
                    </a>
                    <a href="pet/delete_pet.php?id=<?php echo $pet['id']; ?>" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#f5f2f0] text-[#181511] text-sm font-bold leading-normal tracking-[0.015em]" onclick="return confirm('Are you sure?')">
                      <span class="truncate">Delete</span>
                    </a>
                    <a href="pet/view_pet.php?id=<?php echo $pet['id']; ?>" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#f5f2f0] text-[#181511] text-sm font-bold leading-normal tracking-[0.015em]">
                      <span class="truncate">View</span>
                    </a>
                  </div>
                </div>
              <?php endwhile; ?>
            </div>
            <?php if (mysqli_num_rows($pets_result) == 0): ?>
              <p class="text-[#8a7760] text-sm font-normal leading-normal px-4 text-center py-3">
                No pets posted yet. 
                <a href="pet/add_pet.php" class="text-[#f39224] underline">Add a pet</a>
              </p>
            <?php endif; ?>

            <!-- Adoption Requests Section -->
            <h3 class="text-[#181511] text-xl font-bold leading-tight px-4 py-3">Adoption Requests</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 px-4 py-3">
              <?php while ($app = mysqli_fetch_assoc($applications_result)): ?>
                <div class="flex flex-col gap-3 rounded-xl border border-[#e4e1dd] bg-white p-4">
                  <h3 class="text-[#181511] text-lg font-bold leading-tight">Pet: <?php echo htmlspecialchars($app['pet_name']); ?></h3>
                  <p class="text-[#8a7760] text-sm font-normal leading-normal">
                    <strong>Status:</strong> <?php echo htmlspecialchars($app['status']); ?>
                  </p>
                  <?php if ($app['status'] == 'pending'): ?>
                    <div class="flex gap-2">
                      <a href="adoption/approve_application.php?id=<?php echo $app['id']; ?>" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#f39224] text-[#181511] text-sm font-bold leading-normal tracking-[0.015em]">
                        <span class="truncate">Approve</span>
                      </a>
                      <a href="adoption/delete_application.php?id=<?php echo $app['id']; ?>" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#DC3545] text-white text-sm font-bold leading-normal tracking-[0.015em]">
                        <span class="truncate">Reject</span>
                      </a>
                    </div>
                  <?php endif; ?>
                </div>
              <?php endwhile; ?>
            </div>
            <?php if (mysqli_num_rows($applications_result) == 0): ?>
              <p class="text-[#8a7760] text-sm font-normal leading-normal px-4 text-center py-3">
                No adoption requests yet.
              </p>
            <?php endif; ?>

            <!-- Medical Logs Section -->
            <div class="flex justify-between items-center px-4 py-3">
              <h3 class="text-[#181511] text-xl font-bold leading-tight">Medical Logs</h3>
              <a href="medical/add_medical.php" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#f39224] text-[#181511] text-sm font-bold leading-normal tracking-[0.015em]">
                <span class="truncate">Add Medical Record</span>
              </a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 px-4 py-3">
              <?php while ($medical = mysqli_fetch_assoc($medical_result)): ?>
                <div class="flex flex-col gap-3 rounded-xl border border-[#e4e1dd] bg-white p-4">
                  <h3 class="text-[#181511] text-lg font-bold leading-tight">Pet: <?php echo htmlspecialchars($medical['pet_name']); ?></h3>
                  <p class="text-[#8a7760] text-sm font-normal leading-normal">
                    <strong>Vaccine:</strong> <?php echo htmlspecialchars($medical['vaccine_name']); ?>
                  </p>
                  <p class="text-[#8a7760] text-sm font-normal leading-normal">
                    <strong>Date:</strong> <?php echo htmlspecialchars($medical['vaccine_date']); ?>
                  </p>
                  <div class="flex gap-2">
                    <a href="medical/edit_medical.php?id=<?php echo $medical['id']; ?>" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#f39224] text-[#181511] text-sm font-bold leading-normal tracking-[0.015em]">
                      <span class="truncate">Edit</span>
                    </a>
                    <a href="medical/delete_medical.php?id=<?php echo $medical['id']; ?>" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#f5f2f0] text-[#181511] text-sm font-bold leading-normal tracking-[0.015em]" onclick="return confirm('Are you sure?')">
                      <span class="truncate">Delete</span>
                    </a>
                  </div>
                </div>
              <?php endwhile; ?>
            </div>
            <?php if (mysqli_num_rows($pets_result) == 0): ?>
              <p class="text-[#8a7760] text-sm font-normal leading-normal px-4 text-center py-3">
                No medical records yet. 
                <a href="medical/add_medical.php" class="text-[#f39224] underline">Add a record</a>
              </p>
            <?php endif; ?>
          </div>
        </div>
        <?php include 'includes/footer.php'; ?>
      </div>
    </div>
</body>

</html>