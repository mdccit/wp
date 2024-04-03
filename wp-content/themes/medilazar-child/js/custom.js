jQuery(function ($) {
    $(document).ready(function () {
  
      // Function to get window height
      const windowHeight = () => $(window).height();
  
      // Function to update the bottom buttons positions
      const bottomButtons = [$("a.scrollup"), $("#punchout_return")];
      const bottomButtonsnOrgPos = 20, bottomOffset = 80;
  
      function updateButtonPositions() {
        const height = windowHeight();
        var scrollHeight = $(document).height();
        var scrollTop = $(window).scrollTop();

        bottomButtons.forEach(btn => {
          if (scrollHeight - (scrollTop + height) <= bottomOffset) {
            btn.css({
            bottom: bottomOffset + "px"
          });
          } else {
            btn.css({
              bottom: bottomButtonsnOrgPos + "px"
            });
          }
        });
        
      }
  
      updateButtonPositions();
  
      // Call the functions when scrolling
      $(window).scroll(function () {
        updateButtonPositions();
      });
    })
  })
  
  