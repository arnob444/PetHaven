<?php
session_start();
require_once '../includes/config.php';

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

    <title>PetHaven - Manage Application</title>
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
              <a class="text-[#181511] text-sm font-medium leading-normal" href="../../index.php">Home</a>
              <a class="text-[#181511] text-sm font-medium leading-normal" href="../../search.php?listing_type=adoption">Adopt</a>
              <a class="text-[#181511] text-sm font-medium leading-normal" href="../../search.php?listing_type=buy_sell">Buy/Sell</a>
            </div>
            <div class="flex gap-2">
              <?php if (isset($_SESSION['user_id'])): ?>
                <a href="../../dashboard.php" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#f39224] text-[#181511] text-sm font-bold leading-normal tracking-[0.015em]">
                  <span class="truncate">Dashboard</span>
                </a>
                <a href="../../auth/logout.php" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#f5f2f0] text-[#181511] text-sm font-bold leading-normal tracking-[0.015em]">
                  <span class="truncate">Logout</span>
                </a>
              <?php else: ?>
                <a href="../../auth/login.php" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#f39224] text-[#181511] text-sm font-bold leading-normal tracking-[0.015em]">
                  <span class="truncate">Log in</span>
                </a>
                <a href="../../auth/register.php" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#f5f2f0] text-[#181511] text-sm font-bold leading-normal tracking-[0.015em]">
                  <span class="truncate">Sign up</span>
                </a>
              <?php endif; ?>
            </div>
          </div>
        </header>
        <div class="px-40 flex flex-1 justify-center py-5">
          <div class="layout-content-container flex flex-col w-[512px] max-w-[512px] py-5 max-w-[960px] flex-1">
            <h2 class="text-[#181511] tracking-light text-[28px] font-bold leading-tight px-4 text-center pb-3 pt-5">Manage Application</h2>
            <?php if (isset($error)): ?>
              <p class="text-[#ff0000] text-sm font-normal leading-normal px-4 text-center pb-3"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <p class="text-[#8a7760] text-sm font-normal leading-normal px-4 text-center pb-3">
              Application for a pet by user ID <?php echo htmlspecialchars($app['user_id']); ?>.
            </p>
            <form method="POST" action="approve_application.php?id=<?php echo $app_id; ?>" class="flex flex-col gap-4 px-4 py-3">
              <div class="flex gap-3">
                <button
                  type="submit"
                  name="action"
                  value="approve"
                  class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#f39224] text-[#181511] text-sm font-bold leading-normal tracking-[0.015em]"
                >
                  <span class="truncate">Approve</span>
                </button>
                <button
                  type="submit"
                  name="action"
                  value="reject"
                  class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#DC3545] text-white text-sm font-bold leading-normal tracking-[0.015em]"
                >
                <a href="adoption/delete_application.php?id=<?php echo $app['id']; ?>"
                  <span class="truncate">Reject</span>
                </button>
                <a href="../dashboard.php" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#f5f2f0] text-[#181511] text-sm font-bold leading-normal tracking-[0.015em]">
                  <span class="truncate">Back to Dashboard</span>
                </a>
              </div>
            </form>
          </div>
        </div>
        <?php include '../includes/footer.php'; ?>
      </div>
    </div>
</body>

</html>