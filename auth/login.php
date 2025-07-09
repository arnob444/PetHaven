<?php
session_start();
require_once '../includes/config.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $password = $_POST['password'];

  $query = "SELECT * FROM users WHERE email = ? LIMIT 1";
  $stmt = mysqli_prepare($conn, $query);
  mysqli_stmt_bind_param($stmt, "s", $email);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  $user = mysqli_fetch_assoc($result);

  if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    header('Location: ../dashboard.php');
    exit();
  } else {
    $error = "Invalid email or password.";
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
    href="https://fonts.googleapis.com/css2?display=swap&family=Noto+Sans%3Awght%40400%3B500%3B700%3B900&family=Plus+Jakarta+Sans%3Awght%40400%3B500%3B700%3B800" />

  <title>PetHaven - Login</title>
  <link rel="icon" type="image/x-icon" href="data:image/x-icon;base64," />

  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
</head>

<body>
  <div class="relative flex size-full min-h-screen flex-col bg-white group/design-root overflow-x-hidden" style='font-family: "Plus Jakarta Sans", "Noto Sans", sans-serif;'>
    <div class="layout-container flex h-full grow flex-col">
      <header class="flex items-center justify-between whitespace-nowrap border-b border-solid border-b-[#f4f2f1] px-10 py-3">
        <div class="flex items-center gap-4 text-[#171512]">
          <div class="size-4">
            <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path
                fill-rule="evenodd"
                clip-rule="evenodd"
                d="M12.0799 24L4 19.2479L9.95537 8.75216L18.04 13.4961L18.0446 4H29.9554L29.96 13.4961L38.0446 8.75216L44 19.2479L35.92 24L44 28.7521L38.0446 39.2479L29.96 34.5039L29.9554 44H18.0446L18.04 34.5039L9.95537 39.2479L4 28.7521L12.0799 24Z"
                fill="currentColor"></path>
            </svg>
          </div>
          <h2 class="text-[#171512] text-lg font-bold leading-tight tracking-[-0.015em]">PetHaven</h2>
        </div>
        <div class="flex flex-1 justify-end gap-8">
          <div class="flex items-center gap-9">
            <a class="text-[#171512] text-sm font-medium leading-normal" href="../index.php">Home</a>
            <a class="text-[#171512] text-sm font-medium leading-normal" href="../search.php?listing_type=adoption">Adopt</a>
            <a class="text-[#171512] text-sm font-medium leading-normal" href="../search.php?listing_type=buy_sell">Buy/Sell</a>
          </div>
          <div class="flex gap-2">
            <?php if (isset($_SESSION['user_id'])): ?>
              <a href="../dashboard.php" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 px-4 bg-[#f3e6d7] text-[#171512] text-sm font-bold leading-normal tracking-[0.015em]">
                <span class="truncate">Dashboard</span>
              </a>
              <a href="../auth/logout.php" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 px-4 bg-[#f4f2f1] text-[#171512] text-sm font-bold leading-normal tracking-[0.015em]">
                <span class="truncate">Logout</span>
              </a>
            <?php else: ?>
              <a href="../auth/login.php" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 px-4 bg-[#f3e6d7] text-[#171512] text-sm font-bold leading-normal tracking-[0.015em]">
                <span class="truncate">Login</span>
              </a>
              <a href="../auth/register.php" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 px-4 bg-[#f4f2f1] text-[#171512] text-sm font-bold leading-normal tracking-[0.015em]">
                <span class="truncate">Register</span>
              </a>
            <?php endif; ?>
          </div>
        </div>
      </header>

      <div class="px-40 flex flex-1 justify-center py-5">
        <div class="layout-content-container flex flex-col w-[512px] max-w-[512px] py-5 max-w-[960px] flex-1">
          <h2 class="text-[#181511] tracking-light text-[28px] font-bold leading-tight px-4 text-center pb-3 pt-5">Welcome back</h2>
          <?php if ($error): ?>
            <p class="text-[#ff0000] text-sm font-normal leading-normal px-4 text-center pb-3"><?php echo htmlspecialchars($error); ?></p>
          <?php endif; ?>
          <form method="POST" action="login.php" class="flex flex-col ">
            <div class="flex max-w-[480px] flex-wrap items-end gap-4 px-4 py-3">
              <label class="flex flex-col min-w-40 flex-1">
                <p class="text-[#181511] text-base font-medium leading-normal pb-2">Email</p>
                <input
                  name="email"
                  placeholder="Email"
                  class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#181511] focus:outline-0 focus:ring-0 border-none bg-[#f5f2f0] focus:border-none h-14 placeholder:text-[#8a7760] p-4 text-base font-normal leading-normal"
                  value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" />
              </label>
            </div>
            <div class="flex max-w-[480px] flex-wrap items-end gap-4 px-4 py-3">
              <label class="flex flex-col min-w-40 flex-1">
                <p class="text-[#181511] text-base font-medium leading-normal pb-2">Password</p>
                <input
                  name="password"
                  type="password"
                  placeholder="Password"
                  class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#181511] focus:outline-0 focus:ring-0 border-none bg-[#f5f2f0] focus:border-none h-14 placeholder:text-[#8a7760] p-4 text-base font-normal leading-normal"
                  value="" />
              </label>
            </div>
            <p class="text-[#8a7760] text-sm font-normal leading-normal pb-3 pt-1 px-4"><a href="../auth/forgot_password.php">Forgot password?</a></p>
            <div class="flex px-4 py-3">
              <button
                type="submit"
                class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-12 px-5 flex-1 bg-[#f39224] text-[#181511] text-base font-bold leading-normal tracking-[0.015em]">
                <span class="truncate">Log in</span>
              </button>
            </div>
            <p class="text-[#8a7760] text-sm font-normal leading-normal pb-3 pt-1 px-5 text-center ">Don't have an account?<a href="../auth/register.php">&nbsp;&nbsp;&nbsp;Sign up</a></p>
          </form>
        </div>
      </div>
    </div>
  </div>
</body>

</html>