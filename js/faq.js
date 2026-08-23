// ==========================
// FAQ ACCORDION
// ==========================

const questions = document.querySelectorAll(".question");

questions.forEach(question => {

    question.addEventListener("click", () => {

        const answer = question.nextElementSibling;
        const icon = question.querySelector("i");

        // Close all other FAQs
        document.querySelectorAll(".answer").forEach(item => {

            if(item !== answer){

                item.style.display = "none";

            }

        });

        document.querySelectorAll(".question i").forEach(i => {

            if(i !== icon){

                i.classList.remove("fa-minus");
                i.classList.add("fa-plus");

            }

        });

        // Toggle current FAQ
        if(answer.style.display === "block"){

            answer.style.display = "none";

            icon.classList.remove("fa-minus");
            icon.classList.add("fa-plus");

        }else{

            answer.style.display = "block";

            icon.classList.remove("fa-plus");
            icon.classList.add("fa-minus");

        }

    });

});