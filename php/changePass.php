<?php
include('connect.php');
session_start();

$userID = "";
$isLoggedIn = isset($_SESSION['custID']); // Check if user is logged in

// If the user is logged in, retrieve their username
if ($isLoggedIn) {
    $userID = $_SESSION['custID'];
}

if (isset($_POST['changepass'])) {
    $userID = $isLoggedIn ? $_SESSION['custID'] : $_POST['userID'];
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmNewPassword = $_POST['confirmNewPassword'];

    $hashedCurrentPassword = md5($currentPassword);
    $hashedNewPassword = md5($newPassword);

    if ($newPassword !== $confirmNewPassword) {
        $_SESSION['error'] = "New passwords do not match!";
        header("Location: changePass.php");
        exit();
    }

    $sql = "SELECT * FROM customer WHERE custID='$userID' AND custPassword='$hashedCurrentPassword'";
    $result = $connect->query($sql);

    if ($result->num_rows > 0) {
        $updateQuery = "UPDATE customer SET custPassword='$hashedNewPassword' WHERE custID='$userID'";
        if ($connect->query($updateQuery) === TRUE) {
            $_SESSION['success'] = "Password updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating password: " . $connect->error;
        }
    } else {
        $_SESSION['error'] = "Username or current password is incorrect!";
    }

    header("Location: changePass.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
    rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.6.5/dist/sweetalert2.min.css" rel="stylesheet">

</head>

<body>
    <section class="py-5">
        <div class="container">
            <div class="d-flex justify-content-center align-items-center" style="overflow-y: hidden; height:90vh">
                <div class="col-md-8">
                <h1 class="text-center">Change Password</h1>
                    <div class="change-password-container">
                        <form method="post" action="changePass.php" id="changepass">
                            <div class="mb-3 position-relative">
                                <input type="text" class="form-control" name="userID" placeholder="Username" 
                                    value="<?php echo htmlspecialchars($userID); ?>" 
                                    <?php echo $isLoggedIn ? 'disabled' : 'required'; ?>>
                            </div>
                            <div class="mb-3 position-relative">
                                <input type="password" id="currentPassword" placeholder="Current Password"
                                    name="currentPassword" class="form-control" required>
                                <i class="form-icon bi bi-eye-slash"></i>
                            </div>
                            <div class="mb-3 position-relative">
                                <input type="password" id="newPassword" placeholder="New Password" name="newPassword"
                                    class="form-control" required>
                                <i class="form-icon bi bi-eye-slash"></i>
                            </div>
                            <div id="password-strength" class="mt-2"></div>
                            <div class="mb-3 position-relative">
                                <input type="password" id="confirmNewPassword" placeholder="Confirm Password"
                                    name="confirmNewPassword" class="form-control" required>
                                <i class="form-icon bi bi-lock"></i>
                            </div>
                            <div class="d-grid">
                                <input type="submit" name="changepass" class="btn btn-warning btn-lg" style="font-weight: 700"></input>
                            </div>
                            <div class="mt-3 d-flex justify-content-start">
                            <a href="<?php echo $isLoggedIn ? 'custProfile.php' : 'login.php'; ?>" style="margin:10px 0 10px; color:red; text-decoration:none; font-size:1.2rem" >Go Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        const passwordInput = document.querySelector('input[name="newPassword"]');
        passwordInput.addEventListener('input', function () {
            checkPasswordStrength(passwordInput.value);
        });

        function checkPasswordStrength(password) {
            let strength = "Weak";
            let color = "red";
            const passwordStrength = document.getElementById('password-strength');
            if (password.length >= 8) {
                strength = "Medium";
                color = "gold";
                if (/[A-Z]/.test(password) && /[!@#$%^&*(),.?":{}|<>]/.test(password)) {
                    strength = "Strong";
                    color = "green";
                }
            }
            passwordStrength.textContent = `Password Strength: ${strength}`;
            passwordStrength.style.color = color;
        }

        document.querySelectorAll(".form-icon").forEach((icon) => {
            icon.addEventListener("click", function () {
                const input = this.previousElementSibling;
                const type = input.getAttribute("type") === "password" ? "text" : "password";
                input.setAttribute("type", type);
                this.classList.toggle("bi-eye");
                this.classList.toggle("bi-eye-slash");
            });
        });
    document.getElementById('changepass').addEventListener('submit', function (event) {
        event.preventDefault(); // Prevent the form from submitting immediately

        // Display SweetAlert confirmation dialog
        Swal.fire({
            title: 'Are you sure?',
            text: 'Your password will be changed.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, change it',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('changepass').submit();
            }
        });
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.6.5/dist/sweetalert2.min.js"></script>

    <style>
        body,
        html {
            margin: 0;
            height: 100%;
            overflow: hidden;
            font-family: 'Montserrat', sans-serif;
            background-color: #333;
        }

        h1,
        h2,
        h3,
        h4 {
            color: #eee;
            margin: 10px 0;
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            line-height: 1.5;
        }

        .change-password-container {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 20px;
            background-color: #eee;
            max-width: 700px;
            margin: auto;
        }

        .change-password-container input{
            border-radius: 20px;
            height: 50px;
        }

        .change-password-container h1 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .form-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
    </style>
</body>

</html>