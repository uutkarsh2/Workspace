<?php

$current_page = basename($_SERVER['PHP_SELF']);

$user_name = $_SESSION['user_name'] ?? 'User';
$user_role = $_SESSION['user_role'] ?? 'User';

?>

<!-- ========================================= -->
<!-- MOBILE TOP BAR -->
<!-- ========================================= -->

<div
    class="lg:hidden fixed top-0 left-0 right-0 z-40
           h-16 bg-[#3b0a1e] text-white
           flex items-center justify-between
           px-4 shadow-lg">

    <!-- Hamburger -->

    <button
        id="openSidebar"
        type="button"
        class="w-10 h-10 rounded-xl
               flex items-center justify-center
               text-2xl
               hover:bg-white/10
               transition">

        ☰

    </button>


    <!-- Logo -->

    <h1 class="text-lg font-bold">

        Workspace<span class="text-pink-400">Hub</span>

    </h1>


    <!-- User -->

    <div
        class="w-9 h-9 rounded-full
               bg-pink-600
               flex items-center justify-center
               font-semibold">

        <?= strtoupper(substr($user_name, 0, 1)) ?>

    </div>

</div>


<!-- ========================================= -->
<!-- OVERLAY -->
<!-- ========================================= -->

<div
    id="sidebarOverlay"
    class="fixed inset-0 z-40
           bg-black/60 hidden lg:hidden">
</div>


<!-- ========================================= -->
<!-- SIDEBAR -->
<!-- ========================================= -->

<aside
    id="sidebar"
    class="fixed lg:static
           inset-y-0 left-0
           z-50
           w-64
           min-h-screen
           bg-[#3b0a1e]
           text-white
           flex flex-col
           shrink-0

           -translate-x-full
           lg:translate-x-0

           transition-transform
           duration-300
           ease-in-out">


    <!-- ========================================= -->
    <!-- LOGO -->
    <!-- ========================================= -->

    <div class="px-7 py-7 border-b border-white/10">

        <div class="flex items-center justify-between">

            <h1 class="text-2xl font-bold">

                Workspace<span class="text-pink-400">Hub</span>

            </h1>


            <!-- Close -->

            <button
                id="closeSidebar"
                type="button"
                class="lg:hidden
                       w-9 h-9
                       rounded-lg
                       text-white/60
                       hover:text-white
                       hover:bg-white/10
                       transition">

                ✕

            </button>

        </div>


        <p class="text-sm text-white/50 mt-1">

            Meeting Management

        </p>

    </div>


    <!-- ========================================= -->
    <!-- NAVIGATION -->
    <!-- ========================================= -->

    <nav class="flex-1 px-4 py-7 overflow-y-auto">


        <p class="text-xs uppercase
                  tracking-wider
                  text-white/40
                  px-4 mb-4">

            Menu

        </p>


        <!-- Dashboard -->

        <a
            href="/Project/index.php"
            class="flex items-center gap-4
                   px-4 py-3.5
                   rounded-xl mb-2
                   transition

                   <?= $current_page === 'index.php'
                       ? 'bg-pink-600 text-white shadow-lg shadow-pink-950/30'
                       : 'text-white/70 hover:bg-white/10 hover:text-white'
                   ?>">

            <span class="text-xl">▦</span>

            <span class="font-medium">
                Dashboard
            </span>

        </a>


        <!-- Workspaces -->

        <a
            href="/Project/pages/workspaces.php"
            class="flex items-center gap-4
                   px-4 py-3.5
                   rounded-xl mb-2
                   transition

                   <?= $current_page === 'workspaces.php'
                       ? 'bg-pink-600 text-white shadow-lg shadow-pink-950/30'
                       : 'text-white/70 hover:bg-white/10 hover:text-white'
                   ?>">

            <span class="text-xl">□</span>

            <span class="font-medium">
                Workspaces
            </span>

        </a>


    
        <!-- My Bookings -->

        <a
            href="/Project/pages/mybooking.php"
            class="flex items-center gap-4
                   px-4 py-3.5
                   rounded-xl mb-2
                   transition

                   <?= $current_page === 'mybooking.php'
                       ? 'bg-pink-600 text-white shadow-lg shadow-pink-950/30'
                       : 'text-white/70 hover:bg-white/10 hover:text-white'
                   ?>">

            <span class="text-xl">▤</span>

            <span class="font-medium">
                My Bookings
            </span>

        </a>


        <div class="border-t border-white/10 my-7"></div>


        <!-- Settings -->

        <a
            href="/Project/pages/settings.php"
            class="flex items-center gap-4
                   px-4 py-3.5
                   rounded-xl
                   transition

                   <?= $current_page === 'settings.php'
                       ? 'bg-pink-600 text-white shadow-lg shadow-pink-950/30'
                       : 'text-white/70 hover:bg-white/10 hover:text-white'
                   ?>">

            <span class="text-xl">⚙</span>

            <span class="font-medium">
                Settings
            </span>

        </a>

    </nav>


    <!-- ========================================= -->
    <!-- USER -->
    <!-- ========================================= -->

    <div class="px-5 py-6 border-t border-white/10">


        <div class="flex items-center gap-3 px-2 mb-5">

            <div
                class="w-11 h-11 rounded-full
                       bg-pink-600
                       flex items-center justify-center
                       font-semibold shrink-0">

                <?= strtoupper(substr($user_name, 0, 1)) ?>

            </div>


            <div class="min-w-0">

                <p class="font-medium truncate">

                    <?= htmlspecialchars($user_name) ?>

                </p>

                <p class="text-xs text-white/50 capitalize">

                    <?= htmlspecialchars($user_role) ?>

                </p>

            </div>

        </div>


        <!-- Logout -->

        <a
            href="/Project/logout.php"
            class="flex items-center gap-4
                   px-4 py-3
                   rounded-xl
                   text-white/70
                   hover:bg-white/10
                   hover:text-white
                   transition">

            <span class="text-xl">↪</span>

            <span class="font-medium">
                Logout
            </span>

        </a>

    </div>

</aside>


<!-- ========================================= -->
<!-- SIDEBAR JAVASCRIPT -->
<!-- ========================================= -->

<script>

document.addEventListener("DOMContentLoaded", function () {

    const sidebar = document.getElementById("sidebar");
    const openBtn = document.getElementById("openSidebar");
    const closeBtn = document.getElementById("closeSidebar");
    const overlay = document.getElementById("sidebarOverlay");


    function openMenu() {

        sidebar.classList.remove("-translate-x-full");

        overlay.classList.remove("hidden");

        document.body.classList.add("overflow-hidden");

    }


    function closeMenu() {

        sidebar.classList.add("-translate-x-full");

        overlay.classList.add("hidden");

        document.body.classList.remove("overflow-hidden");

    }


    if (openBtn) {
        openBtn.addEventListener("click", openMenu);
    }


    if (closeBtn) {
        closeBtn.addEventListener("click", closeMenu);
    }


    if (overlay) {
        overlay.addEventListener("click", closeMenu);
    }


    // Close sidebar after navigation on mobile

    const links = sidebar.querySelectorAll("a");

    links.forEach(function (link) {

        link.addEventListener("click", function () {

            if (window.innerWidth < 1024) {
                closeMenu();
            }

        });

    });


    // Reset mobile menu on desktop

    window.addEventListener("resize", function () {

        if (window.innerWidth >= 1024) {

            overlay.classList.add("hidden");

            document.body.classList.remove("overflow-hidden");

        }

    });

});

</script>