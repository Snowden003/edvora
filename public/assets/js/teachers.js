// Teachers Page JavaScript
document.addEventListener("DOMContentLoaded", function () {
  loadTeachers();
  initializeFilters();
  initializeSearch();
});

let allTeachers = [];
let displayedTeachers = [];
let teachersPerPage = 12;
let currentPage = 1;

// Sample teachers data
const teachersData = [
  {
    id: 1,
    name: "Dr. Sarah Johnson",
    title: "Senior Software Engineer",
    company: "Google",
    category: "programming",
    experience: "senior",
    rating: 4.9,
    students: 15420,
    courses: 8,
    image:
      "https://static.vecteezy.com/system/resources/thumbnails/035/804/736/small_2x/ai-generated-pretty-teacher-smiling-at-camera-at-back-of-classroom-at-the-elementary-school-free-photo.jpg",
    bio: "Dr. Sarah Johnson is a Senior Software Engineer at Google with over 10 years of experience in full-stack development. She specializes in JavaScript, React, and Node.js.",
    skills: ["JavaScript", "React", "Node.js", "Python", "AWS"],
    education: "PhD in Computer Science - Stanford University",
    achievements: [
      "Google Developer Expert",
      "Published Author",
      "Conference Speaker",
    ],
  },
  {
    id: 2,
    name: "Michael Chen",
    title: "UX Design Lead",
    company: "Apple",
    category: "design",
    experience: "senior",
    rating: 4.8,
    students: 12350,
    courses: 6,
    image:
      "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=300&h=300&fit=crop&crop=face",
    bio: "Michael Chen is a UX Design Lead at Apple with expertise in user-centered design and design systems. He has worked on several award-winning mobile applications.",
    skills: [
      "UI/UX Design",
      "Figma",
      "Sketch",
      "Prototyping",
      "Design Systems",
    ],
    education: "Master's in Design - Art Center College of Design",
    achievements: [
      "Apple Design Award Winner",
      "Design Team Lead",
      "UX Mentor",
    ],
  },
  {
    id: 3,
    name: "Emily Rodriguez",
    title: "Marketing Director",
    company: "HubSpot",
    category: "marketing",
    experience: "senior",
    rating: 4.9,
    students: 18750,
    courses: 10,
    image:
      "https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=300&h=300&fit=crop&crop=face",
    bio: "Emily Rodriguez is a Marketing Director at HubSpot with extensive experience in digital marketing, content strategy, and growth hacking.",
    skills: [
      "Digital Marketing",
      "SEO",
      "Content Strategy",
      "Analytics",
      "Growth Hacking",
    ],
    education: "MBA in Marketing - Harvard Business School",
    achievements: [
      "Marketing Excellence Award",
      "Growth Hacker of the Year",
      "Keynote Speaker",
    ],
  },
  {
    id: 4,
    name: "Dr. James Wilson",
    title: "Data Science Manager",
    company: "Netflix",
    category: "data-science",
    experience: "expert",
    rating: 4.9,
    students: 22100,
    courses: 12,
    image:
      "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=300&h=300&fit=crop&crop=face",
    bio: "Dr. James Wilson is a Data Science Manager at Netflix with expertise in machine learning, statistical analysis, and big data processing.",
    skills: ["Python", "Machine Learning", "Statistics", "SQL", "TensorFlow"],
    education: "PhD in Statistics - MIT",
    achievements: [
      "Netflix Innovation Award",
      "ML Research Publications",
      "Data Science Mentor",
    ],
  },
  {
    id: 5,
    name: "Lisa Thompson",
    title: "Business Consultant",
    company: "McKinsey & Company",
    category: "business",
    experience: "senior",
    rating: 4.8,
    students: 9800,
    courses: 5,
    image:
      "https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?w=300&h=300&fit=crop&crop=face",
    bio: "Lisa Thompson is a Senior Business Consultant at McKinsey & Company with expertise in strategy, operations, and organizational transformation.",
    skills: [
      "Strategy",
      "Operations",
      "Leadership",
      "Project Management",
      "Analytics",
    ],
    education: "MBA - Wharton School",
    achievements: [
      "McKinsey Partner Track",
      "Strategy Expert",
      "Business Transformation Leader",
    ],
  },
  {
    id: 6,
    name: "Carlos Martinez",
    title: "Full Stack Developer",
    company: "Spotify",
    category: "programming",
    experience: "mid",
    rating: 4.7,
    students: 8500,
    courses: 7,
    image:
      "https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=300&h=300&fit=crop&crop=face",
    bio: "Carlos Martinez is a Full Stack Developer at Spotify specializing in modern web technologies and scalable applications.",
    skills: ["JavaScript", "Vue.js", "Python", "Docker", "Kubernetes"],
    education: "BS in Computer Science - UC Berkeley",
    achievements: [
      "Spotify Innovation Award",
      "Open Source Contributor",
      "Tech Mentor",
    ],
  },
  {
    id: 7,
    name: "Dr. Anna Kim",
    title: "Language Professor",
    company: "Harvard University",
    category: "languages",
    experience: "expert",
    rating: 4.9,
    students: 14200,
    courses: 15,
    image:
      "https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=300&h=300&fit=crop&crop=face",
    bio: "Dr. Anna Kim is a Professor of Linguistics at Harvard University with expertise in multiple languages and language acquisition.",
    skills: ["English", "Spanish", "French", "Mandarin", "Linguistics"],
    education: "PhD in Linguistics - Harvard University",
    achievements: [
      "Language Excellence Award",
      "Published Researcher",
      "International Speaker",
    ],
  },
  {
    id: 8,
    name: "Robert Davis",
    title: "Creative Director",
    company: "Adobe",
    category: "design",
    experience: "senior",
    rating: 4.8,
    students: 11600,
    courses: 9,
    image:
      "https://images.unsplash.com/photo-1560250097-0b93528c311a?w=300&h=300&fit=crop&crop=face",
    bio: "Robert Davis is a Creative Director at Adobe with extensive experience in graphic design, branding, and creative strategy.",
    skills: [
      "Graphic Design",
      "Branding",
      "Adobe Creative Suite",
      "Typography",
      "Creative Strategy",
    ],
    education: "MFA in Graphic Design - RISD",
    achievements: [
      "Adobe Creative Award",
      "Design Leadership",
      "Brand Strategy Expert",
    ],
  },
];

// Load teachers
function loadTeachers() {
  allTeachers = [...teachersData];
  displayedTeachers = allTeachers.slice(0, teachersPerPage);
  renderTeachers();
}

// Render teachers grid
function renderTeachers() {
  const grid = document.getElementById("teachersGrid");
  if (!grid) return;

  grid.innerHTML = displayedTeachers
    .map((teacher) => {
      // Create a short bio by taking the first sentence.
      const shortBio = teacher.bio.split(".")[0] + ".";

      return `
        <div class="col-lg-4 col-md-6 col-sm-12 d-flex justify-content-center card-width">
            <div class="card course-card h-100" style="width: 100%; max-width: 350px;">
                <div class="position-relative">
                    <img src="${teacher.image}" class="card-img-top" alt="${teacher.name}" style="height: 250px; object-fit: cover;">
                    <div class="position-absolute top-0 start-0 m-2">
                        ${teacher.rating >= 4.9 ? '<span class="badge bg-danger">Top Rated</span>' : ""}
                    </div>
                </div>
                <div class="card-body d-flex flex-column">
                    <div class="mb-2">
                        <span class="badge bg-primary">${getCategoryName(teacher.category)}</span>
                        <span class="badge bg-secondary ms-1">${capitalizeFirstLetter(teacher.experience)}</span>
                    </div>
                    <h5 class="card-title">${teacher.name}</h5>
                    <p class="text-muted mb-2">${teacher.title}</p>
                    <p class="card-text small text-muted mb-3">${shortBio}</p>
                    
                    <div class="mt-auto">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rating me-2">
                                ${generateStars(teacher.rating)}
                            </div>
                            <span class="text-muted small">(${teacher.rating})</span>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3 text-muted small">
                            <div>
                                <i class="bi bi-people-fill me-1"></i>${teacher.students.toLocaleString()}
                            </div>
                            <div>
                                <i class="bi bi-book-fill me-1"></i>${teacher.courses} Courses
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary w-100" onclick="showTeacherDetails(${teacher.id})">
                                View Profile
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    })
    .join("");

  // Update load more button
  updateLoadMoreButton();
}

// Helper function to capitalize first letter
function capitalizeFirstLetter(string) {
  return string.charAt(0).toUpperCase() + string.slice(1);
}

// Helper function to get nice category name
function getCategoryName(category) {
  const categories = {
    programming: "Programming",
    "data-science": "Data Science",
    design: "Design",
    marketing: "Marketing",
    business: "Business",
    languages: "Languages",
  };
  return categories[category] || category;
}

// Helper function to generate star rating HTML
function generateStars(rating) {
  const fullStars = Math.floor(rating);
  const hasHalfStar = rating % 1 !== 0; // Check for .5
  let stars = "";

  for (let i = 0; i < fullStars; i++) {
    stars += '<i class="bi bi-star-fill text-warning"></i>';
  }

  if (hasHalfStar) {
    stars += '<i class="bi bi-star-half text-warning"></i>';
  }

  const emptyStars = 5 - Math.ceil(rating);
  for (let i = 0; i < emptyStars; i++) {
    stars += '<i class="bi bi-star text-warning"></i>';
  }

  return stars;
}

// Initialize filters
function initializeFilters() {
  const categoryFilter = document.getElementById("categoryFilter");
  const experienceFilter = document.getElementById("experienceFilter");
  const ratingFilter = document.getElementById("ratingFilter");

  [categoryFilter, experienceFilter, ratingFilter].forEach((filter) => {
    if (filter) {
      filter.addEventListener("change", applyFilters);
    }
  });
}

// Initialize search
function initializeSearch() {
  const searchInput = document.getElementById("teacherSearch");
  if (!searchInput) return;

  let searchTimeout;
  searchInput.addEventListener("input", function () {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
      applyFilters();
    }, 300);
  });
}

// Apply filters
function applyFilters() {
  const searchTerm =
    document.getElementById("teacherSearch")?.value.toLowerCase() || "";
  const category = document.getElementById("categoryFilter")?.value || "";
  const experience = document.getElementById("experienceFilter")?.value || "";
  const rating = document.getElementById("ratingFilter")?.value || "";

  let filteredTeachers = allTeachers.filter((teacher) => {
    const matchesSearch =
      teacher.name.toLowerCase().includes(searchTerm) ||
      teacher.title.toLowerCase().includes(searchTerm) ||
      teacher.company.toLowerCase().includes(searchTerm) ||
      teacher.skills.some((skill) => skill.toLowerCase().includes(searchTerm));

    const matchesCategory = !category || teacher.category === category;
    const matchesExperience = !experience || teacher.experience === experience;
    const matchesRating = !rating || teacher.rating >= parseFloat(rating);

    return (
      matchesSearch && matchesCategory && matchesExperience && matchesRating
    );
  });

  // Reset pagination
  currentPage = 1;
  displayedTeachers = filteredTeachers.slice(0, teachersPerPage);
  renderTeachers();
}

// Load more teachers
function loadMoreTeachers() {
  const searchTerm =
    document.getElementById("teacherSearch")?.value.toLowerCase() || "";
  const category = document.getElementById("categoryFilter")?.value || "";
  const experience = document.getElementById("experienceFilter")?.value || "";
  const rating = document.getElementById("ratingFilter")?.value || "";

  let filteredTeachers = allTeachers.filter((teacher) => {
    const matchesSearch =
      teacher.name.toLowerCase().includes(searchTerm) ||
      teacher.title.toLowerCase().includes(searchTerm) ||
      teacher.company.toLowerCase().includes(searchTerm) ||
      teacher.skills.some((skill) => skill.toLowerCase().includes(searchTerm));

    const matchesCategory = !category || teacher.category === category;
    const matchesExperience = !experience || teacher.experience === experience;
    const matchesRating = !rating || teacher.rating >= parseFloat(rating);

    return (
      matchesSearch && matchesCategory && matchesExperience && matchesRating
    );
  });

  currentPage++;
  const startIndex = (currentPage - 1) * teachersPerPage;
  const endIndex = startIndex + teachersPerPage;
  const newTeachers = filteredTeachers.slice(startIndex, endIndex);

  displayedTeachers = [...displayedTeachers, ...newTeachers];
  renderTeachers();
}

// Update load more button
function updateLoadMoreButton() {
  const loadMoreBtn = document.getElementById("loadMoreTeachers");
  if (!loadMoreBtn) return;

  const searchTerm =
    document.getElementById("teacherSearch")?.value.toLowerCase() || "";
  const category = document.getElementById("categoryFilter")?.value || "";
  const experience = document.getElementById("experienceFilter")?.value || "";
  const rating = document.getElementById("ratingFilter")?.value || "";

  let filteredTeachers = allTeachers.filter((teacher) => {
    const matchesSearch =
      teacher.name.toLowerCase().includes(searchTerm) ||
      teacher.title.toLowerCase().includes(searchTerm) ||
      teacher.company.toLowerCase().includes(searchTerm) ||
      teacher.skills.some((skill) => skill.toLowerCase().includes(searchTerm));

    const matchesCategory = !category || teacher.category === category;
    const matchesExperience = !experience || teacher.experience === experience;
    const matchesRating = !rating || teacher.rating >= parseFloat(rating);

    return (
      matchesSearch && matchesCategory && matchesExperience && matchesRating
    );
  });

  if (displayedTeachers.length >= filteredTeachers.length) {
    loadMoreBtn.style.display = "none";
  } else {
    loadMoreBtn.style.display = "block";
    loadMoreBtn.onclick = loadMoreTeachers;
  }
}

// Show teacher details modal
function showTeacherDetails(teacherId) {
  const teacher = allTeachers.find((t) => t.id === teacherId);
  if (!teacher) return;

  const modalBody = document.getElementById("teacherModalBody");
  if (!modalBody) return;

  modalBody.innerHTML = `
        <div class="row">
            <div class="col-md-4 text-center">
                <img src="${teacher.image}" alt="${teacher.name}" class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                <div class="mb-3">
                    <div class="d-flex justify-content-center align-items-center mb-2">
                        <div class="me-3">
                            <i class="bi bi-star-fill text-warning"></i> ${teacher.rating}
                        </div>
                        <div>
                            <i class="bi bi-people-fill text-primary"></i> ${teacher.students.toLocaleString()} students
                        </div>
                    </div>
                    <div class="text-muted">
                        <i class="bi bi-book-fill"></i> ${teacher.courses} courses
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <h4 class="fw-bold mb-2" style="color: var(--primary-blue);">${teacher.name}</h4>
                <p class="text-muted mb-1">${teacher.title}</p>
                <p class="text-muted mb-3">
                    <i class="bi bi-building me-1"></i>${teacher.company}
                </p>
                
                <div class="mb-3">
                    <h6 class="fw-bold">About</h6>
                    <p class="text-muted">${teacher.bio}</p>
                </div>
                
                <div class="mb-3">
                    <h6 class="fw-bold">Education</h6>
                    <p class="text-muted">${teacher.education}</p>
                </div>
                
                <div class="mb-3">
                    <h6 class="fw-bold">Skills</h6>
                    <div>
                        ${teacher.skills
                          .map(
                            (skill) =>
                              `<span class="badge bg-primary me-1 mb-1" style="background-color: var(--primary-blue) !important;">${skill}</span>`,
                          )
                          .join("")}
                    </div>
                </div>
                
                <div class="mb-3">
                    <h6 class="fw-bold">Achievements</h6>
                    <ul class="list-unstyled">
                        ${teacher.achievements
                          .map(
                            (achievement) =>
                              `<li><i class="bi bi-award-fill text-warning me-2"></i>${achievement}</li>`,
                          )
                          .join("")}
                    </ul>
                </div>
            </div>
        </div>
    `;

  const modal = new bootstrap.Modal(document.getElementById("teacherModal"));
  modal.show();
}
