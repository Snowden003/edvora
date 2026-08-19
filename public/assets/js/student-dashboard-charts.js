// Student Dashboard Charts - Chart.js initialization
document.addEventListener('DOMContentLoaded', function () {
  initActivityChart();
  initCourseChart();
});

function initActivityChart() {
  const activityCtx = document.getElementById('activityChart');
  if (!activityCtx) return;

  const labels = JSON.parse(activityCtx.dataset.labels || '[]');
  const data = JSON.parse(activityCtx.dataset.values || '[]');

  new Chart(activityCtx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'Activities',
        data: data,
        borderColor: '#1f8fff',
        backgroundColor: 'rgba(31, 143, 255, 0.1)',
        borderWidth: 3,
        fill: true,
        tension: 0.4,
        pointBackgroundColor: '#1f8fff',
        pointBorderColor: '#fff',
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
          backgroundColor: '#0d0d0d',
          padding: 12,
          cornerRadius: 8,
          displayColors: false
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: { stepSize: 1, color: '#6c757d' },
          grid: { color: 'rgba(0,0,0,0.05)' }
        },
        x: {
          ticks: { color: '#6c757d' },
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

  new Chart(courseCtx, {
    type: 'doughnut',
    data: {
      labels: ['Completed', 'In Progress', 'Not Started'],
      datasets: [{
        data: [completed, inProgress, notStarted],
        backgroundColor: [
          '#22c55e',
          '#1f8fff',
          '#e5e7eb'
        ],
        borderWidth: 0,
        hoverOffset: 4
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '70%',
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            usePointStyle: true,
            padding: 20,
            color: '#6c757d'
          }
        }
      }
    }
  });
}
