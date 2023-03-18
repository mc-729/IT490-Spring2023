

$(document).ready(function() {
  function toggleLikeStatus(buttonElement, liked) {
    if (liked) {
      $(buttonElement).text('Unliked');
    } else {
      $(buttonElement).text('Like');
    }
  }
  window.likeDrink = function(idDrink, buttonElement) {
    let isLiked = $(buttonElement).data('is-liked');
    // Find the corresponding buttons in the card and the modal using the idDrink and the like-button class
    const cardButton = $(`.card[data-drink-id="${idDrink}"] .like-button`);
    const modalButton = $(`#modal-${idDrink} .like-button`);
  
    const targetUrl = '/sendDrinkData?action=' + (isLiked ? 'unlike' : 'like')
  
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
        // Toggle the isLiked variable on success
        isLiked = !isLiked;
        $(buttonElement).data('is-liked', isLiked);
        // Show the success message
        const successMessage = isLiked ? 'Data sent successfully!' : 'Data removed successfully!';
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
  window.submitIngredient = function(buttonElement) {
    const listItem = $(buttonElement).closest('.list-group-item');
    const ingredient = listItem.find('.ingredient-name').text();
    const amount = listItem.find('.amount-input').val();
    const measurement = listItem.find('.measurement-input').val();
    const toastMessage = $('#toast-message');
    const toast = new bootstrap.Toast(toastMessage[0]);
  
    const data = {
      ingredient: ingredient,
      amount: amount,
      measurement: measurement
    };
  
    $.ajax({
      url: '/submit_ingredient',
      type: 'POST',
      data: JSON.stringify(data),
      contentType: 'application/json',
      success: function(response) {
        if (response.status === 'success') {
         
          toastMessage.addClass('bg-success text-white');
          toastMessage.removeClass('bg-danger');
          toastMessage.find('.toast-body').text(response.message);
          toast.show();
        } else {
          const errorMessage = response.message;
          toastMessage.addClass('bg-danger text-white');
        toastMessage.removeClass('bg-success');
        toastMessage.find('.toast-body').text('Error sending data: ' + errorMessage);
        toast.show();
        }
      },
      error: function(xhr) {
       
        toastMessage.addClass('bg-danger text-white');
        toastMessage.removeClass('bg-success');
        toastMessage.find('.toast-body').text('Error sending data: ' + xhr.statusText);
        toast.show();
      }
    });
  };
  
});






