<?php

require_once "../config/auth.php";
require_once "../config/database.php";


// ==========================================
// ONLY POST REQUEST
// ==========================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: workspaces.php");
    exit;
}


// ==========================================
// LOGGED-IN USER
// ==========================================

$user_id = $_SESSION['user_id'];


// ==========================================
// GET FORM DATA
// ==========================================

$workspace_id   = (int) ($_POST['workspace_id'] ?? 0);
$company_name   = trim($_POST['company_name'] ?? '');
$meeting_title  = trim($_POST['meeting_title'] ?? '');
$booking_date   = $_POST['booking_date'] ?? '';
$start_time     = $_POST['start_time'] ?? '';
$end_time       = $_POST['end_time'] ?? '';
$recipient_email = trim($_POST['recipient_email'] ?? '');
$full_day       = isset($_POST['full_day']) && $_POST['full_day'] == '1';


// ==========================================
// BASIC VALIDATION
// ==========================================

if (
    $workspace_id <= 0 ||
    empty($company_name) ||
    empty($meeting_title) ||
    empty($booking_date) ||
    empty($recipient_email)
) {

    die("
        <div style='
            font-family: Arial;
            padding: 40px;
            text-align: center;
        '>
            <h2>Missing Required Information</h2>

            <p>
                Please fill all required booking details.
            </p>

            <a href='javascript:history.back()'>
                ← Go Back
            </a>
        </div>
    ");

}


// ==========================================
// EMAIL VALIDATION
// ==========================================

if (!filter_var($recipient_email, FILTER_VALIDATE_EMAIL)) {

    die("
        <div style='
            font-family: Arial;
            padding: 40px;
            text-align: center;
        '>
            <h2>Invalid Email</h2>

            <p>
                Please enter a valid recipient email address.
            </p>

            <a href='javascript:history.back()'>
                ← Go Back
            </a>
        </div>
    ");

}


// ==========================================
// DATE VALIDATION
// ==========================================

if ($booking_date < date('Y-m-d')) {

    die("
        <div style='
            font-family: Arial;
            padding: 40px;
            text-align: center;
        '>
            <h2>Invalid Booking Date</h2>

            <p>
                You cannot book a workspace for a past date.
            </p>

            <a href='javascript:history.back()'>
                ← Go Back
            </a>
        </div>
    ");

}


// ==========================================
// CHECK WORKSPACE
// ==========================================

$workspace_sql = "
    SELECT
        id,
        workspace_name,
        type
    FROM workspaces
    WHERE id = ?
";

$workspace_stmt = $conn->prepare($workspace_sql);

$workspace_stmt->bind_param(
    "i",
    $workspace_id
);

$workspace_stmt->execute();

$workspace_result =
    $workspace_stmt->get_result();


if ($workspace_result->num_rows === 0) {

    die("
        <div style='
            font-family: Arial;
            padding: 40px;
            text-align: center;
        '>
            <h2>Workspace Not Found</h2>

            <p>
                The selected workspace does not exist.
            </p>

            <a href='workspaces.php'>
                ← Back to Workspaces
            </a>
        </div>
    ");

}


$workspace =
    $workspace_result->fetch_assoc();


$workspace_stmt->close();


// ==========================================
// BOOKING TYPE
// ==========================================

if ($full_day) {

    $booking_type = "Full Day";

    $start_time = "00:00:00";

    $end_time = "23:59:59";

} else {

    $booking_type = "Time Slot";


    // ======================================
    // TIME REQUIRED
    // ======================================

    if (
        empty($start_time) ||
        empty($end_time)
    ) {

        die("
            <div style='
                font-family: Arial;
                padding: 40px;
                text-align: center;
            '>
                <h2>Time Required</h2>

                <p>
                    Please select both start and end time.
                </p>

                <a href='javascript:history.back()'>
                    ← Go Back
                </a>
            </div>
        ");

    }


    // ======================================
    // TIME VALIDATION
    // ======================================

    if ($start_time >= $end_time) {

        die("
            <div style='
                font-family: Arial;
                padding: 40px;
                text-align: center;
            '>
                <h2>Invalid Time</h2>

                <p>
                    End time must be later than start time.
                </p>

                <a href='javascript:history.back()'>
                    ← Go Back
                </a>
            </div>
        ");

    }

}


// ==========================================
// CHECK OVERLAPPING BOOKINGS
// ==========================================

$overlap_sql = "
    SELECT id
    FROM bookings
    WHERE workspace_id = ?
    AND booking_date = ?
    AND status = 'Booked'
    AND start_time < ?
    AND end_time > ?
    LIMIT 1
";

$overlap_stmt =
    $conn->prepare($overlap_sql);


$overlap_stmt->bind_param(
    "isss",
    $workspace_id,
    $booking_date,
    $end_time,
    $start_time
);


$overlap_stmt->execute();


$overlap_result =
    $overlap_stmt->get_result();


if ($overlap_result->num_rows > 0) {

    $overlap_stmt->close();

    die("
        <div style='
            font-family: Arial;
            padding: 40px;
            text-align: center;
            background: #fff7f8;
            min-height: 100vh;
        '>

            <div style='
                max-width: 550px;
                margin: 80px auto;
                background: white;
                padding: 35px;
                border-radius: 18px;
                border: 1px solid #ffe4e6;
            '>

                <h2 style='
                    color: #be123c;
                    margin-bottom: 10px;
                '>
                    Workspace Already Booked
                </h2>

                <p style='
                    color: #6b5b63;
                    line-height: 1.6;
                '>
                    This workspace is already booked
                    during the selected time.
                    Please choose another time or date.
                </p>

                <br>

                <a
                    href='javascript:history.back()'
                    style='
                        display: inline-block;
                        padding: 12px 22px;
                        background: #3b0a1e;
                        color: white;
                        text-decoration: none;
                        border-radius: 10px;
                    '>

                    ← Choose Another Time

                </a>

            </div>

        </div>
    ");

}


$overlap_stmt->close();


// ==========================================
// INSERT BOOKING
// ==========================================

$insert_sql = "
    INSERT INTO bookings
    (
        user_id,
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
    VALUES
    (
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        ?,
        'Booked'
    )
";


$stmt = $conn->prepare($insert_sql);


if (!$stmt) {

    die(
        "Booking preparation failed: "
        . $conn->error
    );

}


// ==========================================
// BIND PARAMETERS
// ==========================================

$stmt->bind_param(
    "iisssssss",
    $user_id,
    $workspace_id,
    $company_name,
    $meeting_title,
    $booking_date,
    $start_time,
    $end_time,
    $booking_type,
    $recipient_email
);


// ==========================================
// EXECUTE
// ==========================================

if ($stmt->execute()) {

    $booking_id =
        $stmt->insert_id;

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


    <body
        class="min-h-screen
               bg-[#fff7f8]
               flex
               items-center
               justify-center
               px-5">


        <div
            class="w-full
                   max-w-lg
                   bg-white
                   rounded-3xl
                   border border-rose-100
                   shadow-xl
                   shadow-rose-950/5
                   p-8 sm:p-10
                   text-center">


            <!-- SUCCESS ICON -->

            <div
                class="w-20 h-20
                       mx-auto
                       rounded-full
                       bg-emerald-50
                       text-emerald-600
                       flex
                       items-center
                       justify-center
                       text-3xl
                       font-bold">

                ✓

            </div>


            <!-- TITLE -->

            <h1
                class="text-2xl
                       sm:text-3xl
                       font-bold
                       mt-6">

                Booking Confirmed!

            </h1>


            <p
                class="text-[#927e87]
                       mt-3
                       leading-6">

                Your workspace has been
                successfully booked.

            </p>


            <!-- DETAILS -->

            <div
                class="mt-7
                       p-5
                       rounded-2xl
                       bg-[#fff7f8]
                       border border-rose-100
                       text-left">


                <div
                    class="flex
                           justify-between
                           gap-4
                           py-2">


                    <span
                        class="text-sm
                               text-[#927e87]">

                        Booking ID

                    </span>


                    <span
                        class="text-sm
                               font-semibold">

                        #<?= $booking_id ?>

                    </span>


                </div>


                <div
                    class="flex
                           justify-between
                           gap-4
                           py-2">


                    <span
                        class="text-sm
                               text-[#927e87]">

                        Workspace

                    </span>


                    <span
                        class="text-sm
                               font-semibold
                               text-right">

                        <?= htmlspecialchars(
                            $workspace['workspace_name']
                        ) ?>

                    </span>


                </div>


                <div
                    class="flex
                           justify-between
                           gap-4
                           py-2">


                    <span
                        class="text-sm
                               text-[#927e87]">

                        Date

                    </span>


                    <span
                        class="text-sm
                               font-semibold">

                        <?= date(
                            "d M Y",
                            strtotime($booking_date)
                        ) ?>

                    </span>


                </div>


                <div
                    class="flex
                           justify-between
                           gap-4
                           py-2">


                    <span
                        class="text-sm
                               text-[#927e87]">

                        Booking Type

                    </span>


                    <span
                        class="text-sm
                               font-semibold">

                        <?= htmlspecialchars(
                            $booking_type
                        ) ?>

                    </span>


                </div>


                <div
                    class="flex
                           justify-between
                           gap-4
                           py-2">


                    <span
                        class="text-sm
                               text-[#927e87]">

                        Time

                    </span>


                    <span
                        class="text-sm
                               font-semibold">

                        <?php

                        if ($booking_type === "Full Day") {

                            echo "Full Day";

                        } else {

                            echo date(
                                "h:i A",
                                strtotime($start_time)
                            );

                            echo " - ";

                            echo date(
                                "h:i A",
                                strtotime($end_time)
                            );

                        }

                        ?>

                    </span>


                </div>


            </div>


            <!-- BUTTONS -->

            <div
                class="grid
                       grid-cols-1
                       sm:grid-cols-2
                       gap-3
                       mt-7">


                <a
                    href="mybooking.php"
                    class="inline-flex
                           items-center
                           justify-center
                           py-3.5
                           rounded-xl
                           bg-[#3b0a1e]
                           text-white
                           text-sm
                           font-semibold
                           hover:bg-rose-600
                           transition">

                    My Bookings

                </a>


                <a
                    href="workspaces.php"
                    class="inline-flex
                           items-center
                           justify-center
                           py-3.5
                           rounded-xl
                           border border-rose-200
                           bg-white
                           text-[#3b0a1e]
                           text-sm
                           font-semibold
                           hover:bg-rose-50
                           transition">

                    Workspaces

                </a>


            </div>


        </div>


    </body>

    </html>

    <?php

} else {

    $error_message =
        $stmt->error;

    $stmt->close();

    $conn->close();

    die("
        <div style='
            font-family: Arial;
            padding: 40px;
            text-align: center;
            background: #fff7f8;
            min-height: 100vh;
        '>

            <h2 style='color:#be123c;'>
                Booking Failed
            </h2>

            <p>
                Something went wrong while
                saving your booking.
            </p>

            <a href='javascript:history.back()'>
                ← Try Again
            </a>

        </div>
    ");

}

?>