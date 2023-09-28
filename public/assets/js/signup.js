// document.addEventListener("DOMContentLoaded", function() {
//     const form = document.getElementById("signup-form");
//     const steps = Array.from(form.querySelectorAll(".step"));
//     let currentStep = 0;

//     const updateStep = () => {
//         steps.forEach((step, index) => {
//             step.style.display = index === currentStep ? "block" : "none";
//         });
//     };

//     const updateConfirmation = () => {
//         const firstname = document.getElementById("firstname").value;
//         const lastname = document.getElementById("lastname").value;
//         const email = document.getElementById("email").value;
//         document.getElementById("confirm-firstname").textContent = firstname;
//         document.getElementById("confirm-lastname").textContent = lastname;
//         document.getElementById("confirm-email").textContent = email;
//     };

//     form.addEventListener("submit", (e) => {
//         e.preventDefault();
//         // Handle form submission here
//         alert("Form submitted!");
//     });

//     form.addEventListener("click", (e) => {
//         if (e.target.classList.contains("next-step")) {
//             currentStep++;
//             if (currentStep >= steps.length) {
//                 currentStep = steps.length - 1;
//             }
//             updateStep();
//             if (currentStep === 2) {
//                 updateConfirmation();
//             }
//         } else if (e.target.classList.contains("prev-step")) {
//             currentStep--;
//             if (currentStep < 0) {
//                 currentStep = 0;
//             }
//             updateStep();
//         }
//     });

//     updateStep();
// });
