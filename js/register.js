// =========================
// PASSWORD TOGGLE
// =========================

function togglePassword(){

    let password = document.getElementById("password");
    let icon = document.querySelector(".password-toggle");

    if(password.type === "password"){

        password.type = "text";

        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");

    }else{

        password.type = "password";

        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");

    }

}

// =========================
// GET ELEMENTS
// =========================

const password = document.getElementById("password");

const form = document.getElementById("registerForm");

// =========================
// LIVE VALIDATION
// =========================

if(password){

password.addEventListener("keyup", function(){

    let value = password.value;

    let score = 0;

    checkRule("rule-length", value.length >= 8);

    checkRule("rule-upper", /[A-Z]/.test(value));

    checkRule("rule-lower", /[a-z]/.test(value));

    checkRule("rule-number", /[0-9]/.test(value));

    checkRule("rule-special", /[!@#$%^&*(),.?":{}|<>]/.test(value));

    if(value.length >= 8) score++;

    if(/[A-Z]/.test(value)) score++;

    if(/[a-z]/.test(value)) score++;

    if(/[0-9]/.test(value)) score++;

    if(/[!@#$%^&*(),.?":{}|<>]/.test(value)) score++;

    updateStrength(score);

});

}

// =========================
// RULE CHECK
// =========================

function checkRule(id, valid){

    const rule = document.getElementById(id);

    if(!rule) return;

    const icon = rule.querySelector("i");

    if(valid){

        rule.classList.remove("invalid");
        rule.classList.add("valid");

        icon.className = "fa-solid fa-circle-check";

    }else{

        rule.classList.remove("valid");
        rule.classList.add("invalid");

        icon.className = "fa-solid fa-circle-xmark";

    }

}

// =========================
// STRENGTH BAR
// =========================

function updateStrength(score){

    const fill = document.getElementById("strengthFill");

    const text = document.getElementById("strengthText");

    if(!fill || !text) return;

    switch(score){

        case 0:
        case 1:

            fill.style.width="20%";
            fill.style.background="#ff3b30";
            text.innerHTML="Weak";
            text.style.color="#ff3b30";

        break;

        case 2:
        case 3:

            fill.style.width="60%";
            fill.style.background="#ff9800";
            text.innerHTML="Medium";
            text.style.color="#ff9800";

        break;

        case 4:

            fill.style.width="80%";
            fill.style.background="#ffd600";
            text.innerHTML="Good";
            text.style.color="#ffd600";

        break;

        case 5:

            fill.style.width="100%";
            fill.style.background="#18b566";
            text.innerHTML="Strong";
            text.style.color="#18b566";

        break;

    }

}

// =========================
// FORM VALIDATION
// =========================

if(form){

form.addEventListener("submit", function(e){

    let value = password.value;

    let pattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&.#])[A-Za-z\d@$!%*?&.#]{8,}$/;

    if(!pattern.test(value)){

        alert("Password must contain:\n\n• At least 8 characters\n• One uppercase letter\n• One lowercase letter\n• One number\n• One special character");

        e.preventDefault();

    }

});

}