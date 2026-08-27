// Student Dashboard Charts - Modern Chart.js initialization
document.addEventListener('DOMContentLoaded', function () {
  initActivityChart();
  initCourseChart();
});

function initActivityChart() {
  const activityCtx = document.getElementById('activityChart');
  if (!activityCtx) return;

  const labels = JSON.parse(activityCtx.dataset.labels || '[]');
  const data = JSON.parse(activityCtx.dataset.values || '[]');

  const ctx = activityCtx.getContext('2d');
  const gradient = ctx.createLinearGradient(0, 0, 0, 240);
  gradient.addColorStop(0, 'rgba(31, 143, 255, 0.35)');
  gradient.addColorStop(1, 'rgba(31, 143, 255, 0.0)');

  new Chart(activityCtx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'Activities',
        data: data,
        borderColor: '#1f8fff',
        backgroundColor: gradient,
        borderWidth: 3,
        fill: true,
        tension: 0.4,
        pointBackgroundColor: '#1f8fff',
        pointBorderColor: '#ffffff',
        pointBorderWidth: 2,
        pointRadius: 5,
        pointHoverRadius: 7
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: '#0f172a',
          titleFont: { family: 'Poppins', size: 12, weight: '600' },
          bodyFont: { family: 'Poppins', size: 12 },
          padding: 12,
          cornerRadius: 10,
          displayColors: false
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: { stepSize: 1, color: '#94a3b8', font: { family: 'Poppins', size: 11 } },
          grid: { color: 'rgba(148, 163, 184, 0.12)' }
        },
        x: {
          ticks: { color: '#94a3b8', font: { family: 'Poppins', size: 11 } },
          grid: { display: false }
        }
      }
    }
  });
}

function initCourseChart() {
  const courseCtx = document.getElementById('courseChart');
  if (!courseCtx) return;

  const completed = parseInt(courseCtx.dataset.completed || '0');
  const inProgress = parseInt(courseCtx.dataset.inprogress || '0');
  const notStarted = parseInt(courseCtx.dataset.notstarted || '0');

  const total = completed + inProgress + notStarted;

  new Chart(courseCtx, {
    type: 'doughnut',
    data: {
      labels: ['Completed', 'In Progress', 'Not Started'],
      datasets: [{
        data: total > 0 ? [completed, inProgress, notStarted] : [0, 0, 1],
        backgroundColor: [
          '#10b981',
          '#1f8fff',
          '#e2e8f0'
        ],
        borderWidth: 0,
        hoverOffset: 6
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '72%',
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            usePointStyle: true,
            padding: 16,
            color: '#64748b',
            font: { family: 'Poppins', size: 11, weight: '500' }
          }
        },
        tooltip: {
          backgroundColor: '#0f172a',
          padding: 10,
          cornerRadius: 8,
          bodyFont: { family: 'Poppins', size: 12 }
        }
      }
    }
  });
}
