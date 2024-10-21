jQuery(document).ready(function ($) {
 
  $("#ajax_search_notice").insertBefore(".wrap");


  $("#post_type_selector").on("change", function () {
    var postType = $(this).val();
    var priceRow = $("#price_row");

    if (postType === "product") {
      priceRow.show();
    } else {
      priceRow.hide();
    }
  });
  $("#copy_shortcode_btn").on("click", function () {
    var copyText = $("#generated_shortcode").val();

    navigator.clipboard
      .writeText(copyText)
      .then(function () {
        var success_message = $("#success_message");
        success_message.show();
        setTimeout(function () {
          success_message.hide();
        }, 2000);
      })
      .catch(function (error) {
        console.error("خطا در کپی شورتکد: ", error);
      });
  });
  $('.ajax-search-increment').on('click', function (e) {
    e.preventDefault(); // جلوگیری از رفتار پیش‌فرض دکمه
    var input = $('#num_results');
    input[0].stepUp();
});

$('.ajax-search-decrement').on('click', function (e) {
    e.preventDefault(); // جلوگیری از رفتار پیش‌فرض دکمه
    var input = $('#num_results');
    input[0].stepDown();
});
});
