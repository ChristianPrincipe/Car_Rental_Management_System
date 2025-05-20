
  // USER ACCOUNT DROP DOWN 
    const dropdownMenu = document.getElementById('dropdownMenu');
    const accountInfo = document.getElementById('accountInfo');

    // Toggle dropdown visibility on account info click
    accountInfo.addEventListener('click', function(event) {
      // Prevent click event from propagating to document
      event.stopPropagation();
      dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
    });

    // Close the dropdown when clicking outside of it
    document.addEventListener('click', function(event) {
      if (!accountInfo.contains(event.target) && !dropdownMenu.contains(event.target)) {
        dropdownMenu.style.display = 'none';
      }
    });


// THAT SELECT BUTTON WITH YELLOW BORDERD WHEN SELECTED, LIKE HOVER WHEN YOU SELECT IT BORDER CHANGE TO YELLOW
    // Sa katong pilipili renatal type, button rana function
    document.querySelectorAll('.rental-btn').forEach(btn => {
    btn.addEventListener('click', function () {
      document.querySelectorAll('.rental-btn').forEach(b => b.classList.remove('active'));
      this.classList.add('active');
    });
  });

  // Sa katong pilipili price type, button rana function
  document.querySelectorAll('.amount-btn').forEach(btn => {
    btn.addEventListener('click', function () {
      document.querySelectorAll('.amount-btn').forEach(b => b.classList.remove('active'));
      this.classList.add('active');
    });
  });

// Sa katong pilipili driver type, button rana function
document.querySelectorAll('.driver-btn').forEach(btn => {
  btn.addEventListener('click', function () {
    // Remove the 'active' class from all driver buttons
    document.querySelectorAll('.driver-btn').forEach(b => b.classList.remove('active'));

    // Add the 'active' class to the clicked button
    this.classList.add('active');

    // Set the selected driver type in the hidden input
    document.getElementById('driverTypeInput').value = this.dataset.value;
  });
});




  
  
//FROM  BOOKING-FIRST-PROCESS.HTML, THAT SELECT BUTTON WITH YELLOW BORDERD WHEN SELECTED
document.getElementById('locationForm').addEventListener('submit', function (e) {
  // Get the rental type and price type input elements
  const rentalTypeInput = document.getElementById('rentalTypeInput');
  const priceTypeInput = document.getElementById('priceTypeInput');
  
  // Get the error message elements
  const rentalTypeError = document.getElementById('rentalTypeError');
  const priceTypeError = document.getElementById('priceTypeError');

  // Reset error messages
  rentalTypeError.style.display = 'none';
  priceTypeError.style.display = 'none';

  // Validate rental type
  if (!rentalTypeInput.value) {
    e.preventDefault(); // Prevent form submission
    rentalTypeError.style.display = 'block'; // Show rental type error message
  }

  // Validate price type
  if (!priceTypeInput.value) {
    e.preventDefault(); // Prevent form submission
    priceTypeError.style.display = 'block'; // Show price type error message
  }
});



//FROM BOOKING-FIRST-PROCESS.HTML, THAT SELECT BUTTON WITH YELLOW BORDERD WHEN SELECTED
// Rental Type Button Click Event
//  morag gina store niya ang value atong choose bitaw na delivery ba or self pick up
document.querySelectorAll('.rental-btn').forEach(btn => {
  btn.addEventListener('click', function () {
    document.querySelectorAll('.rental-btn').forEach(b => b.classList.remove('active'));
    this.classList.add('active');
    document.getElementById('rentalTypeInput').value = this.dataset.value; // Set the selected rental type in the hidden input
  });
});

// Price Type Button Click Event
// sam ras babaw
document.querySelectorAll('.amount-btn').forEach(btn => {
  btn.addEventListener('click', function () {
    document.querySelectorAll('.amount-btn').forEach(b => b.classList.remove('active'));
    this.classList.add('active');
    document.getElementById('priceTypeInput').value = this.dataset.value; // Set the selected price type in the hidden input
  });
});












