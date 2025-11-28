// =============================================
// NAVIGATION FUNCTIONS
// =============================================

// This function shows the page the user selected.
// It hides all other pages and removes active styles from buttons.
function showPage(pageId) {
  // Remove "active" class from all pages
  document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));

  // Remove "active" class from all navigation buttons
  document.querySelectorAll('.nav-btn').forEach(b => b.classList.remove('active'));

  // Show the selected page
  document.getElementById(pageId).classList.add('active');

  // Highlight the clicked navigation button
  event.target.closest('.nav-btn').classList.add('active');
}

// =============================================
// ATTENDANCE CALCULATION FUNCTIONS
// =============================================

// Calculates absences and participation for each student
function calculateAttendance() {
  const rows = document.querySelectorAll("#attendanceTable tbody tr");
  
  rows.forEach(row => {
    const cells = row.querySelectorAll("td");
    const checkboxes = row.querySelectorAll("input");
    let absences = 0;
    let participations = 0;
    
    // We loop two inputs at a time: [present, participated]
    for (let j = 0; j < checkboxes.length; j += 2) {
      if (!checkboxes[j].checked) absences++;        // If "present" is not checked → absence
      if (checkboxes[j + 1]?.checked) participations++; // Count participation
    }
    
    // Update the table with calculated values
    cells[cells.length - 3].textContent = absences;
    cells[cells.length - 2].textContent = participations;
    
    // Reset previous status classes
    row.classList.remove("status-good", "status-warning", "status-bad");
    
    // Apply color depending on number of absences
    if (absences < 3) row.classList.add("status-good");
    else if (absences <= 4) row.classList.add("status-warning");
    else row.classList.add("status-bad");
    
    // Build feedback message
    let message = "";
    if (absences < 3 && participations >= 4) {
      message = "Good attendance – Excellent participation";
    } else if (absences < 3 && participations < 4) {
      message = "Good attendance – You need to participate more";
    } else if (absences >= 3 && absences <= 4 && participations >= 3) {
      message = "Warning – attendance low – Good participation";
    } else if (absences >= 3 && absences <= 4 && participations < 3) {
      message = "Warning – attendance low – You need to participate more";
    } else if (absences >= 5 && participations >= 3) {
      message = "Excluded – too many absences – Good participation";
    } else {
      message = "Excluded – too many absences – You need to participate more";
    }
    
    // Display message in the last column
    cells[cells.length - 1].textContent = message;
  });
  
  showToast("Attendance calculated successfully!", "success");
}

// =============================================
// FORM VALIDATION & STUDENT ADDITION
// =============================================

// Handles the submission of the "Add Student" form
document.getElementById('studentForm').addEventListener('submit', function(e) {
  e.preventDefault(); // Prevent page reload
  let valid = true;
  
  // Get user input values
  const id = document.getElementById('studentId').value.trim();
  const last = document.getElementById('lastName').value.trim();
  const first = document.getElementById('firstName').value.trim();
  const email = document.getElementById('email').value.trim();
  
  // Clear previous errors
  ['idError', 'lastError', 'firstError', 'emailError'].forEach(x => {
    document.getElementById(x).textContent = "";
  });
  document.getElementById('confirmation').style.display = 'none';

  // Validate Student ID
  if (!id) {
    document.getElementById('idError').innerHTML = '<i class="fa-solid fa-exclamation-circle"></i> Student ID is required';
    valid = false;
  } else if (!/^\d+$/.test(id)) {
    document.getElementById('idError').innerHTML = '<i class="fa-solid fa-exclamation-circle"></i> Student ID must contain only numbers';
    valid = false;
  }
  
  // Validate last name
  if (!last) {
    document.getElementById('lastError').innerHTML = '<i class="fa-solid fa-exclamation-circle"></i> Last Name is required';
    valid = false;
  } else if (!/^[A-Za-zÀ-ÿ\s\-']+$/.test(last)) {
    document.getElementById('lastError').innerHTML = '<i class="fa-solid fa-exclamation-circle"></i> Last Name must contain only letters';
    valid = false;
  }
  
  // Validate first name
  if (!first) {
    document.getElementById('firstError').innerHTML = '<i class="fa-solid fa-exclamation-circle"></i> First Name is required';
    valid = false;
  } else if (!/^[A-Za-zÀ-ÿ\s\-']+$/.test(first)) {
    document.getElementById('firstError').innerHTML = '<i class="fa-solid fa-exclamation-circle"></i> First Name must contain only letters';
    valid = false;
  }
  
  // Validate email
  if (!email) {
    document.getElementById('emailError').innerHTML = '<i class="fa-solid fa-exclamation-circle"></i> Email is required';
    valid = false;
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
    document.getElementById('emailError').innerHTML = '<i class="fa-solid fa-exclamation-circle"></i> Email is not valid';
    valid = false;
  }
  
  // Stop if there are validation errors
  if (!valid) return;

  // Add new student row to the table
  const tbody = document.querySelector('#attendanceTable tbody');
  const newRow = tbody.insertRow();
  newRow.classList.add("status-good");
  
  // First + last name columns
  let html = `<td>${last}</td><td>${first}</td>`;

  // Add 6 sessions → each session has 2 checkboxes
  for (let k = 0; k < 6; k++) {
    html += '<td><input type="checkbox"></td><td><input type="checkbox"></td>';
  }

  // Add default statistics
  html += '<td>0</td><td>0</td><td>New student - attendance not calculated yet</td>';
  newRow.innerHTML = html;
  
  // Show confirmation
  document.getElementById('confirmation').style.display = 'flex';
  this.reset(); // Clear form after submission
  
  // Automatically switch to attendance page
  setTimeout(() => {
    showPage('attendance');
    showToast(`Student ${first} ${last} added successfully!`, "success");
  }, 1500);
});

// =============================================
// REPORT GENERATION FUNCTIONS
// =============================================

// Generates attendance report and renders chart
function showReport() {
  const rows = document.querySelectorAll("#attendanceTable tbody tr");
  const totalStudents = rows.length;
  let presentStudents = 0;
  let participatedStudents = 0;
  
  // Count presence and participation
  rows.forEach(row => {
    const checkboxes = row.querySelectorAll("input");
    let hasPresent = false;
    let hasParticipated = false;
    
    // Check presence
    for (let j = 0; j < checkboxes.length; j += 2) {
      if (checkboxes[j].checked) hasPresent = true;
    }

    // Check participation
    for (let j = 1; j < checkboxes.length; j += 2) {
      if (checkboxes[j].checked) hasParticipated = true;
    }
    
    if (hasPresent) presentStudents++;
    if (hasParticipated) participatedStudents++;
  });
  
  // Update summary text
  document.getElementById("totalStudents").textContent = totalStudents;
  document.getElementById("studentsPresent").textContent = presentStudents;
  document.getElementById("studentsParticipated").textContent = participatedStudents;
  document.getElementById("reportSection").style.display = "block";

  // Prepare chart
  const ctx = document.getElementById("reportChart").getContext("2d");
  if (window.attendanceChart) window.attendanceChart.destroy();
  
  // Create bar chart
  window.attendanceChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Total Students', 'Present Students', 'Participated Students'],
      datasets: [{
        label: 'Number of Students',
        data: [totalStudents, presentStudents, participatedStudents],
        backgroundColor: ['#7c3aed', '#10b981', '#f59e0b'],
        borderWidth: 0,
        borderRadius: 8
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        title: {
          display: true,
          text: 'Attendance Statistics Overview',
          font: { size: 16, weight: 600 },
          color: '#1e293b'
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: { stepSize: 1 }
        }
      }
    }
  });
  
  showToast("Report generated successfully!", "success");
}

// =============================================
// SEARCH FUNCTIONALITY
// =============================================

// Filters rows while typing in search input
document.getElementById('searchInput').addEventListener('input', function() {
  const term = this.value.toLowerCase().trim();
  
  document.querySelectorAll('#attendanceTable tbody tr').forEach(row => {
    const cells = row.querySelectorAll('td');
    const lastName = cells[0]?.textContent.toLowerCase() || '';
    const firstName = cells[1]?.textContent.toLowerCase() || '';
    
    // Show rows that match the search
    const matches = lastName.includes(term) || firstName.includes(term);
    row.style.display = matches ? '' : 'none';
  });
});

// =============================================
// SORTING FUNCTIONS
// =============================================

// Sort table by number of absences
function sortByAbsences() {
  const tbody = document.querySelector('#attendanceTable tbody');
  const rows = Array.from(tbody.querySelectorAll('tr'));
  
  // Sort ascending
  rows.sort((a, b) => {
    const aVal = parseInt(a.querySelectorAll('td')[a.querySelectorAll('td').length - 3].textContent) || 0;
    const bVal = parseInt(b.querySelectorAll('td')[b.querySelectorAll('td').length - 3].textContent) || 0;
    return aVal - bVal;
  });
  
  rows.forEach(row => tbody.appendChild(row));
  document.getElementById('sortMessage').innerHTML = '<i class="fa-solid fa-arrow-up-short-wide"></i> Currently sorted by absences (ascending)';
  showToast("Table sorted by absences (ascending)", "success");
}

// Sort table by participation count
function sortByParticipation() {
  const tbody = document.querySelector('#attendanceTable tbody');
  const rows = Array.from(tbody.querySelectorAll('tr'));
  
  // Sort descending
  rows.sort((a, b) => {
    const aVal = parseInt(a.querySelectorAll('td')[a.querySelectorAll('td').length - 2].textContent) || 0;
    const bVal = parseInt(b.querySelectorAll('td')[b.querySelectorAll('td').length - 2].textContent) || 0;
    return bVal - aVal;
  });
  
  rows.forEach(row => tbody.appendChild(row));
  document.getElementById('sortMessage').innerHTML = '<i class="fa-solid fa-arrow-down-wide-short"></i> Currently sorted by participation (descending)';
  showToast("Table sorted by participation (descending)", "success");
}

// =============================================
// HIGHLIGHT & RESET FUNCTIONS
// =============================================

// Highlight students with good attendance
document.getElementById('highlightBtn').addEventListener('click', function() {
  calculateAttendance(); // Recalculate before highlighting
  
  document.querySelectorAll('#attendanceTable tbody tr').forEach(row => {
    row.classList.remove('highlighted');
    const cells = row.querySelectorAll('td');
    const abs = parseInt(cells[cells.length - 3].textContent) || 0;
    
    // Highlight rows with low absences
    if (abs < 3) {
      row.classList.add('highlighted');
    }
  });
  
  showToast("Excellent students highlighted!", "success");
});

// Reset all row colors and styles
document.getElementById('resetColorsBtn').addEventListener('click', function() {
  document.querySelectorAll('#attendanceTable tbody tr').forEach(row => {
    row.classList.remove('status-good', 'status-warning', 'status-bad', 'highlighted');
    row.style = '';
  });
  
  showToast("All colors reset to original appearance!", "info");
});

// =============================================
// ROW CLICK HANDLER
// =============================================

// When clicking on a table row, show a quick popup with student info
document.getElementById('attendanceTable').addEventListener('click', function(e) {
  const row = e.target.closest('tr');

  // Ignore table header + clicking on checkbox
  if (!row || row.closest('thead')) return;
  if (e.target.type === 'checkbox') return;
  
  const cells = row.querySelectorAll('td');
  if (cells.length > 0) {
    const lastName = cells[0].textContent;
    const firstName = cells[1].textContent;
    const absences = cells[cells.length - 3].textContent;
    const participations = cells[cells.length - 2].textContent;
    
    alert(`Student: ${firstName} ${lastName}\nAbsences: ${absences}\nParticipations: ${participations}`);
  }
});

// =============================================
// HOVER EFFECT
// =============================================

// Add hover class when mouse enters a row
document.getElementById('attendanceTable').addEventListener('mouseenter', function(e) {
  const row = e.target.closest('tr');
  if (row && row.querySelector('td')) {
    row.classList.add('row-hover');
  }
}, true);

// Remove hover class when mouse leaves row
document.getElementById('attendanceTable').addEventListener('mouseleave', function(e) {
  const row = e.target.closest('tr');
  if (row) {
    row.classList.remove('row-hover');
  }
}, true);

// =============================================
// TOAST NOTIFICATION
// =============================================

// Small popup notification for user feedback
function showToast(message, type = 'info') {
  const toast = document.createElement('div');
  toast.className = `toast ${type}`;
  
  // Choose icon based on message type
  const icon = type === 'success' ? 'circle-check' : 'circle-info';
  toast.innerHTML = `<i class="fa-solid fa-${icon}"></i> ${message}`;
  
  document.body.appendChild(toast);
  
  // Small delay → show animation
  setTimeout(() => toast.classList.add('show'), 100);
  
  // Hide after 3 seconds
  setTimeout(() => {
    toast.classList.remove('show');
    setTimeout(() => toast.remove(), 400);
  }, 3000);
}
