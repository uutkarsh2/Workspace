<?php

require_once "../config/auth.php";
require_once "../config/database.php";

$user_id = $_SESSION['user_id'];


// =========================
// GET USER BOOKINGS
// =========================

$sql = "
    SELECT
        bookings.id,
        bookings.company_name,
        bookings.meeting_title,
        bookings.booking_date,
        bookings.start_time,
        bookings.end_time,
        bookings.booking_type,
        bookings.recipient_email,
        bookings.status,
        bookings.created_at,

        workspaces.workspace_name,
        workspaces.type

    FROM bookings

    INNER JOIN workspaces
        ON bookings.workspace_id = workspaces.id

    WHERE bookings.user_id = ?

    ORDER BY
        bookings.booking_date ASC,
        bookings.start_time ASC
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("SQL prepare error: " . $conn->error);
}

$stmt->bind_param("i", $user_id);

$stmt->execute();

$result = $stmt->get_result();

$upcoming_bookings = [];
$history_bookings = [];


// =========================
// SEPARATE BOOKINGS
// =========================

while ($booking = $result->fetch_assoc()) {

    $booking_datetime = $booking['booking_date'] . ' ' . $booking['end_time'];

    if (
        $booking['booking_date'] > date('Y-m-d')
        ||
        (
            $booking['booking_date'] === date('Y-m-d')
            &&
            $booking['end_time'] > date('H:i:s')
        )
    ) {

        $upcoming_bookings[] = $booking;

    } else {

        $history_bookings[] = $booking;

    }
}

$stmt->close();


// =========================
// FORMAT TIME
// =========================

function formatTime($time)
{
    return date("h:i A", strtotime($time));
}


// =========================
// FORMAT DATE
// =========================

function formatDate($date)
{
    return date("d M Y", strtotime($date));
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>My Bookings | WorkspaceHub</title>

    <link
        rel="stylesheet"
        href="../assets/css/output.css">

</head>


<body class="bg-slate-50 text-slate-900">


<div class="min-h-screen flex">


    <!-- ========================= -->
    <!-- NAVBAR -->
    <!-- ========================= -->

    <?php require_once "../components/navbar.php"; ?>


    <!-- ========================= -->
    <!-- MAIN -->
    <!-- ========================= -->

    <main class="flex-1 min-w-0">


        <!-- HEADER -->

        <header class="bg-white border-b border-slate-200">

            <div class="px-6 lg:px-10 py-5">

                <p class="text-sm text-slate-400">
                    Booking Management
                </p>

                <h1 class="text-xl font-semibold">
                    My Bookings
                </h1>

            </div>

        </header>


        <!-- CONTENT -->

        <section class="px-6 lg:px-10 py-10">

            <div class="max-w-6xl mx-auto">


                <!-- PAGE INTRO -->

                <div class="mb-8">

                    <h2 class="text-2xl font-bold">
                        My Bookings
                    </h2>

                    <p class="text-slate-500 mt-2">
                        View and manage your upcoming meetings and booking history.
                    </p>

                </div>


                <!-- ========================= -->
                <!-- UPCOMING BOOKINGS -->
                <!-- ========================= -->

                <div class="mb-12">

                    <div class="flex items-center justify-between mb-5">

                        <div>

                            <h3 class="text-lg font-semibold">
                                Upcoming Bookings
                            </h3>

                            <p class="text-sm text-slate-400 mt-1">
                                Your active and upcoming meetings
                            </p>

                        </div>

                        <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-600 text-sm font-medium">
                            <?= count($upcoming_bookings) ?>
                        </span>

                    </div>


                    <?php if (count($upcoming_bookings) > 0): ?>

                        <div class="space-y-4">

                            <?php foreach ($upcoming_bookings as $booking): ?>

                                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition">


                                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">


                                        <!-- LEFT -->

                                        <div class="flex-1">


                                            <div class="flex items-center gap-3 mb-3">

                                                <span class="px-3 py-1 rounded-lg bg-blue-50 text-blue-600 text-xs font-semibold">
                                                    <?= htmlspecialchars($booking['type']) ?>
                                                </span>

                                                <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 text-xs font-medium">
                                                    <?= htmlspecialchars($booking['status']) ?>
                                                </span>

                                            </div>


                                            <h4 class="text-lg font-semibold">

                                                <?= htmlspecialchars($booking['meeting_title']) ?>

                                            </h4>


                                            <p class="text-sm text-slate-500 mt-1">

                                                <?= htmlspecialchars($booking['company_name']) ?>

                                            </p>


                                            <div class="flex flex-wrap gap-x-6 gap-y-2 mt-4 text-sm text-slate-500">

                                                <span>
                                                    📍
                                                    <?= htmlspecialchars($booking['workspace_name']) ?>
                                                </span>

                                                <span>
                                                    📅
                                                    <?= formatDate($booking['booking_date']) ?>
                                                </span>


                                                <span>

                                                    ⏰

                                                    <?php if ($booking['booking_type'] === 'Full Day'): ?>

                                                        Full Day

                                                    <?php else: ?>

                                                        <?= formatTime($booking['start_time']) ?>
                                                        -
                                                        <?= formatTime($booking['end_time']) ?>

                                                    <?php endif; ?>

                                                </span>

                                            </div>

                                        </div>


                                        <!-- RIGHT -->

                                        <div class="lg:text-right">

                                            <p class="text-xs text-slate-400">
                                                Booking ID
                                            </p>

                                            <p class="font-semibold text-slate-700 mt-1">
                                                #<?= htmlspecialchars($booking['id']) ?>
                                            </p>

                                            <p class="text-xs text-slate-400 mt-4">
                                                Recipient
                                            </p>

                                            <p class="text-sm text-slate-600 mt-1 break-all">
                                                <?= htmlspecialchars($booking['recipient_email']) ?>
                                            </p>

                                        </div>


                                    </div>

                                </div>

                            <?php endforeach; ?>

                        </div>


                    <?php else: ?>

                        <div class="bg-white border border-dashed border-slate-300 rounded-2xl p-10 text-center">

                            <div class="text-4xl mb-4">
                                📅
                            </div>

                            <h4 class="font-semibold text-lg">
                                No Upcoming Bookings
                            </h4>

                            <p class="text-sm text-slate-400 mt-2">
                                You don't have any upcoming meetings.
                            </p>

                            <a
                                href="workspaces.php"
                                class="inline-block mt-5 px-5 py-3 rounded-xl bg-slate-900 text-white text-sm font-medium hover:bg-blue-600 transition">

                                Book a Workspace

                            </a>

                        </div>

                    <?php endif; ?>

                </div>


                <!-- ========================= -->
                <!-- BOOKING HISTORY -->
                <!-- ========================= -->

                <div>

                    <div class="flex items-center justify-between mb-5">

                        <div>

                            <h3 class="text-lg font-semibold">
                                Booking History
                            </h3>

                            <p class="text-sm text-slate-400 mt-1">
                                Your previous meetings
                            </p>

                        </div>

                        <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-600 text-sm font-medium">
                            <?= count($history_bookings) ?>
                        </span>

                    </div>


                    <?php if (count($history_bookings) > 0): ?>

                        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">

                            <div class="overflow-x-auto">

                                <table class="w-full text-sm">

                                    <thead class="bg-slate-50 border-b border-slate-200">

                                        <tr>

                                            <th class="text-left px-6 py-4 font-semibold text-slate-600">
                                                Meeting
                                            </th>

                                            <th class="text-left px-6 py-4 font-semibold text-slate-600">
                                                Workspace
                                            </th>

                                            <th class="text-left px-6 py-4 font-semibold text-slate-600">
                                                Date
                                            </th>

                                            <th class="text-left px-6 py-4 font-semibold text-slate-600">
                                                Time
                                            </th>

                                            <th class="text-left px-6 py-4 font-semibold text-slate-600">
                                                Status
                                            </th>

                                            <th class="text-left px-6 py-4 font-semibold text-slate-600">
                                                ID
                                            </th>

                                        </tr>

                                    </thead>


                                    <tbody class="divide-y divide-slate-100">

                                        <?php foreach ($history_bookings as $booking): ?>

                                            <tr class="hover:bg-slate-50 transition">


                                                <td class="px-6 py-5">

                                                    <p class="font-medium text-slate-800">

                                                        <?= htmlspecialchars($booking['meeting_title']) ?>

                                                    </p>

                                                    <p class="text-xs text-slate-400 mt-1">

                                                        <?= htmlspecialchars($booking['company_name']) ?>

                                                    </p>

                                                </td>


                                                <td class="px-6 py-5">

                                                    <p class="text-slate-700">

                                                        <?= htmlspecialchars($booking['workspace_name']) ?>

                                                    </p>

                                                    <p class="text-xs text-blue-600 mt-1">

                                                        <?= htmlspecialchars($booking['type']) ?>

                                                    </p>

                                                </td>


                                                <td class="px-6 py-5 text-slate-600">

                                                    <?= formatDate($booking['booking_date']) ?>

                                                </td>


                                                <td class="px-6 py-5 text-slate-600">

                                                    <?php if ($booking['booking_type'] === 'Full Day'): ?>

                                                        Full Day

                                                    <?php else: ?>

                                                        <?= formatTime($booking['start_time']) ?>
                                                        -
                                                        <?= formatTime($booking['end_time']) ?>

                                                    <?php endif; ?>

                                                </td>


                                                <td class="px-6 py-5">

                                                    <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-medium">

                                                        <?= htmlspecialchars($booking['status']) ?>

                                                    </span>

                                                </td>


                                                <td class="px-6 py-5 font-medium text-slate-500">

                                                    #<?= htmlspecialchars($booking['id']) ?>

                                                </td>


                                            </tr>

                                        <?php endforeach; ?>

                                    </tbody>

                                </table>

                            </div>

                        </div>


                    <?php else: ?>

                        <div class="bg-white border border-dashed border-slate-300 rounded-2xl p-10 text-center">

                            <div class="text-4xl mb-4">
                                🗂️
                            </div>

                            <h4 class="font-semibold text-lg">
                                No Booking History
                            </h4>

                            <p class="text-sm text-slate-400 mt-2">
                                Your previous bookings will appear here.
                            </p>

                        </div>

                    <?php endif; ?>

                </div>


            </div>

        </section>


    </main>

</div>


</body>
</html>