document.addEventListener('DOMContentLoaded', function() {
    console.log('JavaScript loaded - starting attendance system');
    
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
});