

$(document).ready(function() {
  function toggleLikeStatus(buttonElement, liked) {
    if (liked) {
      $(buttonElement).text('Unliked');
    } else {
      $(buttonElement).text('Like');
    }
  }

  function saveStatus(buttonElement, liked) {
    if (liked) {
      $(buttonElement).text('Unsave');
    } else {
      $(buttonElement).text('Save');
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
    });
  };

  window.saveEvent = function(idEvent, buttonElement) {
    const isLiked = $(buttonElement).text() === 'Save';

    // Find the corresponding buttons in the card and the modal using the idDrink and the like-button class
    const cardButton = $(`.card[data-event-id="${idEvent}"] .like-button`);

    // Toggle the like status for both the card button and the modal button
    
    const targetUrl = '/sendEventData';

    // Construct the ID of the hidden input field based on the idDrink
    const hiddenEventId = 'hidden-event-' + idEvent;
    const hiddenDateId = 'hidden-date-' + idEvent;
    const hiddenEvent = document.getElementById(hiddenEventId);
    const hiddenDate = document.getElementById(hiddenDateId);
    const eventData = hiddenEvent.value;  // Assuming the value is a JSON string
    const dateData = hiddenDate.value;
    const combinedData = {event:eventData, date:dateData}

    $.ajax({
      url: targetUrl,
      type: 'POST',
      data: JSON.stringify(eventData),
      contentType: 'application/json',
      success: function(response) {
        // Show the success message
        const successMessage = 'Data sent successfully!';
        $('#success-message').html(successMessage).fadeIn(500).delay(3000).fadeOut(500);
        toggleLikeStatus(cardButton, isLiked);
      },
      error: function(xhr) {
        // Handle failed POST request (e.g., show an error message)
        console.error('Error sending data:', xhr.statusText);
      }
    });
  };
});





