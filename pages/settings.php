<?php
require_once "../config/auth.php";
require_once "../config/database.php";

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT name, email, role
    FROM users
    WHERE id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    die("User not found.");
}

$success = "";
$error = "";

/* Change Password */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $current_password = $_POST['current_password'] ?? "";
    $new_password = $_POST['new_password'] ?? "";
    $confirm_password = $_POST['confirm_password'] ?? "";

    if (
        empty($current_password) ||
        empty($new_password) ||
        empty($confirm_password)
    ) {
        $error = "Please fill all password fields.";
    } elseif ($new_password !== $confirm_password) {
        $error = "New password and confirm password do not match.";
    } elseif (strlen($new_password) < 6) {
        $error = "New password must be at least 6 characters.";
    } else {

        $password_stmt = $conn->prepare("
            SELECT password
            FROM users
            WHERE id = ?
        ");

        $password_stmt->bind_param("i", $user_id);
        $password_stmt->execute();

        $password_result = $password_stmt->get_result();
        $password_data = $password_result->fetch_assoc();

        if ($password_data && $current_password === $password_data['password']) {

            $update_stmt = $conn->prepare("
                UPDATE users
                SET password = ?
                WHERE id = ?
            ");

            $update_stmt->bind_param(
                "si",
                $new_password,
                $user_id
            );

            if ($update_stmt->execute()) {
                $success = "Password changed successfully.";
            } else {
                $error = "Unable to change password.";
            }

            $update_stmt->close();

        } else {
            $error = "Current password is incorrect.";
        }

        $password_stmt->close();
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Settings | WorkspaceHub</title>

    <link rel="stylesheet" href="../assets/css/output.css">
</head>

<body class="bg-[#FFF7F8] text-[#24171d]">

<div class="min-h-screen flex">

    <!-- Navbar / Sidebar -->
    <?php include "../components/navbar.php"; ?>

    <!-- Main Content -->
    <main class="flex-1 min-w-0 pt-16 lg:pt-0">

        <div class="p-5 sm:p-7 lg:p-10 max-w-6xl mx-auto">

            <!-- Header -->
            <div class="mb-8">

                <p class="text-sm font-medium text-rose-600 mb-1">
                    Account
                </p>

                <h1 class="text-3xl font-bold text-[#24171d]">
                    Settings
                </h1>

                <p class="text-[#6B5B63] mt-2">
                    Manage your profile and account preferences.
                </p>

            </div>


            <!-- Messages -->
            <?php if ($success): ?>

                <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-5 py-4 text-green-700">
                    <?= htmlspecialchars($success) ?>
                </div>

            <?php endif; ?>


            <?php if ($error): ?>

                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-red-700">
                    <?= htmlspecialchars($error) ?>
                </div>

            <?php endif; ?>


            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">


                <!-- Profile Card -->
                <div class="lg:col-span-2">

                    <div class="bg-white rounded-2xl border border-rose-100 shadow-sm">

                        <div class="px-6 py-5 border-b border-rose-100">

                            <h2 class="text-lg font-semibold">
                                Profile Information
                            </h2>

                            <p class="text-sm text-[#6B5B63] mt-1">
                                Your account information.
                            </p>

                        </div>


                        <div class="p-6 space-y-5">

                            <!-- Name -->
                            <div>

                                <label class="block text-sm font-medium mb-2">
                                    Full Name
                                </label>

                                <input
                                    type="text"
                                    value="<?= htmlspecialchars($user['name']) ?>"
                                    readonly
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none"
                                >

                            </div>


                            <!-- Email -->
                            <div>

                                <label class="block text-sm font-medium mb-2">
                                    Email Address
                                </label>

                                <input
                                    type="email"
                                    value="<?= htmlspecialchars($user['email']) ?>"
                                    readonly
                                    class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm outline-none"
                                >

                            </div>


                            <!-- Role -->
                            <div>

                                <label class="block text-sm font-medium mb-2">
                                    Account Role
                                </label>

                                <div class="flex items-center gap-3">

                                    <span class="inline-flex items-center rounded-full bg-rose-50 px-4 py-2 text-sm font-medium text-rose-700">

                                        <?= htmlspecialchars(ucfirst($user['role'])) ?>

                                    </span>

                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- Change Password -->
                    <div class="bg-white rounded-2xl border border-rose-100 shadow-sm mt-6">

                        <div class="px-6 py-5 border-b border-rose-100">

                            <h2 class="text-lg font-semibold">
                                Change Password
                            </h2>

                            <p class="text-sm text-[#6B5B63] mt-1">
                                Update your account password.
                            </p>

                        </div>


                        <form method="POST" class="p-6 space-y-5">

                            <!-- Current Password -->
                            <div>

                                <label class="block text-sm font-medium mb-2">
                                    Current Password
                                </label>

                                <input
                                    type="password"
                                    name="current_password"
                                    required
                                    class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-rose-500 focus:ring-2 focus:ring-rose-100 outline-none"
                                    placeholder="Enter current password"
                                >

                            </div>


                            <!-- New Password -->
                            <div>

                                <label class="block text-sm font-medium mb-2">
                                    New Password
                                </label>

                                <input
                                    type="password"
                                    name="new_password"
                                    required
                                    minlength="6"
                                    class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-rose-500 focus:ring-2 focus:ring-rose-100 outline-none"
                                    placeholder="Enter new password"
                                >

                            </div>


                            <!-- Confirm Password -->
                            <div>

                                <label class="block text-sm font-medium mb-2">
                                    Confirm New Password
                                </label>

                                <input
                                    type="password"
                                    name="confirm_password"
                                    required
                                    minlength="6"
                                    class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-rose-500 focus:ring-2 focus:ring-rose-100 outline-none"
                                    placeholder="Confirm new password"
                                >

                            </div>


                            <!-- Button -->
                            <div class="pt-2">

                                <button
                                    type="submit"
                                    class="rounded-xl bg-[#3B0A1E] px-6 py-3 text-sm font-semibold text-white hover:bg-[#4d0d29] transition"
                                >
                                    Change Password
                                </button>

                            </div>

                        </form>

                    </div>

                </div>


                <!-- Right Side -->
                <div>

                    <!-- Account Summary -->
                    <div class="bg-[#3B0A1E] rounded-2xl p-6 text-white">

                        <div class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center text-xl font-bold mb-5">

                            <?= strtoupper(substr($user['name'], 0, 1)) ?>

                        </div>


                        <h2 class="text-xl font-bold">
                            <?= htmlspecialchars($user['name']) ?>
                        </h2>

                        <p class="text-white/70 text-sm mt-1">
                            <?= htmlspecialchars($user['email']) ?>
                        </p>


                        <div class="mt-6 pt-5 border-t border-white/10">

                            <p class="text-xs uppercase tracking-wider text-white/50">
                                Role
                            </p>

                            <p class="mt-1 font-medium">
                                <?= htmlspecialchars(ucfirst($user['role'])) ?>
                            </p>

                        </div>

                    </div>


                    <!-- Security Info -->
                    <div class="bg-white rounded-2xl border border-rose-100 shadow-sm p-6 mt-6">

                        <div class="flex items-center gap-3 mb-4">

                            <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center">
                                🔐
                            </div>

                            <h2 class="font-semibold">
                                Security
                            </h2>

                        </div>


                        <p class="text-sm text-[#6B5B63] leading-6">
                            Keep your password secure and do not share
                            your login credentials with other users.
                        </p>

                    </div>


                    <!-- Logout -->
                    <div class="bg-white rounded-2xl border border-rose-100 shadow-sm p-6 mt-6">

                        <h2 class="font-semibold mb-2">
                            Session
                        </h2>

                        <p class="text-sm text-[#6B5B63] mb-4">
                            Sign out from your WorkspaceHub account.
                        </p>

                        <a
                            href="../logout.php"
                            class="inline-flex items-center justify-center w-full rounded-xl border border-rose-200 px-4 py-3 text-sm font-semibold text-rose-600 hover:bg-rose-50 transition"
                        >
                            Logout
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </main>

</div>

</body>
</html>