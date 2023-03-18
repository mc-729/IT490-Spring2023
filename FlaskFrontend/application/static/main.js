

$(document).ready(function() {
  function toggleLikeStatus(buttonElement, liked) {
    if (liked) {
      $(buttonElement).text('Unliked');
    } else {
      $(buttonElement).text('Like');
    }
  }

  window.likeDrink = function(idDrink, buttonElement) {
    const isLiked = $(buttonElement).text() === 'Like';

    // Find the corresponding buttons in the card and the modal using the idDrink and the like-button class
    const cardButton = $(`.card[data-drink-id="${idDrink}"] .like-button`);
    const modalButton = $(`#modal-${idDrink} .like-button`);

    // Toggle the like status for both the card button and the modal button
    


    const targetUrl = '/sendDrinkData';

    // Construct the ID of the hidden input field based on the idDrink
    const hiddenInputId = 'hidden-input-' + idDrink;
    const hiddenInput = document.getElementById(hiddenInputId);
    const drinkData = hiddenInput.value;  // Assuming the value is a JSON string
if(isLiked){
    $.ajax({
      url: targetUrl,
      type: 'POST',
      data: JSON.stringify(drinkData),
      contentType: 'application/json',
      success: function(response) {
        // Show the success message
        const successMessage = 'Data sent successfully!';
        $('#success-message').html(successMessage).fadeIn(500).delay(3000).fadeOut(500);
        toggleLikeStatus(cardButton, isLiked);
    toggleLikeStatus(modalButton, isLiked);
      },
      error: function(xhr) {
        // Handle failed POST request (e.g., show an error message)
        console.error('Error sending data:', xhr.statusText);
      }
    });}
  };
  window.submitIngredient = function(element) {
    const listItem = element.closest('.list-group-item');
    const ingredient = listItem.querySelector('.col-md-4').textContent.trim();
    const amount = listItem.querySelector('.amount-input').value;
    const measurement = listItem.querySelector('.measurement-input').value;

    // Replace '/submit_ingredient' with your desired Flask route
    const targetUrl = '/submit_ingredient';
    const data = {
        'ingredient': ingredient,
        'amount': amount,
        'measurement': measurement
    };

    // Send AJAX request to the Flask route
    $.ajax({
        url: targetUrl,
        type: 'POST',
        data: JSON.stringify(data),
        contentType: 'application/json',
        success: function(response) {
            // Show the success message
            const successMessage = 'Ingredient data sent successfully!';
            $('#success-message').html(successMessage).fadeIn(500).delay(3000).fadeOut(500);
        },
        error: function(xhr) {
            // Handle failed POST request (e.g., show an error message)
            console.error('Error sending data:', xhr.statusText);
        }
    });
}
});






