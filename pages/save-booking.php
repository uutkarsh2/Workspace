<?php
require_once "../config/auth.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../config/database.php";


// ======================================================
// CHECK REQUEST
// ======================================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    die("Invalid request.");

}


// ======================================================
// GET FORM DATA
// ======================================================

$workspace_id = $_POST['workspace_id'] ?? '';

$company_name = trim($_POST['company_name'] ?? '');

$meeting_title = trim($_POST['meeting_title'] ?? '');

$booking_date = $_POST['booking_date'] ?? '';

$start_time = $_POST['start_time'] ?? '';

$end_time = $_POST['end_time'] ?? '';

$recipient_email = trim($_POST['recipient_email'] ?? '');

$full_day = isset($_POST['full_day']) && $_POST['full_day'] === '1';


// ======================================================
// BASIC VALIDATION
// ======================================================

if (
    empty($workspace_id) ||
    empty($company_name) ||
    empty($meeting_title) ||
    empty($booking_date) ||
    empty($recipient_email)
) {

    die("Please fill all required fields.");

}


if (!is_numeric($workspace_id)) {

    die("Invalid workspace.");

}


$workspace_id = (int) $workspace_id;


// ======================================================
// EMAIL VALIDATION
// ======================================================

if (!filter_var($recipient_email, FILTER_VALIDATE_EMAIL)) {

    die("Please enter a valid recipient email.");

}


// ======================================================
// CHECK DATE
// ======================================================

if ($booking_date < date('Y-m-d')) {

    die("Booking date cannot be in the past.");

}


// ======================================================
// GET WORKSPACE
// ======================================================

$sql = "SELECT * FROM workspaces WHERE id = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {

    die("SQL prepare error: " . $conn->error);

}

$stmt->bind_param("i", $workspace_id);

$stmt->execute();

$result = $stmt->get_result();

$workspace = $result->fetch_assoc();

$stmt->close();


if (!$workspace) {

    $conn->close();

    die("Workspace not found.");

}


// ======================================================
// FULL DAY BOOKING
// ======================================================

if ($full_day) {

    $booking_type = "Full Day";

    /*
     * Full day is represented as:
     * 00:00 - 23:59
     */

    $start_time = "00:00:00";

    $end_time = "23:59:59";


} else {

    $booking_type = "Time Slot";


    // Make sure times are provided

    if (empty($start_time) || empty($end_time)) {

        $conn->close();

        die("Please select start and end time.");

    }


    // Validate time

    if ($start_time >= $end_time) {

        $conn->close();

        die("End time must be later than start time.");

    }


    // Add seconds for MySQL TIME format

    if (strlen($start_time) === 5) {

        $start_time .= ":00";

    }

    if (strlen($end_time) === 5) {

        $end_time .= ":00";

    }

}


// ======================================================
// CHECK OVERLAPPING BOOKINGS
// ======================================================

$sql = "
    SELECT id
    FROM bookings
    WHERE workspace_id = ?
    AND booking_date = ?
    AND status = 'Booked'
    AND start_time < ?
    AND end_time > ?
";

$stmt = $conn->prepare($sql);

if (!$stmt) {

    $conn->close();

    die("SQL prepare error: " . $conn->error);

}

$stmt->bind_param(
    "isss",
    $workspace_id,
    $booking_date,
    $end_time,
    $start_time
);

$stmt->execute();

$result = $stmt->get_result();


if ($result->num_rows > 0) {

    $stmt->close();

    $conn->close();

    die("This workspace is already booked for the selected time.");

}

$stmt->close();


// ======================================================
// INSERT BOOKING
// ======================================================

$sql = "
    INSERT INTO bookings
    (
        workspace_id,
        company_name,
        meeting_title,
        booking_date,
        start_time,
        end_time,
        booking_type,
        recipient_email,
        status
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Booked')
";

$stmt = $conn->prepare($sql);

if (!$stmt) {

    $conn->close();

    die("SQL prepare error: " . $conn->error);

}


$stmt->bind_param(
    "isssssss",
    $workspace_id,
    $company_name,
    $meeting_title,
    $booking_date,
    $start_time,
    $end_time,
    $booking_type,
    $recipient_email
);


// ======================================================
// SAVE BOOKING
// ======================================================

if ($stmt->execute()) {

    $booking_id = $stmt->insert_id;

    $stmt->close();

    $conn->close();

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        Booking Confirmed | WorkspaceHub
    </title>

    <link
        rel="stylesheet"
        href="../assets/css/output.css">

</head>


<body class="bg-slate-50 text-slate-900">


<div class="min-h-screen flex items-center justify-center p-6">


    <div
        class="bg-white border border-slate-200 rounded-2xl p-10 max-w-lg w-full text-center shadow-sm">


        <!-- Success Icon -->

        <div
            class="w-16 h-16 mx-auto rounded-full bg-emerald-50 flex items-center justify-center text-3xl text-emerald-600">

            ✓

        </div>


        <!-- Title -->

        <h1 class="text-2xl font-bold mt-6">

            Booking Confirmed

        </h1>


        <p class="text-slate-500 mt-2">

            Your workspace has been successfully booked.

        </p>


        <!-- Booking Information -->

        <div
            class="mt-7 bg-slate-50 rounded-xl p-5 text-left space-y-4">


            <!-- Booking ID -->

            <div>

                <p class="text-xs uppercase tracking-wide text-slate-400">

                    Booking ID

                </p>

                <p class="font-semibold mt-1">

                    #<?= $booking_id ?>

                </p>

            </div>


            <!-- Company -->

            <div>

                <p class="text-xs uppercase tracking-wide text-slate-400">

                    Company

                </p>

                <p class="font-medium mt-1">

                    <?= htmlspecialchars($company_name) ?>

                </p>

            </div>


            <!-- Meeting -->

            <div>

                <p class="text-xs uppercase tracking-wide text-slate-400">

                    Meeting

                </p>

                <p class="font-medium mt-1">

                    <?= htmlspecialchars($meeting_title) ?>

                </p>

            </div>


            <!-- Workspace -->

            <div>

                <p class="text-xs uppercase tracking-wide text-slate-400">

                    Workspace

                </p>

                <p class="font-medium mt-1">

                    <?= htmlspecialchars($workspace['workspace_name']) ?>

                </p>

            </div>


            <!-- Date -->

            <div>

                <p class="text-xs uppercase tracking-wide text-slate-400">

                    Date

                </p>

                <p class="font-medium mt-1">

                    <?= date("d M Y", strtotime($booking_date)) ?>

                </p>

            </div>


            <!-- Time -->

            <div>

                <p class="text-xs uppercase tracking-wide text-slate-400">

                    Booking Time

                </p>

                <p class="font-medium mt-1">

                    <?php if ($full_day): ?>

                        Full Day

                    <?php else: ?>

                        <?= date("h:i A", strtotime($start_time)) ?>

                        -

                        <?= date("h:i A", strtotime($end_time)) ?>

                    <?php endif; ?>

                </p>

            </div>


            <!-- Email -->

            <div>

                <p class="text-xs uppercase tracking-wide text-slate-400">

                    Recipient

                </p>

                <p class="font-medium mt-1">

                    <?= htmlspecialchars($recipient_email) ?>

                </p>

            </div>


            <!-- Status -->

            <div>

                <p class="text-xs uppercase tracking-wide text-slate-400">

                    Status

                </p>

                <span
                    class="inline-block mt-1 px-3 py-1 text-xs font-medium rounded-full bg-emerald-50 text-emerald-600">

                    Booked

                </span>

            </div>


        </div>


        <!-- Buttons -->

        <div class="mt-7 flex flex-col sm:flex-row gap-3">


            <a
                href="mybooking.php"
                class="w-full py-3 rounded-xl bg-slate-900 text-white font-medium hover:bg-blue-600 transition">

                My Bookings

            </a>


            <a
                href="workspaces.php"
                class="w-full py-3 rounded-xl border border-slate-200 text-slate-700 font-medium hover:bg-slate-50 transition">

                Workspaces

            </a>


        </div>


    </div>

</div>


</body>

</html>

<?php

} else {

    $stmt->close();

    $conn->close();

    die("Booking failed. Please try again.");

}

?>