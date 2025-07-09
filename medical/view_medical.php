<?php
session_start();
require_once '../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$pet_id = isset($_GET['pet_id']) ? (int)$_GET['pet_id'] : 0;
$user_id = $_SESSION['user_id'];

$pet_query = "SELECT name FROM pets WHERE id = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $pet_query);
mysqli_stmt_bind_param($stmt, "ii", $pet_id, $user_id);
mysqli_stmt_execute($stmt);
$pet = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$pet) {
    header('Location: ../dashboard.php');
    exit();
}

$records_query = "SELECT * FROM medical_records WHERE pet_id = ? ORDER BY created_at DESC";
$stmt = mysqli_prepare($conn, $records_query);
mysqli_stmt_bind_param($stmt, "i", $pet_id);
mysqli_stmt_execute($stmt);
$records_result = mysqli_stmt_get_result($stmt);
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

    <title>PetHaven - View Medical Records</title>
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
            <h2 class="text-[#181511] text-lg font-bold leading-tight tracking-[-0.015em]"><a href="index.php">PetHaven</a></h2>
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
          <div class="layout-content-container flex flex-col max-w-[960px] flex-1">
            <h2 class="text-[#181511] tracking-light text-[28px] font-bold leading-tight px-4 text-center pb-3 pt-5">
              Medical Records for <?php echo htmlspecialchars($pet['name']); ?>
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 px-4 py-3">
              <?php while ($record = mysqli_fetch_assoc($records_result)): ?>
                <div class="flex flex-col gap-3 rounded-xl border border-[#e4e1dd] bg-white p-4">
                  <h3 class="text-[#181511] text-lg font-bold leading-tight"><?php echo htmlspecialchars($record['vaccine_name']); ?></h3>
                  <p class="text-[#8a7760] text-sm font-normal leading-normal">
                    <strong>Date:</strong> <?php echo htmlspecialchars($record['vaccine_date']); ?>
                  </p>
                  <p class="text-[#8a7760] text-sm font-normal leading-normal">
                    <strong>Added:</strong> <?php echo htmlspecialchars($record['created_at']); ?>
                  </p>
                  <div class="flex gap-2">
                    <a href="edit_medical.php?id=<?php echo $record['id']; ?>" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#f39224] text-[#181511] text-sm font-bold leading-normal tracking-[0.015em]">
                      <span class="truncate">Edit</span>
                    </a>
                    <a href="delete_medical.php?id=<?php echo $record['id']; ?>" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#f5f2f0] text-[#181511] text-sm font-bold leading-normal tracking-[0.015em]" onclick="return confirm('Are you sure?')">
                      <span class="truncate">Delete</span>
                    </a>
                  </div>
                </div>
              <?php endwhile; ?>
            </div>
            <?php if (mysqli_num_rows($records_result) == 0): ?>
              <p class="text-[#8a7760] text-sm font-normal leading-normal px-4 text-center py-3">
                No medical records found for this pet. 
                <a href="add_medical.php" class="text-[#f39224] underline">Add a record</a>
              </p>
            <?php endif; ?>
            <div class="flex px-4 py-3">
              <a href="../dashboard.php" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#f39224] text-[#181511] text-sm font-bold leading-normal tracking-[0.015em]">
                <span class="truncate">Back to Dashboard</span>
              </a>
            </div>
          </div>
        </div>
        <?php include '../includes/footer.php'; ?>
      </div>
    </div>
</body>

</html>