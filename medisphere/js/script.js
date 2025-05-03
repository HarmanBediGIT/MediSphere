$(document).ready(function () {

    $('.product-list').slick({
        slidesToShow: 3, // Number of visible slides
        slidesToScroll: 1, // Number of slides to scroll on next/prev
        centerMode: true, // Enable center mode for highlighting
        centerPadding: '60px', // Padding around center item
        infinite: true, // Infinite loop
        autoplay: true, // Enable autoplay
        autoplaySpeed: 2000, // Speed of autoplay
        prevArrow: '<div class="slick-prev"></div>', // Custom prev arrow
        nextArrow: '<div class="slick-next"></div>', // Custom next arrow
        responsive: [ // Responsive settings
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 1, // Show 1 slide on small screens
                    centerPadding: '40px'
                }
            }
        ]
    });

    function showCartNotification() {
      var notification = document.getElementById('cartNotification');
      
      // Show the notification
      notification.classList.add('show');
      
      // Hide the notification after 3 seconds
      setTimeout(function() {
          notification.classList.add('hide');
      }, 3000); // After 3 seconds
  
      // Completely remove the notification after fade-out (0.5s transition)
      setTimeout(function() {
          notification.classList.remove('show', 'hide');
      }, 3500); // After the fade-out duration
  }
  });
  
  // Function to update progress bar width on scroll
window.onscroll = function() {
    updateProgressBar();
};

function updateProgressBar() {
    // Calculate the total scrollable height
    var scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
    var scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;

    // Calculate the percentage of how much the user has scrolled
    var scrollPercentage = (scrollTop / scrollHeight) * 100;

    // Update the progress bar width based on the scroll percentage
    document.getElementById('progressBar').style.width = scrollPercentage + "%";
}
