// Javascript for Admin Panel
document.addEventListener("DOMContentLoaded", function() {

    (function ($, Drupal, once) {
    Drupal.behaviors.carrollBaseBehavior = {
      attach: function (context, settings) {
        once('carroll_admin_class', context).forEach(function (element) {
          //
          CarrollAdmin.applyClass(element);
        });

        once('carroll_admin_event', context).forEach(function (element) {
          //
          CarrollAdmin.applyEventHandlers(element);
        });

      }
    };
  })(jQuery, Drupal, once);

});

class CarrollAdmin {

  static applyClass(elem) {
    // console.log('carroll admin : class');
    // console.log(elem);
  }

  static applyEventHandlers(elem) {
    // console.log('carroll admin : event');
    // console.log(elem);
  }

}
