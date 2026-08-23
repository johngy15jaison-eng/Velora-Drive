// Vehicle Price Per Day
const pricePerDay = parseInt(
    document.querySelector("input[name='price']").value
);

// Elements
const pickup = document.getElementById("pickupDate");
const drop = document.getElementById("returnDate");

const extras = document.querySelectorAll(".extra");

const daysText = document.getElementById("days");
const extrasPrice = document.getElementById("extrasPrice");
const totalPrice = document.getElementById("totalPrice");

// Today's date
let today = new Date().toISOString().split("T")[0];

pickup.min = today;
drop.min = today;

// Calculate Price
function calculateBooking(){

    let days = 1;

    if(pickup.value && drop.value){

        let start = new Date(pickup.value);
        let end = new Date(drop.value);

        let diff = (end - start) / (1000*60*60*24);

        if(diff >= 1){

            days = diff;

        }

    }

    // Extras
    let extraTotal = 0;

    extras.forEach(extra=>{

        if(extra.checked){

            extraTotal += parseInt(extra.dataset.price);

        }

    });

    let subtotal = (pricePerDay * days) + extraTotal;

    let gst = Math.round(subtotal * 0.18);

    let total = subtotal + gst;

    daysText.innerHTML = days;

    extrasPrice.innerHTML = "₹" + extraTotal;

    totalPrice.innerHTML = "₹" + total;

}

// Events
pickup.addEventListener("change",function(){

    drop.min = pickup.value;

    calculateBooking();

});

drop.addEventListener("change",calculateBooking);

extras.forEach(item=>{

    item.addEventListener("change",calculateBooking);

});

// Initial
calculateBooking();