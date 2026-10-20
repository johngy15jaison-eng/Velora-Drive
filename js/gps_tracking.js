
(function () {
    'use strict';

    const bookings = Array.isArray(window.gpsBookings)
        ? window.gpsBookings
        : [];

    const $ = (id) => document.getElementById(id);

    if (!$('vehicleList')) return;

    let map = null;
    let marker = null;
    let routeLine = null;
    let startMarker = null;
    let endMarker = null;
    let selectedIndex = 0;
    let lastSelectedIndex = -1;
    let animationStart = Date.now();

    $('vehicleCount').textContent = String(bookings.length);

    function safeText(value, fallback = 'Not specified') {
        if (value === undefined || value === null ||
            String(value).trim() === '') {
            return fallback;
        }

        return String(value);
    }

    function getBookingId(booking) {
        return booking.id ?? booking.booking_id ?? booking.bookingId;
    }

    function getVehicleName(booking) {
        return booking.vehicle_name ?? booking.vehicleName;
    }

    function getPickup(booking) {
        return booking.pickup_location ?? booking.pickupLocation;
    }

    function getDestination(booking) {
        return booking.return_location ??
            booking.returnLocation ??
            booking.destination;
    }

    // Interpret MySQL DATE and DATETIME strings as local dates.
    function parseBookingDate(value, isReturn = false) {
        if (!value) return null;

        const match = String(value).match(
            /^(\d{4})-(\d{2})-(\d{2})(?:[ T](\d{2}):(\d{2})(?::(\d{2}))?)?$/
        );

        if (match) {
            const year = Number(match[1]);
            const month = Number(match[2]) - 1;
            const day = Number(match[3]);
            const hasTime = match[4] !== undefined;

            if (!hasTime && isReturn) {
                return new Date(year, month, day, 23, 59, 59);
            }

            return new Date(
                year,
                month,
                day,
                Number(match[4] || 0),
                Number(match[5] || 0),
                Number(match[6] || 0)
            );
        }

        const parsed = new Date(value);
        return Number.isNaN(parsed.getTime()) ? null : parsed;
    }

    function formatDate(date) {
        return date ? date.toLocaleString() : 'Not specified';
    }

    // Approximate coordinates for illustrative Kerala routes.
    const places = {
        'pathanamthitta': [9.2648, 76.7870],
        'thiruvalla': [9.3816, 76.5748],
        'kochi': [9.9312, 76.2673],
        'ernakulam': [9.9816, 76.2999],
        'trivandrum': [8.5241, 76.9366],
        'thiruvananthapuram': [8.5241, 76.9366],
        'kollam': [8.8932, 76.6141],
        'alappuzha': [9.4981, 76.3388],
        'kottayam': [9.5916, 76.5222],
        'idukki': [9.8494, 76.9720],
        'thrissur': [10.5276, 76.2144],
        'palakkad': [10.7867, 76.6548],
        'malappuram': [11.0510, 76.0711],
        'kozhikode': [11.2588, 75.7804],
        'calicut': [11.2588, 75.7804],
        'wayanad': [11.6854, 76.1320],
        'kannur': [11.8745, 75.3704],
        'kasaragod': [12.4996, 74.9869]
    };

    function getCoordinates(location) {
        const key = String(location || '').trim().toLowerCase();

        if (places[key]) return places[key];

        for (const name of Object.keys(places)) {
            if (key.includes(name)) return places[name];
        }

        // Fallback for unrecognized locations.
        return [9.9312, 76.2673];
    }

    function getTripState(booking, now) {
        const start = parseBookingDate(
            booking.booking_date ?? booking.bookingDate
        );

        const end = parseBookingDate(
            booking.return_date ?? booking.returnDate,
            true
        );

        if (!start || !end) {
            return {
                start,
                end,
                status: 'Dates unavailable',
                active: false
            };
        }

        if (end <= start) {
            return {
                start,
                end,
                status: 'Invalid booking dates',
                active: false
            };
        }

        if (now < start) {
            return {
                start,
                end,
                status: 'Trip Not Started',
                active: false
            };
        }

        if (now >= end) {
            return {
                start,
                end,
                status: 'Trip Completed',
                active: false
            };
        }

        return {
            start,
            end,
            status: 'Trip Active',
            active: true
        };
    }

    function setText(id, value) {
        const element = $(id);
        if (element) element.textContent = value;
    }

    function initializeMap() {
        if (!window.L || !$('map')) return;

        map = L.map('map').setView([10.1, 76.3], 7);

        L.tileLayer(
            'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            {
                attribution: '&copy; OpenStreetMap contributors',
                maxZoom: 19
            }
        ).addTo(map);
    }

    function updateMap(booking, state) {
        if (!map) return;

        const pickup = getCoordinates(getPickup(booking));
        const destination = getCoordinates(getDestination(booking));

        // During an active trip, the marker travels back and forth
        // along the illustrative route every 60 seconds.
        // Trip status still depends on the actual booking dates.
        let progress = 0;

        if (state.status === 'Trip Completed') {
            progress = 1;
        } else if (state.active) {
            const elapsed = (Date.now() - animationStart) % 60000;
            const forwardProgress = elapsed / 60000;

            // Smooth back-and-forth movement, so the car doesn't teleport
            // from the destination back to the pickup point.
            progress = forwardProgress < 0.5
                ? forwardProgress * 2
                : 2 - forwardProgress * 2;
        }

        const lat = pickup[0] +
            (destination[0] - pickup[0]) * progress;
        const lng = pickup[1] +
            (destination[1] - pickup[1]) * progress;

        if (lastSelectedIndex !== selectedIndex) {
            if (marker) map.removeLayer(marker);
            if (routeLine) map.removeLayer(routeLine);
            if (startMarker) map.removeLayer(startMarker);
            if (endMarker) map.removeLayer(endMarker);

            routeLine = L.polyline(
                [pickup, destination],
                {
                    color: '#c8a43b',
                    weight: 4,
                    dashArray: '8 8'
                }
            ).addTo(map);

            startMarker = L.circleMarker(pickup, {
                radius: 6,
                color: '#218838',
                fillColor: '#218838',
                fillOpacity: 1
            }).addTo(map).bindPopup(
                'Pickup: ' + safeText(getPickup(booking))
            );

            endMarker = L.circleMarker(destination, {
                radius: 6,
                color: '#c0392b',
                fillColor: '#c0392b',
                fillOpacity: 1
            }).addTo(map).bindPopup(
                'Destination: ' + safeText(getDestination(booking))
            );

            marker = L.marker([lat, lng]).addTo(map);

            map.fitBounds(L.latLngBounds([pickup, destination]), {
                padding: [35, 35],
                maxZoom: 10
            });

            lastSelectedIndex = selectedIndex;
        }

        marker.setLatLng([lat, lng]);

        const bookingId = getBookingId(booking);

        marker.bindPopup(
            '<strong>' +
            safeText(getVehicleName(booking), 'Vehicle') +
            '</strong><br>Booking ID: ' +
            (bookingId == null ? 'Unavailable' : '#' + bookingId) +
            '<br>' + state.status
        );

        setText('vehicleLatitude', lat.toFixed(5));
        setText('vehicleLongitude', lng.toFixed(5));

        if (state.active) {
            setText('vehicleSpeed', '35 km/h (simulated)');
        } else {
            setText('vehicleSpeed', '0 km/h (simulated)');
        }

        if (state.status === 'Trip Completed') {
            setText('tripProgress', '100%');
        } else if (state.status === 'Trip Not Started') {
            setText('tripProgress', '0%');
        } else if (state.active) {
            setText(
                'tripProgress',
                Math.round(progress * 100) + '% (simulation)'
            );
        } else {
            setText('tripProgress', '--');
        }
    }

    function updateSelectedBooking() {
        if (!bookings.length) return;

        const booking = bookings[selectedIndex];
        const now = new Date();
        const state = getTripState(booking, now);
        const bookingId = getBookingId(booking);

        setText(
            'selectedVehicle',
            safeText(getVehicleName(booking), 'Vehicle name unavailable')
        );

        setText(
            'selectedVehicleId',
            bookingId == null ? 'Unavailable' : '#' + bookingId
        );

        setText('vehiclePickup', safeText(getPickup(booking)));
        setText('vehicleDestination', safeText(getDestination(booking)));
        setText('bookingStart', formatDate(state.start));
        setText('bookingReturn', formatDate(state.end));
        setText('tripStatus', state.status);
        setText('lastUpdate', now.toLocaleTimeString());

        updateMap(booking, state);
    }

    function renderBookings() {
        const list = $('vehicleList');
        list.replaceChildren();

        if (!bookings.length) {
            const message = document.createElement('p');
            message.textContent = 'No confirmed bookings are available.';
            list.appendChild(message);

            setText('vehicleCount', '0');
            setText('selectedVehicle', 'No bookings');
            setText('selectedVehicleId', '--');
            setText('vehiclePickup', '--');
            setText('vehicleDestination', '--');
            setText('bookingStart', '--');
            setText('bookingReturn', '--');
            setText('vehicleSpeed', '--');
            setText('vehicleLatitude', '--');
            setText('vehicleLongitude', '--');
            setText('tripProgress', '--');
            setText('tripStatus', '--');
            setText('lastUpdate', '--');
            return;
        }

        bookings.forEach((booking, index) => {
            const card = document.createElement('button');
            card.type = 'button';
            card.className = 'booking-card';
            card.setAttribute(
                'aria-pressed',
                index === selectedIndex ? 'true' : 'false'
            );

            const title = document.createElement('strong');
            title.textContent = safeText(
                getVehicleName(booking),
                'Vehicle name unavailable'
            );

            const idLine = document.createElement('span');
            const bookingId = getBookingId(booking);
            idLine.textContent = bookingId == null
                ? 'Booking ID: Unavailable'
                : 'Booking ID: #' + bookingId;

            const pickup = document.createElement('span');
            pickup.textContent =
                'Pickup: ' + safeText(getPickup(booking));

            const destination = document.createElement('span');
            destination.textContent =
                'Destination: ' + safeText(getDestination(booking));

            card.append(title, idLine, pickup, destination);

            card.addEventListener('click', function () {
                selectedIndex = index;
                animationStart = Date.now();
                lastSelectedIndex = -1;

                list.querySelectorAll('.booking-card').forEach(
                    (otherCard, otherIndex) => {
                        otherCard.setAttribute(
                            'aria-pressed',
                            otherIndex === selectedIndex ? 'true' : 'false'
                        );
                    }
                );

                updateSelectedBooking();
            });

            list.appendChild(card);
        });

        updateSelectedBooking();
    }

    initializeMap();
    renderBookings();

    // Update the simulated vehicle location every second.
    window.setInterval(updateSelectedBooking, 1000);
})();
