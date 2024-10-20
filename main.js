document
  .getElementById("post_type_selector")
  .addEventListener("change", function () {
    var postType = this.value;
    var priceRow = document.getElementById("price_row");

    if (postType === "product") {
      priceRow.style.display = "table-row";
    } else {
      priceRow.style.display = "none";
    }
  });

document
  .getElementById("copy_shortcode_btn")
  .addEventListener("click", function () {
    var copyText = document.getElementById("generated_shortcode").value;

    navigator.clipboard
      .writeText(copyText)
      .then(function () {
        var success_message = document.getElementById("success_message");
        success_message.style.display = "inline";
        setTimeout(function () {
          success_message.style.display = "none";
        }, 2000);
      })
      .catch(function (error) {
        console.error("خطا در کپی شورتکد: ", error);
      });
  });

