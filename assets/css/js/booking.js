document.addEventListener("DOMContentLoaded", function () {

    const form = document.getElementById("bookingForm");
    const bookingDate = document.getElementById("booking_date");
    const startTime = document.getElementById("start_time");
    const endTime = document.getElementById("end_time");
    const fullDay = document.getElementById("full_day");

    const today = new Date().toISOString().split("T")[0];

    // Prevent past dates
    bookingDate.setAttribute("min", today);


    form.addEventListener("submit", function (event) {

        // Date validation
        if (bookingDate.value < today) {

            event.preventDefault();

            alert("Please select today or a future date.");

            return;
        }


        // Time validation only for normal booking
        if (!fullDay.checked) {

            if (!startTime.value || !endTime.value) {

                event.preventDefault();

                alert("Please select start and end time.");

                return;
            }


            if (startTime.value >= endTime.value) {

                event.preventDefault();

                alert("End time must be later than start time.");

                return;
            }
        }

    });

});