const emailInput = document.getElementById('Uname');
const emailValidation = document.getElementById('emailValidation');

const submitButton = document.querySelector('button[type="submit"]');

emailInput.addEventListener('input', function () {
    const email = emailInput.value;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (emailRegex.test(email)) {
        emailValidation.innerHTML = '&#10004;'; // check mark
        emailValidation.style.color = 'green';
    } else {
        emailValidation.innerHTML = '&#10060;'; // cross mark
        emailValidation.style.color = 'red';
    }
});


submitButton.addEventListener('click', function (event) {
    event.preventDefault(); // prevent form submission

    const email = emailInput.value;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!emailRegex.test(email)) {
        alert('Please enter a valid email!');
    } else {
        document.getElementById('signup').submit(); // submit the form
    }
});
