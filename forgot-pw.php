<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="edit-account.css"> <!-- reuse your existing CSS -->
</head>
<body>

    <div class="container">
        <h2>Reset Your Password</h2>
        <form action="forgot-pw-submit.php" method="POST">

            <!-- Email -->
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>

            <!-- New Password -->
            <div class="form-group">
                <label for="newPassword">New Password</label>
                <input type="password" id="newPassword" name="newPassword" placeholder="Enter new password" required>
            </div>

            <!-- Confirm New Password -->
            <div class="form-group">
                <label for="confirmPassword">Re-enter New Password</label>
                <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Re-enter new password" required>
            </div>

            <!-- Reset Button -->
            <div class="form-group">
                <button type="submit">Reset Password</button>
            </div>

        </form>
    </div>

</body>
</html>