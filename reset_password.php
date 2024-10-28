<?php
// Include database connection
require 'database.php'; // Ensure this file contains the correct database connection code

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Validate the token and expiry
    $stmt = $conn->prepare("SELECT email FROM usercredentials WHERE reset_token = ? AND token_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Show password reset form with CSS styles
        echo '<!DOCTYPE html>
              <html lang="en">
              <head>
                  <meta charset="UTF-8">
                  <meta name="viewport" content="width=device-width, initial-scale=1.0">
                  <title>Password Reset</title>
                  <link rel="stylesheet" href="style.css"> <!-- Link to your CSS file -->
              </head>
              <body>
                  <div class="update-password-container">
                      <form method="POST" action="update_password.php">
                          <input type="hidden" name="token" value="' . htmlspecialchars($token) . '">
                              <label for="new_password">New Password:</label>
                          <div class="input-box">
                              <input type="password" name="new_password" id="new_password" required>
                          </div>
                            <label for="confirm_password">Confirm Password:</label>
                          <div class="input-box">
                              <input type="password" name="confirm_password" id="confirm_password" required>
                          </div>
                          <input type="submit" value="Reset Password">
                      </form>
                  </div>
              </body>
              </html>';
    } else {
        echo 'Invalid or expired token.';
    }
} else {
    echo 'No token provided.';
}
?>