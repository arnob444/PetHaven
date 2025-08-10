<?php
session_start();
  include '../includes/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$record_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = $_SESSION['user_id'];

$query = "SELECT m.*, p.user_id FROM medical_records m JOIN pets p ON m.pet_id = p.id WHERE m.id = ? AND p.user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $record_id, $user_id);
mysqli_stmt_execute($stmt);
$record = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

$pets_query = "SELECT id, name FROM pets WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $pets_query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$pets_result = mysqli_stmt_get_result($stmt);

if (!$record) {
    header('Location: ../dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pet_id = (int)$_POST['pet_id'];
    $vaccine_name = mysqli_real_escape_string($conn, $_POST['vaccine_name']);
    $vaccine_date = mysqli_real_escape_string($conn, $_POST['vaccine_date']);

    $verify_query = "SELECT id FROM pets WHERE id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $verify_query);
    mysqli_stmt_bind_param($stmt, "ii", $pet_id, $user_id);
    mysqli_stmt_execute($stmt);
    $pet = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$pet) {
        $error = "Invalid pet selected.";
    } else {
        $update_query = "UPDATE medical_records SET pet_id = ?, vaccine_name = ?, vaccine_date = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param($stmt, "issi", $pet_id, $vaccine_name, $vaccine_date, $record_id);

        if (mysqli_stmt_execute($stmt)) {
            header('Location: ../dashboard.php');
            exit();
        } else {
            $error = "Failed to update medical record.";
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
      href="https://fonts.googleapis.com/css2?display=swap&family=Noto+Sans%3Awght%40400%3B500%3B700%3B900&family=Plus+Jakarta+Sans%3Awght%40400%3B500%3B700%3B800"
    />

    <title>PetHaven - Edit Medical Record</title>
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
          <div class="layout-content-container flex flex-col w-[512px] max-w-[512px] py-5 max-w-[960px] flex-1">
            <h2 class="text-[#181511] tracking-light text-[28px] font-bold leading-tight px-4 text-center pb-3 pt-5">Edit Medical Record</h2>
            <?php if (isset($error)): ?>
              <p class="text-[#ff0000] text-sm font-normal leading-normal px-4 text-center pb-3"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <form method="POST" action="edit_medical.php?id=<?php echo $record_id; ?>" class="flex flex-col gap-4 px-4 py-3">
              <label class="flex flex-col min-w-40 flex-1">
                <p class="text-[#181511] text-base font-medium leading-normal pb-2">Pet</p>
                <select
                  name="pet_id"
                  class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#181511] focus:outline-0 focus:ring-0 border border-[#e4e1dd] bg-white focus:border-[#e4e1dd] h-14 bg-[image:--select-button-svg] placeholder:text-[#8a7760] p-[15px] text-base font-normal leading-normal"
                  required
                >
                  <option value="">Select a pet</option>
                  <?php
                  mysqli_data_seek($pets_result, 0); // Reset pointer to fetch pets again
                  while ($pet = mysqli_fetch_assoc($pets_result)): ?>
                    <option value="<?php echo $pet['id']; ?>" <?php echo $record['pet_id'] == $pet['id'] ? 'selected' : ''; ?>>
                      <?php echo htmlspecialchars($pet['name']); ?>
                    </option>
                  <?php endwhile; ?>
                </select>
              </label>
              <label class="flex flex-col min-w-40 flex-1">
                <p class="text-[#181511] text-base font-medium leading-normal pb-2">Vaccine Name</p>
                <input
                  type="text"
                  name="vaccine_name"
                  placeholder="Vaccine Name"
                  class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#181511] focus:outline-0 focus:ring-0 border border-[#e4e1dd] bg-white focus:border-[#e4e1dd] h-14 placeholder:text-[#8a7760] p-[15px] text-base font-normal leading-normal"
                  value="<?php echo htmlspecialchars($record['vaccine_name']); ?>"
                  required
                />
              </label>
              <label class="flex flex-col min-w-40 flex-1">
                <p class="text-[#181511] text-base font-medium leading-normal pb-2">Vaccine Date</p>
                <input
                  type="date"
                  name="vaccine_date"
                  class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#181511] focus:outline-0 focus:ring-0 border border-[#e4e1dd] bg-white focus:border-[#e4e1dd] h-14 placeholder:text-[#8a7760] p-[15px] text-base font-normal leading-normal"
                  value="<?php echo htmlspecialchars($record['vaccine_date']); ?>"
                  required
                />
              </label>
              <div class="flex gap-3">
                <button
                  type="submit"
                  class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-xl h-10 px-4 bg-[#f39224] text-[#181511] text-sm font-bold leading-normal tracking-[0.015em]"
                >
                  <span class="truncate">Update Record</span>
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