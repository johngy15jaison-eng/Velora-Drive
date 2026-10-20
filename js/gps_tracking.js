
(function () {
    'use strict';

    const bookings = Array.isArray(window.gpsBookings)
        ? window.gpsBookings
        : [];

    const $ = (id) => document.getElementById(id);

    const fields = [
        'vehicleCount', 'selectedVehicle', 'selectedVehicleId',
        'vehiclePickup', 'vehicleDestination', 'bookingStart',
        'bookingReturn', 'vehicleSpeed', 'vehicleLatitude',
        'vehicleLongitude', 'tripProgress', 'tripStatus', 'lastUpdate'
    ];

    // Stop cleanly if the page does not contain the tracking panel.
    if (!$('vehicleList')) return;

    $('vehicleCount').textContent = String(bookings.length);

    function text(value, fallback = 'Not specified') {
        if (value === null || value === undefined ||
            String(value).trim() === '') {
            return fallback;
        }
        return String(value);
    }

    function dateValue(value, isReturn = false) {
        if (!value) return null;

        // Parse database DATE or DATETIME values as local time.
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
                year, month, day,
                Number(match[4] || 0),
                Number(match[5] || 0),
                Number(match[6] || 0)
            );
        }

        const parsed = new Date(value);
        return Number.isNaN(parsed.getTime()) ? null : parsed;
    }

    function formatDate(value) {
        if (!value) return 'Not specified';
        return value.toLocaleString();
    }

    // Approximate Kerala locations for the illustrative map.
    // Unknown place names use a Kerala fallback, not geocoded GPS data.
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

    function locationPoint(location) {
        const key = String(location || '').trim().toLowerCase();
        if (places[key]) return places[key];

        for (const name of Object.keys(places)) {
            if (key.includes(name)) return places[name];
        }

        return [9.9312, 76.2673];
    }

    let map = null;
    let marker = null;
    let routeLine = null;
    let selectedIndex = 0;
    let selectedRoute = null;

    if (window.L && $('map')) {
        map = L.map('map').setView([10.2, 76.2], 7);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);
    }

    function set(id, value) {
        const element = $(id);
        if (element) element.textContent = value;
    }

    function getBookingStatus(booking, now) {
        const start = dateValue(booking.booking_date ?? booking.bookingDate);
        const end = dateValue(
            booking.return_date ?? booking.returnDate,
            true
        );

        if (!start || !end) {
            return {
                start, end, progress: 0, speed: 0,
                status: 'Dates unavailable'
            };
        }

        if (end <= start) {
            return {
                start, end, progress: 0, speed: 0,
                status: 'Invalid booking dates'
            };
        }

        if (now < start) {
            return {
                start, end, progress: 0, speed: 0,
                status: 'Trip Not Started'
            };
        }

        if (now >= end) {
            return {
                start, end, progress: 100, speed: 0,
                status: 'Trip Completed'
            };
        }

        const progress = Math.max(0, Math.min(
            100, ((now - start) / (end - start)) * 100
        ));

        return {
            start, end, progress, speed: 35,
            status: 'Trip Active'
        };
    }

    function updateSelectedBooking() {
        if (!bookings.length) return;

        const booking = bookings[selectedIndex];
        const now = new Date();

        const id = booking.id ?? booking.booking_id ?? booking.bookingId;
        const name = booking.vehicle_name ?? booking.vehicleName;
        const pickup = booking.pickup_location ?? booking.pickupLocation;
        const destination = booking.return_location ??
            booking.returnLocation ?? booking.destination;

        const state = getBookingStatus(booking, now);

        set('selectedVehicle', text(name, 'Vehicle name unavailable'));
        set('selectedVehicleId', id === undefined || id === null
            ? 'Unavailable' : '#' + id);
        set('vehiclePickup', text(pickup));
        set('vehicleDestination', text(destination));
        set('bookingStart', formatDate(state.start));
        set('bookingReturn', formatDate(state.end));
        set('vehicleSpeed', state.speed + ' km/h');
        set('tripProgress', state.progress.toFixed(1) + '%');
        set('tripStatus', state.status);
        set('lastUpdate', now.toLocaleTimeString());

        if (!map) return;

        const startPoint = locationPoint(pickup);
        const endPoint = locationPoint(destination);

        // Illustrative straight line between approximate town locations.
        const fraction = state.progress / 100;
        const lat = startPoint[0] +
            (endPoint[0] - startPoint[0]) * fraction;
        const lng = startPoint[1] +
            (endPoint[1] - startPoint[1]) * fraction;

        set('vehicleLatitude', lat.toFixed(5));
        set('vehicleLongitude', lng.toFixed(5));

        if (!selectedRoute) {
            selectedRoute = [startPoint, endPoint];
            routeLine = L.polyline(selectedRoute, {
                color: '#c8a43b',
                weight: 4,
                dashArray: '8 8'
            }).addTo(map);
        } else {
            selectedRoute = [startPoint, endPoint];
            routeLine.setLatLngs(selectedRoute);
        }

        if (!marker) {
            marker = L.marker([lat, lng]).addTo(map);
        } else {
            marker.setLatLng([lat, lng]);
        }

        marker.bindPopup(
            '<strong>' + text(name, 'Vehicle') + '</strong><br>' +
            'Booking ID: ' + (id == null ? 'Unavailable' : '#' + id) +
            '<br>' + state.status
        );

        map.fitBounds(L.latLngBounds(selectedRoute), {
            padding: [30, 30],
            maxZoom: 10
        });
    }

    function renderBookings() {
        const list = $('vehicleList');
        list.replaceChildren();

        if (!bookings.length) {
            const message = document.createElement('p');
            message.textContent = 'No confirmed bookings are available.';
            list.appendChild(message);
            fields.forEach(id => {
                if (id !== 'vehicleCount') set(id, '--');
            });
            set('vehicleCount', '0');
            return;
        }

        bookings.forEach((booking, index) => {
            const card = document.createElement('button');
            card.type = 'button';
            card.className = 'booking-card';
            card.setAttribute('aria-pressed', index === selectedIndex
                ? 'true' : 'false');

            const title = document.createElement('strong');
            title.textContent = text(
                booking.vehicle_name ?? booking.vehicleName,
                'Vehicle name unavailable'
            );

            const id = booking.id ?? booking.booking_id ?? booking.bookingId;
            const idLine = document.createElement('span');
            idLine.textContent = id == null
                ? 'Booking ID: Unavailable'
                : 'Booking ID: #' + id;

            const pickup = document.createElement('span');
            pickup.textContent = 'Pickup: ' + text(
                booking.pickup_location ?? booking.pickupLocation
            );

            const destination = document.createElement('span');
            destination.textContent = 'Destination: ' + text(
                booking.return_location ?? booking.returnLocation ??
                booking.destination
            );

            card.append(title, idLine, pickup, destination);

            card.addEventListener('click', function () {
                selectedIndex = index;
                selectedRoute = null;

                if (routeLine && map) {
                    map.removeLayer(routeLine);
                    routeLine = null;
                }

                card.parentElement.querySelectorAll('.booking-card')
                    .forEach((other, i) => {
                        other.setAttribute('aria-pressed',
                            i === selectedIndex ? 'true' : 'false');
                    });

                updateSelectedBooking();
            });

            list.appendChild(card);
        });

        updateSelectedBooking();
    }

    renderBookings();

    // Update progress based on the booking dates, not an endlessly
    // looping animation.
    window.setInterval(updateSelectedBooking, 5000);
})();
