
"use strict";

document.addEventListener("DOMContentLoaded", function () {
    const mapElement = document.getElementById("map");
    const vehicleList = document.getElementById("vehicleList");

    if (typeof L === "undefined") {
        mapElement.textContent =
            "The map library could not load. Check your internet connection and refresh.";
        return;
    }

    // Booking records are loaded securely by gps_tracking.php.
    const bookings = Array.isArray(window.gpsBookings)
        ? window.gpsBookings
        : [];

    if (bookings.length === 0) {
        mapElement.textContent = "No eligible bookings are available to track.";
        return;
    }

    const map = L.map("map").setView([10.5, 76.2], 7);

    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution:
            '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap contributors</a>',
        maxZoom: 19
    }).addTo(map);

    // Illustrative Kerala routes for demonstration only.
    // These coordinates do not represent actual vehicle locations.
    const demoRoutes = [
        [
            [9.9312, 76.2673],
            [10.0261, 76.3125],
            [10.2200, 76.4000],
            [10.5276, 76.2144],
            [10.8505, 76.2711],
            [11.2588, 75.7804]
        ],
        [
            [8.5241, 76.9366],
            [8.6000, 76.9000],
            [8.8932, 76.6141],
            [9.2648, 76.7870],
            [9.4981, 76.3388],
            [9.9312, 76.2673]
        ],
        [
            [11.2588, 75.7804],
            [11.4500, 75.8500],
            [11.6854, 76.1310],
            [11.8745, 75.9970],
            [12.2958, 76.6394]
        ]
    ];

    const markerIcon = L.divIcon({
        className: "",
        html: '<div class="vehicle-marker">🚘</div>',
        iconSize: [34, 34],
        iconAnchor: [17, 17],
        popupAnchor: [0, -17]
    });

    const vehicles = bookings.map(function (booking, index) {
        return {
            id: String(booking.bookingId),
            name: String(booking.name || "Rental Vehicle"),
            pickup: String(booking.pickup || "Not specified"),
            destination: String(booking.destination || "Not specified"),
            bookingDate: String(booking.bookingDate || ""),
            returnDate: String(booking.returnDate || ""),
            tripStatus: String(booking.tripStatus || "Active"),
            route: demoRoutes[index % demoRoutes.length],
            progress: 0.05 + ((index % 5) * 0.12),
            speed: 25 + ((index * 7) % 30),
            marker: null,
            routeLine: null,
            card: null
        };
    });

    let selectedVehicle = vehicles[0];

    function getPosition(route, progress) {
        const segments = route.length - 1;
        const scaled = progress * segments;
        const index = Math.min(Math.floor(scaled), segments - 1);
        const fraction = scaled - index;

        const start = route[index];
        const end = route[index + 1];

        return [
            start[0] + (end[0] - start[0]) * fraction,
            start[1] + (end[1] - start[1]) * fraction
        ];
    }

    function getVehiclePosition(vehicle) {
        return getPosition(vehicle.route, vehicle.progress);
    }

    function renderVehicleDetails() {
        const vehicle = selectedVehicle;
        const position = getVehiclePosition(vehicle);

        document.getElementById("selectedVehicle").textContent =
            vehicle.name;

        document.getElementById("selectedVehicleId").textContent =
            "#" + vehicle.id;

        document.getElementById("vehicleRegion").textContent =
            vehicle.pickup;

        document.getElementById("vehicleSpeed").textContent =
            vehicle.destination;

        document.getElementById("vehicleLatitude").textContent =
            position[0].toFixed(5);

        document.getElementById("vehicleLongitude").textContent =
            position[1].toFixed(5);

        document.getElementById("tripProgress").textContent =
            Math.round(vehicle.progress * 100) + "%";

        document.getElementById("lastUpdate").textContent =
            new Date().toLocaleTimeString();

        document.getElementById("tripStatus").textContent =
            "SIMULATED TRIP";

        document.getElementById("selectionHint").textContent =
            "Booking #" + vehicle.id +
            " · " + vehicle.bookingDate +
            " · Simulated information only";

        vehicles.forEach(function (item) {
            if (item.card) {
                item.card.classList.toggle(
                    "selected",
                    item.id === selectedVehicle.id
                );
            }

            if (item.markerElement) {
                item.markerElement.classList.toggle(
                    "selected-marker",
                    item.id === selectedVehicle.id
                );
            }
        });
    }

    function updatePopup(vehicle) {
        const position = getVehiclePosition(vehicle);

        vehicle.marker.setPopupContent(
            "<strong>" + escapeHtml(vehicle.name) + "</strong><br>" +
            "Booking ID: #" + escapeHtml(vehicle.id) + "<br>" +
            "Pickup: " + escapeHtml(vehicle.pickup) + "<br>" +
            "Destination: " + escapeHtml(vehicle.destination) + "<br>" +
            "Simulated speed: " + vehicle.speed + " km/h<br>" +
            "Latitude: " + position[0].toFixed(5) + "<br>" +
            "Longitude: " + position[1].toFixed(5) + "<br>" +
            "Status: Simulated GPS"
        );
    }

    function escapeHtml(value) {
        return String(value).replace(/[&<>"']/g, function (character) {
            const entities = {
                "&": "&amp;",
                "<": "&lt;",
                ">": "&gt;",
                '"': "&quot;",
                "'": "&#39;"
            };

            return entities[character];
        });
    }

    function selectVehicle(vehicle, shouldZoom) {
        selectedVehicle = vehicle;
        renderVehicleDetails();

        if (shouldZoom) {
            map.flyTo(getVehiclePosition(vehicle), 10, {
                duration: 0.8
            });

            vehicle.marker.openPopup();
        }
    }

    vehicles.forEach(function (vehicle) {
        const initialPosition = getVehiclePosition(vehicle);

        vehicle.marker = L.marker(initialPosition, {
            icon: markerIcon,
            title: vehicle.name,
            keyboard: true
        }).addTo(map);

        vehicle.markerElement =
            vehicle.marker.getElement()?.querySelector(".vehicle-marker") || null;

        vehicle.routeLine = L.polyline(vehicle.route, {
            color: "#c8a43b",
            weight: 4,
            opacity: 0.75,
            dashArray: "7 7"
        }).addTo(map);

        vehicle.marker.on("click", function () {
            selectVehicle(vehicle, false);
        });

        updatePopup(vehicle);

        const card = document.createElement("button");
        card.type = "button";
        card.className = "vehicle-card";

        const top = document.createElement("div");
        top.className = "vehicle-card-top";

        const information = document.createElement("div");
        const title = document.createElement("h3");
        title.textContent = vehicle.name;

        const bookingId = document.createElement("p");
        bookingId.textContent = "Booking ID: #" + vehicle.id;

        information.append(title, bookingId);

        const tag = document.createElement("span");
        tag.className = "vehicle-tag";
        tag.textContent = "DEMO";

        top.append(information, tag);
        card.appendChild(top);

        const pickup = document.createElement("p");
        pickup.textContent = "Pickup: " + vehicle.pickup;

        const destination = document.createElement("p");
        destination.textContent = "Destination: " + vehicle.destination;

        const speed = document.createElement("p");
        speed.className = "vehicle-speed";
        speed.append("Simulated speed: ");

        const speedValue = document.createElement("span");
        speedValue.className = "card-speed";
        speedValue.textContent = vehicle.speed;

        speed.append(speedValue, " km/h");
        card.append(pickup, destination, speed);

        card.addEventListener("click", function () {
            selectVehicle(vehicle, true);
        });

        vehicle.card = card;
        vehicleList.appendChild(card);
    });

    document.getElementById("vehicleCount").textContent =
        vehicles.length;

    function moveVehicles() {
        vehicles.forEach(function (vehicle) {
            vehicle.progress += 0.003;

            if (vehicle.progress >= 1) {
                vehicle.progress = 0;
            }

            const change = Math.floor(Math.random() * 7) - 3;

            vehicle.speed = Math.max(
                15,
                Math.min(75, vehicle.speed + change)
            );

            vehicle.marker.setLatLng(getVehiclePosition(vehicle));
            updatePopup(vehicle);

            const speedElement =
                vehicle.card.querySelector(".card-speed");

            speedElement.textContent = vehicle.speed;
        });

        renderVehicleDetails();
    }

    document.getElementById("indiaBtn").addEventListener("click", function () {
        map.setView([22.5, 79.0], 5);
    });

    document.getElementById("keralaBtn").addEventListener("click", function () {
        map.setView([10.5, 76.2], 7);
    });

    selectVehicle(vehicles[0], false);
    moveVehicles();

    window.setInterval(moveVehicles, 1500);
});
