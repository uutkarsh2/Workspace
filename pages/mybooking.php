<?php

require_once "../config/auth.php";
require_once "../config/database.php";


// ==========================================
// CURRENT USER
// ==========================================

$user_id = $_SESSION['user_id'];


// ==========================================
// GET BOOKINGS
// ==========================================

$sql = "
    SELECT
        b.*,
        w.workspace_name,
        w.type AS workspace_type

    FROM bookings b

    INNER JOIN workspaces w
        ON b.workspace_id = w.id

    WHERE b.user_id = ?

    ORDER BY
        b.booking_date DESC,
        b.start_time DESC
";


$stmt = $conn->prepare($sql);


$stmt->bind_param(
    "i",
    $user_id
);


$stmt->execute();


$result = $stmt->get_result();


// ==========================================
// SEPARATE BOOKINGS
// ==========================================

$upcoming_bookings = [];
$history_bookings = [];

$current_datetime = new DateTime();


while ($booking = $result->fetch_assoc()) {


    $booking_datetime = new DateTime(
        $booking['booking_date']
        . ' '
        . $booking['end_time']
    );


    if (
        $booking_datetime >= $current_datetime
        &&
        $booking['status'] === 'Booked'
    ) {

        $upcoming_bookings[] = $booking;

    } else {

        $history_bookings[] = $booking;

    }

}


$stmt->close();


// ==========================================
// CANCELLATION MESSAGE
// ==========================================

$cancelled =
    isset($_GET['cancelled'])
    &&
    $_GET['cancelled'] == '1';

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        My Bookings | WorkspaceHub
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

                    Booking Management

                </p>


                <h1
                    class="text-xl lg:text-2xl
                           font-semibold">

                    My Bookings

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
                <!-- SUCCESS MESSAGE -->
                <!-- ================================= -->

                <?php if ($cancelled): ?>

                    <div
                        class="mb-6
                               flex
                               items-center
                               gap-3
                               p-4
                               rounded-2xl
                               bg-emerald-50
                               border border-emerald-100
                               text-emerald-700">


                        <div
                            class="w-9 h-9
                                   rounded-xl
                                   bg-white
                                   flex
                                   items-center
                                   justify-center
                                   font-bold">

                            ✓

                        </div>


                        <div>

                            <p
                                class="text-sm
                                       font-semibold">

                                Booking Cancelled

                            </p>


                            <p
                                class="text-xs
                                       mt-0.5
                                       text-emerald-600">

                                Your booking has been
                                successfully cancelled.

                            </p>

                        </div>


                    </div>

                <?php endif; ?>


                <!-- ================================= -->
                <!-- INTRO -->
                <!-- ================================= -->

                <div
                    class="flex
                           flex-col
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

                            Your Bookings

                        </h2>


                        <p
                            class="text-[#8f7c84]
                                   mt-2">

                            View and manage your
                            workspace reservations.

                        </p>

                    </div>


                    <a
                        href="workspaces.php"
                        class="inline-flex
                               items-center
                               justify-center
                               gap-2
                               px-5 py-3
                               rounded-xl
                               bg-[#3b0a1e]
                               text-white
                               text-sm
                               font-semibold
                               hover:bg-rose-600
                               transition
                               w-fit">

                        + New Booking

                    </a>


                </div>


                <!-- ================================= -->
                <!-- UPCOMING BOOKINGS -->
                <!-- ================================= -->

                <div class="mb-10">


                    <div
                        class="flex
                               items-center
                               justify-between
                               mb-5">


                        <div>

                            <h3
                                class="text-xl
                                       font-bold">

                                Upcoming Bookings

                            </h3>


                            <p
                                class="text-sm
                                       text-[#9b858e]
                                       mt-1">

                                Your active and upcoming
                                workspace reservations.

                            </p>

                        </div>


                        <span
                            class="px-3 py-1.5
                                   rounded-full
                                   bg-rose-50
                                   text-rose-600
                                   text-xs
                                   font-semibold">

                            <?= count($upcoming_bookings) ?>

                        </span>


                    </div>


                    <?php if (count($upcoming_bookings) > 0): ?>


                        <div
                            class="grid
                                   grid-cols-1
                                   lg:grid-cols-2
                                   gap-5">


                            <?php foreach (
                                $upcoming_bookings
                                as $booking
                            ): ?>


                                <div
                                    class="bg-white
                                           rounded-2xl
                                           border border-rose-100
                                           shadow-sm
                                           hover:shadow-lg
                                           transition
                                           overflow-hidden">


                                    <!-- CARD -->

                                    <div class="p-6">


                                        <!-- TOP -->

                                        <div
                                            class="flex
                                                   items-start
                                                   justify-between
                                                   gap-4">


                                            <div
                                                class="flex
                                                       items-center
                                                       gap-4">


                                                <div
                                                    class="w-12 h-12
                                                           rounded-xl
                                                           bg-rose-50
                                                           text-rose-600
                                                           flex
                                                           items-center
                                                           justify-center
                                                           font-bold">

                                                    <?= htmlspecialchars(
                                                        $booking['workspace_type']
                                                    ) ?>

                                                </div>


                                                <div>

                                                    <h4
                                                        class="font-bold
                                                               text-lg">

                                                        <?= htmlspecialchars(
                                                            $booking['workspace_name']
                                                        ) ?>

                                                    </h4>


                                                    <p
                                                        class="text-sm
                                                               text-[#9b858e]">

                                                        <?= htmlspecialchars(
                                                            $booking['workspace_type']
                                                        ) ?>

                                                    </p>

                                                </div>


                                            </div>


                                            <!-- STATUS -->

                                            <span
                                                class="inline-flex
                                                       items-center
                                                       gap-1.5
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

                                                Booked

                                            </span>


                                        </div>


                                        <!-- DIVIDER -->

                                        <div
                                            class="border-t
                                                   border-rose-100
                                                   my-5">
                                        </div>


                                        <!-- MEETING -->

                                        <div class="mb-5">

                                            <p
                                                class="text-xs
                                                       uppercase
                                                       tracking-wider
                                                       text-[#a18e96]
                                                       font-semibold">

                                                Meeting

                                            </p>


                                            <p
                                                class="font-semibold
                                                       mt-1">

                                                <?= htmlspecialchars(
                                                    $booking['meeting_title']
                                                ) ?>

                                            </p>

                                        </div>


                                        <!-- DATE / TIME -->

                                        <div
                                            class="grid
                                                   grid-cols-1
                                                   sm:grid-cols-2
                                                   gap-4">


                                            <div>

                                                <p
                                                    class="text-xs
                                                           uppercase
                                                           tracking-wider
                                                           text-[#a18e96]
                                                           font-semibold">

                                                    Date

                                                </p>


                                                <p
                                                    class="text-sm
                                                           font-semibold
                                                           mt-1">

                                                    <?= date(
                                                        "d M Y",
                                                        strtotime(
                                                            $booking['booking_date']
                                                        )
                                                    ) ?>

                                                </p>

                                            </div>


                                            <div>

                                                <p
                                                    class="text-xs
                                                           uppercase
                                                           tracking-wider
                                                           text-[#a18e96]
                                                           font-semibold">

                                                    Time

                                                </p>


                                                <p
                                                    class="text-sm
                                                           font-semibold
                                                           mt-1">

                                                    <?php if (
                                                        $booking['booking_type']
                                                        === 'Full Day'
                                                    ): ?>

                                                        Full Day

                                                    <?php else: ?>

                                                        <?= date(
                                                            "h:i A",
                                                            strtotime(
                                                                $booking['start_time']
                                                            )
                                                        ) ?>

                                                        -

                                                        <?= date(
                                                            "h:i A",
                                                            strtotime(
                                                                $booking['end_time']
                                                            )
                                                        ) ?>

                                                    <?php endif; ?>

                                                </p>

                                            </div>


                                        </div>


                                        <!-- ================================= -->
                                        <!-- ACTIONS -->
                                        <!-- ================================= -->

                                        <div
                                            class="flex
                                                   flex-col
                                                   sm:flex-row
                                                   gap-3
                                                   mt-6
                                                   pt-5
                                                   border-t
                                                   border-rose-100">


                                            <!-- BOOKING ID -->

                                            <div
                                                class="flex-1
                                                       px-4 py-3
                                                       rounded-xl
                                                       bg-[#fff7f8]">


                                                <p
                                                    class="text-xs
                                                           text-[#a18e96]">

                                                    Booking ID

                                                </p>


                                                <p
                                                    class="text-sm
                                                           font-semibold
                                                           mt-1">

                                                    #<?= $booking['id'] ?>

                                                </p>


                                            </div>


                                            <!-- CANCEL -->

                                            <form
                                                action="cancel-booking.php"
                                                method="POST"
                                                class="flex-1"
                                                onsubmit="return confirmCancel();">


                                                <input
                                                    type="hidden"
                                                    name="booking_id"
                                                    value="<?= $booking['id'] ?>">


                                                <button
                                                    type="submit"
                                                    class="w-full
                                                           px-4 py-3
                                                           rounded-xl
                                                           border border-rose-200
                                                           bg-white
                                                           text-rose-600
                                                           text-sm
                                                           font-semibold
                                                           hover:bg-rose-50
                                                           hover:border-rose-300
                                                           transition">

                                                    Cancel Booking

                                                </button>


                                            </form>


                                        </div>


                                    </div>


                                </div>


                            <?php endforeach; ?>


                        </div>


                    <?php else: ?>


                        <!-- EMPTY -->

                        <div
                            class="bg-white
                                   rounded-2xl
                                   border border-dashed
                                   border-rose-200
                                   p-10
                                   text-center">


                            <div
                                class="w-16 h-16
                                       mx-auto
                                       rounded-2xl
                                       bg-rose-50
                                       text-rose-500
                                       flex
                                       items-center
                                       justify-center
                                       text-2xl">

                                ▤

                            </div>


                            <h4
                                class="text-lg
                                       font-semibold
                                       mt-5">

                                No Upcoming Bookings

                            </h4>


                            <p
                                class="text-sm
                                       text-[#9b858e]
                                       mt-2">

                                You don't have any upcoming
                                workspace reservations.

                            </p>


                            <a
                                href="workspaces.php"
                                class="inline-flex
                                       items-center
                                       justify-center
                                       mt-5
                                       px-5 py-3
                                       rounded-xl
                                       bg-[#3b0a1e]
                                       text-white
                                       text-sm
                                       font-semibold
                                       hover:bg-rose-600
                                       transition">

                                Book a Workspace

                            </a>


                        </div>


                    <?php endif; ?>


                </div>


                <!-- ================================= -->
                <!-- HISTORY -->
                <!-- ================================= -->

                <div>


                    <div
                        class="flex
                               items-center
                               justify-between
                               mb-5">


                        <div>

                            <h3
                                class="text-xl
                                       font-bold">

                                Booking History

                            </h3>


                            <p
                                class="text-sm
                                       text-[#9b858e]
                                       mt-1">

                                Your previous and cancelled
                                workspace reservations.

                            </p>

                        </div>


                        <span
                            class="px-3 py-1.5
                                   rounded-full
                                   bg-gray-100
                                   text-gray-600
                                   text-xs
                                   font-semibold">

                            <?= count($history_bookings) ?>

                        </span>


                    </div>


                    <?php if (count($history_bookings) > 0): ?>


                        <div
                            class="bg-white
                                   rounded-2xl
                                   border border-rose-100
                                   shadow-sm
                                   overflow-hidden">


                            <div class="hidden md:block overflow-x-auto">


                                <table
                                    class="w-full text-left">


                                    <thead
                                        class="bg-[#fff7f8]
                                               border-b
                                               border-rose-100">


                                        <tr>


                                            <th
                                                class="px-6 py-4
                                                       text-xs
                                                       uppercase
                                                       tracking-wider
                                                       text-[#9b858e]">

                                                Workspace

                                            </th>


                                            <th
                                                class="px-6 py-4
                                                       text-xs
                                                       uppercase
                                                       tracking-wider
                                                       text-[#9b858e]">

                                                Meeting

                                            </th>


                                            <th
                                                class="px-6 py-4
                                                       text-xs
                                                       uppercase
                                                       tracking-wider
                                                       text-[#9b858e]">

                                                Date & Time

                                            </th>


                                            <th
                                                class="px-6 py-4
                                                       text-xs
                                                       uppercase
                                                       tracking-wider
                                                       text-[#9b858e]">

                                                Type

                                            </th>


                                            <th
                                                class="px-6 py-4
                                                       text-xs
                                                       uppercase
                                                       tracking-wider
                                                       text-[#9b858e]">

                                                Status

                                            </th>


                                        </tr>


                                    </thead>


                                    <tbody
                                        class="divide-y
                                               divide-rose-100">


                                        <?php foreach (
                                            $history_bookings
                                            as $booking
                                        ): ?>


                                            <tr
                                                class="hover:bg-[#fffafb]
                                                       transition">


                                                <!-- WORKSPACE -->

                                                <td
                                                    class="px-6 py-5">


                                                    <div
                                                        class="flex
                                                               items-center
                                                               gap-3">


                                                        <div
                                                            class="w-9 h-9
                                                                   rounded-lg
                                                                   bg-rose-50
                                                                   text-rose-600
                                                                   flex
                                                                   items-center
                                                                   justify-center
                                                                   text-xs
                                                                   font-bold">

                                                            <?= htmlspecialchars(
                                                                $booking['workspace_type']
                                                            ) ?>

                                                        </div>


                                                        <div>

                                                            <p
                                                                class="font-semibold
                                                                       text-sm">

                                                                <?= htmlspecialchars(
                                                                    $booking['workspace_name']
                                                                ) ?>

                                                            </p>


                                                            <p
                                                                class="text-xs
                                                                       text-[#a18e96]">

                                                                #<?= $booking['id'] ?>

                                                            </p>

                                                        </div>


                                                    </div>


                                                </td>


                                                <!-- MEETING -->

                                                <td
                                                    class="px-6 py-5">


                                                    <p
                                                        class="text-sm
                                                               font-medium">

                                                        <?= htmlspecialchars(
                                                            $booking['meeting_title']
                                                        ) ?>

                                                    </p>


                                                </td>


                                                <!-- DATE -->

                                                <td
                                                    class="px-6 py-5">


                                                    <p
                                                        class="text-sm
                                                               font-medium">

                                                        <?= date(
                                                            "d M Y",
                                                            strtotime(
                                                                $booking['booking_date']
                                                            )
                                                        ) ?>

                                                    </p>


                                                    <p
                                                        class="text-xs
                                                               text-[#a18e96]
                                                               mt-1">


                                                        <?php if (
                                                            $booking['booking_type']
                                                            === 'Full Day'
                                                        ): ?>

                                                            Full Day

                                                        <?php else: ?>

                                                            <?= date(
                                                                "h:i A",
                                                                strtotime(
                                                                    $booking['start_time']
                                                                )
                                                            ) ?>

                                                            -

                                                            <?= date(
                                                                "h:i A",
                                                                strtotime(
                                                                    $booking['end_time']
                                                                )
                                                            ) ?>

                                                        <?php endif; ?>


                                                    </p>


                                                </td>


                                                <!-- TYPE -->

                                                <td
                                                    class="px-6 py-5">


                                                    <span
                                                        class="text-xs
                                                               font-medium
                                                               px-2.5 py-1
                                                               rounded-lg
                                                               bg-rose-50
                                                               text-rose-600">

                                                        <?= htmlspecialchars(
                                                            $booking['booking_type']
                                                        ) ?>

                                                    </span>


                                                </td>


                                                <!-- STATUS -->

                                                <td
                                                    class="px-6 py-5">


                                                    <?php if (
                                                        $booking['status']
                                                        === 'Cancelled'
                                                    ): ?>


                                                        <span
                                                            class="text-xs
                                                                   font-medium
                                                                   px-2.5 py-1
                                                                   rounded-lg
                                                                   bg-rose-50
                                                                   text-rose-600">

                                                            Cancelled

                                                        </span>


                                                    <?php else: ?>


                                                        <span
                                                            class="text-xs
                                                                   font-medium
                                                                   px-2.5 py-1
                                                                   rounded-lg
                                                                   bg-gray-100
                                                                   text-gray-600">

                                                            <?= htmlspecialchars(
                                                                $booking['status']
                                                            ) ?>

                                                        </span>


                                                    <?php endif; ?>


                                                </td>


                                            </tr>


                                        <?php endforeach; ?>


                                    </tbody>


                                </table>


                            </div>


                            <!-- ================================= -->
                            <!-- MOBILE HISTORY -->
                            <!-- ================================= -->

                            <div
                                class="md:hidden
                                       divide-y
                                       divide-rose-100">


                                <?php foreach (
                                    $history_bookings
                                    as $booking
                                ): ?>


                                    <div
                                        class="p-5">


                                        <div
                                            class="flex
                                                   items-start
                                                   justify-between
                                                   gap-3">


                                            <div>

                                                <p
                                                    class="font-semibold">

                                                    <?= htmlspecialchars(
                                                        $booking['workspace_name']
                                                    ) ?>

                                                </p>


                                                <p
                                                    class="text-xs
                                                           text-[#a18e96]
                                                           mt-1">

                                                    #<?= $booking['id'] ?>

                                                </p>

                                            </div>


                                            <?php if (
                                                $booking['status']
                                                === 'Cancelled'
                                            ): ?>


                                                <span
                                                    class="text-xs
                                                           px-2.5 py-1
                                                           rounded-lg
                                                           bg-rose-50
                                                           text-rose-600">

                                                    Cancelled

                                                </span>


                                            <?php else: ?>


                                                <span
                                                    class="text-xs
                                                           px-2.5 py-1
                                                           rounded-lg
                                                           bg-gray-100
                                                           text-gray-600">

                                                    <?= htmlspecialchars(
                                                        $booking['status']
                                                    ) ?>

                                                </span>


                                            <?php endif; ?>


                                        </div>


                                        <p
                                            class="text-sm
                                                   font-medium
                                                   mt-4">

                                            <?= htmlspecialchars(
                                                $booking['meeting_title']
                                            ) ?>

                                        </p>


                                        <p
                                            class="text-xs
                                                   text-[#9b858e]
                                                   mt-2">

                                            <?= date(
                                                "d M Y",
                                                strtotime(
                                                    $booking['booking_date']
                                                )
                                            ) ?>

                                            •

                                            <?php if (
                                                $booking['booking_type']
                                                === 'Full Day'
                                            ): ?>

                                                Full Day

                                            <?php else: ?>

                                                <?= date(
                                                    "h:i A",
                                                    strtotime(
                                                        $booking['start_time']
                                                    )
                                                ) ?>

                                                -

                                                <?= date(
                                                    "h:i A",
                                                    strtotime(
                                                        $booking['end_time']
                                                    )
                                                ) ?>

                                            <?php endif; ?>


                                        </p>


                                    </div>


                                <?php endforeach; ?>


                            </div>


                        </div>


                    <?php else: ?>


                        <div
                            class="bg-white
                                   rounded-2xl
                                   border border-dashed
                                   border-rose-200
                                   p-10
                                   text-center">


                            <div
                                class="w-14 h-14
                                       mx-auto
                                       rounded-xl
                                       bg-gray-50
                                       text-gray-400
                                       flex
                                       items-center
                                       justify-center
                                       text-xl">

                                ◷

                            </div>


                            <h4
                                class="text-lg
                                       font-semibold
                                       mt-4">

                                No Booking History

                            </h4>


                            <p
                                class="text-sm
                                       text-[#9b858e]
                                       mt-2">

                                Your completed or cancelled
                                bookings will appear here.

                            </p>


                        </div>


                    <?php endif; ?>


                </div>


            </div>


        </section>


    </main>


</div>


<!-- ================================= -->
<!-- CANCEL CONFIRMATION -->
<!-- ================================= -->

<script>

function confirmCancel() {

    return confirm(
        "Are you sure you want to cancel this booking?"
    );

}

</script>


</body>

</html>