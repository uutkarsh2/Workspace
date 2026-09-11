<?php

require_once "../config/auth.php";

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Settings | WorkspaceHub</title>

    <link rel="stylesheet" href="../assets/css/output.css">

</head>

<body class="bg-slate-50 text-slate-900">

    <div class="min-h-screen p-6 lg:p-10">

        <div class="max-w-4xl mx-auto">

            <!-- Header -->

            <div class="mb-8">

                <p class="text-sm text-blue-600 font-medium mb-1">
                    SYSTEM SETTINGS
                </p>

                <h1 class="text-3xl font-bold">
                    Settings
                </h1>

                <p class="text-slate-500 mt-2">
                    Manage your workspace management preferences.
                </p>

            </div>


            <!-- Profile Settings -->

            <div class="bg-white border border-slate-200 rounded-2xl p-6 mb-6">

                <h2 class="text-lg font-semibold">
                    Profile
                </h2>

                <p class="text-sm text-slate-500 mt-1">
                    Manage your basic profile information.
                </p>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-6">

                    <div>

                        <label class="block text-sm font-medium mb-2">
                            Name
                        </label>

                        <input
                            type="text"
                            placeholder="Enter your name"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-blue-500"
                        >

                    </div>


                    <div>

                        <label class="block text-sm font-medium mb-2">
                            Email
                        </label>

                        <input
                            type="email"
                            placeholder="Enter your email"
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-blue-500"
                        >

                    </div>

                </div>


                <button
                    class="mt-6 px-6 py-3 bg-slate-900 text-white rounded-xl text-sm font-medium hover:bg-blue-600 transition">
                    Save Changes
                </button>

            </div>


            <!-- Application Settings -->

            <div class="bg-white border border-slate-200 rounded-2xl p-6">

                <h2 class="text-lg font-semibold">
                    Application Preferences
                </h2>

                <p class="text-sm text-slate-500 mt-1">
                    Manage your application preferences.
                </p>


                <div class="mt-6 space-y-5">

                    <div class="flex items-center justify-between">

                        <div>

                            <p class="font-medium">
                                Email Notifications
                            </p>

                            <p class="text-sm text-slate-500">
                                Receive updates about your meetings.
                            </p>

                        </div>

                        <input
                            type="checkbox"
                            class="w-5 h-5 accent-blue-600"
                            checked
                        >

                    </div>


                    <div class="flex items-center justify-between">

                        <div>

                            <p class="font-medium">
                                Booking Reminders
                            </p>

                            <p class="text-sm text-slate-500">
                                Get reminders before scheduled meetings.
                            </p>

                        </div>

                        <input
                            type="checkbox"
                            class="w-5 h-5 accent-blue-600"
                            checked
                        >

                    </div>

                </div>

            </div>

        </div>

    </div>

</body>

</html>