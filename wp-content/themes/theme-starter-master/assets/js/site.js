var SITE = (function($) {
  function init() {}

  function anotherOne() {}

  return {
    init: init,
    anotherOne: anotherOne
  };
})(jQuery);

$(document).ready(function() {
  SITE.init();
  SITE.anotherOne();
});

// var mySwiper = new Swiper(".swiper-container", {
//   // Optional parameters
//   direction: "horizontal",
//   loop: true,
//   slidesPerView: 1,
//   centeredSlides: true,

//   // If we need pagination
//   pagination: {
//     el: ".swiper-pagination"
//   }
// });

//eslint-disable-line;
