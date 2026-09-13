<?php

require_once "../config/auth.php";
require_once "../config/database.php";


// ==========================================
// GET WORKSPACES
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


$workspaces = [];

while ($workspace = $result->fetch_assoc()) {

    $workspaces[] = $workspace;

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
        Workspaces | WorkspaceHub
    </title>

    <link
        rel="stylesheet"
        href="../assets/css/output.css">

</head>


<body class="bg-[#fff7f8] text-[#24171d]">


<div class="min-h-screen flex">


    <!-- ================================= -->
    <!-- SIDEBAR -->
    <!-- ================================= -->

    <?php require_once "../components/navbar.php"; ?>


    <!-- ================================= -->
    <!-- MAIN -->
    <!-- ================================= -->

    <main class="flex-1 min-w-0 pt-16 lg:pt-0">


        <!-- ================================= -->
        <!-- HEADER -->
        <!-- ================================= -->

        <header
            class="bg-white border-b border-rose-100">

            <div
                class="px-6 lg:px-10 py-5">

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

                    Workspaces

                </h1>

            </div>

        </header>


        <!-- ================================= -->
        <!-- CONTENT -->
        <!-- ================================= -->

        <section
            class="px-6 lg:px-10
                   py-8 lg:py-10">


            <div
                class="max-w-7xl
                       mx-auto">


                <!-- ================================= -->
                <!-- PAGE INTRO -->
                <!-- ================================= -->

                <div
                    class="flex flex-col
                           sm:flex-row
                           sm:items-end
                           sm:justify-between
                           gap-4
                           mb-8">


                    <div>

                        <p
                            class="text-sm
                                   uppercase
                                   tracking-wider
                                   font-medium
                                   text-rose-500">

                            WorkspaceHub

                        </p>


                        <h2
                            class="text-3xl
                                   font-bold
                                   tracking-tight
                                   mt-2">

                            Choose a Workspace

                        </h2>


                        <p
                            class="text-[#8f7c84]
                                   mt-2">

                            Find and book the right space
                            for your next meeting.

                        </p>

                    </div>


                    <!-- Workspace Count -->

                    <div
                        class="inline-flex
                               items-center
                               gap-2
                               px-4 py-2.5
                               bg-white
                               border border-rose-100
                               rounded-xl
                               shadow-sm
                               w-fit">

                        <span
                            class="w-2 h-2
                                   rounded-full
                                   bg-rose-500">
                        </span>

                        <span
                            class="text-sm
                                   font-medium
                                   text-[#6f5d65]">

                            <?= count($workspaces) ?>
                            Workspaces

                        </span>

                    </div>

                </div>


                <!-- ================================= -->
                <!-- WORKSPACE GRID -->
                <!-- ================================= -->

                <div
                    class="grid
                           grid-cols-1
                           md:grid-cols-2
                           xl:grid-cols-3
                           gap-6">


                    <?php foreach ($workspaces as $workspace): ?>


                        <!-- ================================= -->
                        <!-- CARD -->
                        <!-- ================================= -->

                        <div
                            class="group
                                   bg-white
                                   border border-rose-100
                                   rounded-2xl
                                   overflow-hidden
                                   shadow-sm
                                   hover:shadow-xl
                                   hover:-translate-y-1
                                   transition-all
                                   duration-300">


                            <!-- ================================= -->
                            <!-- CARD TOP -->
                            <!-- ================================= -->

                            <div
                                class="p-6">


                                <div
                                    class="flex
                                           items-center
                                           justify-between
                                           mb-6">


                                    <!-- TYPE -->

                                    <div
                                        class="w-12 h-12
                                               rounded-xl
                                               bg-rose-50
                                               text-rose-600
                                               flex items-center
                                               justify-center
                                               text-xl
                                               font-semibold">

                                        <?= htmlspecialchars($workspace['type']) ?>

                                    </div>


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


                                <!-- ================================= -->
                                <!-- NAME -->
                                <!-- ================================= -->

                                <h3
                                    class="text-xl
                                           font-bold
                                           text-[#24171d]">

                                    <?= htmlspecialchars($workspace['workspace_name']) ?>

                                </h3>


                                <!-- ================================= -->
                                <!-- TYPE -->
                                <!-- ================================= -->

                                <p
                                    class="text-sm
                                           text-[#9b858e]
                                           mt-1">

                                    Workspace Type:
                                    <?= htmlspecialchars($workspace['type']) ?>

                                </p>


                                <!-- ================================= -->
                                <!-- TIME -->
                                <!-- ================================= -->

                                <div
                                    class="mt-6
                                           p-4
                                           rounded-xl
                                           bg-[#fff7f8]
                                           border border-rose-100">


                                    <p
                                        class="text-xs
                                               uppercase
                                               tracking-wider
                                               font-semibold
                                               text-[#a18e96]">

                                        <?= $workspace['current_status'] === 'Booked'
                                            ? 'Booked Time'
                                            : 'Available Time'
                                        ?>

                                    </p>


                                    <p
                                        class="text-base
                                               font-semibold
                                               text-[#24171d]
                                               mt-2">


                                        <?php if (
                                            $workspace['current_status'] === 'Booked'
                                            &&
                                            !empty($workspace['booked_start_time'])
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

                                </div>


                            </div>


                            <!-- ================================= -->
                            <!-- CARD FOOTER -->
                            <!-- ================================= -->

                            <div
                                class="px-6 pb-6">


                                <a
                                    href="workspace-details.php?id=<?= $workspace['id'] ?>"
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
                                           hover:bg-rose-600
                                           transition">


                                    View Workspace

                                    <span
                                        class="text-lg
                                               group-hover:translate-x-1
                                               transition">

                                        →

                                    </span>


                                </a>


                            </div>


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
                            class="w-16 h-16
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
                            class="text-xl
                                   font-semibold
                                   mt-5">

                            No Workspaces Available

                        </h3>


                        <p
                            class="text-sm
                                   text-[#9b858e]
                                   mt-2">

                            There are currently no workspaces
                            available for booking.

                        </p>


                    </div>


                <?php endif; ?>


            </div>

        </section>


    </main>


</div>


</body>

</html>