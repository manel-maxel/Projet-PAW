document.addEventListener('DOMContentLoaded', function() {
    console.log('JavaScript loaded - starting attendance system');
    
    // Initialize attendance system
    initializeAttendanceSystem();
    
    // Initialize form validation
    initializeFormValidation();
});

function initializeAttendanceSystem() {
    const TOTAL_SESSIONS = 6;

    function updateRow(row) {
        console.log('Updating row:', row);
        
        const presentCheckboxes = row.querySelectorAll('input.present');
        const participateCheckboxes = row.querySelectorAll('input.participate');
        
        console.log('Found present checkboxes:', presentCheckboxes.length);
        console.log('Found participate checkboxes:', participateCheckboxes.length);

        let presentCount = 0;
        let participateCount = 0;
        
        presentCheckboxes.forEach((checkbox, index) => {
            if (checkbox.checked) {
                presentCount++;
                
                const participateCheckbox = participateCheckboxes[index];
                participateCheckbox.disabled = false;
                
                if (participateCheckbox.checked) {
                    participateCount++;
                }
            } else {
                const participateCheckbox = participateCheckboxes[index];
                participateCheckbox.disabled = true;
                participateCheckbox.checked = false;
            }
        });

        const absences = TOTAL_SESSIONS - presentCount;
        
        console.log('Present:', presentCount, 'Participate:', participateCount, 'Absences:', absences);

        const absencesCell = row.querySelector('.absences');
        const participationCell = row.querySelector('.participation');
        const messageCell = row.querySelector('.message');

        if (absencesCell) absencesCell.textContent = absences;
        if (participationCell) participationCell.textContent = participateCount;

        let message = '';
        if (absences >= 5) {
            message = 'Excluded – too many absences';
        } else if (absences >= 3) {
            message = 'Warning – attendance low';
        } else {
            message = 'Good attendance';
        }

        if (participateCount <= 1 && presentCount > 0) {
            message += ' – Participate more';
        } else if (participateCount >= 4) {
            message += ' – Excellent participation';
        }

        if (messageCell) messageCell.textContent = message;

        row.classList.remove('status-good', 'status-warn', 'status-bad');
        if (absences < 3) {
            row.classList.add('status-good');
        } else if (absences <= 4) {
            row.classList.add('status-warn');
        } else {
            row.classList.add('status-bad');
        }
    }

    function updateAllRows() {
        console.log('Updating all rows...');
        
        const allRows = document.querySelectorAll('#attendance-list table tr');
        const dataRows = [];
        
        for (let i = 2; i < allRows.length; i++) {
            dataRows.push(allRows[i]);
        }
        
        console.log('Found data rows:', dataRows.length);
        
        dataRows.forEach((row, index) => {
            console.log('Processing row', index);
            updateRow(row);
        });
    }

    function setupEventListeners() {
        console.log('Setting up event listeners...');
        
        const presentCheckboxes = document.querySelectorAll('input.present');
        const participateCheckboxes = document.querySelectorAll('input.participate');
        
        console.log('Present checkboxes found:', presentCheckboxes.length);
        console.log('Participate checkboxes found:', participateCheckboxes.length);

        presentCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const row = this.closest('tr');
                const checkboxIndex = Array.from(row.querySelectorAll('input.present')).indexOf(this);
                const participateCheckbox = row.querySelectorAll('input.participate')[checkboxIndex];
                
                if (!this.checked) {
                    participateCheckbox.disabled = true;
                    participateCheckbox.checked = false;
                } else {
                    participateCheckbox.disabled = false;
                }
                
                updateAllRows();
            });
        });

        participateCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateAllRows);
        });
    }

    setupEventListeners();
    updateAllRows();
    
    console.log('Attendance system initialized successfully');
}

function initializeFormValidation() {
    const form = document.getElementById('addStudentForm');
    
    if (!form) {
        console.log('Form not found - skipping validation');
        return;
    }
    
    console.log('Initializing form validation');
    
    const studentIdInput = document.getElementById('Student');
    const lastNameInput = document.getElementById('LastName');
    const firstNameInput = document.getElementById('FirstName');
    const emailInput = document.getElementById('Email');

    if (!studentIdInput || !lastNameInput || !firstNameInput || !emailInput) {
        console.log('Form inputs not found - skipping validation');
        return;
    }

    // Real-time validation as user types
    studentIdInput.addEventListener('input', validateStudentId);
    lastNameInput.addEventListener('input', validateLastName);
    firstNameInput.addEventListener('input', validateFirstName);
    emailInput.addEventListener('input', validateEmail);

    // Form submission handler
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        
        console.log('Form submission attempted');
        
        // Validate all fields
        const isStudentIdValid = validateStudentId();
        const isLastNameValid = validateLastName();
        const isFirstNameValid = validateFirstName();
        const isEmailValid = validateEmail();

        console.log('Validation results:', {
            studentId: isStudentIdValid,
            lastName: isLastNameValid,
            firstName: isFirstNameValid,
            email: isEmailValid
        });

        // If all valid, submit the form
        if (isStudentIdValid && isLastNameValid && isFirstNameValid && isEmailValid) {
            alert('Student added successfully!');
            form.reset();
            clearAllValidations();
        } else {
            alert('Please fix the validation errors before submitting.');
        }
    });

    // Blur validation (when user leaves field)
    studentIdInput.addEventListener('blur', validateStudentId);
    lastNameInput.addEventListener('blur', validateLastName);
    firstNameInput.addEventListener('blur', validateFirstName);
    emailInput.addEventListener('blur', validateEmail);
    
    console.log('Form validation initialized successfully');
}

function validateStudentId() {
    const studentId = document.getElementById('Student').value.trim();
    const errorElement = document.getElementById('studentIdError');
    const inputElement = document.getElementById('Student');
    
    if (!errorElement || !inputElement) {
        console.log('Student ID validation elements not found');
        return false;
    }
    
    clearError(inputElement, errorElement);
    
    // Validation rules
    if (studentId === '') {
        return showError(inputElement, errorElement, 'Student ID is required');
    }
    
    if (!/^\d+$/.test(studentId)) {
        return showError(inputElement, errorElement, 'Student ID must contain only numbers');
    }
    
    if (studentId.length < 3) {
        return showError(inputElement, errorElement, 'Student ID must be at least 3 digits long');
    }
    
    showValid(inputElement);
    return true;
}

function validateLastName() {
    const lastName = document.getElementById('LastName').value.trim();
    const errorElement = document.getElementById('lastNameError');
    const inputElement = document.getElementById('LastName');
    
    if (!errorElement || !inputElement) return false;
    
    clearError(inputElement, errorElement);
    
    if (lastName === '') {
        return showError(inputElement, errorElement, 'Last Name is required');
    }
    
    if (!/^[A-Za-zÀ-ÿ\s\-']+$/.test(lastName)) {
        return showError(inputElement, errorElement, 'Last Name can only contain letters, spaces, hyphens, and apostrophes');
    }
    
    if (lastName.length < 2) {
        return showError(inputElement, errorElement, 'Last Name must be at least 2 characters long');
    }
    
    showValid(inputElement);
    return true;
}

function validateFirstName() {
    const firstName = document.getElementById('FirstName').value.trim();
    const errorElement = document.getElementById('firstNameError');
    const inputElement = document.getElementById('FirstName');
    
    if (!errorElement || !inputElement) return false;
    
    clearError(inputElement, errorElement);
    
    if (firstName === '') {
        return showError(inputElement, errorElement, 'First Name is required');
    }
    
    if (!/^[A-Za-zÀ-ÿ\s\-']+$/.test(firstName)) {
        return showError(inputElement, errorElement, 'First Name can only contain letters, spaces, hyphens, and apostrophes');
    }
    
    if (firstName.length < 2) {
        return showError(inputElement, errorElement, 'First Name must be at least 2 characters long');
    }
    
    showValid(inputElement);
    return true;
}

function validateEmail() {
    const email = document.getElementById('Email').value.trim();
    const errorElement = document.getElementById('emailError');
    const inputElement = document.getElementById('Email');
    
    if (!errorElement || !inputElement) return false;
    
    clearError(inputElement, errorElement);
    
    if (email === '') {
        return showError(inputElement, errorElement, 'Email is required');
    }
    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        return showError(inputElement, errorElement, 'Please enter a valid email address (e.g., name@example.com)');
    }
    
    showValid(inputElement);
    return true;
}

// Helper functions
function showError(inputElement, errorElement, message) {
    inputElement.classList.add('invalid');
    inputElement.classList.remove('valid');
    errorElement.textContent = message;
    return false;
}

function showValid(inputElement) {
    inputElement.classList.remove('invalid');
    inputElement.classList.add('valid');
}

function clearError(inputElement, errorElement) {
    inputElement.classList.remove('invalid', 'valid');
    errorElement.textContent = '';
}

function clearAllValidations() {
    const inputs = document.querySelectorAll('#addStudentForm input');
    const errorMessages = document.querySelectorAll('#addStudentForm .error-message');
    
    inputs.forEach(input => {
        input.classList.remove('invalid', 'valid');
    });
    
    errorMessages.forEach(error => {
        error.textContent = '';
    });
}