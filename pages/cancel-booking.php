<?php

require_once "../config/auth.php";
require_once "../config/database.php";


// ==========================================
// ONLY POST REQUEST
// ==========================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: mybooking.php");
    exit;
}


// ==========================================
// CURRENT USER
// ==========================================

$user_id = $_SESSION['user_id'];


// ==========================================
// GET BOOKING ID
// ==========================================

$booking_id = (int) ($_POST['booking_id'] ?? 0);


if ($booking_id <= 0) {

    header("Location: mybooking.php");
    exit;

}


// ==========================================
// CHECK BOOKING
// ==========================================
// User can cancel ONLY their own booking
// ==========================================

$sql = "
    SELECT
        id,
        booking_date,
        end_time,
        status
    FROM bookings
    WHERE id = ?
    AND user_id = ?
    LIMIT 1
";


$stmt = $conn->prepare($sql);


$stmt->bind_param(
    "ii",
    $booking_id,
    $user_id
);


$stmt->execute();


$result = $stmt->get_result();


if ($result->num_rows === 0) {

    $stmt->close();
    $conn->close();

    die("
        <div style='
            font-family: Arial;
            background: #fff7f8;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        '>

            <div style='
                background: white;
                padding: 35px;
                border-radius: 20px;
                border: 1px solid #ffe4e6;
                text-align: center;
                max-width: 450px;
            '>

                <h2 style='color: #be123c;'>
                    Booking Not Found
                </h2>

                <p style='color: #6b5b63;'>
                    This booking does not exist or
                    does not belong to your account.
                </p>

                <a
                    href='mybooking.php'
                    style='
                        display: inline-block;
                        margin-top: 15px;
                        padding: 12px 20px;
                        background: #3b0a1e;
                        color: white;
                        text-decoration: none;
                        border-radius: 10px;
                    '>

                    ← My Bookings

                </a>

            </div>

        </div>
    ");

}


$booking = $result->fetch_assoc();

$stmt->close();


// ==========================================
// CHECK STATUS
// ==========================================

if ($booking['status'] !== 'Booked') {

    $conn->close();

    header("Location: mybooking.php");
    exit;

}


// ==========================================
// CHECK BOOKING TIME
// ==========================================

$booking_end = new DateTime(
    $booking['booking_date']
    . ' '
    . $booking['end_time']
);

$now = new DateTime();


if ($booking_end <= $now) {

    $conn->close();

    header("Location: mybooking.php");
    exit;

}


// ==========================================
// CANCEL BOOKING
// ==========================================

$update_sql = "
    UPDATE bookings
    SET status = 'Cancelled'
    WHERE id = ?
    AND user_id = ?
    AND status = 'Booked'
";


$update_stmt =
    $conn->prepare($update_sql);


$update_stmt->bind_param(
    "ii",
    $booking_id,
    $user_id
);


if ($update_stmt->execute()) {

    $update_stmt->close();
    $conn->close();

    header("Location: mybooking.php?cancelled=1");
    exit;

}


// ==========================================
// FAILURE
// ==========================================

$update_stmt->close();
$conn->close();

die("
    <div style='
        font-family: Arial;
        background: #fff7f8;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    '>

        <div style='
            background: white;
            padding: 35px;
            border-radius: 20px;
            border: 1px solid #ffe4e6;
            text-align: center;
            max-width: 450px;
        '>

            <h2 style='color: #be123c;'>
                Cancellation Failed
            </h2>

            <p style='color: #6b5b63;'>
                Something went wrong while
                cancelling the booking.
            </p>

            <a
                href='mybooking.php'
                style='
                    display: inline-block;
                    margin-top: 15px;
                    padding: 12px 20px;
                    background: #3b0a1e;
                    color: white;
                    text-decoration: none;
                    border-radius: 10px;
                '>

                ← My Bookings

            </a>

        </div>

    </div>
");

?>