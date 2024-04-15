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

    function checkScrollUp() {
      // console.log('kk');
      if ($('.scrollup').hasClass('activate')) {
        $("#punchout_return").css({
          right: "89px"
        });
      } else {
        $("#punchout_return").css({
          right: "24px"
        });
      }
    }

    // Functions to call on load
    updateButtonPositions();
    checkScrollUp();

    // Call the functions when scrolling
    $(window).scroll(function () {
      updateButtonPositions();
      checkScrollUp();
    });
  })
})
