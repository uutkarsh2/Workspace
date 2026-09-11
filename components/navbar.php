<?php

$current_page = basename($_SERVER['PHP_SELF']);

$user_name = $_SESSION['user_name'] ?? 'User';
$user_role = $_SESSION['user_role'] ?? 'User';

?>

<aside class="w-64 min-h-screen bg-slate-900 text-white flex flex-col shrink-0">

    <!-- ========================= -->
    <!-- LOGO -->
    <!-- ========================= -->

    <div class="px-8 py-7 border-b border-slate-700">

        <h1 class="text-2xl font-bold">
            Workspace<span class="text-blue-500">Hub</span>
        </h1>

        <p class="text-sm text-slate-400 mt-1">
            Meeting Management
        </p>

    </div>


    <!-- ========================= -->
    <!-- NAVIGATION -->
    <!-- ========================= -->

    <nav class="flex-1 px-5 py-8">

        <p class="text-xs uppercase tracking-wider text-slate-500 px-4 mb-4">
            Menu
        </p>


        <!-- ========================= -->
        <!-- DASHBOARD -->
        <!-- ========================= -->

        <a
            href="/Project/index.php"
            class="flex items-center gap-4 px-4 py-3 rounded-xl mb-2 transition
            <?php
            echo $current_page === 'index.php'
                ? 'bg-blue-600 text-white'
                : 'text-slate-300 hover:bg-slate-800 hover:text-white';
            ?>">

            <span class="text-lg">
                ▦
            </span>

            <span class="font-medium">
                Dashboard
            </span>

        </a>


        <!-- ========================= -->
        <!-- WORKSPACES -->
        <!-- ========================= -->

        <a
            href="/Project/pages/workspaces.php"
            class="flex items-center gap-4 px-4 py-3 rounded-xl mb-2 transition
            <?php
            echo $current_page === 'workspaces.php'
                ? 'bg-blue-600 text-white'
                : 'text-slate-300 hover:bg-slate-800 hover:text-white';
            ?>">

            <span class="text-lg">
                □
            </span>

            <span class="font-medium">
                Workspaces
            </span>

        </a>


        <!-- ========================= -->
        <!-- MEETINGS -->
        <!-- ========================= -->

        <a
            href="/Project/pages/meetings.php"
            class="flex items-center gap-4 px-4 py-3 rounded-xl mb-2 transition
            <?php
            echo $current_page === 'meetings.php'
                ? 'bg-blue-600 text-white'
                : 'text-slate-300 hover:bg-slate-800 hover:text-white';
            ?>">

            <span class="text-lg">
                ◷
            </span>

            <span class="font-medium">
                Meetings
            </span>

        </a>


        <!-- ========================= -->
        <!-- MY BOOKINGS -->
        <!-- ========================= -->

        <a
            href="/Project/pages/mybooking.php"
            class="flex items-center gap-4 px-4 py-3 rounded-xl mb-2 transition
            <?php
            echo $current_page === 'mybooking.php'
                ? 'bg-blue-600 text-white'
                : 'text-slate-300 hover:bg-slate-800 hover:text-white';
            ?>">

            <span class="text-lg">
                ▤
            </span>

            <span class="font-medium">
                My Bookings
            </span>

        </a>


        <!-- ========================= -->
        <!-- DIVIDER -->
        <!-- ========================= -->

        <div class="border-t border-slate-700 my-8"></div>


        <!-- ========================= -->
        <!-- SETTINGS -->
        <!-- ========================= -->

        <a
            href="/Project/pages/settings.php"
            class="flex items-center gap-4 px-4 py-3 rounded-xl transition
            <?php
            echo $current_page === 'settings.php'
                ? 'bg-blue-600 text-white'
                : 'text-slate-300 hover:bg-slate-800 hover:text-white';
            ?>">

            <span class="text-lg">
                ⚙
            </span>

            <span class="font-medium">
                Settings
            </span>

        </a>

    </nav>


    <!-- ========================= -->
    <!-- USER / LOGOUT -->
    <!-- ========================= -->

    <div class="px-5 py-6 border-t border-slate-700">


        <!-- User Information -->

        <div class="flex items-center gap-3 px-3 mb-5">

            <!-- Avatar -->

            <div
                class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-semibold">

                <?= strtoupper(substr($user_name, 0, 1)) ?>

            </div>


            <!-- Name -->

            <div class="min-w-0">

                <p class="font-medium truncate">

                    <?= htmlspecialchars($user_name) ?>

                </p>

                <p class="text-xs text-slate-400 capitalize">

                    <?= htmlspecialchars($user_role) ?>

                </p>

            </div>

        </div>


        <!-- Logout -->

        <a
            href="/Project/logout.php"
            class="flex items-center gap-4 px-4 py-3 rounded-xl text-slate-300 hover:bg-slate-800 hover:text-white transition">

            <span class="text-lg">
                ↪
            </span>

            <span class="font-medium">
                Logout
            </span>

        </a>

    </div>

</aside>