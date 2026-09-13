<?php
require_once "config/auth.php";
require_once "config/database.php";


// ==========================================
// GET WORKSPACE DATA
// ==========================================

$sql = "
    SELECT
        w.*,

        CASE
            WHEN EXISTS (
                SELECT 1
                FROM bookings b
                WHERE b.workspace_id = w.id
                AND b.status = 'Booked'
                AND b.booking_date = CURDATE()
                AND b.end_time > CURTIME()
            )
            THEN 'Booked'
            ELSE 'Available'
        END AS current_status,

        (
            SELECT b.start_time
            FROM bookings b
            WHERE b.workspace_id = w.id
            AND b.status = 'Booked'
            AND b.booking_date = CURDATE()
            AND b.end_time > CURTIME()
            ORDER BY b.start_time ASC
            LIMIT 1
        ) AS booked_start_time,

        (
            SELECT b.end_time
            FROM bookings b
            WHERE b.workspace_id = w.id
            AND b.status = 'Booked'
            AND b.booking_date = CURDATE()
            AND b.end_time > CURTIME()
            ORDER BY b.start_time ASC
            LIMIT 1
        ) AS booked_end_time

    FROM workspaces w

    ORDER BY w.id ASC
";


$result = $conn->query($sql);

if (!$result) {
    die("Workspace query failed: " . $conn->error);
}


// ==========================================
// WORKSPACE COUNTS
// ==========================================

$total_workspaces = $result->num_rows;

$available_count = 0;
$booked_count = 0;


// Store workspaces because result is used again
$workspaces = [];

while ($workspace = $result->fetch_assoc()) {

    $workspaces[] = $workspace;

    if ($workspace['current_status'] === 'Available') {
        $available_count++;
    } else {
        $booked_count++;
    }
}


?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        Workspace Dashboard | WorkspaceHub
    </title>

    <link
        rel="stylesheet"
        href="assets/css/output.css">

</head>


<body class="bg-[#fff7f8] text-[#24171d]">


<div class="min-h-screen flex">


    <!-- ========================================== -->
    <!-- SIDEBAR -->
    <!-- ========================================== -->

    <?php require_once "components/navbar.php"; ?>


    <!-- ========================================== -->
    <!-- MAIN CONTENT -->
    <!-- ========================================== -->

    <main class="flex-1 min-w-0 pt-16 lg:pt-0">


        <!-- ====================================== -->
        <!-- TOP HEADER -->
        <!-- ====================================== -->

        <header
            class="bg-white border-b border-rose-100">

            <div
                class="px-6 lg:px-10 py-5
                       flex items-center
                       justify-between gap-5">


                <!-- LEFT -->

                <div>

                    <p
                        class="text-sm
                               text-rose-500
                               font-medium">

                        Workspace Management

                    </p>

                    <h1
                        class="text-xl lg:text-2xl
                               font-semibold
                               text-[#24171d]">

                        Workspace Dashboard

                    </h1>

                </div>


                <!-- RIGHT -->

                <div class="flex items-center gap-4">


                    <!-- Notification -->

                    <button
                        type="button"
                        class="hidden sm:flex
                               w-11 h-11
                               rounded-full
                               bg-rose-50
                               border border-rose-100
                               items-center justify-center
                               text-lg
                               hover:bg-rose-100
                               transition">

                        🔔

                    </button>


                    <!-- User -->

                    <div
                        class="hidden sm:flex
                               items-center gap-3">


                        <div class="text-right">

                            <p
                                class="font-semibold
                                       text-[#24171d]">

                                <?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?>

                            </p>

                            <p
                                class="text-xs
                                       text-[#9b858e]">

                                <?= htmlspecialchars(ucfirst($_SESSION['user_role'] ?? 'User')) ?>

                            </p>

                        </div>


                        <div
                            class="w-11 h-11
                                   rounded-full
                                   bg-rose-600
                                   text-white
                                   flex items-center
                                   justify-center
                                   font-semibold">

                            <?= strtoupper(
                                substr(
                                    $_SESSION['user_name'] ?? 'U',
                                    0,
                                    1
                                )
                            ) ?>

                        </div>

                    </div>

                </div>

            </div>

        </header>


        <!-- ====================================== -->
        <!-- DASHBOARD CONTENT -->
        <!-- ====================================== -->

        <section
            class="px-6 lg:px-10
                   py-8 lg:py-10">


            <div
                class="max-w-7xl
                       mx-auto">


                <!-- ================================= -->
                <!-- WELCOME -->
                <!-- ================================= -->

                <div class="mb-8">


                    <p
                        class="text-sm
                               font-medium
                               text-rose-500
                               uppercase
                               tracking-wider">

                        WorkspaceHub

                    </p>


                    <h2
                        class="text-3xl lg:text-4xl
                               font-bold
                               tracking-tight
                               text-[#24171d]
                               mt-2">

                        Find Your Perfect Workspace

                    </h2>


                    <p
                        class="text-[#8f7c84]
                               mt-2
                               text-base">

                        Book a workspace for productive meetings
                        and discussions.

                    </p>

                </div>


                <!-- ================================= -->
                <!-- STAT CARDS -->
                <!-- ================================= -->

                <div
                    class="grid
                           grid-cols-1
                           sm:grid-cols-3
                           gap-4
                           mb-10">


                    <!-- TOTAL -->

                    <div
                        class="bg-white
                               border border-rose-100
                               rounded-2xl
                               p-5
                               shadow-sm
                               hover:shadow-md
                               transition">


                        <div
                            class="flex items-center
                                   justify-between">


                            <div>

                                <p
                                    class="text-sm
                                           text-[#9b858e]">

                                    Total Workspaces

                                </p>

                                <p
                                    class="text-3xl
                                           font-bold
                                           text-[#24171d]
                                           mt-2">

                                    <?= $total_workspaces ?>

                                </p>

                            </div>


                            <div
                                class="w-12 h-12
                                       rounded-xl
                                       bg-rose-50
                                       text-rose-600
                                       flex items-center
                                       justify-center
                                       text-xl">

                                ◫

                            </div>

                        </div>

                    </div>


                    <!-- AVAILABLE -->

                    <div
                        class="bg-white
                               border border-emerald-100
                               rounded-2xl
                               p-5
                               shadow-sm
                               hover:shadow-md
                               transition">


                        <div
                            class="flex items-center
                                   justify-between">


                            <div>

                                <p
                                    class="text-sm
                                           text-[#9b858e]">

                                    Available Now

                                </p>

                                <p
                                    class="text-3xl
                                           font-bold
                                           text-emerald-600
                                           mt-2">

                                    <?= $available_count ?>

                                </p>

                            </div>


                            <div
                                class="w-12 h-12
                                       rounded-xl
                                       bg-emerald-50
                                       text-emerald-600
                                       flex items-center
                                       justify-center
                                       text-xl">

                                ✓

                            </div>

                        </div>

                    </div>


                    <!-- BOOKED -->

                    <div
                        class="bg-white
                               border border-rose-100
                               rounded-2xl
                               p-5
                               shadow-sm
                               hover:shadow-md
                               transition">


                        <div
                            class="flex items-center
                                   justify-between">


                            <div>

                                <p
                                    class="text-sm
                                           text-[#9b858e]">

                                    Booked Today

                                </p>

                                <p
                                    class="text-3xl
                                           font-bold
                                           text-rose-600
                                           mt-2">

                                    <?= $booked_count ?>

                                </p>

                            </div>


                            <div
                                class="w-12 h-12
                                       rounded-xl
                                       bg-rose-50
                                       text-rose-600
                                       flex items-center
                                       justify-center
                                       text-xl">

                                ◷

                            </div>

                        </div>

                    </div>

                </div>


                <!-- ================================= -->
                <!-- AVAILABLE WORKSPACES HEADER -->
                <!-- ================================= -->

                <div
                    class="flex flex-col
                           sm:flex-row
                           sm:items-end
                           sm:justify-between
                           gap-3
                           mb-5">


                    <div>

                        <h3
                            class="text-xl
                                   font-bold
                                   text-[#24171d]">

                            Available Workspaces

                        </h3>


                        <p
                            class="text-sm
                                   text-[#9b858e]
                                   mt-1">

                            Choose a workspace for your next meeting.

                        </p>

                    </div>


                    <a
                        href="pages/workspaces.php"
                        class="text-sm
                               font-medium
                               text-rose-600
                               hover:text-rose-700
                               transition">

                        View All →

                    </a>

                </div>


                <!-- ================================= -->
                <!-- WORKSPACE CARDS -->
                <!-- ================================= -->

                <div
                    class="grid
                           grid-cols-1
                           md:grid-cols-2
                           xl:grid-cols-3
                           gap-5">


                    <?php foreach ($workspaces as $workspace): ?>


                        <!-- ============================= -->
                        <!-- WORKSPACE CARD -->
                        <!-- ============================= -->

                        <div
                            class="group
                                   bg-white
                                   border border-rose-100
                                   rounded-2xl
                                   p-6
                                   shadow-sm
                                   hover:shadow-lg
                                   hover:-translate-y-1
                                   transition-all
                                   duration-300">


                            <!-- CARD TOP -->

                            <div
                                class="flex items-center
                                       justify-between
                                       mb-6">


                                <!-- TYPE -->

                                <span
                                    class="text-sm
                                           font-bold
                                           text-rose-600">

                                    <?= htmlspecialchars($workspace['type']) ?>

                                </span>


                                <!-- STATUS -->

                                <?php if ($workspace['current_status'] === 'Available'): ?>

                                    <span
                                        class="inline-flex
                                               items-center
                                               gap-2
                                               px-3 py-1.5
                                               rounded-full
                                               bg-emerald-50
                                               text-emerald-600
                                               text-xs
                                               font-semibold">

                                        <span
                                            class="w-1.5 h-1.5
                                                   rounded-full
                                                   bg-emerald-500">
                                        </span>

                                        Available

                                    </span>

                                <?php else: ?>

                                    <span
                                        class="inline-flex
                                               items-center
                                               gap-2
                                               px-3 py-1.5
                                               rounded-full
                                               bg-rose-50
                                               text-rose-600
                                               text-xs
                                               font-semibold">

                                        <span
                                            class="w-1.5 h-1.5
                                                   rounded-full
                                                   bg-rose-500">
                                        </span>

                                        Booked

                                    </span>

                                <?php endif; ?>

                            </div>


                            <!-- WORKSPACE ICON -->

                            <div
                                class="w-12 h-12
                                       rounded-xl
                                       bg-rose-50
                                       text-rose-600
                                       flex items-center
                                       justify-center
                                       text-xl
                                       mb-5">

                                ◫

                            </div>


                            <!-- WORKSPACE NAME -->

                            <h4
                                class="text-xl
                                       font-bold
                                       text-[#24171d]">

                                <?= htmlspecialchars($workspace['workspace_name']) ?>

                            </h4>


                            <!-- TIME LABEL -->

                            <p
                                class="text-xs
                                       uppercase
                                       tracking-wider
                                       font-medium
                                       text-[#a18e96]
                                       mt-5">

                                <?= $workspace['current_status'] === 'Booked'
                                    ? 'Booked Time'
                                    : 'Available Time'
                                ?>

                            </p>


                            <!-- TIME -->

                            <p
                                class="text-lg
                                       font-semibold
                                       text-[#24171d]
                                       mt-1">


                                <?php if (
                                    $workspace['current_status'] === 'Booked'
                                    && !empty($workspace['booked_start_time'])
                                ): ?>

                                    <?= date(
                                        "h:i A",
                                        strtotime($workspace['booked_start_time'])
                                    ) ?>

                                    -

                                    <?= date(
                                        "h:i A",
                                        strtotime($workspace['booked_end_time'])
                                    ) ?>

                                <?php else: ?>

                                    <?= htmlspecialchars($workspace['time_slot']) ?>

                                <?php endif; ?>


                            </p>


                            <!-- DIVIDER -->

                            <div
                                class="border-t
                                       border-rose-100
                                       my-5">
                            </div>


                            <!-- VIEW BUTTON -->

                            <a
                                href="pages/workspace-details.php?id=<?= $workspace['id'] ?>"
                                class="flex items-center
                                       justify-center
                                       gap-2
                                       w-full
                                       py-3.5
                                       rounded-xl
                                       bg-[#3b0a1e]
                                       text-white
                                       text-sm
                                       font-semibold
                                       group-hover:bg-rose-600
                                       transition">

                                View Workspace

                                <span
                                    class="text-lg
                                           transition
                                           group-hover:translate-x-1">

                                    →

                                </span>

                            </a>


                        </div>


                    <?php endforeach; ?>


                </div>


                <!-- ================================= -->
                <!-- EMPTY STATE -->
                <!-- ================================= -->

                <?php if (count($workspaces) === 0): ?>

                    <div
                        class="bg-white
                               border border-dashed
                               border-rose-200
                               rounded-2xl
                               p-12
                               text-center">


                        <div
                            class="w-14 h-14
                                   mx-auto
                                   rounded-2xl
                                   bg-rose-50
                                   text-rose-500
                                   flex items-center
                                   justify-center
                                   text-2xl">

                            ◫

                        </div>


                        <h3
                            class="text-lg
                                   font-semibold
                                   mt-4">

                            No Workspaces Found

                        </h3>


                        <p
                            class="text-sm
                                   text-[#9b858e]
                                   mt-2">

                            There are currently no workspaces available.

                        </p>

                    </div>

                <?php endif; ?>


            </div>

        </section>


    </main>

</div>


</body>

</html>