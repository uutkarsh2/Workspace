<?php

session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "config/database.php";


// If already logged in, go to dashboard

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}


$error = "";


// Handle login

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';


    if (empty($email) || empty($password)) {

        $error = "Please enter your email and password.";

    } else {

        $sql = "SELECT id, name, email, password, role FROM users WHERE email = ?";

        $stmt = $conn->prepare($sql);

        if (!$stmt) {

            $error = "Something went wrong. Please try again.";

        } else {

            $stmt->bind_param("s", $email);

            $stmt->execute();

            $result = $stmt->get_result();

            $user = $result->fetch_assoc();

            $stmt->close();


            if ($user && $password === $user['password']) {

                session_regenerate_id(true);

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];

                $conn->close();

                header("Location: index.php");
                exit;

            } else {

                $error = "Invalid email or password.";

            }

        }

    }

}

$conn->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Login | WorkspaceHub</title>

    <link
        rel="stylesheet"
        href="assets/css/output.css">

</head>


<body class="min-h-screen bg-slate-950">


<div class="min-h-screen flex items-center justify-center p-6">


    <div class="w-full max-w-md">


        <!-- Logo -->

        <div class="text-center mb-8">

            <h1 class="text-3xl font-bold text-white">

                Workspace<span class="text-blue-500">Hub</span>

            </h1>

            <p class="text-slate-400 mt-2">

                Meeting Management System

            </p>

        </div>


        <!-- Login Card -->

        <div class="bg-white rounded-2xl shadow-xl p-8">


            <div class="mb-7">

                <h2 class="text-2xl font-bold text-slate-900">

                    Welcome back

                </h2>

                <p class="text-slate-500 mt-2">

                    Sign in to manage your workspace bookings.

                </p>

            </div>


            <!-- Error -->

            <?php if (!empty($error)): ?>

                <div
                    class="mb-6 px-4 py-3 rounded-xl bg-red-50 border border-red-100 text-red-600 text-sm">

                    <?= htmlspecialchars($error) ?>

                </div>

            <?php endif; ?>


            <!-- Form -->

            <form
                method="POST"
                action="login.php">


                <!-- Email -->

                <div class="mb-5">

                    <label
                        for="email"
                        class="block text-sm font-medium text-slate-700 mb-2">

                        Email Address

                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        placeholder="admin@workspacehub.com"
                        required
                        class="w-full px-4 py-3 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                </div>


                <!-- Password -->

                <div class="mb-6">

                    <label
                        for="password"
                        class="block text-sm font-medium text-slate-700 mb-2">

                        Password

                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter your password"
                        required
                        class="w-full px-4 py-3 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                </div>


                <!-- Login -->

                <button
                    type="submit"
                    class="w-full py-3.5 rounded-xl bg-slate-900 text-white font-medium hover:bg-blue-600 transition">

                    Sign In

                </button>


            </form>


            <!-- Development Account -->

            <div class="mt-6 p-4 rounded-xl bg-slate-50 border border-slate-100">

                <p class="text-xs text-slate-400 uppercase tracking-wide">

                    Development Login

                </p>

                <p class="text-sm text-slate-600 mt-2">

                    admin@workspacehub.com

                </p>

                <p class="text-sm text-slate-600">

                    Password: password

                </p>

            </div>


        </div>


        <p class="text-center text-xs text-slate-500 mt-6">

            © <?= date('Y') ?> WorkspaceHub

        </p>


    </div>

</div>


</body>

</html>