<?php
require_once '../includes/header.php';
require_once '../includes/modal.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo "<script>showToast('Password reset link sent!', 'success');</script>";
}
renderModal('forgot-password-modal', 'Reset Password', 
    '<form id="resetForm" method="POST" onsubmit="return validateForm(\'resetForm\');">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required aria-describedby="email-error">
        <button type="submit">Send Reset Link</button>
    </form>');
?>
<main>
    <h2>Forgot Password</h2>
    <p>Click below to open the reset form.</p>
    <button class="btn" onclick="showModal('forgot-password-modal')">Reset Password</button>
</main>
<?php require_once '../includes/footer.php'; ?>