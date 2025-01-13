const sign_in_btn = document.querySelector("#sign-in-btn");
const sign_up_btn = document.querySelector("#sign-up-btn");
const container = document.querySelector(".container");

sign_up_btn.addEventListener("click", () => {
  container.classList.add("sign-up-mode");
});

sign_in_btn.addEventListener("click", () => {
  container.classList.remove("sign-up-mode");
});

// Inside the submit event listener for the sign-up form
signUpForm.addEventListener('submit', function(event) {
    // Your existing validation code here...

    // If all validations pass
    if (allValidationsPass) {
        // Display success message
        const successMessage = document.querySelector('.success-message');
        successMessage.style.display = 'block';

        // Optional: You can reset the form here if needed
        signUpForm.reset();
    }
});
