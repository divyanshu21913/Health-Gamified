document.addEventListener("DOMContentLoaded", () => {
    const themeToggle = document.getElementById("theme-toggle")
    const moonIcon = document.querySelector(".theme-toggle .moon")
    const sunIcon = document.querySelector(".theme-toggle .sun")
  
    // Check for saved theme preference or use device preference
    const savedTheme = localStorage.getItem("theme")
    const prefersDark = window.matchMedia("(prefers-color-scheme: dark)").matches
  
    // Set initial theme
    if (savedTheme === "light") {
      document.body.classList.add("light-theme")
      moonIcon.style.display = "none"
      sunIcon.style.display = "block"
    } else if (savedTheme === "dark" || prefersDark) {
      document.body.classList.add("dark-theme")
      moonIcon.style.display = "block"
      sunIcon.style.display = "none"
    }
  
    // Toggle theme when button is clicked
    if (themeToggle) {
      themeToggle.addEventListener("click", () => {
        document.body.classList.toggle("light-theme")
        document.body.classList.toggle("dark-theme")
  
        // Update icon visibility
        if (document.body.classList.contains("light-theme")) {
          moonIcon.style.display = "none"
          sunIcon.style.display = "block"
          localStorage.setItem("theme", "light")
        } else {
          moonIcon.style.display = "block"
          sunIcon.style.display = "none"
          localStorage.setItem("theme", "dark")
        }
      })
    }
  })
  