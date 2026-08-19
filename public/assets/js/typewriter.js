// Typewriter Animation Script
class TypewriterEffect {
  constructor(element, options = {}) {
    this.element = element;
    this.text = element.getAttribute("data-text") || element.textContent;
    this.speed = options.speed || 100;
    this.delay = options.delay || 0;
    this.cursor = options.cursor || "|";
    this.cursorSpeed = options.cursorSpeed || 500;

    this.element.textContent = "";
    this.currentIndex = 0;
    this.isComplete = false;

    this.init();
  }

  init() {
    setTimeout(() => {
      this.type();
    }, this.delay);
  }

  type() {
    if (this.currentIndex < this.text.length) {
      this.element.textContent = this.text.substring(0, this.currentIndex + 1);
      this.currentIndex++;
      setTimeout(() => this.type(), this.speed);
    } else {
      this.isComplete = true;
      this.element.classList.add("typing-complete");
    }
  }
}

// Auto-initialize typewriter elements when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
  const typewriterElements = document.querySelectorAll(".typewriter-text");

  typewriterElements.forEach((element, index) => {
    // Check if it's courses page or teachers page
    const isCoursesPage =
      document.body.classList.contains("courses-page") ||
      window.location.pathname.includes("courses");
    const isTeachersPage =
      document.body.classList.contains("teachers-page") ||
      window.location.pathname.includes("teachers");

    let speed, delay;

    if (isCoursesPage) {
      // Courses page settings
      speed = element.classList.contains("hero-title") ? 100 : 30;
      delay = element.classList.contains("hero-title") ? 800 : 3000;
      delay += index * 1500;
    } else if (isTeachersPage) {
      // Teachers page settings - single line
      speed = 100; // Normal speed for full title
      delay = 1000; // Start after 1 second
    } else {
      // Default settings
      speed = 100;
      delay = 800 + index * 1500;
    }

    new TypewriterEffect(element, {
      speed: speed,
      delay: delay,
    });
  });
});
