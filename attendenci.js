document.addEventListener('DOMContentLoaded', function() {
    console.log('JavaScript loaded - starting attendance system');

    initializeAttendanceSystem();
    
    initializeFormValidation();
});
function initializeAttendanceSystem() {
    const TOTAL_SESSIONS = 6;

    function updateRow(row) {
        console.log('Updating row:', row);
        
        const presentCheckboxes = row.querySelectorAll('input.present');
        const participateCheckboxes = row.querySelectorAll('input.participate');
        

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

    studentIdInput.addEventListener('input', validateStudentId);
    lastNameInput.addEventListener('input', validateLastName);
    firstNameInput.addEventListener('input', validateFirstName);
    emailInput.addEventListener('input', validateEmail);

   form.addEventListener('submit', function(event) {
    event.preventDefault();

    console.log('Form submission attempted');

    const isStudentIdValid = validateStudentId();
    const isLastNameValid = validateLastName();
    const isFirstNameValid = validateFirstName();
    const isEmailValid = validateEmail();

    if (isStudentIdValid && isLastNameValid && isFirstNameValid && isEmailValid) {
      
        const table = document.querySelector('#attendance-list table');
        if (!table) {
            alert('Attendance table not found.');
            return;
        }

        const newRow = document.createElement('tr');

        
        const lastName = document.getElementById('LastName').value.trim();
        const firstName = document.getElementById('FirstName').value.trim();

        newRow.innerHTML = `
            <td>${lastName}</td>
            <td>${firstName}</td>
            ${Array(6).fill('<td><input class="present" type="checkbox"></td><td><input class="participate" type="checkbox"></td>').join('')}
            <td class="absences">0</td>
            <td class="participation num">0</td>
            <td class="message small"></td>`;

        table.appendChild(newRow);

       
        initializeAttendanceSystem();

       
        alert('✅ Student added successfully!');

       
        form.reset();
        clearAllValidations();
    } else {
        alert('Please fix the validation errors before submitting.');
    }
});

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

const showReportBtn = document.getElementById('showReportBtn');
const reportCanvas = document.getElementById('reportChart');
let reportChart = null;

if (showReportBtn) {
    showReportBtn.addEventListener('click', () => {
        const rows = document.querySelectorAll('#attendance-list table tr');
        let totalStudents = 0;
        let presentCount = 0;
        let participateCount = 0;

        rows.forEach((row, index) => {
            
            if (index === 0) return;

            totalStudents++;

            
            const presentBoxes = row.querySelectorAll('input.present');
            const participateBoxes = row.querySelectorAll('input.participate');

            presentBoxes.forEach(box => {
                if (box.checked) presentCount++;
            });

            participateBoxes.forEach(box => {
                if (box.checked) participateCount++;
            });
        });


        console.log(`Total: ${totalStudents}, Present: ${presentCount}, Participate: ${participateCount}`);

        
        reportCanvas.style.display = 'block';

        const data = {
            labels: ['Total Students', 'Present', 'Participated'],
            datasets: [{
                label: 'Attendance Report',
                data: [totalStudents, presentCount, participateCount],
                backgroundColor: ['#007bff', '#28a745', '#ffc107']
            }]
        };

        const config = {
            type: 'bar',
            data: data,
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        };

        
        if (reportChart) {
            reportChart.destroy();
        }

        reportChart = new Chart(reportCanvas, config);
    });
}
$("#showReport").on("click", function() {
    
    let total = $("#attendanceTable tbody tr").length;
    let presentCount = 0, participatedCount = 0;
  
    $("#attendanceTable tbody tr").each(function() {
      let pres = 0, part = 0;
      $(this).find("input.present:checked").each(() => pres++);
      $(this).find("input.part:checked").each(() => part++);
      if (pres > 0) presentCount++;
      if (part > 0) participatedCount++;
      
    });

    $("#reportText").html(`
      <p><strong>Total Students:</strong> ${total}</p>
      <p><strong>Marked Present:</strong> ${presentCount}</p>
      <p><strong>Participated:</strong> ${participatedCount}</p> `);

    new Chart(document.getElementById("reportChart"), {
      type: "bar",
      data: {
        labels: ["Total", "Present", "Participated"],
        datasets: [{
          label: "Attendance Summary",
          data: [total, presentCount, participatedCount],
          backgroundColor: ["#ff99c8", "#b8f2e6", "#f6bd60"]
        }]
      },
      options: { responsive: true }
    });
  });
//  Exercise 5 
$(document).ready(function() {
   
    $('#attendance-list table tr').hover(
        function() {
            $(this).css('background-color', '#d1ecf1'); 
        },
        function() {
            $(this).css('background-color', ''); 
        }
    );

    $('#attendance-list table tr').on('click', function() {
        if ($(this).index() === 0) return;

        const lastName = $(this).find('td').eq(0).text();
        const firstName = $(this).find('td').eq(1).text();
        const absences = $(this).find('.absences').text();

        alert(`👩‍🎓 Student: ${firstName} ${lastName}\n❌ Absences: ${absences}`);
    });
});
//Exercise 6 
$(document).ready(function() {
    $('#highlightExcellentBtn').click(function() {
        $('#attendance-list table tr').each(function(index) {
            if (index === 0) return;

            const absences = parseInt($(this).find('.absences').text()) || 0;

            if (absences < 3) {
                $(this)
                    .css('background-color', '#d4edda') 
                    .fadeOut(300)
                    .fadeIn(300)
                    .animate({ opacity: 1.0 }, 500);
            }
        });
    });

    $('#resetColorsBtn').click(function() {
        $('#attendance-list table tr').each(function(index) {
            if (index === 0) return;
            $(this).css('background-color', ''); // Reset to original
        });
    });
});
//  Exercise 7 
$(document).ready(function() {
   
    $('#searchByName').on('input', function() {
        const searchTerm = $(this).val().toLowerCase().trim();
        
        if (searchTerm === '') {
          
            $('#attendance-list table tr').show();
            return;
        }
        
        $('#attendance-list table tr').each(function(index) {
           
            if (index === 0 || index === 1) return;
            
            const lastName = $(this).find('td').eq(0).text().toLowerCase();
            const firstName = $(this).find('td').eq(1).text().toLowerCase();
            
            if (lastName.includes(searchTerm) || firstName.includes(searchTerm)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
    
    $('#sortByAbsencesAsc').click(function() {
        const rows = $('#attendance-list table tr').slice(2).toArray(); // Get data rows only (skip headers)
        
        rows.sort(function(a, b) {
            const absencesA = parseInt($(a).find('.absences').text()) || 0;
            const absencesB = parseInt($(b).find('.absences').text()) || 0;
            
            return absencesA - absencesB; 
        });
       
        const tbody = $('#attendance-list table');
        rows.forEach(row => {
            tbody.append(row);
        });
        
        $('#sortMessage').text('Currently sorted by absences (ascending)').fadeIn();
        
        console.log('Sorted by absences (ascending)');
    });
    
    $('#sortByParticipationDesc').click(function() {
        const rows = $('#attendance-list table tr').slice(2).toArray(); 
       
        rows.sort(function(a, b) {
            const participationA = parseInt($(a).find('.participation').text()) || 0;
            const participationB = parseInt($(b).find('.participation').text()) || 0;
            
            return participationB - participationA;
        });
        
        const tbody = $('#attendance-list table');
        rows.forEach(row => {
            tbody.append(row);
        });
        
        $('#sortMessage').text('Currently sorted by participation (descending)').fadeIn();
        
        console.log('Sorted by participation (descending)');
    });
    
    $('#searchByName').on('input', function() {
        if ($(this).val().trim() !== '') {
            $('#sortMessage').fadeOut();
        }
    });
});

