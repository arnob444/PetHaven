<?php
session_start();
require_once 'includes/config.php';

$where = "WHERE 1=1";
$params = [];
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $where .= " AND category = ?";
    $params[] = $_GET['category'];
}
if (isset($_GET['age']) && !empty($_GET['age'])) {
    $where .= " AND age = ?";
    $params[] = (int)$_GET['age'];
}
if (isset($_GET['breed']) && !empty($_GET['breed'])) {
    $where .= " AND breed LIKE ?";
    $params[] = "%" . $_GET['breed'] . "%";
}
if (isset($_GET['location']) && !empty($_GET['location'])) {
    $where .= " AND location LIKE ?";
    $params[] = "%" . $_GET['location'] . "%";
}
if (isset($_GET['listing_type']) && !empty($_GET['listing_type'])) {
    $where .= " AND listing_type = ?";
    $params[] = $_GET['listing_type'];
}

$query = "SELECT * FROM pets $where ORDER BY created_at DESC";
$stmt = mysqli_prepare($conn, $query);
if (!empty($params)) {
    $types = str_repeat('s', count($params));
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
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

    <title>Pawsitive Match - Search</title>
    <link rel="icon" type="image/x-icon" href="data:image/x-icon;base64," />

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
</head>

<body>
    <div class="relative flex size-full min-h-screen flex-col bg-white group/design-root overflow-x-hidden" style='--select-button-svg: url("data:image/svg+xml,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%2724px%27 height=%2724px%27 fill=%27rgb(130,118,104)%27 viewBox=%270 0 256 256%27%3e%3cpath d=%27M181.66,170.34a8,8,0,0,1,0,11.32l-48,48a8,8,0,0,1-11.32,0l-48-48a8,8,0,0,1,11.32-11.32L128,212.69l42.34-42.35A8,8,0,0,1,181.66,170.34Zm-96-84.68L128,43.31l42.34,42.35a8,8,0,0,0,11.32-11.32l-48-48a8,8,0,0,0-11.32,0l-48,48A8,8,0,0,0,85.66,85.66Z%27%3e%3c/path%3e%3c/svg%3e"); font-family: "Plus Jakarta Sans", "Noto Sans", sans-serif;'>
      <div class="layout-container flex h-full grow flex-col">
        <header class="flex items-center justify-between whitespace-nowrap border-b border-solid border-b-[#f5f2f0] px-10 py-3">
          <div class="flex items-center gap-4 text-[#181511]">
            <div class="size-4">
              <svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M24 4H6V17.3333V30.6667H24V44H42V30.6667V17.3333H24V4Z" fill="currentColor"></path>
              </svg>
            </div>
            <h2 class="text-[#181511] text-lg font-bold leading-tight tracking-[-0.015em]">Pawsitive Match</h2>
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
        <div class="gap-1 px-6 flex flex-1 justify-center py-5">
          <div class="layout-content-container flex flex-col w-80">
            <h3 class="text-[#181511] text-lg font-bold leading-tight tracking-[-0.015em] px-4 pb-2 pt-4">Filter</h3>
            <form method="GET" action="search.php" class="flex flex-col max-w-[480px] flex-wrap items-end gap-4 px-4 py-3">
              <label class="flex flex-col min-w-40 flex-1">
                <p class="text-[#181511] text-base font-medium leading-normal pb-2">Type</p>
                <select
                  name="listing_type"
                  class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#181511] focus:outline-0 focus:ring-0 border border-[#e4e1dd] bg-white focus:border-[#e4e1dd] h-14 bg-[image:--select-button-svg] placeholder:text-[#8a7760] p-[15px] text-base font-normal leading-normal"
                >
                  <option value="">Any</option>
                  <option value="adoption" <?php echo isset($_GET['listing_type']) && $_GET['listing_type'] == 'adoption' ? 'selected' : ''; ?>>Adoption</option>
                  <option value="buy_sell" <?php echo isset($_GET['listing_type']) && $_GET['listing_type'] == 'buy_sell' ? 'selected' : ''; ?>>Buy/Sell</option>
                </select>
              </label>
              <label class="flex flex-col min-w-40 flex-1">
                <p class="text-[#181511] text-base font-medium leading-normal pb-2">Category</p>
                <select
                  name="category"
                  class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#181511] focus:outline-0 focus:ring-0 border border-[#e4e1dd] bg-white focus:border-[#e4e1dd] h-14 bg-[image:--select-button-svg] placeholder:text-[#8a7760] p-[15px] text-base font-normal leading-normal"
                >
                  <option value="">Any</option>
                  <option value="dog" <?php echo isset($_GET['category']) && $_GET['category'] == 'dog' ? 'selected' : ''; ?>>Dog</option>
                  <option value="cat" <?php echo isset($_GET['category']) && $_GET['category'] == 'cat' ? 'selected' : ''; ?>>Cat</option>
                  <option value="other" <?php echo isset($_GET['category']) && $_GET['category'] == 'other' ? 'selected' : ''; ?>>Other</option>
                </select>
              </label>
              <label class="flex flex-col min-w-40 flex-1">
                <p class="text-[#181511] text-base font-medium leading-normal pb-2">Breed</p>
                <input
                  name="breed"
                  placeholder="Breed"
                  class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#181511] focus:outline-0 focus:ring-0 border border-[#e4e1dd] bg-white focus:border-[#e4e1dd] h-14 placeholder:text-[#8a7760] p-[15px] text-base font-normal leading-normal"
                  value="<?php echo isset($_GET['breed']) ? htmlspecialchars($_GET['breed']) : ''; ?>"
                />
              </label>
              <label class="flex flex-col min-w-40 flex-1">
                <p class="text-[#181511] text-base font-medium leading-normal pb-2">Age</p>
                <input
                  name="age"
                  type="number"
                  placeholder="Age"
                  min="0"
                  class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#181511] focus:outline-0 focus:ring-0 border border-[#e4e1dd] bg-white focus:border-[#e4e1dd] h-14 placeholder:text-[#8a7760] p-[15px] text-base font-normal leading-normal"
                  value="<?php echo isset($_GET['age']) ? htmlspecialchars($_GET['age']) : ''; ?>"
                />
              </label>
              <label class="flex flex-col min-w-40 flex-1">
                <p class="text-[#181511] text-base font-medium leading-normal pb-2">Location</p>
                <input
                  name="location"
                  placeholder="Location"
                  class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#181511] focus:outline-0 focus:ring-0 border border-[#e4e1dd] bg-white focus:border-[#e4e1dd] h-14 placeholder:text-[#8a7760] p-[15px] text-base font-normal leading-normal"
                  value="<?php echo isset($_GET['location']) ? htmlspecialchars($_GET['location']) : ''; ?>"
                />
              </label>
              <button type="submit" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 px-4 bg-[#f39224] text-[#181511] text-sm font-bold leading-normal tracking-[0.015em]">
                <span class="truncate">Search</span>
              </button>
            </form>
          </div>
          <div class="layout-content-container flex flex-col max-w-[960px] flex-1">
            <h2 class="text-[#181511] text-[22px] font-bold leading-tight tracking-[-0.015em] px-4 pb-3 pt-5">Search Results</h2>
            <div class="grid grid-cols-[repeat(auto-fit,minmax(158px,1fr))] gap-3 p-4">
              <?php while ($pet = mysqli_fetch_assoc($result)): ?>
                <div class="flex flex-col gap-3 pb-3">
                  <div
                    class="w-full bg-center bg-no-repeat aspect-[3/4] bg-cover rounded-xl"
                    style='background-image: url("<?php echo $pet['photo'] ? "assets/images/uploads/" . htmlspecialchars(basename($pet['photo'])) : "assets/images/placeholder.jpg"; ?>");'
                  ></div>
                  <div>
                    <p class="text-[#181511] text-base font-medium leading-normal"><?php echo htmlspecialchars($pet['name']); ?></p>
                    <p class="text-[#8a7760] text-sm font-normal leading-normal"><?php echo htmlspecialchars($pet['breed'] ? $pet['breed'] : "Unknown Breed"); ?>, <?php echo htmlspecialchars($pet['age'] ? $pet['age'] . " years old" : "Age unknown"); ?></p>
                    <p class="text-[#8a7760] text-sm font-normal leading-normal"><?php echo htmlspecialchars($pet['location'] ? $pet['location'] : "Location unknown"); ?></p>
                    <p class="text-[#8a7760] text-sm font-normal leading-normal"><?php echo ucfirst(htmlspecialchars($pet['listing_type'])); ?><?php echo $pet['listing_type'] == 'buy_sell' && $pet['price'] ? " - $" . number_format($pet['price'], 2) : ""; ?></p>
                    <a href="pet/view_pet.php?id=<?php echo $pet['id']; ?>" class="text-[#181511] text-sm font-bold leading-normal tracking-[0.015em]">View Details</a>
                  </div>
                </div>
              <?php endwhile; ?>
              <?php if (mysqli_num_rows($result) == 0): ?>
                <p class="text-[#181511] text-base font-medium leading-normal px-4">No pets found matching your criteria.</p>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <footer class="bg-[#f5f2f0] px-10 py-6 mt-auto">
          <div class="flex flex-wrap justify-between gap-6">
            <div>
              <h4 class="text-[#181511] text-sm font-bold leading-normal mb-2">Connect with Us</h4>
              <p class="text-[#8a7760] text-sm font-normal leading-normal">Email: <a href="mailto:pethaven@gmail.com" class="underline">pethaven@gmail.com</a></p>
            </div>
            <div>
              <h4 class="text-[#181511] text-sm font-bold leading-normal mb-2">Quick Links</h4>
              <p class="text-[#8a7760] text-sm font-normal leading-normal"><a href="index.php" class="underline">Home</a></p>
              <p class="text-[#8a7760] text-sm font-normal leading-normal"><a href="search.php?listing_type=adoption" class="underline">Adoption</a></p>
            </div>
            <div>
              <h4 class="text-[#181511] text-sm font-bold leading-normal mb-2">Follow Us</h4>
              <p class="text-[#8a7760] text-sm font-normal leading-normal"><a href="#" class="underline">Instagram</a></p>
            </div>
          </div>
        </footer>
      </div>
    </div>
</body>

</html>