<?php
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username == "admin" && $password == "1234") {
        $_SESSION['admin'] = true;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid login credentials!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box; /* 🔥 FIX */
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: url('../assets/images/bg.jpg') center/cover no-repeat;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .overlay {
            position: absolute;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.55);
        }

        .box {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 380px;
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 18px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.25);
            text-align: center;
        }

        .logo {
            font-size: 30px;
            color: #2d6a4f;
            margin-bottom: 10px;
        }

        .box h2 {
            margin-bottom: 20px;
        }

        .input-group {
            position: relative;
            width: 100%; /* 🔥 FIX */
            margin-bottom: 15px;
        }

        .input-group i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #888;
        }

        input {
            width: 100%;
            padding: 11px 12px 11px 40px; /* space for icon */
            border-radius: 10px;
            border: 1px solid #ddd;
        }

        input:focus {
            border-color: #2d6a4f;
            outline: none;
        }

        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #2d6a4f, #1b4332);
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
        }

        button:hover {
            transform: translateY(-1px);
        }

        .error {
            color: #e63946;
            margin-bottom: 10px;
        }

        @media (max-width: 768px) {
            .box {
                margin: 15px;
            }
        }
    </style>
</head>

<body>

<div class="overlay"></div>

<div class="box">

    <div class="logo">
        <i class="fa-solid fa-paw"></i>
    </div>

    <h2>Admin Login</h2>

    <?php if ($error): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">

        <div class="input-group">
            <i class="fa-solid fa-user"></i>
            <input type="text" name="username" placeholder="Username" required>
        </div>

        <div class="input-group">
            <i class="fa-solid fa-lock"></i>
            <input type="password" name="password" placeholder="Password" required>
        </div>

        <button type="submit">
            <i class="fa-solid fa-right-to-bracket"></i> Login
        </button>

    </form>

</div>

</body>
</html>