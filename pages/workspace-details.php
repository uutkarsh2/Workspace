<?php

require_once "../config/auth.php";
require_once "../config/database.php";


// ==========================================
// GET WORKSPACE ID
// ==========================================

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: workspaces.php");
    exit;
}

$workspace_id = (int) $_GET['id'];


// ==========================================
// GET WORKSPACE DETAILS
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
    WHERE w.id = ?
";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $workspace_id);

$stmt->execute();

$result = $stmt->get_result();


// ==========================================
// WORKSPACE NOT FOUND
// ==========================================

if ($result->num_rows === 0) {
    header("Location: workspaces.php");
    exit;
}

$workspace = $result->fetch_assoc();

$stmt->close();


// ==========================================
// VARIABLES
// ==========================================

$is_available = $workspace['current_status'] === 'Available';

$workspace_name = $workspace['workspace_name'];
$workspace_type = $workspace['type'];
$time_slot = $workspace['time_slot'];


// ==========================================
// TODAY'S BOOKING TIME
// ==========================================

$booked_time = null;

if (
    !$is_available &&
    !empty($workspace['booked_start_time']) &&
    !empty($workspace['booked_end_time'])
) {

    $booked_time =
        date("h:i A", strtotime($workspace['booked_start_time']))
        . " - " .
        date("h:i A", strtotime($workspace['booked_end_time']));
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
        <?= htmlspecialchars($workspace_name) ?> | WorkspaceHub
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
            class="bg-white
                   border-b border-rose-100">

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
                           font-semibold">

                    Workspace Details

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
                class="max-w-5xl
                       mx-auto">


                <!-- ================================= -->
                <!-- BACK BUTTON -->
                <!-- ================================= -->

                <a
                    href="workspaces.php"
                    class="inline-flex
                           items-center
                           gap-2
                           text-sm
                           font-medium
                           text-[#806d76]
                           hover:text-rose-600
                           transition
                           mb-6">

                    ← Back to Workspaces

                </a>


                <!-- ================================= -->
                <!-- MAIN CARD -->
                <!-- ================================= -->

                <div
                    class="bg-white
                           rounded-3xl
                           border border-rose-100
                           shadow-sm
                           overflow-hidden">


                    <!-- ================================= -->
                    <!-- TOP SECTION -->
                    <!-- ================================= -->

                    <div
                        class="p-6 sm:p-8 lg:p-10">


                        <div
                            class="flex
                                   flex-col
                                   sm:flex-row
                                   sm:items-start
                                   sm:justify-between
                                   gap-6">


                            <!-- LEFT -->

                            <div
                                class="flex
                                       items-center
                                       gap-5">


                                <!-- TYPE ICON -->

                                <div
                                    class="w-16 h-16
                                           rounded-2xl
                                           bg-rose-50
                                           text-rose-600
                                           flex
                                           items-center
                                           justify-center
                                           text-2xl
                                           font-bold
                                           shrink-0">

                                    <?= htmlspecialchars($workspace_type) ?>

                                </div>


                                <div>

                                    <p
                                        class="text-sm
                                               font-medium
                                               text-rose-500">

                                        <?= htmlspecialchars($workspace_type) ?>

                                    </p>


                                    <h2
                                        class="text-2xl
                                               sm:text-3xl
                                               font-bold
                                               mt-1">

                                        <?= htmlspecialchars($workspace_name) ?>

                                    </h2>


                                    <p
                                        class="text-sm
                                               text-[#9b858e]
                                               mt-1">

                                        Workspace

                                    </p>

                                </div>


                            </div>


                            <!-- STATUS -->

                            <div>

                                <?php if ($is_available): ?>

                                    <span
                                        class="inline-flex
                                               items-center
                                               gap-2
                                               px-4 py-2
                                               rounded-full
                                               bg-emerald-50
                                               text-emerald-600
                                               text-sm
                                               font-semibold">

                                        <span
                                            class="w-2 h-2
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
                                               px-4 py-2
                                               rounded-full
                                               bg-rose-50
                                               text-rose-600
                                               text-sm
                                               font-semibold">

                                        <span
                                            class="w-2 h-2
                                                   rounded-full
                                                   bg-rose-500">
                                        </span>

                                        Currently Booked

                                    </span>

                                <?php endif; ?>

                            </div>


                        </div>


                        <!-- ================================= -->
                        <!-- DIVIDER -->
                        <!-- ================================= -->

                        <div
                            class="border-t
                                   border-rose-100
                                   my-8">
                        </div>


                        <!-- ================================= -->
                        <!-- INFORMATION GRID -->
                        <!-- ================================= -->

                        <div
                            class="grid
                                   grid-cols-1
                                   sm:grid-cols-2
                                   gap-5">


                            <!-- WORKSPACE TYPE -->

                            <div
                                class="p-5
                                       rounded-2xl
                                       bg-[#fff7f8]
                                       border border-rose-100">

                                <p
                                    class="text-xs
                                           uppercase
                                           tracking-wider
                                           font-semibold
                                           text-[#a18e96]">

                                    Workspace Type

                                </p>


                                <p
                                    class="text-lg
                                           font-semibold
                                           mt-2">

                                    <?= htmlspecialchars($workspace_type) ?>

                                </p>

                            </div>


                            <!-- TIME SLOT -->

                            <div
                                class="p-5
                                       rounded-2xl
                                       bg-[#fff7f8]
                                       border border-rose-100">

                                <p
                                    class="text-xs
                                           uppercase
                                           tracking-wider
                                           font-semibold
                                           text-[#a18e96]">

                                    Default Time Slot

                                </p>


                                <p
                                    class="text-lg
                                           font-semibold
                                           mt-2">

                                    <?= htmlspecialchars($time_slot) ?>

                                </p>

                            </div>


                            <!-- TODAY STATUS -->

                            <div
                                class="p-5
                                       rounded-2xl
                                       bg-[#fff7f8]
                                       border border-rose-100">

                                <p
                                    class="text-xs
                                           uppercase
                                           tracking-wider
                                           font-semibold
                                           text-[#a18e96]">

                                    Today's Status

                                </p>


                                <p
                                    class="text-lg
                                           font-semibold
                                           mt-2
                                           <?= $is_available
                                               ? 'text-emerald-600'
                                               : 'text-rose-600'
                                           ?>">

                                    <?= $is_available
                                        ? 'Available'
                                        : 'Booked'
                                    ?>

                                </p>

                            </div>


                            <!-- CURRENT BOOKING -->

                            <div
                                class="p-5
                                       rounded-2xl
                                       bg-[#fff7f8]
                                       border border-rose-100">

                                <p
                                    class="text-xs
                                           uppercase
                                           tracking-wider
                                           font-semibold
                                           text-[#a18e96]">

                                    Current Booking

                                </p>


                                <p
                                    class="text-lg
                                           font-semibold
                                           mt-2">

                                    <?= $booked_time
                                        ? htmlspecialchars($booked_time)
                                        : 'No active booking'
                                    ?>

                                </p>

                            </div>


                        </div>


                    </div>


                    <!-- ================================= -->
                    <!-- ACTION SECTION -->
                    <!-- ================================= -->

                    <div
                        class="px-6 sm:px-8 lg:px-10
                               py-6
                               bg-[#fff7f8]
                               border-t border-rose-100">


                        <?php if ($is_available): ?>


                            <div
                                class="flex
                                       flex-col
                                       sm:flex-row
                                       sm:items-center
                                       sm:justify-between
                                       gap-5">


                                <div>

                                    <h3
                                        class="font-semibold
                                               text-lg">

                                        Ready to book?

                                    </h3>


                                    <p
                                        class="text-sm
                                               text-[#927e87]
                                               mt-1">

                                        Reserve this workspace
                                        for your meeting.

                                    </p>

                                </div>


                                <a
                                    href="book-workspace.php?id=<?= $workspace_id ?>"
                                    class="inline-flex
                                           items-center
                                           justify-center
                                           gap-2
                                           px-6 py-3.5
                                           rounded-xl
                                           bg-[#3b0a1e]
                                           text-white
                                           text-sm
                                           font-semibold
                                           hover:bg-rose-600
                                           transition
                                           shadow-lg
                                           shadow-rose-950/10">

                                    Book Workspace

                                    <span class="text-lg">

                                        →

                                    </span>

                                </a>


                            </div>


                        <?php else: ?>


                            <div
                                class="flex
                                       flex-col
                                       sm:flex-row
                                       sm:items-center
                                       sm:justify-between
                                       gap-5">


                                <div>

                                    <h3
                                        class="font-semibold
                                               text-lg">

                                        Workspace is currently booked

                                    </h3>


                                    <p
                                        class="text-sm
                                               text-[#927e87]
                                               mt-1">

                                        You can check again after
                                        the current booking ends.

                                    </p>

                                </div>


                                <a
                                    href="workspaces.php"
                                    class="inline-flex
                                           items-center
                                           justify-center
                                           gap-2
                                           px-6 py-3.5
                                           rounded-xl
                                           border border-rose-200
                                           bg-white
                                           text-[#3b0a1e]
                                           text-sm
                                           font-semibold
                                           hover:bg-rose-50
                                           transition">

                                    ←

                                    View Other Workspaces

                                </a>


                            </div>


                        <?php endif; ?>


                    </div>


                </div>


                <!-- ================================= -->
                <!-- NOTE -->
                <!-- ================================= -->

                <div
                    class="mt-6
                           flex
                           gap-3
                           p-5
                           rounded-2xl
                           bg-rose-50
                           border border-rose-100">


                    <div
                        class="w-8 h-8
                               rounded-lg
                               bg-white
                               text-rose-600
                               flex
                               items-center
                               justify-center
                               font-semibold
                               shrink-0">

                        i

                    </div>


                    <div>

                        <p
                            class="text-sm
                                   font-semibold
                                   text-[#3b0a1e]">

                            Booking Information

                        </p>


                        <p
                            class="text-sm
                                   text-[#806d76]
                                   mt-1">

                            Workspace availability is
                            automatically updated based on
                            active bookings for today.

                        </p>

                    </div>


                </div>


            </div>


        </section>


    </main>


</div>


</body>

</html>