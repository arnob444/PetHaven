<?php
session_start();
  include '../includes/config.php';

$error = '';
$success = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $username = mysqli_real_escape_string($conn, $_POST['username']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $password = $_POST['password'];
  $confirm_password = $_POST['confirm_password'];

  if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
    $error = "All fields are required.";
  } elseif ($password !== $confirm_password) {
    $error = "Passwords do not match.";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = "Invalid email format.";
  } else {
    $query = "SELECT * FROM users WHERE email = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
      $error = "Email already exists.";
    } else {
      $hashed_password = password_hash($password, PASSWORD_DEFAULT);
      $query = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
      $stmt = mysqli_prepare($conn, $query);
      mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hashed_password);

      if (mysqli_stmt_execute($stmt)) {
        $success = "Registration successful! You can now <a href='login.php' class='underline'>log in</a>.";
      } else {
        $error = "Registration failed. Please try again.";
      }
    }
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

  <title>PetHaven Match - Register</title>

  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
</head>

<body>
  <div class="relative flex size-full min-h-screen flex-col bg-white group/design-root overflow-x-hidden" style="font-family: 'Plus Jakarta Sans', 'Noto Sans', sans-serif">
    <div class="layout-container flex h-full grow flex-col">
      <header class="flex items-center justify-between whitespace-nowrap border-b border-solid border-b-[#f5f2f0] px-10 py-3">
        <div class="flex items-center gap-4 text-[#181511]">
          <div class="size-4">
              <img src="../assets/images/icons/logo.png" alt="PetHaven Logo" class="w-5 h-4" />
            </div>
          <h2 class="text-[#181511] text-lg font-bold leading-tight tracking-[-0.015em]"><a href="index.php">PetHaven</a></h2>
        </div>
        <div class="flex flex-1 justify-end gap-8">
          <div class="flex items-center gap-9">
            <a class="text-[#181511] text-sm font-medium leading-normal" href="../index.php">Home</a>
            <a class="text-[#181511] text-sm font-medium leading-normal" href="../search.php?listing_type=adoption"">Adopt</a>
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
              <a href="../auth/login.php" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#f5f2f0] text-[#181511] text-sm font-bold leading-normal tracking-[0.015em]">
                <span class="truncate">Sign in</span>
              </a>
            <?php endif; ?>
          </div>
        </div>
      </header>
      <div class="px-40 flex flex-1 justify-center py-5">
        <div class="layout-content-container flex flex-col w-[512px] max-w-[512px] py-5 max-w-[960px] flex-1">
          <h2 class="text-[#181511] tracking-light text-[28px] font-bold leading-tight px-4 text-center pb-3 pt-5">Create an account</h2>
          <?php if ($error): ?>
            <p class="text-[#ff0000] text-sm font-normal leading-normal px-4 text-center pb-3"><?php echo htmlspecialchars($error); ?></p>
          <?php endif; ?>
          <?php if ($success): ?>
            <p class="text-[#181511] text-sm font-normal leading-normal px-4 text-center pb-3"><?php echo $success; ?></p>
          <?php else: ?>
            <form method="POST" action="register.php" class="flex flex-col">
              <div class="flex max-w-[480px] flex-wrap items-end gap-4 px-4 py-3">
                <label class="flex flex-col min-w-40 flex-1">
                  <p class="text-[#181511] text-base font-medium leading-normal pb-2">Username</p>
                  <input
                    name="username"
                    placeholder="Username"
                    class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#181511] focus:outline-0 focus:ring-0 border-none bg-[#f5f2f0] focus:border-none h-14 placeholder:text-[#8a7760] p-4 text-base font-normal leading-normal"
                    value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" />
                </label>
              </div>
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
              <div class="flex max-w-[480px] flex-wrap items-end gap-4 px-4 py-3">
                <label class="flex flex-col min-w-40 flex-1">
                  <p class="text-[#181511] text-base font-medium leading-normal pb-2">Confirm Password</p>
                  <input
                    name="confirm_password"
                    type="password"
                    placeholder="Confirm password"
                    class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#181511] focus:outline-0 focus:ring-0 border-none bg-[#f5f2f0] focus:border-none h-14 placeholder:text-[#8a7760] p-4 text-base font-normal leading-normal"
                    value="" />
                </label>
              </div>
              <div class="flex px-4 py-3">
                <button
                  type="submit"
                  class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 flex-1 bg-[#f39224] text-[#181511] text-sm font-bold leading-normal tracking-[0.015em]">
                  <span class="truncate">Sign up</span>
                </button>
              </div>
              <p class="text-[#8a7760] text-sm font-normal leading-normal pb-3 pt-1 px-4 text-center">
                By signing up, you agree to our terms of service and privacy policy.
              </p>
            </form>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</body>

</html>