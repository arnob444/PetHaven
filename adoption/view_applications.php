<?php
session_start();
require_once '../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch adoption applications made by the user
$query = "SELECT a.*, p.name AS pet_name FROM adoption_applications a JOIN pets p ON a.pet_id = p.id WHERE a.user_id = ? ORDER BY a.created_at DESC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$applications_result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="" />
    <link
        rel="stylesheet"
        as="style"
        onload="this.rel='stylesheet'"
        href="https://fonts.googleapis.com/css2?display=swap&family=Noto+Sans%3Awght%40400%3B500%3B700%3B900&family=Plus+Jakarta+Sans%3Awght%40400%3B500%3B700%3B800" />

    <title>PetHaven</title>
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
                    <h2 class="text-[#181511] tracking-light text-[28px] font-bold leading-tight px-4 text-center pb-3 pt-5">My Adoption Applications</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 px-4 py-3">
                        <?php while ($app = mysqli_fetch_assoc($applications_result)): ?>
                            <div class="flex flex-col gap-3 rounded-xl border border-[#e4e1dd] bg-white p-4">
                                <h3 class="text-[#181511] text-lg font-bold leading-tight">Pet: <?php echo htmlspecialchars($app['pet_name']); ?></h3>
                                <p class="text-[#8a7760] text-sm font-normal leading-normal">
                                    <strong>Status:</strong> <?php echo htmlspecialchars($app['status']); ?>
                                </p>
                                <p class="text-[#8a7760] text-sm font-normal leading-normal">
                                    <strong>Applied on:</strong> <?php echo htmlspecialchars($app['created_at']); ?>
                                </p>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    <?php if (mysqli_num_rows($applications_result) == 0): ?>
                        <p class="text-[#8a7760] text-sm font-normal leading-normal px-4 text-center py-3">
                            You have not applied for any pets yet.
                            <a href="../index.php" class="text-[#f39224] underline">Browse pets</a>
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