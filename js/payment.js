// =============================
// PAYMENT METHOD SWITCHING
// =============================

const methods = document.querySelectorAll(".method");

const cardBox = document.getElementById("cardBox");
const upiBox = document.getElementById("upiBox");
const cashBox = document.getElementById("cashBox");

methods.forEach(method => {

    method.addEventListener("click", function(){

        // Remove active class
        methods.forEach(item=>{
            item.classList.remove("active");
        });

        // Add active class
        this.classList.add("active");

        // Select radio button
        this.querySelector("input").checked = true;

        let payment = this.querySelector("input").value;

        // Hide all sections
        cardBox.style.display = "none";
        upiBox.style.display = "none";
        cashBox.style.display = "none";

        // Show selected section
        if(payment=="Card"){

            cardBox.style.display="block";

        }

        if(payment=="UPI"){

            upiBox.style.display="block";

        }

        if(payment=="Cash"){

            cashBox.style.display="block";

        }

    });

});


// =============================
// CARD NUMBER FORMAT
// =============================

const cardInput=document.querySelector("input[name='card_number']");

if(cardInput){

cardInput.addEventListener("input",function(e){

let value=e.target.value.replace(/\D/g,"");

value=value.substring(0,16);

value=value.replace(/(.{4})/g,"$1 ").trim();

e.target.value=value;

});

}


// =============================
// EXPIRY DATE FORMAT
// =============================

const expiry=document.querySelector("input[name='expiry']");

if(expiry){

expiry.addEventListener("input",function(e){

let value=e.target.value.replace(/\D/g,"");

if(value.length>4){

value=value.substring(0,4);

}

if(value.length>2){

value=value.substring(0,2)+"/"+value.substring(2);

}

e.target.value=value;

});

}


// =============================
// CVV
// =============================

const cvv=document.querySelector("input[name='cvv']");

if(cvv){

cvv.addEventListener("input",function(e){

e.target.value=e.target.value.replace(/\D/g,"");

});

}


// =============================
// UPI VALIDATION
// =============================

const upi=document.querySelector("input[name='upi_id']");

if(upi){

upi.addEventListener("blur",function(){

if(this.value!=""){

const regex=/^[a-zA-Z0-9.\-_]{2,}@[a-zA-Z]{2,}$/;

if(!regex.test(this.value)){

alert("Please enter a valid UPI ID.");

this.focus();

}

}

});

}


// =============================
// PAYMENT BUTTON EFFECT
// =============================

const payBtn=document.querySelector(".pay-btn");

if(payBtn){

payBtn.addEventListener("mouseenter",()=>{

payBtn.style.transform="scale(1.02)";

});

payBtn.addEventListener("mouseleave",()=>{

payBtn.style.transform="scale(1)";

});

}


// =============================
// LOADING EFFECT
// =============================

const form=document.querySelector("form");

if(form){

form.addEventListener("submit",function(){

payBtn.innerHTML="Processing Payment...";

payBtn.disabled=true;

});

}