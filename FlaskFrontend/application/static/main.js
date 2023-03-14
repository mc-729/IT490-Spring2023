



$(document).ready(function() {
    window.likeDrink = function(idDrink) {
      // Replace the URL below with the actual URL of the page you want to send the data to
      const targetUrl = '/sendDrinkData';
  
      // Construct the ID of the hidden input field based on the idDrink
      const hiddenInputId = 'hidden-input-' + idDrink;
      const hiddenInput = document.getElementById(hiddenInputId);
      const drinkData = hiddenInput.value;  // Assuming the value is a JSON string
  
      $.ajax({
        url: targetUrl,
        type: 'POST',
        data: JSON.stringify(drinkData),
        contentType: 'application/json',
        success: function(response) {
          // Handle successful POST request (e.g., show a success message or redirect the user)
          console.log('Data sent successfully:', drinkData);
        },
        error: function(xhr) {
          // Handle failed POST request (e.g., show an error message)
          console.error('Error sending data:', xhr.statusText);
        }
      });
    };
  });