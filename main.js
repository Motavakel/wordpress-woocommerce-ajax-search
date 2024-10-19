document.getElementById('post_type_selector').addEventListener('change', function() {
    var postType = this.value;
    var priceRow = document.getElementById('price_row');
    var categoryRow = document.getElementById('category_row');

    if (postType === 'product') {
        priceRow.style.display = 'table-row';
    } else {
        priceRow.style.display = 'none';
    }
});



document.getElementById('copy_shortcode_btn').addEventListener('click', function() {
    var copyText = document.getElementById("generated_shortcode").value;

    // استفاده از Clipboard API برای کپی کردن شورتکد
    navigator.clipboard.writeText(copyText).then(function() {
        // نمایش آیکون تیک
        var successIcon = document.getElementById("success_icon");
        successIcon.style.display = "inline"; // نمایش آیکون
        setTimeout(function() {
            successIcon.style.display = "none"; // مخفی کردن آیکون بعد از 2 ثانیه
        }, 2000);
    }).catch(function(error) {
        console.error("خطا در کپی شورتکد: ", error);
    });
});
