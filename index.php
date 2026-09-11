<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "config/database.php";
require_once "config/auth.php";


// Get all workspaces with dynamic booking status

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

$workspaces = [];

if ($result && $result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {

        $workspaces[] = $row;

    }

}

$conn->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Workspace Dashboard | WorkspaceHub</title>

    <link rel="stylesheet" href="assets/css/output.css">

</head>


<body class="bg-slate-50 text-slate-900">


<div class="min-h-screen flex">

    <?php  require_once "components/navbar.php"; ?>

    <main class="flex-1 min-w-0">


        <header class="bg-white border-b border-slate-200">


            <div class="px-6 lg:px-10 py-5 flex items-center justify-between">


                <!-- Company -->

                <div>

                    <p class="text-sm text-slate-400">

                        ABC Technologies

                    </p>

                    <h2 class="text-xl font-semibold">

                        Workspace Dashboard

                    </h2>

                </div>


                <!-- User -->

                <div class="flex items-center gap-5">


                    <!-- Notification -->

                    <button
                        class="w-12 h-12 rounded-full border border-slate-200 flex items-center justify-center text-xl hover:bg-slate-50 transition">

                        🔔

                    </button>


                    <!-- User Info -->

                    <div class="hidden sm:block text-right">

                        <p class="font-semibold">
                            Admin
                        </p>

                        <p class="text-sm text-slate-400">
                            Administrator
                        </p>

                    </div>


                    <!-- Avatar -->

                    <div
                        class="w-12 h-12 rounded-full bg-blue-600 text-white flex items-center justify-center font-semibold text-lg">

                        A

                    </div>


                </div>

            </div>


        </header>


        <!-- ========================= -->
        <!-- DASHBOARD CONTENT -->
        <!-- ========================= -->

        <section class="px-6 lg:px-12 py-12">


            <div class="max-w-7xl mx-auto">


                <!-- Heading -->

                <div class="mb-10">


                    <p class="text-sm font-medium text-blue-600 mb-2">

                        WORKSPACE MANAGEMENT

                    </p>


                    <h1 class="text-4xl font-bold tracking-tight">

                        Available Workspaces

                    </h1>


                    <p class="text-slate-500 text-lg mt-3">

                        Choose a workspace for your next meeting.

                    </p>

                </div>


                <!-- ========================= -->
                <!-- WORKSPACE CARDS -->
                <!-- ========================= -->

                <?php if (count($workspaces) > 0): ?>


                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-7">


                        <?php foreach ($workspaces as $workspace): ?>


                            <div
                                class="bg-white border border-slate-200 rounded-2xl p-8 hover:shadow-lg transition">


                                <!-- Top -->

                                <div class="flex items-center justify-between">


                                    <!-- Type -->

                                    <span
                                        class="text-sm font-semibold text-blue-600">

                                        <?= htmlspecialchars($workspace['type']) ?>

                                    </span>


                                    <!-- Status -->

                                    <?php if ($workspace['current_status'] === 'Booked'): ?>


                                        <span
                                            class="px-3 py-1 text-xs font-medium rounded-full bg-red-50 text-red-600">

                                            Booked

                                        </span>


                                    <?php else: ?>


                                        <span
                                            class="px-3 py-1 text-xs font-medium rounded-full bg-emerald-50 text-emerald-600">

                                            Available

                                        </span>


                                    <?php endif; ?>


                                </div>


                                <!-- Workspace Name -->

                                <h2 class="text-2xl font-semibold mt-8">

                                    <?= htmlspecialchars($workspace['workspace_name']) ?>

                                </h2>


                                <!-- Company -->

                                <p class="text-slate-500 mt-3">

                                    <?= htmlspecialchars($workspace['company_name']) ?>

                                </p>


                                <!-- Divider -->

                                <div class="border-t border-slate-100 my-7"></div>


                                <!-- Time Slot -->

                                <div>

                                 <?php if ($workspace['current_status'] === 'Booked'): ?>

                                 <p class="text-xs uppercase tracking-wide text-red-400">
                                      Booked Time
                                  </p>

                                      <p class="text-lg font-medium mt-2 text-red-600">

                                      <?= date(
                                        "h:i A",
                                          strtotime($workspace['booked_start_time'])
                                           ) ?>

                                                -

                                                   <?= date(
                                                        "h:i A",
                                                           strtotime($workspace['booked_end_time'])
                                                     ) ?>

                                                               </p>

                                                                 <?php else: ?>

                                                                    <p class="text-xs uppercase tracking-wide text-slate-400">
            Available Time
        </p>

        <p class="text-lg font-medium mt-2">

            <?= htmlspecialchars($workspace['time_slot']) ?>

        </p>

    <?php endif; ?>

</div>


                                <!-- View Workspace -->

                                <a
                                    href="pages/workspace-details.php?id=<?= $workspace['id'] ?>"
                                    class="block text-center w-full mt-7 py-3.5 rounded-xl bg-slate-900 text-white text-sm font-medium hover:bg-blue-600 transition">

                                    View Workspace

                                </a>


                            </div>


                        <?php endforeach; ?>


                    </div>


                <?php else: ?>


                    <!-- No Workspaces -->

                    <div
                        class="bg-white border border-slate-200 rounded-2xl p-12 text-center">


                        <div
                            class="w-16 h-16 mx-auto bg-blue-50 rounded-full flex items-center justify-center text-2xl">

                            □

                        </div>


                        <h2 class="text-xl font-semibold mt-5">

                            No Workspaces Found

                        </h2>


                        <p class="text-slate-500 mt-2">

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