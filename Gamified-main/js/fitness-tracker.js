// Initialize fitness tracker functionality
document.addEventListener("DOMContentLoaded", () => {
  console.log("Fitness tracker script loaded")

  // Initialize date picker with today's date
  const datePicker = document.getElementById("fitness-date")
  const today = new Date().toISOString().split("T")[0]

  if (datePicker) {
    console.log("Date picker found")
    datePicker.value = today
    datePicker.max = today

    // Add event listener for date change
    datePicker.addEventListener("change", function () {
      console.log("Date changed to:", this.value)
      loadDailyData(this.value)
    })
  } else {
    console.error("Date picker element not found")
  }

  // Handle fitness form submission
  const fitnessForm = document.getElementById("fitness-form")
  if (fitnessForm) {
    console.log("Fitness form found")
    fitnessForm.addEventListener("submit", function (e) {
      e.preventDefault()
      console.log("Form submitted")

      const formData = new FormData(this)

      // Log form data for debugging
      for (const pair of formData.entries()) {
        console.log(pair[0] + ": " + pair[1])
      }

      // Send data to server
      fetch("api/save_fitness_data.php", {
        method: "POST",
        body: formData,
        headers: {
          'Accept': 'application/json'
        }
      })
        .then((response) => {
          console.log("Response status:", response.status)
          return response.json()
        })
        .then((data) => {
          console.log("Response data:", data)
          if (data.success) {
            // Update progress bars
            updateProgressBars(
              Number.parseInt(formData.get("steps")),
              Number.parseInt(formData.get("calories")),
              Number.parseInt(formData.get("active_minutes")),
            )

            // Show success notification
            showNotification("Fitness data saved successfully!", "success")

            // Update points if earned
            if (data.points_earned > 0) {
              const pointsDisplay = document.querySelector(".points-value")
              if (pointsDisplay) {
                const currentPoints = Number.parseInt(pointsDisplay.textContent.replace(/,/g, ""))
                pointsDisplay.textContent = (currentPoints + data.points_earned).toLocaleString()
              }

              showNotification(`You earned ${data.points_earned} points!`, "success")
            }

            // Refresh fitness history
            loadFitnessHistory()

          } else {
            showNotification("Error saving data: " + data.message, "error")
          }
        })
        .catch((error) => {
          console.error("Error:", error)
          showNotification("An error occurred while saving data: " + error.message, "error")
          
          // Log the full error details
          console.log("Full error details:", {
            error: error,
            formData: Object.fromEntries(formData),
            endpoint: "api/save_fitness_data.php"
          })
        })
    })
  } else {
    console.error("Fitness form element not found")
  }

  // Function to update progress bars
  function updateProgressBars(steps, calories, activeMinutes) {
    console.log("Updating progress bars with:", steps, calories, activeMinutes)
    const stepsGoal = 10000
    const caloriesGoal = 2000
    const activeMinutesGoal = 60

    const stepsPercentage = Math.min((steps / stepsGoal) * 100, 100)
    const caloriesPercentage = Math.min((calories / caloriesGoal) * 100, 100)
    const activeMinutesPercentage = Math.min((activeMinutes / activeMinutesGoal) * 100, 100)

    // Update progress bars
    const stepsProgressBar = document.querySelector(".stat-card:nth-child(1) .progress-fill")
    const caloriesProgressBar = document.querySelector(".stat-card:nth-child(2) .progress-fill")
    const activeMinutesProgressBar = document.querySelector(".stat-card:nth-child(3) .progress-fill")

    if (stepsProgressBar) stepsProgressBar.style.width = `${stepsPercentage}%`
    if (caloriesProgressBar) caloriesProgressBar.style.width = `${caloriesPercentage}%`
    if (activeMinutesProgressBar) activeMinutesProgressBar.style.width = `${activeMinutesPercentage}%`

    // Update text values
    const stepsCount = document.getElementById("steps-count")
    const caloriesCount = document.getElementById("calories-count")
    const activeMinutesCount = document.getElementById("active-minutes")

    if (stepsCount) stepsCount.textContent = steps.toLocaleString()
    if (caloriesCount) caloriesCount.textContent = calories.toLocaleString()
    if (activeMinutesCount) activeMinutesCount.textContent = activeMinutes
  }

  // Function to load daily data
  function loadDailyData(date) {
    console.log("Loading data for date:", date)
    fetch(`api/get_fitness_data_by_date.php?date=${date}`)
      .then((response) => response.json())
      .then((data) => {
        console.log("Daily data response:", data)
        if (data.success) {
          // Update form values
          const stepsInput = document.getElementById("steps-input")
          const caloriesInput = document.getElementById("calories-input")
          const activeMinutesInput = document.getElementById("active-minutes-input")

          if (stepsInput) stepsInput.value = data.data.steps
          if (caloriesInput) caloriesInput.value = data.data.calories
          if (activeMinutesInput) activeMinutesInput.value = data.data.active_minutes

          // Update progress bars
          updateProgressBars(
            Number.parseInt(data.data.steps),
            Number.parseInt(data.data.calories),
            Number.parseInt(data.data.active_minutes),
          )
        } else {
          // Clear form if no data found
          const stepsInput = document.getElementById("steps-input")
          const caloriesInput = document.getElementById("calories-input")
          const activeMinutesInput = document.getElementById("active-minutes-input")

          if (stepsInput) stepsInput.value = ""
          if (caloriesInput) caloriesInput.value = ""
          if (activeMinutesInput) activeMinutesInput.value = ""

          // Reset progress bars
          updateProgressBars(0, 0, 0)
        }
      })
      .catch((error) => {
        console.error("Error loading daily data:", error)
      })
  }

  // Function to show notification
  function showNotification(message, type) {
    console.log("Showing notification:", message, type)
    
    // Remove existing notification
    const existingNotification = document.querySelector(".notification")
    if (existingNotification) {
      existingNotification.remove()
    }

    // Create notification
    const notification = document.createElement("div")
    notification.className = `notification ${type}`
    notification.textContent = message

    // Add to body
    document.body.appendChild(notification)

    // Show notification with animation after a brief delay
    setTimeout(() => {
      notification.classList.add('show')
    }, 10)

    // Hide after 2 seconds with smooth fade out
    setTimeout(() => {
      notification.style.transform = 'translate(-50%, -100%)'
      notification.style.opacity = '0'
      
      setTimeout(() => {
        notification.remove()
      }, 400)
    }, 2000)
  }

  // Function to update averages
  function updateAverages(averages) {
    console.log("Updating averages:", averages)
    if (averages) {
      const avgStepsEl = document.getElementById("avg-steps")
      const avgCaloriesEl = document.getElementById("avg-calories")
      const avgActiveMinutesEl = document.getElementById("avg-active-minutes")

      if (avgStepsEl) avgStepsEl.textContent = Math.round(averages.avg_steps).toLocaleString()
      if (avgCaloriesEl) avgCaloriesEl.textContent = Math.round(averages.avg_calories).toLocaleString()
      if (avgActiveMinutesEl) avgActiveMinutesEl.textContent = Math.round(averages.avg_active_minutes)
    }
  }

  // Function to update suggestions
  function updateSuggestions(suggestions) {
    console.log("Updating suggestions:", suggestions)
    const suggestionsContainer = document.getElementById("fitness-suggestions")

    if (suggestionsContainer && suggestions && suggestions.length > 0) {
      // Clear existing content
      suggestionsContainer.innerHTML = ""

      // Create list of suggestions
      const list = document.createElement("ul")

      suggestions.forEach((suggestion) => {
        const item = document.createElement("li")
        item.textContent = suggestion
        list.appendChild(item)
      })

      suggestionsContainer.appendChild(list)
    }
  }
// Function to load fitness history
function loadFitnessHistory() {
  console.log("Loading fitness history")
  fetch('api/get_fitness_data.php')
    .then((response) => {
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      return response.json();
    })
    .then((data) => {
      console.log("Fitness history response:", data)
      if (data.success) {
        // Update table with the fitness data
        updateFitnessTable(data.data)

        // Update averages and suggestions
        updateAverages(data.averages)
        updateSuggestions(data.suggestions)
      } else {
        console.error("API returned error:", data.message);
        showNotification("Error loading fitness history: " + data.message, "error");
      }
    })
    .catch((error) => {
      console.error("Error loading fitness history:", error);
      showNotification("Failed to load fitness history. Please try refreshing the page.", "error");
      
      // Show error in table
      const tableBody = document.querySelector("#fitness-history-table tbody");
      if (tableBody) {
        tableBody.innerHTML = `<tr><td colspan="4" class="error-message">Error loading fitness data. Please try refreshing the page.</td></tr>`;
      }
    })
}

// Function to update fitness history table
function updateFitnessTable(data) {
  console.log("Updating fitness table with data:", data)
  const tableBody = document.querySelector("#fitness-history-table tbody")
  
  if (!tableBody) {
    console.error("Fitness history table not found!")
    return
  }

  // Clear existing rows
  tableBody.innerHTML = ""

  if (!data || data.length === 0) {
    const emptyRow = document.createElement("tr")
    emptyRow.innerHTML = '<td colspan="4" style="text-align: center;">No fitness data available</td>'
    tableBody.appendChild(emptyRow)
    return
  }

  // Sort data by date in descending order
  data.sort((a, b) => new Date(b.date) - new Date(a.date))

  // Create table rows
  data.forEach((item) => {
    const row = document.createElement("tr")
    const date = new Date(item.date)
    row.innerHTML = `
      <td>${date.toLocaleDateString("en-US", { month: "short", day: "numeric", year: "numeric" })}</td>
      <td>${Number.parseInt(item.steps).toLocaleString()}</td>
      <td>${Number.parseInt(item.calories).toLocaleString()}</td>
      <td>${Number.parseInt(item.active_minutes)}</td>
    `
    tableBody.appendChild(row)
  })

  console.log("Fitness table updated successfully")
}

// Load initial data
console.log("Loading initial data...")
loadDailyData(today)

// Load fitness history
console.log("Loading initial fitness history...")
loadFitnessHistory()
})
