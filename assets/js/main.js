document.addEventListener('DOMContentLoaded', function () {
    const toggleButtons = document.querySelectorAll('.toggle-pop-up');
    
    toggleButtons.forEach(function (button) {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const popup = document.querySelector('.contact-pop-up');
            const dataService = button.getAttribute('data-service');
            const inputField = document.getElementById('dataService');

            if (inputField && dataService) {
                inputField.value = dataService; // Вставляем значение в инпут
            }

            if (popup) {
                popup.classList.toggle('active');
            }
        });
    });
});


