// Admin Dashboard JavaScript
document.addEventListener('DOMContentLoaded', function() {
    initCharts();
    loadStudentsTable();
    loadTeachersTable();
    loadRecentActivity();
    initSidebarNavigation();
});

// Initialize Charts
function initCharts() {
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Revenue ($)',
                data: [32000, 35000, 38000, 42000, 39000, 45000, 48000, 52000, 49000, 55000, 58000, 62000],
                borderColor: '#1F8FFF',
                backgroundColor: 'rgba(31, 143, 255, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Category Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: ['Programming', 'Data Science', 'Design', 'Marketing', 'Business'],
            datasets: [{
                data: [35, 25, 20, 12, 8],
                backgroundColor: [
                    '#1F8FFF',
                    '#1F8FFF',
                    '#1F8FFF',
                    '#28a745',
                    '#dc3545'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                }
            }
        }
    });
}

// Load Students Table
function loadStudentsTable() {
    const studentsTable = document.getElementById('studentsTable');
    if (!studentsTable) return;

    const students = [
        {
            id: 1,
            name: 'Alice Johnson',
            email: 'alice.johnson@email.com',
            courses: 3,
            status: 'Active',
            joinDate: '2024-01-15'
        },
        {
            id: 2,
            name: 'Bob Smith',
            email: 'bob.smith@email.com',
            courses: 2,
            status: 'Active',
            joinDate: '2024-01-20'
        },
        {
            id: 3,
            name: 'Carol Davis',
            email: 'carol.davis@email.com',
            courses: 1,
            status: 'Inactive',
            joinDate: '2024-01-10'
        },
        {
            id: 4,
            name: 'David Wilson',
            email: 'david.wilson@email.com',
            courses: 4,
            status: 'Active',
            joinDate: '2024-01-25'
        },
        {
            id: 5,
            name: 'Eva Brown',
            email: 'eva.brown@email.com',
            courses: 2,
            status: 'Active',
            joinDate: '2024-01-30'
        }
    ];

    studentsTable.innerHTML = students.map(student => `
        <tr>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                        ${student.name.charAt(0)}
                    </div>
                    <div>
                        <div class="fw-medium">${student.name}</div>
                        <small class="text-muted">Joined ${student.joinDate}</small>
                    </div>
                </div>
            </td>
            <td>${student.email}</td>
            <td>
                <span class="badge bg-info">${student.courses} courses</span>
            </td>
            <td>
                <span class="badge ${student.status === 'Active' ? 'bg-success' : 'bg-secondary'}">
                    ${student.status}
                </span>
            </td>
            <td>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                        Actions
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="viewStudent(${student.id})">
                            <i class="bi bi-eye me-2"></i>View
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="editStudent(${student.id})">
                            <i class="bi bi-pencil me-2"></i>Edit
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#" onclick="deleteStudent(${student.id})">
                            <i class="bi bi-trash me-2"></i>Delete
                        </a></li>
                    </ul>
                </div>
            </td>
        </tr>
    `).join('');
}

// Load Teachers Table
function loadTeachersTable() {
    const teachersTable = document.getElementById('teachersTable');
    if (!teachersTable) return;

    const teachers = [
        {
            id: 1,
            name: 'Dr. Sarah Johnson',
            specialization: 'Web Development',
            students: 245,
            rating: 4.9
        },
        {
            id: 2,
            name: 'Prof. Michael Chen',
            specialization: 'Data Science',
            students: 189,
            rating: 4.8
        },
        {
            id: 3,
            name: 'Emma Rodriguez',
            specialization: 'Digital Marketing',
            students: 156,
            rating: 4.7
        },
        {
            id: 4,
            name: 'Alex Thompson',
            specialization: 'UI/UX Design',
            students: 134,
            rating: 4.6
        },
        {
            id: 5,
            name: 'Dr. Lisa Wang',
            specialization: 'Machine Learning',
            students: 98,
            rating: 4.9
        }
    ];

    teachersTable.innerHTML = teachers.map(teacher => `
        <tr>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-sm bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center me-2">
                        ${teacher.name.split(' ')[1].charAt(0)}
                    </div>
                    <div>
                        <div class="fw-medium">${teacher.name}</div>
                    </div>
                </div>
            </td>
            <td>${teacher.specialization}</td>
            <td>
                <span class="badge bg-primary">${teacher.students} students</span>
            </td>
            <td>
                <div class="d-flex align-items-center">
                    <span class="me-1">${teacher.rating}</span>
                    <div class="text-warning">
                        ${generateStars(teacher.rating)}
                    </div>
                </div>
            </td>
            <td>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                        Actions
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="viewTeacher(${teacher.id})">
                            <i class="bi bi-eye me-2"></i>View
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="editTeacher(${teacher.id})">
                            <i class="bi bi-pencil me-2"></i>Edit
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#" onclick="deleteTeacher(${teacher.id})">
                            <i class="bi bi-trash me-2"></i>Delete
                        </a></li>
                    </ul>
                </div>
            </td>
        </tr>
    `).join('');
}

// Load Recent Activity
function loadRecentActivity() {
    const activityContainer = document.getElementById('recentActivity');
    if (!activityContainer) return;

    const activities = [
        {
            type: 'student',
            icon: 'bi-person-plus',
            message: 'New student Alice Johnson enrolled in Web Development course',
            time: '2 minutes ago',
            color: 'success'
        },
        {
            type: 'course',
            icon: 'bi-book',
            message: 'Course "Advanced JavaScript" was updated by Dr. Sarah Johnson',
            time: '15 minutes ago',
            color: 'info'
        },
        {
            type: 'payment',
            icon: 'bi-credit-card',
            message: 'Payment of $299 received from Bob Smith',
            time: '1 hour ago',
            color: 'warning'
        },
        {
            type: 'teacher',
            icon: 'bi-person-badge',
            message: 'New teacher Emma Rodriguez joined the platform',
            time: '2 hours ago',
            color: 'primary'
        },
        {
            type: 'competition',
            icon: 'bi-trophy',
            message: 'Coding Challenge 2024 competition started',
            time: '3 hours ago',
            color: 'danger'
        }
    ];

    activityContainer.innerHTML = activities.map(activity => `
        <div class="d-flex align-items-start mb-3 pb-3 border-bottom">
            <div class="flex-shrink-0">
                <div class="avatar-sm bg-${activity.color} text-white rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi ${activity.icon}"></i>
                </div>
            </div>
            <div class="flex-grow-1 ms-3">
                <p class="mb-1">${activity.message}</p>
                <small class="text-muted">${activity.time}</small>
            </div>
        </div>
    `).join('');
}

// Sidebar Navigation
function initSidebarNavigation() {
    const navLinks = document.querySelectorAll('.dashboard-sidebar .nav-link');
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            if (this.getAttribute('href').startsWith('#')) {
                e.preventDefault();
                
                // Remove active class from all links
                navLinks.forEach(l => l.classList.remove('active'));
                
                // Add active class to clicked link
                this.classList.add('active');
                
                // Here you would typically show/hide different sections
                // For now, we'll just update the page title
                const section = this.getAttribute('href').substring(1);
                updatePageContent(section);
            }
        });
    });
}

// Update Page Content based on navigation
function updatePageContent(section) {
    const pageTitle = document.querySelector('main h2');
    const sections = {
        'dashboard': 'Admin Dashboard',
        'students': 'Student Management',
        'teachers': 'Teacher Management',
        'courses': 'Course Management',
        'events': 'Event Management',
        'competitions': 'Competition Management',
        'analytics': 'Analytics & Reports',
        'settings': 'System Settings'
    };
    
    if (pageTitle && sections[section]) {
        pageTitle.textContent = sections[section];
    }
}

// Modal Functions
function addStudent() {
    const form = document.getElementById('addStudentForm');
    const formData = new FormData(form);
    
    // Here you would typically send data to server

    // Close modal and refresh table
    const modal = bootstrap.Modal.getInstance(document.getElementById('addStudentModal'));
    modal.hide();
    
    // Show success message
    showToast('Student added successfully!', 'success');
    
    // Refresh table (in real app, you'd reload from server)
    setTimeout(() => {
        loadStudentsTable();
    }, 500);
}

function addTeacher() {
    const form = document.getElementById('addTeacherForm');
    const formData = new FormData(form);
    
    // Here you would typically send data to server

    // Close modal and refresh table
    const modal = bootstrap.Modal.getInstance(document.getElementById('addTeacherModal'));
    modal.hide();
    
    // Show success message
    showToast('Teacher added successfully!', 'success');
    
    // Refresh table
    setTimeout(() => {
        loadTeachersTable();
    }, 500);
}

// CRUD Operations
function viewStudent(id) {

    showToast(`Viewing student ${id}`, 'info');
}

function editStudent(id) {

    showToast(`Editing student ${id}`, 'info');
}

function deleteStudent(id) {
    if (confirm('Are you sure you want to delete this student?')) {

        showToast('Student deleted successfully!', 'success');
        loadStudentsTable();
    }
}

function viewTeacher(id) {

    showToast(`Viewing teacher ${id}`, 'info');
}

function editTeacher(id) {

    showToast(`Editing teacher ${id}`, 'info');
}

function deleteTeacher(id) {
    if (confirm('Are you sure you want to delete this teacher?')) {

        showToast('Teacher deleted successfully!', 'success');
        loadTeachersTable();
    }
}

// Utility Functions
function generateStars(rating) {
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 !== 0;
    let stars = '';
    
    for (let i = 0; i < fullStars; i++) {
        stars += '<i class="bi bi-star-fill"></i>';
    }
    
    if (hasHalfStar) {
        stars += '<i class="bi bi-star-half"></i>';
    }
    
    const emptyStars = 5 - Math.ceil(rating);
    for (let i = 0; i < emptyStars; i++) {
        stars += '<i class="bi bi-star"></i>';
    }
    
    return stars;
}

function showToast(message, type = 'info') {
    // Create toast element
    const toastContainer = document.getElementById('toastContainer') || createToastContainer();
    
    const toastId = 'toast-' + Date.now();
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    // Remove toast element after it's hidden
    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toastContainer';
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    container.style.zIndex = '1055';
    document.body.appendChild(container);
    return container;
}

// Search and Filter Functions
function searchStudents(query) {
    // Implement student search logic

}

function searchTeachers(query) {
    // Implement teacher search logic

}

function filterByStatus(status) {
    // Implement status filter logic

}

// Export Functions (for reports)
function exportStudentData() {

    showToast('Student data exported successfully!', 'success');
}

function exportTeacherData() {

    showToast('Teacher data exported successfully!', 'success');
}

function exportRevenueReport() {

    showToast('Revenue report exported successfully!', 'success');
}
