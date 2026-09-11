<?php

require_once "../config/auth.php";

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Meetings | WorkspaceHub</title>

    <link rel="stylesheet" href="../assets/css/output.css">

</head>

<body class="bg-slate-50 text-slate-900">

    <div class="min-h-screen p-6 lg:p-10">

        <div class="max-w-7xl mx-auto">

            <!-- Header -->

            <div class="mb-8">

                <p class="text-sm text-blue-600 font-medium mb-1">
                    MEETING MANAGEMENT
                </p>

                <h1 class="text-3xl font-bold">
                    Meetings
                </h1>

                <p class="text-slate-500 mt-2">
                    Manage and track your scheduled meetings.
                </p>

            </div>


            <!-- Empty State -->

            <div class="bg-white border border-slate-200 rounded-2xl p-10 text-center">

                <div class="w-16 h-16 mx-auto bg-blue-50 rounded-full flex items-center justify-center text-2xl">
                    📅
                </div>

                <h2 class="text-xl font-semibold mt-5">
                    No meetings scheduled
                </h2>

                <p class="text-slate-500 mt-2 max-w-md mx-auto">
                    Your scheduled meetings will appear here once you book a workspace.
                </p>

                <a href="workspaces.php"
                   class="inline-block mt-6 px-6 py-3 rounded-xl bg-slate-900 text-white text-sm font-medium hover:bg-blue-600 transition">
                    Browse Workspaces
                </a>
                

            </div>

        </div>

    </div>

</body>

</html>