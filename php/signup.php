<?php
require('connect.php');
session_start();

if (isset($_POST['signUp'])) {
    $custID = $_POST['custID'];
    $custName = $_POST['custName'];
    $custPhone = $_POST['custPhone'];
    $custEmail = $_POST['custEmail'];
    $custPassword = $_POST['custPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    $hashedPassword = md5(string: $custPassword);

    if ($custPassword !== $confirmPassword) {
        $_SESSION['error_password'] = "Passwords do not match!";

    } else {
        $checkCustID = "SELECT * FROM customer WHERE custID = '$custID'";
        $result = $connect->query($checkCustID);
        if ($result->num_rows > 0) {
            $_SESSION['error_username'] = "Username already exists!";
        } else {
            $insertQuery = "INSERT INTO customer (custID, custName, custPhone, custEmail, custPassword) 
                            VALUES ('$custID', '$custName', '$custPhone', '$custEmail', '$hashedPassword')";

            if ($connect->query($insertQuery) === TRUE) {
                header("Location: login.php");
                exit();
            } else {
                $_SESSION['error_general'] = "Error: " . $connect->error;
            }
        }
    }
    header("Location: signup.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
</head>

<body>
    <section class="py-5">
        <div class="container">
            <div class="d-flex justify-content-center align-items-center" style="overflow-y: hidden; height:90vh">
                <div class="col-md-6">
                    <h1 style="text-align: center" class="mb-4">CREATE ACCOUNT</h1>
                    <div class="register-container">
                        <?php if (isset($_SESSION['error_username'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo $_SESSION['error_username'];
                                unset($_SESSION['error_username']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['error_password'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo $_SESSION['error_password'];
                                unset($_SESSION['error_password']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['error_general'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo $_SESSION['error_general'];
                                unset($_SESSION['error_general']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        <form method="POST" action="signup.php" id="registerForm">
                            <div class="mb-3 position-relative">
                                <input type="text" name="custID" class="form-control" placeholder="Username" required>
                                <i class="form-icon bi bi-person"></i>
                            </div>
                            <div class="mb-3 position-relative">
                                <input type="text" name="custName" class="form-control" placeholder="Full Name"
                                    required>
                                <i class="form-icon bi bi-card-text"></i>
                            </div>
                            <div class="mb-3 position-relative">
                                <input type="tel" name="custPhone" class="form-control" placeholder="Tel No" required>
                                <i class="form-icon bi bi-telephone"></i>
                            </div>
                            <div class="mb-3 position-relative">
                                <input type="email" name="custEmail" class="form-control" placeholder="Email" required>
                                <i class="form-icon bi bi-envelope"></i>
                            </div>
                            <div class="mb-3 position-relative">
                                <input type="password" name="custPassword" class="form-control" placeholder="Password"
                                    required>
                                <i class="form-icon bi bi-eye-slash"></i>
                            </div>
                            <div id="password-strength" class="mt-2"></div>
                            <div class="mb-3 position-relative">
                                <input type="password" name="confirmPassword" class="form-control"
                                    placeholder="Confirm Password" required>
                                <i class="form-icon bi bi-eye-slash"></i>

                            </div>
                            <div class="d-grid">
                                <button type="submit" style="margin:10px 0 10px; font-weight:bold"
                                    class="btn btn-warning btn-lg" name="signUp">SIGN UP</button>
                            </div>
                            <hr>
                            <div class="login-link mt-3">
                                <p>Already have an account? <a href="./login.php" style="margin-left: 10px; color:black"
                                        class="btn btn-outline-warning btn-lg" id="logIn">Login</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const passwordInput = document.querySelector('input[name="custPassword"]');
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
    </script>
    <style>
        h1 {
            font-weight: 700;
            line-height: 1.5;
            color: #eee;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #333;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .register-container {
            background-color: #eee;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .register-container input {
            border-radius: 20px;
            height: 50px;
        }

        .register-container .btn-primary {
            width: 100%;
            font-weight: bold;
        }

        .form-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .login-link {
            text-align: center;
        }
    </style>
</body>

</html>