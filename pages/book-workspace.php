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
// GET WORKSPACE
// ==========================================

$sql = "
    SELECT *
    FROM workspaces
    WHERE id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $workspace_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: workspaces.php");
    exit;
}

$workspace = $result->fetch_assoc();

$stmt->close();


// ==========================================
// WORKSPACE DATA
// ==========================================

$workspace_name = $workspace['workspace_name'];
$workspace_type = $workspace['type'];
$time_slot      = $workspace['time_slot'];


// ==========================================
// USER DATA
// ==========================================

$user_name  = $_SESSION['user_name'] ?? '';
$user_email = $_SESSION['user_email'] ?? '';

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        Book <?= htmlspecialchars($workspace_name) ?> | WorkspaceHub
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

                    Book Workspace

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
                <!-- BACK -->
                <!-- ================================= -->

                <a
                    href="workspace-details.php?id=<?= $workspace_id ?>"
                    class="inline-flex
                           items-center
                           gap-2
                           text-sm
                           font-medium
                           text-[#806d76]
                           hover:text-rose-600
                           transition
                           mb-6">

                    ← Back to Workspace

                </a>


                <!-- ================================= -->
                <!-- BOOKING GRID -->
                <!-- ================================= -->

                <div
                    class="grid
                           grid-cols-1
                           lg:grid-cols-3
                           gap-6">


                    <!-- ================================= -->
                    <!-- LEFT: WORKSPACE INFO -->
                    <!-- ================================= -->

                    <div
                        class="lg:col-span-1">


                        <div
                            class="bg-[#3b0a1e]
                                   text-white
                                   rounded-3xl
                                   p-7
                                   shadow-lg
                                   shadow-rose-950/10
                                   sticky
                                   top-6">


                            <!-- ICON -->

                            <div
                                class="w-16 h-16
                                       rounded-2xl
                                       bg-white/10
                                       border border-white/10
                                       flex
                                       items-center
                                       justify-center
                                       text-2xl
                                       font-bold
                                       text-pink-300
                                       mb-6">

                                <?= htmlspecialchars($workspace_type) ?>

                            </div>


                            <!-- NAME -->

                            <p
                                class="text-sm
                                       text-pink-300
                                       font-medium">

                                Workspace

                            </p>


                            <h2
                                class="text-2xl
                                       font-bold
                                       mt-1">

                                <?= htmlspecialchars($workspace_name) ?>

                            </h2>


                            <!-- DETAILS -->

                            <div
                                class="border-t
                                       border-white/10
                                       mt-7 pt-6">


                                <div
                                    class="mb-5">

                                    <p
                                        class="text-xs
                                               uppercase
                                               tracking-wider
                                               text-white/40">

                                        Workspace Type

                                    </p>


                                    <p
                                        class="text-sm
                                               font-medium
                                               mt-1">

                                        <?= htmlspecialchars($workspace_type) ?>

                                    </p>

                                </div>


                                <div>

                                    <p
                                        class="text-xs
                                               uppercase
                                               tracking-wider
                                               text-white/40">

                                        Default Time Slot

                                    </p>


                                    <p
                                        class="text-sm
                                               font-medium
                                               mt-1">

                                        <?= htmlspecialchars($time_slot) ?>

                                    </p>

                                </div>


                            </div>


                            <!-- INFO -->

                            <div
                                class="mt-8
                                       p-4
                                       rounded-2xl
                                       bg-white/5
                                       border border-white/10">


                                <p
                                    class="text-sm
                                           font-semibold">

                                    Booking Tip

                                </p>


                                <p
                                    class="text-xs
                                           text-white/50
                                           leading-5
                                           mt-2">

                                    Select a date and time
                                    according to your meeting
                                    requirement.

                                </p>


                            </div>


                        </div>


                    </div>


                    <!-- ================================= -->
                    <!-- RIGHT: BOOKING FORM -->
                    <!-- ================================= -->

                    <div
                        class="lg:col-span-2">


                        <div
                            class="bg-white
                                   rounded-3xl
                                   border border-rose-100
                                   shadow-sm
                                   overflow-hidden">


                            <!-- FORM HEADER -->

                            <div
                                class="px-6 sm:px-8
                                       py-6
                                       border-b
                                       border-rose-100">


                                <h2
                                    class="text-xl
                                           font-bold">

                                    Booking Details

                                </h2>


                                <p
                                    class="text-sm
                                           text-[#927e87]
                                           mt-1">

                                    Enter the details for your
                                    workspace booking.

                                </p>

                            </div>


                            <!-- FORM -->

                            <form
                                id="bookingForm"
                                action="save-booking.php"
                                method="POST"
                                class="p-6 sm:p-8">


                                <!-- WORKSPACE ID -->

                                <input
                                    type="hidden"
                                    name="workspace_id"
                                    value="<?= $workspace_id ?>">


                                <!-- ================================= -->
                                <!-- COMPANY -->
                                <!-- ================================= -->

                                <div class="mb-6">


                                    <label
                                        for="company_name"
                                        class="block
                                               text-sm
                                               font-semibold
                                               mb-2">

                                        Company Name

                                    </label>


                                    <input
                                        type="text"
                                        id="company_name"
                                        name="company_name"
                                        placeholder="Enter company name"
                                        required
                                        class="w-full
                                               px-4 py-3.5
                                               rounded-xl
                                               border border-rose-100
                                               bg-[#fffafb]
                                               outline-none
                                               text-sm
                                               focus:border-rose-400
                                               focus:ring-4
                                               focus:ring-rose-100
                                               transition">


                                </div>


                                <!-- ================================= -->
                                <!-- MEETING TITLE -->
                                <!-- ================================= -->

                                <div class="mb-6">


                                    <label
                                        for="meeting_title"
                                        class="block
                                               text-sm
                                               font-semibold
                                               mb-2">

                                        Meeting Title

                                    </label>


                                    <input
                                        type="text"
                                        id="meeting_title"
                                        name="meeting_title"
                                        placeholder="e.g. Project Discussion"
                                        required
                                        class="w-full
                                               px-4 py-3.5
                                               rounded-xl
                                               border border-rose-100
                                               bg-[#fffafb]
                                               outline-none
                                               text-sm
                                               focus:border-rose-400
                                               focus:ring-4
                                               focus:ring-rose-100
                                               transition">


                                </div>


                                <!-- ================================= -->
                                <!-- DATE -->
                                <!-- ================================= -->

                                <div class="mb-6">


                                    <label
                                        for="booking_date"
                                        class="block
                                               text-sm
                                               font-semibold
                                               mb-2">

                                        Booking Date

                                    </label>


                                    <input
                                        type="date"
                                        id="booking_date"
                                        name="booking_date"
                                        required
                                        class="w-full
                                               px-4 py-3.5
                                               rounded-xl
                                               border border-rose-100
                                               bg-[#fffafb]
                                               outline-none
                                               text-sm
                                               focus:border-rose-400
                                               focus:ring-4
                                               focus:ring-rose-100
                                               transition">


                                </div>


                                <!-- ================================= -->
                                <!-- TIME -->
                                <!-- ================================= -->

                                <div
                                    class="grid
                                           grid-cols-1
                                           sm:grid-cols-2
                                           gap-5">


                                    <!-- START -->

                                    <div id="startTimeGroup">


                                        <label
                                            for="start_time"
                                            class="block
                                                   text-sm
                                                   font-semibold
                                                   mb-2">

                                            Start Time

                                        </label>


                                        <input
                                            type="time"
                                            id="start_time"
                                            name="start_time"
                                            required
                                            class="w-full
                                                   px-4 py-3.5
                                                   rounded-xl
                                                   border border-rose-100
                                                   bg-[#fffafb]
                                                   outline-none
                                                   text-sm
                                                   focus:border-rose-400
                                                   focus:ring-4
                                                   focus:ring-rose-100
                                                   transition">

                                    </div>


                                    <!-- END -->

                                    <div id="endTimeGroup">


                                        <label
                                            for="end_time"
                                            class="block
                                                   text-sm
                                                   font-semibold
                                                   mb-2">

                                            End Time

                                        </label>


                                        <input
                                            type="time"
                                            id="end_time"
                                            name="end_time"
                                            required
                                            class="w-full
                                                   px-4 py-3.5
                                                   rounded-xl
                                                   border border-rose-100
                                                   bg-[#fffafb]
                                                   outline-none
                                                   text-sm
                                                   focus:border-rose-400
                                                   focus:ring-4
                                                   focus:ring-rose-100
                                                   transition">

                                    </div>


                                </div>


                                <!-- ================================= -->
                                <!-- FULL DAY -->
                                <!-- ================================= -->

                                <div
                                    class="mt-6
                                           p-5
                                           rounded-2xl
                                           bg-rose-50
                                           border border-rose-100">


                                    <div
                                        class="flex
                                               items-center
                                               gap-3">


                                        <input
                                            type="checkbox"
                                            id="full_day"
                                            name="full_day"
                                            value="1"
                                            class="w-5 h-5
                                                   accent-rose-600
                                                   cursor-pointer">


                                        <label
                                            for="full_day"
                                            class="text-sm
                                                   font-semibold
                                                   cursor-pointer">

                                            Book Full Day

                                        </label>


                                    </div>


                                    <p
                                        class="text-xs
                                               text-[#927e87]
                                               mt-2
                                               ml-8">

                                        Select this option if you
                                        want to reserve the
                                        workspace for the entire
                                        day.

                                    </p>


                                </div>


                                <!-- ================================= -->
                                <!-- RECIPIENT EMAIL -->
                                <!-- ================================= -->

                                <div class="mt-6">


                                    <label
                                        for="recipient_email"
                                        class="block
                                               text-sm
                                               font-semibold
                                               mb-2">

                                        Recipient Email

                                    </label>


                                    <input
                                        type="email"
                                        id="recipient_email"
                                        name="recipient_email"
                                        value="<?= htmlspecialchars($user_email) ?>"
                                        placeholder="Enter recipient email"
                                        required
                                        class="w-full
                                               px-4 py-3.5
                                               rounded-xl
                                               border border-rose-100
                                               bg-[#fffafb]
                                               outline-none
                                               text-sm
                                               focus:border-rose-400
                                               focus:ring-4
                                               focus:ring-rose-100
                                               transition">


                                </div>


                                <!-- ================================= -->
                                <!-- SUMMARY -->
                                <!-- ================================= -->

                                <div
                                    class="mt-7
                                           p-5
                                           rounded-2xl
                                           bg-[#fff7f8]
                                           border border-rose-100">


                                    <div
                                        class="flex
                                               items-start
                                               gap-3">


                                        <div
                                            class="w-9 h-9
                                                   rounded-xl
                                                   bg-white
                                                   text-rose-600
                                                   flex
                                                   items-center
                                                   justify-center
                                                   font-semibold
                                                   shrink-0">

                                            ✓

                                        </div>


                                        <div>

                                            <p
                                                class="text-sm
                                                       font-semibold">

                                                Booking Summary

                                            </p>


                                            <p
                                                id="bookingSummary"
                                                class="text-sm
                                                       text-[#927e87]
                                                       mt-1">

                                                Select a date and
                                                time to continue.

                                            </p>

                                        </div>


                                    </div>


                                </div>


                                <!-- ================================= -->
                                <!-- BUTTONS -->
                                <!-- ================================= -->

                                <div
                                    class="flex
                                           flex-col-reverse
                                           sm:flex-row
                                           gap-3
                                           mt-8">


                                    <a
                                        href="workspace-details.php?id=<?= $workspace_id ?>"
                                        class="flex-1
                                               inline-flex
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

                                        Cancel

                                    </a>


                                    <button
                                        type="submit"
                                        class="flex-1
                                               inline-flex
                                               items-center
                                               justify-center
                                               gap-2
                                               py-3.5
                                               rounded-xl
                                               bg-[#3b0a1e]
                                               text-white
                                               text-sm
                                               font-semibold
                                               hover:bg-rose-600
                                               transition
                                               shadow-lg
                                               shadow-rose-950/10">

                                        Confirm Booking

                                        <span class="text-lg">
                                            →
                                        </span>

                                    </button>


                                </div>


                            </form>


                        </div>


                    </div>


                </div>


            </div>


        </section>


    </main>


</div>


<!-- ================================= -->
<!-- BOOKING JAVASCRIPT -->
<!-- ================================= -->

<script>

document.addEventListener("DOMContentLoaded", function () {


    const form =
        document.getElementById("bookingForm");

    const bookingDate =
        document.getElementById("booking_date");

    const startTime =
        document.getElementById("start_time");

    const endTime =
        document.getElementById("end_time");

    const fullDay =
        document.getElementById("full_day");

    const startGroup =
        document.getElementById("startTimeGroup");

    const endGroup =
        document.getElementById("endTimeGroup");

    const summary =
        document.getElementById("bookingSummary");


    // ======================================
    // SET MINIMUM DATE
    // ======================================

    const today =
        new Date().toISOString().split("T")[0];

    bookingDate.setAttribute("min", today);


    // ======================================
    // FULL DAY TOGGLE
    // ======================================

    function toggleFullDay() {


        if (fullDay.checked) {


            startGroup.style.display = "none";

            endGroup.style.display = "none";


            startTime.disabled = true;

            endTime.disabled = true;


            startTime.removeAttribute("required");

            endTime.removeAttribute("required");


            summary.textContent =
                "Full day booking selected. The workspace will be reserved for the entire selected date.";


        } else {


            startGroup.style.display = "block";

            endGroup.style.display = "block";


            startTime.disabled = false;

            endTime.disabled = false;


            startTime.setAttribute(
                "required",
                "required"
            );

            endTime.setAttribute(
                "required",
                "required"
            );


            updateSummary();

        }

    }


    fullDay.addEventListener(
        "change",
        toggleFullDay
    );


    // ======================================
    // UPDATE SUMMARY
    // ======================================

    function updateSummary() {


        if (!bookingDate.value) {

            summary.textContent =
                "Select a date and time to continue.";

            return;
        }


        if (fullDay.checked) {

            summary.textContent =
                "Full day booking selected for " +
                formatDate(bookingDate.value) +
                ".";

            return;
        }


        if (
            bookingDate.value &&
            startTime.value &&
            endTime.value
        ) {


            summary.textContent =
                formatDate(bookingDate.value) +
                " • " +
                formatTime(startTime.value) +
                " - " +
                formatTime(endTime.value);


        } else {


            summary.textContent =
                "Select a date and start/end time.";

        }

    }


    // ======================================
    // DATE FORMAT
    // ======================================

    function formatDate(dateValue) {

        const date =
            new Date(dateValue + "T00:00:00");

        return date.toLocaleDateString(
            "en-IN",
            {
                day: "2-digit",
                month: "short",
                year: "numeric"
            }
        );

    }


    // ======================================
    // TIME FORMAT
    // ======================================

    function formatTime(timeValue) {


        const parts =
            timeValue.split(":");

        let hours =
            parseInt(parts[0]);

        const minutes =
            parts[1];

        const period =
            hours >= 12 ? "PM" : "AM";


        hours =
            hours % 12 || 12;


        return (
            String(hours).padStart(2, "0")
            + ":" +
            minutes +
            " " +
            period
        );

    }


    // ======================================
    // INPUT EVENTS
    // ======================================

    bookingDate.addEventListener(
        "change",
        updateSummary
    );

    startTime.addEventListener(
        "change",
        updateSummary
    );

    endTime.addEventListener(
        "change",
        updateSummary
    );


    // ======================================
    // FORM VALIDATION
    // ======================================

    form.addEventListener(
        "submit",
        function (event) {


            if (
                !bookingDate.value ||
                bookingDate.value < today
            ) {

                event.preventDefault();

                alert(
                    "Please select today or a future date."
                );

                return;

            }


            // Full Day

            if (fullDay.checked) {

                return;

            }


            // Normal Booking

            if (
                !startTime.value ||
                !endTime.value
            ) {

                event.preventDefault();

                alert(
                    "Please select start and end time."
                );

                return;

            }


            if (
                startTime.value >= endTime.value
            ) {

                event.preventDefault();

                alert(
                    "End time must be later than start time."
                );

                return;

            }

        }
    );


    // Initial state

    toggleFullDay();

});

</script>


</body>

</html>