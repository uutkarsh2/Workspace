<?php

require_once "../config/auth.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../config/database.php";


/*
|--------------------------------------------------------------------------
| Validate Workspace ID
|--------------------------------------------------------------------------
*/

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {

    die("Invalid workspace ID.");

}

$workspace_id = (int) $_GET['id'];


/*
|--------------------------------------------------------------------------
| Get Workspace
|--------------------------------------------------------------------------
*/

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

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>
        Book <?= htmlspecialchars($workspace['workspace_name']) ?> | WorkspaceHub
    </title>

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
    <!-- MAIN CONTENT -->
    <!-- ========================= -->

    <main class="flex-1 min-w-0">


        <!-- Top Header -->

        <header class="bg-white border-b border-slate-200">

            <div class="px-6 lg:px-10 py-5">

                <p class="text-sm text-slate-400">

                    Workspace Management

                </p>

                <h2 class="text-xl font-semibold">

                    Book Workspace

                </h2>

            </div>

        </header>


        <!-- ========================= -->
        <!-- CONTENT -->
        <!-- ========================= -->

        <section class="px-6 lg:px-10 py-10">


            <div class="max-w-4xl mx-auto">


                <!-- Back -->

                <a
                    href="workspace-details.php?id=<?= $workspace['id'] ?>"
                    class="inline-flex items-center text-sm text-slate-500 hover:text-blue-600 mb-7 transition">

                    ← Back to Workspace

                </a>


                <!-- ========================= -->
                <!-- BOOKING CARD -->
                <!-- ========================= -->

                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">


                    <!-- Header -->

                    <div class="p-8 border-b border-slate-100">


                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5">


                            <div>


                                <div class="flex items-center gap-3">

                                    <span class="text-sm font-semibold text-blue-600">

                                        <?= htmlspecialchars($workspace['type']) ?>

                                    </span>


                                    <span
                                        class="px-3 py-1 text-xs font-medium rounded-full bg-emerald-50 text-emerald-600">

                                        <?= htmlspecialchars($workspace['status']) ?>

                                    </span>

                                </div>


                                <h1 class="text-2xl font-bold mt-3">

                                    Book <?= htmlspecialchars($workspace['workspace_name']) ?>

                                </h1>


                                <p class="text-sm text-slate-500 mt-2">

                                    Select your meeting details and booking time.

                                </p>

                            </div>


                            <!-- Workspace Icon -->

                            <div
                                class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl">

                                ◫

                            </div>


                        </div>

                    </div>


                    <!-- ========================= -->
                    <!-- BOOKING FORM -->
                    <!-- ========================= -->

                    <form
                        id="bookingForm"
                        action="save-booking.php"
                        method="POST"
                        class="p-8">


                        <!-- Workspace ID -->

                        <input
                            type="hidden"
                            name="workspace_id"
                            value="<?= $workspace['id'] ?>">


                        <!-- ========================= -->
                        <!-- COMPANY NAME -->
                        <!-- ========================= -->

                        <div class="mb-6">


                            <label
                                for="company_name"
                                class="block text-sm font-medium text-slate-700 mb-2">

                                Company Name

                                <span class="text-red-500">
                                    *
                                </span>

                            </label>


                            <input
                                type="text"
                                id="company_name"
                                name="company_name"
                                placeholder="e.g. ABC Technologies"
                                required
                                class="w-full px-4 py-3 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">


                            <p class="text-xs text-slate-400 mt-2">

                                Enter the company or organization for this meeting.

                            </p>

                        </div>


                        <!-- ========================= -->
                        <!-- MEETING TITLE -->
                        <!-- ========================= -->

                        <div class="mb-6">


                            <label
                                for="meeting_title"
                                class="block text-sm font-medium text-slate-700 mb-2">

                                Meeting Title

                                <span class="text-red-500">
                                    *
                                </span>

                            </label>


                            <input
                                type="text"
                                id="meeting_title"
                                name="meeting_title"
                                placeholder="e.g. Project Discussion"
                                required
                                class="w-full px-4 py-3 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                        </div>


                        <!-- ========================= -->
                        <!-- DATE -->
                        <!-- ========================= -->

                        <div class="mb-6">


                            <label
                                for="booking_date"
                                class="block text-sm font-medium text-slate-700 mb-2">

                                Meeting Date

                                <span class="text-red-500">
                                    *
                                </span>

                            </label>


                            <input
                                type="date"
                                id="booking_date"
                                name="booking_date"
                                required
                                class="w-full px-4 py-3 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">

                        </div>


                        <!-- ========================= -->
                        <!-- TIME -->
                        <!-- ========================= -->

                        <div class="mb-6">


                            <div class="flex items-center justify-between mb-3">

                                <label
                                    class="block text-sm font-medium text-slate-700">

                                    Meeting Time

                                </label>


                                <span class="text-xs text-slate-400">

                                    Workspace slot:
                                    <?= htmlspecialchars($workspace['time_slot']) ?>

                                </span>

                            </div>


                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">


                                <!-- Start -->
                                 <div id="startTimeGroup">
    <label for="start_time" class="block text-sm font-medium text-slate-700 mb-2">
        Start Time
    </label>

    <input
        type="time"
        id="start_time"
        name="start_time"
        class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
        required>
</div>

                                


                                <!-- End -->
<div id="endTimeGroup">
    <label for="end_time" class="block text-sm font-medium text-slate-700 mb-2">
        End Time
    </label>

    <input
        type="time"
        id="end_time"
        name="end_time"
        class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
        required>
</div>

                            </div>

                        </div>


                        <!-- ========================= -->
                        <!-- FULL DAY -->
                        <!-- ========================= -->

                       <div class="mt-5 flex items-center gap-3">
    <input
        type="checkbox"
        id="full_day"
        name="full_day"
        value="1"
        class="w-5 h-5 text-blue-600 rounded">

    <label for="full_day" class="text-sm font-medium text-slate-700">
        Book Full Day
    </label>
</div>


                        <!-- ========================= -->
                        <!-- RECIPIENT EMAIL -->
                        <!-- ========================= -->

                        <div class="mb-8">


                            <label
                                for="recipient_email"
                                class="block text-sm font-medium text-slate-700 mb-2">

                                Recipient Email

                                <span class="text-red-500">
                                    *
                                </span>

                            </label>


                            <input
                                type="email"
                                id="recipient_email"
                                name="recipient_email"
                                placeholder="e.g. manager@company.com"
                                required
                                class="w-full px-4 py-3 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">


                            <p class="text-xs text-slate-400 mt-2">

                                Booking information will be sent to this email address.

                            </p>

                        </div>


                        <!-- ========================= -->
                        <!-- BOOKING SUMMARY -->
                        <!-- ========================= -->

                        <div
                            class="bg-blue-50 border border-blue-100 rounded-xl p-5 mb-8">


                            <p class="text-xs uppercase tracking-wide text-blue-600 font-semibold">

                                Workspace

                            </p>


                            <div class="flex items-center justify-between mt-2">


                                <p class="font-semibold text-blue-900">

                                    <?= htmlspecialchars($workspace['workspace_name']) ?>

                                </p>


                                <span class="text-sm text-blue-700">

                                    <?= htmlspecialchars($workspace['type']) ?>

                                </span>

                            </div>


                            <p class="text-sm text-blue-700 mt-1">

                                Default Slot:
                                <?= htmlspecialchars($workspace['time_slot']) ?>

                            </p>

                        </div>


                        <!-- ========================= -->
                        <!-- BUTTONS -->
                        <!-- ========================= -->

                        <div class="flex flex-col sm:flex-row gap-4">


                            <a
                                href="workspace-details.php?id=<?= $workspace['id'] ?>"
                                class="w-full sm:w-1/3 py-3.5 rounded-xl border border-slate-200 text-slate-700 text-center font-medium hover:bg-slate-50 transition">

                                Cancel

                            </a>


                            <button
                                type="submit"
                                class="w-full sm:flex-1 py-3.5 rounded-xl bg-slate-900 text-white font-medium hover:bg-blue-600 transition">

                                Confirm Booking

                            </button>


                        </div>


                    </form>

                </div>


            </div>

        </section>


    </main>

</div>


<!-- Booking JavaScript -->

<script>
document.addEventListener("DOMContentLoaded", function () {

    const fullDay = document.getElementById("full_day");
    const startGroup = document.getElementById("startTimeGroup");
    const endGroup = document.getElementById("endTimeGroup");

    const startTime = document.getElementById("start_time");
    const endTime = document.getElementById("end_time");

    function toggleFullDay() {

        if (fullDay.checked) {

            // Hide time fields
            startGroup.style.display = "none";
            endGroup.style.display = "none";

            // Disable time inputs
            startTime.disabled = true;
            endTime.disabled = true;

            // Remove required
            startTime.removeAttribute("required");
            endTime.removeAttribute("required");

        } else {

            // Show time fields
            startGroup.style.display = "block";
            endGroup.style.display = "block";

            // Enable time inputs
            startTime.disabled = false;
            endTime.disabled = false;

            // Required
            startTime.setAttribute("required", "required");
            endTime.setAttribute("required", "required");
        }
    }

    fullDay.addEventListener("change", toggleFullDay);

    // Run once when page loads
    toggleFullDay();

});
</script>


</body>

</html>