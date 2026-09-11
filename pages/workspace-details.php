<?php
require_once "../config/auth.php";
require_once "../config/database.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid workspace ID.");
}

$id = (int) $_GET['id'];

$sql = "SELECT * FROM workspaces WHERE id = ?";
$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $id);

$stmt->execute();

$result = $stmt->get_result();

$workspace = $result->fetch_assoc();

$stmt->close();
$conn->close();

if (!$workspace) {
    die("Workspace not found.");
}

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?= htmlspecialchars($workspace['workspace_name']) ?> | WorkspaceHub
    </title>

    <link rel="stylesheet" href="../assets/css/output.css">

</head>


<body class="bg-slate-50 text-slate-900">


<div class="min-h-screen p-6 lg:p-10">


    <div class="max-w-4xl mx-auto">


        <!-- Back -->

        <a href="workspaces.php"
           class="inline-flex items-center text-sm text-slate-500 hover:text-blue-600 mb-8">

            ← Back to Workspaces

        </a>


        <!-- Workspace Card -->

        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">


            <!-- Top Section -->

            <div class="p-8">


                <div class="flex items-center justify-between">


                    <!-- Type -->

                    <span class="text-sm font-semibold text-blue-600">

                        <?= htmlspecialchars($workspace['type']) ?>

                    </span>


                    <!-- Status -->

                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-emerald-50 text-emerald-600">

                        <?= htmlspecialchars($workspace['status']) ?>

                    </span>


                </div>


                <!-- Workspace Name -->

                <h1 class="text-3xl font-bold mt-6">

                    <?= htmlspecialchars($workspace['workspace_name']) ?>

                </h1>


                <!-- Company -->

                <p class="text-slate-500 mt-2">

                    <?= htmlspecialchars($workspace['company_name']) ?>

                </p>


            </div>


            <!-- Details -->

            <div class="border-t border-slate-100 p-8">


                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">


                    <!-- Time -->

                    <div class="bg-slate-50 rounded-xl p-5">


                        <p class="text-xs uppercase tracking-wide text-slate-400">

                            Available Time

                        </p>


                        <p class="text-lg font-semibold mt-2">

                            <?= htmlspecialchars($workspace['time_slot']) ?>

                        </p>


                    </div>


                    <!-- Workspace Type -->

                    <div class="bg-slate-50 rounded-xl p-5">


                        <p class="text-xs uppercase tracking-wide text-slate-400">

                            Workspace Type

                        </p>


                        <p class="text-lg font-semibold mt-2">

                            <?= htmlspecialchars($workspace['type']) ?>

                        </p>


                    </div>


                </div>


                <!-- Booking Button -->

                <a
                 href="book-workspace.php?id=<?= $workspace['id'] ?>"
                 class="block text-center w-full mt-8 py-4 rounded-xl bg-slate-900 text-white font-medium hover:bg-blue-600 transition">

                  Book This Workspace

                   </a>


            </div>


        </div>


    </div>


</div>


</body>

</html>