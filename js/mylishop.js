// Shopping cart
(function() {
 
  $("#cart").on("click", function() {
    $(".shopping-cart").fadeToggle( "fast");
  });
  
})();

// Form đăng ký
function validateForm() {
    var pass = document.forms["myForm"]["password"].value;
    var confirm_pass = document.forms["myForm"]["confirmPassword"].value;
    if ( pass != confirm_pass) {
        alert("Bạn phải nhập 2 mật khẩu khớp với nhau!");
        return false;
    }
}
