$(document).ready(function(){

$('.container').tooltip({
    selector: "a[rel=tooltip]"
});

 $('#contact-form').validate(
 {
  rules: {
    name: {
      minlength: 4,
      required: true
    },
    last_name: {
      minlength: 3,
      required: true
    },
    first_name: {
      minlength: 3,
      required: true
    },
    middle_name: {
      minlength: 3,
      required: true
    },
    mobile: {
      minlength: 11,
      required: true
    },
    age: {
      required: true
    },
    email: {
      required: true,
      email: true
    },
    password: {
      minlength: 4,
      required: true
    }
  },
  highlight: function(element) {
    $(element).closest('.control-group').removeClass('success').addClass('error');
  },
  success: function(element) {
    element
    .text('OK!').addClass('valid')
    .closest('.control-group').removeClass('error').addClass('success');
  }
 });
}); // end document.ready
