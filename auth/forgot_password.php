<?php
session_start();
  include '../includes/config.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $email = mysqli_real_escape_string($conn, $_POST['email']);

  $query = "SELECT * FROM users WHERE email = ?";
  $stmt = mysqli_prepare($conn, $query);
  mysqli_stmt_bind_param($stmt, "s", $email);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  $user = mysqli_fetch_assoc($result);

  if ($user) {
    $success = "Password reset link has been sent to your email.";
  } else {
    $error = "No account found with this email.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>PetHaven - Forgot Password</title>
  <link rel="icon" type="image/x-icon" href="data:image/x-icon;base64," />
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin />
  <link rel="stylesheet" as="style" onload="this.rel='stylesheet'" href="https://fonts.googleapis.com/css2?display=swap&family=Noto+Sans:wght@400;500;700;900&family=Plus+Jakarta+Sans:wght@400;500;700;800" />
</head>

<body>
  <div class="relative flex size-full min-h-screen flex-col bg-white group/design-root overflow-x-hidden" style='font-family: "Plus Jakarta Sans", "Noto Sans", sans-serif;'>
    <div class="layout-container flex h-full grow flex-col">
      <header class="flex items-center justify-between whitespace-nowrap border-b border-solid border-b-[#f4f2f1] px-10 py-3">
        <div class="flex items-center gap-4 text-[#171512]">
           <div class="size-4">
              <img src="../assets/images/icons/logo.png" alt="PetHaven Logo" class="w-5 h-4" />
            </div>
          <h2 class="text-[#181511] text-lg font-bold leading-tight tracking-[-0.015em]"><a href="index.php">PetHaven</a></h2>
        </div>
        <div class="flex flex-1 justify-end gap-8">
          <div class="flex items-center gap-9">
            <a class="text-[#171512] text-sm font-medium leading-normal" href="../index.php">Home</a>
            <a class="text-[#171512] text-sm font-medium leading-normal" href="../search.php?listing_type=adoption">Adopt</a>
            <a class="text-[#171512] text-sm font-medium leading-normal" href="../search.php?listing_type=buy_sell">Buy/Sell</a>
          </div>
          <div class="flex gap-2">
            <?php if (isset($_SESSION['user_id'])): ?>
              <a href="../dashboard.php" class="flex min-w-[84px] max-w-[480px] items-center justify-center rounded-full h-10 px-4 bg-[#f3e6d7] text-[#171512] text-sm font-bold">Dashboard</a>
              <a href="../auth/logout.php" class="flex min-w-[84px] max-w-[480px] items-center justify-center rounded-full h-10 px-4 bg-[#f4f2f1] text-[#171512] text-sm font-bold">Logout</a>
            <?php else: ?>
              <a href="../auth/login.php" class="flex min-w-[84px] max-w-[480px] items-center justify-center rounded-full h-10 px-4 bg-[#f3e6d7] text-[#171512] text-sm font-bold">Login</a>
              <a href="../auth/register.php" class="flex min-w-[84px] max-w-[480px] items-center justify-center rounded-full h-10 px-4 bg-[#f4f2f1] text-[#171512] text-sm font-bold">Register</a>
            <?php endif; ?>
          </div>
        </div>
      </header>

      <div class="px-40 flex flex-1 justify-center py-5">
        <div class="layout-content-container flex flex-col w-[512px] max-w-[512px] py-5 flex-1">
          <h2 class="text-[#181511] text-[28px] font-bold leading-tight px-4 text-center pb-3 pt-5">Reset Your Password</h2>
          <?php if ($error): ?>
            <p class="text-[#ff0000] text-sm text-center px-4 pb-3"><?php echo htmlspecialchars($error); ?></p>
          <?php endif; ?>
          <?php if ($success): ?>
            <p class="text-green-600 text-sm text-center px-4 pb-3"><?php echo htmlspecialchars($success); ?></p>
          <?php endif; ?>
          <form method="POST" action="forgot_password.php" class="flex flex-col">
            <div class="flex max-w-[480px] items-end gap-4 px-4 py-3">
              <label class="flex flex-col flex-1">
                <p class="text-[#181511] text-base font-medium leading-normal pb-2">Enter your email address</p>
                <input
                  name="email"
                  type="email"
                  required
                  placeholder="you@example.com"
                  class="form-input w-full rounded-xl h-14 p-4 bg-[#f5f2f0] text-[#181511] placeholder:text-[#8a7760] focus:outline-0 focus:ring-0 border-none"
                  value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" />
              </label>
            </div>
            <div class="flex px-4 py-3">
              <button
                type="submit"
                class="flex flex-1 items-center justify-center h-12 px-5 rounded-xl bg-[#f39224] text-[#181511] text-base font-bold">
                Send Reset Link
              </button>
            </div>
            <p class="text-[#8a7760] text-sm text-center pt-1 px-5">Remember your password? <a href="login.php" class="text-[#f39224] font-semibold">Login</a></p>
          </form>
        </div>
      </div>
    </div>
  </div>
</body>

</html>