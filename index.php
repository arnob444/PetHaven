<?php
session_start();
require_once 'includes/config.php';
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

    <title>PetHaven</title>
    <link rel="icon" type="image/x-icon" href="data:image/x-icon;base64," />

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
</head>

<body>
    <div
      class="relative flex size-full min-h-screen flex-col bg-white group/design-root overflow-x-hidden"
      style='--select-button-svg: url("data:image/svg+xml,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%2724px%27 height=%2724px%27 fill=%27rgb(130,118,104)%27 viewBox=%270 0 256 256%27%3e%3cpath d=%27M181.66,170.34a8,8,0,0,1,0,11.32l-48,48a8,8,0,0,1-11.32,0l-48-48a8,8,0,0,1,11.32-11.32L128,212.69l42.34-42.35A8,8,0,0,1,181.66,170.34Zm-96-84.68L128,43.31l42.34,42.35a8,8,0,0,0,11.32-11.32l-48-48a8,8,0,0,0-11.32,0l-48,48A8,8,0,0,0,85.66,85.66Z%27%3e%3c/path%3e%3c/svg%3e"); font-family: "Plus Jakarta Sans", "Noto Sans", sans-serif;'
    >
      <div class="layout-container flex h-full grow flex-col">
        <header class="flex items-center justify-between whitespace-nowrap border-b border-solid border-b-[#f4f2f1] px-10 py-3">
          <div class="flex items-center gap-4 text-[#171512]">
            <div class="size-4">
              <img src="assets/images/icons/logo.png" alt="PetHaven Logo" class="w-5 h-4" />
            </div>
            <h2 class="text-[#171512] text-lg font-bold leading-tight tracking-[-0.015em]">PetHaven</h2>
          </div>
          <div class="flex flex-1 justify-end gap-8">
            <div class="flex items-center gap-9">
              <a class="text-[#171512] text-sm font-medium leading-normal" href="index.php">Home</a>
              <a class="text-[#171512] text-sm font-medium leading-normal" href="search.php?listing_type=adoption">Adopt</a>
              <a class="text-[#171512] text-sm font-medium leading-normal" href="search.php?listing_type=buy_sell">Buy/Sell</a>
            </div>
            <div class="flex gap-2">
              <?php if (isset($_SESSION['user_id'])): ?>
                <a href="dashboard.php" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 px-4 bg-[#f3e6d7] text-[#171512] text-sm font-bold leading-normal tracking-[0.015em]">
                  <span class="truncate">Dashboard</span>
                </a>
                <a href="auth/logout.php" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 px-4 bg-[#f4f2f1] text-[#171512] text-sm font-bold leading-normal tracking-[0.015em]">
                  <span class="truncate">Logout</span>
                </a>
              <?php else: ?>
                <a href="auth/login.php" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 px-4 bg-[#f3e6d7] text-[#171512] text-sm font-bold leading-normal tracking-[0.015em]">
                  <span class="truncate">Login</span>
                </a>
                <a href="auth/register.php" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 px-4 bg-[#f4f2f1] text-[#171512] text-sm font-bold leading-normal tracking-[0.015em]">
                  <span class="truncate">Register</span>
                </a>
              <?php endif; ?>
            </div>
          </div>
        </header>

        
        <div class="gap-1 px-6 flex flex-1 justify-center py-5">
          <div class="layout-content-container flex flex-col w-80">
            <h3 class="text-[#171512] text-lg font-bold leading-tight tracking-[-0.015em] px-4 pb-2 pt-4">Filter</h3>
            <form method="GET" action="search.php" class="flex flex-col max-w-[480px] flex-wrap items-end gap-4 px-4 py-3">
              <label class="flex flex-col min-w-40 flex-1">
                <p class="text-[#171512] text-base font-medium leading-normal pb-2">Type</p>
                <select
                  name="listing_type"
                  class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#171512] focus:outline-0 focus:ring-0 border border-[#e4e1dd] bg-white focus:border-[#e4e1dd] h-14 bg-[image:--select-button-svg] placeholder:text-[#827668] p-[15px] text-base font-normal leading-normal"
                >
                  <option value="">Any</option>
                  <option value="adoption">Adoption</option>
                  <option value="buy_sell">Buy/Sell</option>
                </select>
              </label>
              <label class="flex flex-col min-w-40 flex-1">
                <p class="text-[#171512] text-base font-medium leading-normal pb-2">Breed</p>
                <select
                  name="breed"
                  class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#171512] focus:outline-0 focus:ring-0 border border-[#e4e1dd] bg-white focus:border-[#e4e1dd] h-14 bg-[image:--select-button-svg] placeholder:text-[#827668] p-[15px] text-base font-normal leading-normal"
                >
                  <option value="">Any</option>
                  <?php
                  $breed_query = "SELECT DISTINCT breed FROM pets WHERE breed IS NOT NULL AND breed != ''";
                  $breed_result = mysqli_query($conn, $breed_query);
                  while ($breed = mysqli_fetch_assoc($breed_result)) {
                      echo "<option value=\"" . htmlspecialchars($breed['breed']) . "\">" . htmlspecialchars($breed['breed']) . "</option>";
                  }
                  ?>
                </select>
              </label>
              <label class="flex flex-col min-w-40 flex-1">
                <p class="text-[#171512] text-base font-medium leading-normal pb-2">Age</p>
                <select
                  name="age"
                  class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#171512] focus:outline-0 focus:ring-0 border border-[#e4e1dd] bg-white focus:border-[#e4e1dd] h-14 bg-[image:--select-button-svg] placeholder:text-[#827668] p-[15px] text-base font-normal leading-normal"
                >
                  <option value="">Any</option>
                  <?php
                  $age_query = "SELECT DISTINCT age FROM pets WHERE age IS NOT NULL ORDER BY age";
                  $age_result = mysqli_query($conn, $age_query);
                  while ($age = mysqli_fetch_assoc($age_result)) {
                      echo "<option value=\"" . htmlspecialchars($age['age']) . "\">" . htmlspecialchars($age['age']) . "</option>";
                  }
                  ?>
                </select>
              </label>
              <label class="flex flex-col min-w-40 flex-1">
                <p class="text-[#171512] text-base font-medium leading-normal pb-2">Location</p>
                <select
                  name="location"
                  class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-xl text-[#171512] focus:outline-0 focus:ring-0 border border-[#e4e1dd] bg-white focus:border-[#e4e1dd] h-14 bg-[image:--select-button-svg] placeholder:text-[#827668] p-[15px] text-base font-normal leading-normal"
                >
                  <option value="">Any</option>
                  <?php
                  $location_query = "SELECT DISTINCT location FROM pets WHERE location IS NOT NULL AND location != ''";
                  $location_result = mysqli_query($conn, $location_query);
                  while ($location = mysqli_fetch_assoc($location_result)) {
                      echo "<option value=\"" . htmlspecialchars($location['location']) . "\">" . htmlspecialchars($location['location']) . "</option>";
                  }
                  ?>
                </select>
              </label>
              <button type="submit" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 px-4 bg-[#f3e6d7] text-[#171512] text-sm font-bold leading-normal tracking-[0.015em]">
                <span class="truncate">Search</span>
              </button>
            </form>
          </div>
          <div class="layout-content-container flex flex-col max-w-[960px] flex-1">
            <div class="@container">
              <div class="@[480px]:p-4">
                <div
                  class="flex min-h-[480px] flex-col gap-6 bg-cover bg-center bg-no-repeat @[480px]:gap-8 @[480px]:rounded-xl items-center justify-center p-4"
                  style='background-image: linear-gradient(rgba(0, 0, 0, 0.1) 0%, rgba(0, 0, 0, 0.4) 100%), url("assets/images/uploads/bg_dog.png");'
                >
                  <h1
                    class="text-white text-4xl font-black leading-tight tracking-[-0.033em] @[480px]:text-5xl @[480px]:font-black @[480px]:leading-tight @[480px]:tracking-[-0.033em] text-center"
                  >
                    Find Your Furry Friend Today!
                  </h1>
                  <div class="flex-wrap gap-3 flex justify-center">
                    <a href="search.php?listing_type=adoption" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 px-4 @[480px]:h-12 @[480px]:px-5 bg-[#f3e6d7] text-[#171512] text-sm font-bold leading-normal tracking-[0.015em] @[480px]:text-base @[480px]:font-bold @[480px]:leading-normal @[480px]:tracking-[0.015em]">
                      <span class="truncate">Adopt a Pet</span>
                    </a>
                    <a href="search.php?listing_type=buy_sell" class="flex min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 px-4 @[480px]:h-12 @[480px]:px-5 bg-[#f4f2f1] text-[#171512] text-sm font-bold leading-normal tracking-[0.015em] @[480px]:text-base @[480px]:font-bold @[480px]:leading-normal @[480px]:tracking-[0.015em]">
                      <span class="truncate">Sell/List a Pet</span>
                    </a>
                  </div>
                </div>
              </div>
            </div>
            <h2 class="text-[#171512] text-[22px] font-bold leading-tight tracking-[-0.015em] px-4 pb-3 pt-5">Pet Listings</h2>
            <div class="grid grid-cols-[repeat(auto-fit,minmax(158px,1fr))] gap-3 p-4">
              <?php
              $query = "SELECT * FROM pets ORDER BY created_at DESC LIMIT 6";
              $result = mysqli_query($conn, $query);
              while ($pet = mysqli_fetch_assoc($result)):
              ?>
                <div class="flex flex-col gap-3 pb-3">
                  <div
                    class="w-full bg-center bg-no-repeat aspect-[3/4] bg-cover rounded-xl"
                    style='background-image: url("<?php echo $pet['photo'] ? "assets/images/uploads/" . htmlspecialchars(basename($pet['photo'])) : "assets/images/placeholder.jpg"; ?>");'
                  ></div>
                  <div>
                    <p class="text-[#171512] text-base font-medium leading-normal"><?php echo htmlspecialchars($pet['name']); ?></p>
                    <p class="text-[#827668] text-sm font-normal leading-normal"><?php echo htmlspecialchars($pet['breed'] ? $pet['breed'] . ", " : "") . ($pet['age'] ? $pet['age'] . " years old" : ""); ?></p>
                    <?php if ($pet['listing_type'] == 'buy_sell' && $pet['price']): ?>
                      <p class="text-[#827668] text-sm font-normal leading-normal">Price: $<?php echo number_format($pet['price'], 2); ?></p>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endwhile; ?>
            </div>
          </div>
        </div>
         <?php include 'includes/footer.php'; ?>
      </div>
    </div>
</body>

</html>