<?php

session_start();

require_once "config/database.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST['email'] ?? "");
    $password = $_POST['password'] ?? "";

    if (empty($email) || empty($password)) {

        $error = "Please enter email and password.";

    } else {

        $stmt = $conn->prepare("
            SELECT id, name, email, password, role
            FROM users
            WHERE email = ?
            LIMIT 1
        ");

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && $password === $user['password']) {

            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];

            $stmt->close();
            $conn->close();

            header("Location: index.php");
            exit;

        } else {

            $error = "Invalid email or password.";
        }

        $stmt->close();
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
        content="width=device-width, initial-scale=1.0"
    >

    <title>Login | WorkspaceHub</title>

    <link
        rel="stylesheet"
        href="assets/css/output.css"
    >

</head>


<body class="min-h-screen bg-[#FFF7F8] text-[#24171d]">


<div class="min-h-screen flex flex-col lg:flex-row">


    <!-- LEFT SIDE -->
    <div
        class="hidden lg:flex lg:w-1/2 bg-[#3B0A1E] text-white relative overflow-hidden"
    >

        <!-- Decorative circles -->

        <div
            class="absolute -top-24 -left-24 w-72 h-72 rounded-full bg-rose-500/10"
        ></div>

        <div
            class="absolute -bottom-32 -right-20 w-96 h-96 rounded-full bg-rose-500/10"
        ></div>


        <div
            class="relative z-10 flex flex-col justify-center px-16 xl:px-24 w-full"
        >

            <!-- Logo -->

            <div class="mb-12">

                <h1 class="text-4xl font-bold tracking-tight">

                    Workspace<span class="text-rose-400">Hub</span>

                </h1>

                <p class="text-white/60 mt-2">
                    Meeting Management System
                </p>

            </div>


            <!-- Main Text -->

            <div>

                <p
                    class="text-rose-400 text-sm font-semibold uppercase tracking-widest mb-4"
                >
                    Workspace Management
                </p>

                <h2
                    class="text-4xl xl:text-5xl font-bold leading-tight"
                >
                    Find the perfect<br>
                    space for your<br>
                    next meeting.
                </h2>

                <p
                    class="text-white/60 mt-6 max-w-md leading-7"
                >
                    Manage meeting rooms, schedule bookings and
                    keep your workspace organized from one place.
                </p>

            </div>


            <!-- Bottom -->

            <div class="mt-14 flex gap-8">

                <div>

                    <p class="text-2xl font-bold">
                        3
                    </p>

                    <p class="text-sm text-white/50">
                        Workspaces
                    </p>

                </div>


                <div>

                    <p class="text-2xl font-bold">
                        24/7
                    </p>

                    <p class="text-sm text-white/50">
                        Management
                    </p>

                </div>


                <div>

                    <p class="text-2xl font-bold">
                        Easy
                    </p>

                    <p class="text-sm text-white/50">
                        Booking
                    </p>

                </div>

            </div>

        </div>

    </div>



    <!-- RIGHT SIDE -->

    <div
        class="flex-1 flex items-center justify-center px-5 py-10 sm:px-8"
    >

        <div class="w-full max-w-md">


            <!-- Mobile Logo -->

            <div class="lg:hidden text-center mb-8">

                <h1 class="text-3xl font-bold">

                    Workspace<span class="text-rose-600">Hub</span>

                </h1>

                <p class="text-sm text-[#6B5B63] mt-2">
                    Meeting Management System
                </p>

            </div>



            <!-- Login Card -->

            <div
                class="bg-white rounded-3xl border border-rose-100 shadow-sm p-7 sm:p-9"
            >


                <!-- Heading -->

                <div class="mb-8">

                    <p
                        class="text-sm font-semibold text-rose-600 mb-2"
                    >
                        Welcome back
                    </p>

                    <h2
                        class="text-3xl font-bold text-[#24171d]"
                    >
                        Sign in
                    </h2>

                    <p
                        class="text-[#6B5B63] mt-2"
                    >
                        Sign in to manage your workspace bookings.
                    </p>

                </div>



                <!-- Error -->

                <?php if (!empty($error)): ?>

                    <div
                        class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
                    >

                        <?= htmlspecialchars($error) ?>

                    </div>

                <?php endif; ?>



                <!-- Login Form -->

                <form method="POST" class="space-y-5">


                    <!-- Email -->

                    <div>

                        <label
                            for="email"
                            class="block text-sm font-semibold text-[#24171d] mb-2"
                        >
                            Email Address
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                            placeholder="Enter your email"
                            required
                            autocomplete="email"
                            class="w-full rounded-xl border border-gray-200 px-4 py-3.5 text-sm outline-none transition focus:border-rose-500 focus:ring-4 focus:ring-rose-100"
                        >

                    </div>



                    <!-- Password -->

                    <div>

                        <div class="flex items-center justify-between mb-2">

                            <label
                                for="password"
                                class="block text-sm font-semibold text-[#24171d]"
                            >
                                Password
                            </label>

                        </div>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Enter your password"
                            required
                            autocomplete="current-password"
                            class="w-full rounded-xl border border-gray-200 px-4 py-3.5 text-sm outline-none transition focus:border-rose-500 focus:ring-4 focus:ring-rose-100"
                        >

                    </div>



                    <!-- Sign In Button -->

                    <button
                        type="submit"
                        class="w-full rounded-xl bg-[#3B0A1E] px-5 py-3.5 text-sm font-semibold text-white transition hover:bg-[#4d0d29] focus:outline-none focus:ring-4 focus:ring-rose-100"
                    >

                        Sign In

                    </button>


                </form>



                <!-- Development Login -->

                <div
                    class="mt-7 rounded-xl bg-rose-50 border border-rose-100 p-4"
                >

                    <p
                        class="text-xs font-semibold uppercase tracking-wide text-rose-600 mb-2"
                    >
                        Development Login
                    </p>

                    <p class="text-sm text-[#6B5B63]">
                        <span class="font-medium">Email:</span>
                        admin@workspacehub.com
                    </p>

                    <p class="text-sm text-[#6B5B63] mt-1">
                        <span class="font-medium">Password:</span>
                        admin123
                    </p>

                </div>


            </div>



            <!-- Footer -->

            <p
                class="text-center text-xs text-[#9b858e] mt-6"
            >
                © <?= date('Y') ?> WorkspaceHub. All rights reserved.
            </p>


        </div>

    </div>

</div>


</body>

</html>