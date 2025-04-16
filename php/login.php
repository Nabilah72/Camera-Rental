<?php
include('connect.php');
session_start();

if (isset($_POST['login'])) {
    $userType = $_POST['userType'];
    $userID = $_POST['userID'];
    $password = $_POST['password'];
    $hashedPassword = md5($password);

    if ($userType == 'customer') {
        $sql = "SELECT * FROM customer WHERE custID='$userID'";
        $result = $connect->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row['custPassword'] === $hashedPassword) {
                $_SESSION['custID'] = $row['custID'];
                $_SESSION['custName'] = $row['custName'];
                error_log("Session custName assigned: " . $_SESSION['custName']);
                header("Location: ./custHome.php");
                exit();
            } else {
                $_SESSION['error'] = "Incorrect password!";
            }
        } else {
            $_SESSION['error'] = "Username does not exist!";
        }
    } elseif ($userType == 'admin') {
        $sql = "SELECT * FROM admin WHERE adminID='$userID'";
        $result = $connect->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row['adminPassword'] === $password) {
                $_SESSION['adminID'] = $row['adminID'];
                $_SESSION['adminName'] = $row['adminName'];
                $_SESSION['adminPhone'] = $row['adminPhone'];
                $_SESSION['adminEmail'] = $row['adminEmail'];
                $_SESSION['adminStatus'] = $row['adminStatus'];
                header("Location: ../adminphp/dashboard.php");
                exit();
            } else {
                $_SESSION['error'] = "Incorrect password!";
            }
        } else {
            $_SESSION['error'] = "Incorrect ID!";
        }
    }

    header("Location: login.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" href=".login.css"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <section>
        <div class="container">
            <div class="d-flex justify-content-center align-items-center vh-100" style="overflow-x: hidden;">
                <div class="col-md-6">
                    <h1 class="text-center mb-4">LOGIN</h1>
                    <div class="login-container">
                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= $_SESSION['error']; ?>
                                <?php unset($_SESSION['error']); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        <div class="toggle-buttons text-center mb-3">
                            <span class="active" id="customerToggle" style="font-size:1.3rem">Customer</span> |
                            <span id="adminToggle" style="font-size:1.3rem">Admin</span>
                        </div>
                        <form action="login.php" method="post" id="customerForm">
                            <input type="hidden" name="userType" id="userTypeCustomer" value="customer">
                            <div class="mb-3 position-relative">
                                <input type="text" class="form-control" placeholder="Username" name="userID" required>
                                <i class="form-icon bi bi-person"></i>
                            </div>
                            <div class="mb-3 position-relative">
                                <input type="password" class="form-control" placeholder="Password" name="password"
                                    required>
                                <i class="form-icon bi bi-eye-slash"></i>
                            </div>
                            <div class="d-flex justify-content-between mb-3 text-end">
                                <a href="./changePass.php"
                                    style="margin-left: auto; text-decoration:none; font-size:1.2rem"
                                    class="forgot-password">Forgot
                                    Password?</a>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-warning btn-lg"
                                    style="color: black; margin:5px 0; font-weight: bold;" name="login">LOGIN</button>
                            </div>
                            <hr>
                            <div class="register-link mt-3">
                                <p>Don't have an account?<a href="./signup.php" style="margin:5px 10px"
                                        class="btn btn-outline-warning btn-lg text-black " id="register">Register
                                        Now</a>
                                </p>
                            </div>

                        </form>
                        <form action="login.php" method="post" id="adminForm" style="display:none">
                            <input type="hidden" name="userType" id="userTypeAdmin" value="admin">
                            <div class="mb-3 position-relative">
                                <input type="text" class="form-control" placeholder="ID" name="userID" required>
                                <i class="form-icon bi bi-person"></i>
                            </div>
                            <div class="mb-3 position-relative">
                                <input type="password" class="form-control" placeholder="Password" name="password"
                                    required>
                                <i class="form-icon bi bi-eye-slash"></i>
                            </div>
                            <hr>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-warning btn-lg" style="font-weight:bold;"
                                    name="login">LOGIN</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script>

        const customerToggle = document.getElementById("customerToggle");
        const adminToggle = document.getElementById("adminToggle");
        const customerForm = document.getElementById("customerForm");
        const adminForm = document.getElementById("adminForm");

        customerToggle.addEventListener("click", () => {
            customerForm.style.display = "block";
            adminForm.style.display = "none";
            customerToggle.classList.add("active");
            adminToggle.classList.remove("active");
            document.getElementById('userTypeCustomer').value = 'customer';
        });

        adminToggle.addEventListener("click", () => {
            adminForm.style.display = "block";
            customerForm.style.display = "none";
            adminToggle.classList.add("active");
            customerToggle.classList.remove("active");
            document.getElementById('userTypeAdmin').value = 'admin';
        });

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
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            line-height: 1.5;
            color: #eee;
        }

        .login-container {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 20px;
            background-color: #eee;
        }

        .login-container .btn-primary {
            width: 100%;
            font-weight: bold;
        }

        .login-container input {
            border-radius: 20px;
            height: 50px;
        }

        .toggle-buttons span {
            cursor: pointer;
            padding: 0 10px;
            font-weight: bold;
        }

        .toggle-buttons span.active {
            color: #fdc50e;
        }

        .form-icon {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .register-link,
        .forgot-password {
            text-align: center;
        }
    </style>
</body>

</html>