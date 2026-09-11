<?php
require_once "../config/auth.php";
require_once "../config/database.php";

$sql = "SELECT * FROM workspaces";
$result = $conn->query($sql);

$workspaces = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $workspaces[] = $row;
    }
}

$conn->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Workspaces | WorkspaceHub</title>

    <link rel="stylesheet" href="../assets/css/output.css">

</head>

<body class="bg-slate-50 text-slate-900">

    <div class="min-h-screen p-6 lg:p-10">

        <div class="max-w-7xl mx-auto">

            <div class="mb-8">

                <p class="text-sm text-blue-600 font-medium mb-1">
                    WORKSPACE MANAGEMENT
                </p>

                <h1 class="text-3xl font-bold">
                    Workspaces
                </h1>

                <p class="text-slate-500 mt-2">
                    View all available meeting and conference spaces.
                </p>

            </div>


            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

                <?php foreach ($workspaces as $workspace): ?>

                    <div class="bg-white border border-slate-200 rounded-2xl p-6 hover:shadow-lg transition">

                        <div class="flex items-center justify-between">

                            <span class="text-sm font-semibold text-blue-600">
                                <?= htmlspecialchars($workspace['type']) ?>
                            </span>

                            <span class="px-3 py-1 text-xs font-medium rounded-full bg-emerald-50 text-emerald-600">
                                <?= htmlspecialchars($workspace['status']) ?>
                            </span>

                        </div>

                        <h2 class="text-xl font-semibold mt-5">
                            <?= htmlspecialchars($workspace['workspace_name']) ?>
                        </h2>

                        <p class="text-sm text-slate-500 mt-2">
                            <?= htmlspecialchars($workspace['company_name']) ?>
                        </p>

                        <div class="mt-6 pt-5 border-t border-slate-100">

                            <p class="text-xs uppercase tracking-wide text-slate-400">
                                Time Slot
                            </p>

                            <p class="font-medium mt-1">
                                <?= htmlspecialchars($workspace['time_slot']) ?>
                            </p>

                        </div>
                             <a
                               href="workspace-details.php?id=<?= $workspace['id'] ?>"
                               class="block text-center w-full mt-6 py-3 rounded-xl bg-slate-900 text-white text-sm font-medium hover:bg-blue-600 transition">
                                  View Workspace
                            </a>

                        
                        

                    </div>

                <?php endforeach; ?>

            </div>

        </div>

    </div>

</body>

</html>