

<div class="profile_nav" >
          <ul>
            <li><a href="profile.php">Profile Settings</a></li>
              <li><a href="update-password.php">Update Password</a></li>
            <li><a href="my-order.php">My Orders</a></li>
            <li><a href="#" id="logoutLinks">Sign Out</a></li>
          </ul>
        </div>
      </div>

      <script>
document.getElementById("logoutLinks").addEventListener("click", function(e) {
    e.preventDefault(); // prevent the default link click action
    var confirmAction = confirm("Are you sure you want to log out?");
    if (confirmAction) {
        // If user confirms logout, redirect to logout.php
        window.location.href = "logout.php";
    }
});
</script>