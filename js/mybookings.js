// Search Bookings

const searchBooking = document.getElementById("searchBooking");

searchBooking.addEventListener("keyup", function () {

    const value = this.value.toLowerCase();

    const cards = document.querySelectorAll(".booking-card");

    cards.forEach(card => {

        if (card.innerText.toLowerCase().includes(value)) {

            card.style.display = "flex";

        } else {

            card.style.display = "none";

        }

    });

});


// Filter Bookings

const statusFilter = document.getElementById("statusFilter");

statusFilter.addEventListener("change", function () {

    const selected = this.value.toLowerCase();

    const cards = document.querySelectorAll(".booking-card");

    cards.forEach(card => {

        const status = card.querySelector(".status");

        if (!status) return;

        const current = status.innerText.toLowerCase();

        if (selected === "all") {

            card.style.display = "flex";

        }

        else if (current === selected) {

            card.style.display = "flex";

        }

        else {

            card.style.display = "none";

        }

    });

});