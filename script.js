// =============================================
// NAVIGATION FUNCTIONS
// =============================================

/**
 * Displays a specific page and updates navigation
 * @param {string} pageId - The ID of the page to display
 */
function showPage(pageId) {
  // Hide all pages
  document.querySelectorAll('.page').forEach(page => page.classList.remove('active'));
  // Deactivate all navigation links
  document.querySelectorAll('.nav-links a').forEach(link => link.classList.remove('active'));
  // Show the requested page
  document.getElementById(pageId).classList.add('active');
  // Activate the corresponding navigation link
  event.target.classList.add('active');
}

// =============================================
// ATTENDANCE CALCULATION FUNCTIONS
// =============================================

/**
 * Calculates absences and participations for each student
 * and updates colors and messages in the table
 */
function calculateAttendance() {
  const table = document.getElementById("attendanceTable");
  const rows = table.getElementsByTagName("tr");

  // Loop through all table rows (starting from index 2 to skip headers)
  for (let i = 2; i < rows.length; i++) {
    const cells = rows[i].getElementsByTagName("td");
    const checkboxes = rows[i].getElementsByTagName("input");

    let absences = 0;
    let participations = 0;

    // Count absences and participations
    // Checkboxes are organized in pairs: [Presence, Participation]
    for (let j = 0; j < checkboxes.length; j += 2) {
      const present = checkboxes[j].checked;
      const participate = checkboxes[j + 1].checked;
      if (!present) absences++;
      if (participate) participations++;
    }

    // Update Absences and Participation columns
    cells[cells.length - 3].textContent = absences;
    cells[cells.length - 2].textContent = participations;

    // Remove existing status classes
    rows[i].classList.remove("status-good", "status-warning", "status-bad");
    
    // Add appropriate status class based on number of absences
    if (absences < 3) {
      rows[i].classList.add("status-good");      // Green - Good attendance
    } else if (absences >= 3 && absences <= 4) {
      rows[i].classList.add("status-warning");   // Yellow - Warning
    } else {
      rows[i].classList.add("status-bad");       // Red - Poor attendance
    }

    // Set message based on attendance and participation
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

    cells[cells.length - 1].textContent = message;
  }
  
  // Show success notification
  showNotification("Attendance calculated successfully!", "success");
}

// =============================================
// FORM VALIDATION & STUDENT ADDITION
// =============================================

// Get the student form element
const form = document.getElementById('studentForm');

// Add submit event listener to the form
form.addEventListener('submit', function(event) {
  event.preventDefault();
  let valid = true;

  // Get form field values and trim whitespace
  const id = document.getElementById('studentId').value.trim();
  const last = document.getElementById('lastName').value.trim();
  const first = document.getElementById('firstName').value.trim();
  const email = document.getElementById('email').value.trim();

  // Reset error and confirmation messages
  document.getElementById('idError').textContent = "";
  document.getElementById('lastError').textContent = "";
  document.getElementById('firstError').textContent = "";
  document.getElementById('emailError').textContent = "";
  document.getElementById('confirmation').textContent = "";

  // VALIDATION CHECKS
  
  // Student ID validation: required and must contain only numbers
  if (!id) {
    document.getElementById('idError').innerHTML = '<i class="fas fa-exclamation-circle"></i> Student ID is required';
    valid = false;
  } else if (!/^\d+$/.test(id)) {
    document.getElementById('idError').innerHTML = '<i class="fas fa-exclamation-circle"></i> Student ID must contain only numbers';
    valid = false;
  }
  
  // Last Name validation: required and must contain only letters
  if (!last) {
    document.getElementById('lastError').innerHTML = '<i class="fas fa-exclamation-circle"></i> Last Name is required';
    valid = false;
  } else if (!/^[A-Za-zÀ-ÿ\s\-']+$/.test(last)) {
    document.getElementById('lastError').innerHTML = '<i class="fas fa-exclamation-circle"></i> Last Name must contain only letters';
    valid = false;
  }
  
  // First Name validation: required and must contain only letters
  if (!first) {
    document.getElementById('firstError').innerHTML = '<i class="fas fa-exclamation-circle"></i> First Name is required';
    valid = false;
  } else if (!/^[A-Za-zÀ-ÿ\s\-']+$/.test(first)) {
    document.getElementById('firstError').innerHTML = '<i class="fas fa-exclamation-circle"></i> First Name must contain only letters';
    valid = false;
  }
  
  // Email validation: required and must be valid format
  if (!email) {
    document.getElementById('emailError').innerHTML = '<i class="fas fa-exclamation-circle"></i> Email is required';
    valid = false;
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
    document.getElementById('emailError').innerHTML = '<i class="fas fa-exclamation-circle"></i> Email is not valid';
    valid = false;
  }
  
  // Stop execution if validation fails
  if (!valid) return;

  // ADD NEW STUDENT TO TABLE
  
  const table = document.getElementById('attendanceTable');
  const newRow = table.insertRow();
  
  // Add status class for the new row
  newRow.classList.add("status-good");
  
  // Build HTML for the new row
  let inner = `<td>${last}</td><td>${first}</td>`;
  // Add 6 sessions with checkboxes for presence and participation
  for (let k = 0; k < 6; k++) {
    inner += '<td><input type="checkbox"></td><td><input type="checkbox"></td>';
  }
  // Add summary columns
  inner += '<td>0</td><td>0</td><td>New student - attendance not calculated yet</td>';
  newRow.innerHTML = inner;

  // Show confirmation message and reset form
  document.getElementById('confirmation').innerHTML = '<i class="fas fa-check-circle"></i> Student added successfully!';
  form.reset();
  
  // Switch to attendance page after a delay
  setTimeout(() => {
    showPage('attendance');
    showNotification(`Student ${first} ${last} added successfully!`, "success");
  }, 1500);
});

// =============================================
// REPORT GENERATION FUNCTIONS
// =============================================

/**
 * Displays the report with statistics and chart
 * Shows:
 * - Total number of students
 * - Number of students marked present
 * - Number of students marked as having participated
 */
function showReport() {
  const table = document.getElementById("attendanceTable");
  const rows = table.getElementsByTagName("tr");
  const totalStudents = rows.length - 2;

  let presentStudents = 0;
  let participatedStudents = 0;

  // Count present and participated students
  for (let i = 2; i < rows.length; i++) {
    const checkboxes = rows[i].getElementsByTagName("input");
    let hasPresent = false;
    let hasParticipated = false;

    // Check if student was present at least once
    for (let j = 0; j < checkboxes.length; j += 2) {
      if (checkboxes[j].checked) hasPresent = true;
    }

    // Check if student participated at least once
    for (let j = 1; j < checkboxes.length; j += 2) {
      if (checkboxes[j].checked) hasParticipated = true;
    }

    if (hasPresent) presentStudents++;
    if (hasParticipated) participatedStudents++;
  }

  // Update statistics cards with requested data
  document.getElementById("totalStudents").textContent = totalStudents;
  document.getElementById("studentsPresent").textContent = presentStudents;
  document.getElementById("studentsParticipated").textContent = participatedStudents;

  // Show report section
  document.getElementById("reportSection").style.display = "block";

  // Create or update chart with the 3 requested statistics
  const ctx = document.getElementById("reportChart").getContext("2d");
  if (window.attendanceChart) window.attendanceChart.destroy();

  window.attendanceChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Total Students', 'Present Students', 'Participated Students'],
      datasets: [{
        label: 'Number of Students',
        data: [totalStudents, presentStudents, participatedStudents],
        backgroundColor: ['#4361ee', '#2a9d8f', '#e9c46a'],
        borderWidth: 0
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false
        },
        title: {
          display: true,
          text: 'Attendance Statistics Overview'
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            stepSize: 1
          }
        }
      }
    }
  });
  
  showNotification("Report generated successfully!", "success");
}

// =============================================
// JQUERY INTERACTIONS
// =============================================

$(document).ready(function() {
  
  // Click on table row to show student details
  $("#attendanceTable").on("click", "tr", function() {
    const cells = $(this).find("td");
    if (cells.length > 0) {
      const lastName = cells.eq(0).text();
      const firstName = cells.eq(1).text();
      const absences = cells.eq(cells.length - 3).text();
      const participations = cells.eq(cells.length - 2).text();
      
      alert(`Student: ${firstName} ${lastName}\nAbsences: ${absences}\nParticipations: ${participations}`);
    }
  });

  // Highlight excellent students (less than 3 absences)
  $("#highlightBtn").on("click", function() {
    calculateAttendance(); // Ensure data is up to date
    
    // Remove previous highlights
    $(".highlighted").removeClass("highlighted");
    
    $("#attendanceTable tr").each(function() {
      const cells = $(this).find("td");
      if (cells.length > 0) {
        const absText = cells.eq(cells.length - 3).text().trim();
        const absences = parseInt(absText) || 0;
        
        // Highlight only students with less than 3 absences
        if (absences < 3) {
          $(this).addClass("highlighted");
          $(this).css({
            'font-weight': 'bold',
            'background-color': '#e0f7fa',
            'box-shadow': '0 0 15px rgba(76, 201, 240, 0.7)',
            'transform': 'scale(1.02)'
          });
        }
      }
    });
    
    showNotification("Excellent students highlighted!", "success");
  });

  // Reset Colors button - Removes ALL colors from the table
  $("#resetColorsBtn").on("click", function() {
    // Remove all color and highlight classes
    $("#attendanceTable tr").each(function() {
      $(this).removeClass("status-good status-warning status-bad highlighted");
      $(this).css({
        'font-weight': '',
        'background-color': '',
        'box-shadow': '',
        'transform': '',
        'border-left': ''
      });
    });
    
    showNotification("All colors reset to original appearance!", "info");
  });
});

// =============================================
// NOTIFICATION FUNCTION
// =============================================

/**
 * Displays a temporary notification
 * @param {string} message - The message to display
 * @param {string} type - The notification type (success, warning, error, info)
 */
function showNotification(message, type) {
  // Create notification element
  const notification = document.createElement('div');
  notification.textContent = message;
  notification.style.cssText = `
    position: fixed;
    top: 100px;
    right: 20px;
    padding: 15px 20px;
    border-radius: var(--border-radius);
    color: white;
    font-weight: 500;
    z-index: 1000;
    box-shadow: var(--box-shadow);
    transition: var(--transition);
    transform: translateX(150%);
  `;
  
  // Set background color based on type
  if (type === "success") {
    notification.style.backgroundColor = "var(--success)";
  } else if (type === "warning") {
    notification.style.backgroundColor = "var(--warning)";
  } else if (type === "error") {
    notification.style.backgroundColor = "var(--danger)";
  } else {
    notification.style.backgroundColor = "var(--primary)";
  }
  
  document.body.appendChild(notification);
  
  // Animate entrance
  setTimeout(() => {
    notification.style.transform = "translateX(0)";
  }, 100);
  
  // Animate exit after 3 seconds
  setTimeout(() => {
    notification.style.transform = "translateX(150%)";
    setTimeout(() => {
      document.body.removeChild(notification);
    }, 300);
  }, 3000);
}