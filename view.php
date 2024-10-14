<?php
defined('ABSPATH') || exit;

$woo_search_form =
    '<div class="woo_search_bar woo_bar_el d-none d-md-block">
        <form class="woo_search woo_bar_el" id="woo_search" autocomplete="off">
            <span class="loading woo_bar_el" >
                <svg width="25px" height="25px" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" fill="none" class="hds-flight-icon--animation-loading woo_bar_el">
                    <g fill="#676767" fill-rule="evenodd" clip-rule="evenodd">
                    <path d="M8 1.5a6.5 6.5 0 100 13 6.5 6.5 0 000-13zM0 8a8 8 0 1116 0A8 8 0 010 8z" opacity=".2"/>
                    <path d="M7.25.75A.75.75 0 018 0a8 8 0 018 8 .75.75 0 01-1.5 0A6.5 6.5 0 008 1.5a.75.75 0 01-.75-.75z"/>
                    </g>
                </svg>
            </span>
            <input type="search" name="s" placeholder="جستجو ..." id="keyword" class="input_search woo_bar_el" onkeyup="searchFetch(this)">
            <button id="mybtn" class="search woo_bar_el">
                <svg width="20px" height="20px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16.6725 16.6412L21 21M19 11C19 15.4183 15.4183 19 11 19C6.58172 19 3 15.4183 3 11C3 6.58172 6.58172 3 11 3C15.4183 3 19 6.58172 19 11Z" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
            <input  type="hidden"  name="post_type" value="product">
            <input  type="hidden"  name="num" value="' . $num . '">
            <input  type="hidden"  name="description" value="' . $description . '">
            <input  type="hidden"  name="cat" value="' . $cat . '">
        </form><div class="search_result woo_bar_el" id="datafetch" style="display: none;">
            <ul>
                <li>لطفا صبر کنید ...</li>
            </ul>
        </div>
    </div>';

$java =
    '<script>
    function searchFetch(e) {
        
        // به محض اینکه بر روی جستجو کلیک شد بیا و آیکون اس وی جی را نمایش بده
        const searchForm = e.parentElement;	
        searchForm.querySelector(".loading").style.visibility = "visible";

        // اگر ورودی وارد شد بیا دیو بعد از فرم را نمایش بده به طور کامل 
        var datafetch = e.parentElement.nextSibling
        if (e.value.trim().length > 0){ 
            datafetch.style.display = "block"; 
        } 
        else { 
        datafetch.style.display = "none";
        }

        e.nextSibling.value = "لطفا صبر کنید ...";

        //با استفاده از شی کلاس فرم دیتا ، تمامی اطلاعات فرم شامل ورودی ها دریافت می کنیم. همچنین مجوز نمایش تصویر را دریافت و ذخیره می کنیم
        var formdata  = new FormData(searchForm);
        formdata.append("image", "' . $image . '");
        formdata.append("action", "woo_search");

        //با استفاده از تابع ای جکس زیر اطلاعات را ارسال و نتیجه را چاپ می کنیم
        Ajaxwoo_search(formdata,e); 
        
    }
    async function Ajaxwoo_search(formdata,e) {
        const url = "' . admin_url("admin-ajax.php") . '?action=woo_search";

        const response = await fetch(url, {
            method: "POST",
            body: formdata,
        });
        const data = await response.text();
        if (data){	
            e.parentElement.nextSibling.innerHTML = data
        }else 
        {
            e.parentElement.nextSibling.innerHTML = 
            `<ul>
                <a href="#" style="display: block; padding-inline-start: 14px;">
                    <li>متاسفانه نتیجه ایی یافت نشد</li>
                </a>
            </ul>`
        }
        e.parentElement.querySelector(".loading").style.visibility = "hidden";
    }	

    function goSearch(id){document.querySelector(id).click(); console.log(`clicked`) }

    //برای زمانی که در جای دیگری از بادی کلیک می کنیم و  صفحه سرچ بسته می شود
    document.addEventListener("click", function(e) { 
    if (document.activeElement.classList.contains("woo_bar_el") == false ){ 
    [...document.querySelectorAll("div.search_result")].forEach(e => e.style.display = "none")
    } 
    else {
        if  (e.target?.value.trim().length > 0) { 
        e.target.parentElement.nextSibling.style.display = "block"}
        } 
    }
    )
</script>';

$css = 
'<style>

form.woo_search {
	display: flex;
	flex-wrap: nowrap;
	border: 1px solid #f0f0f0;
	border-radius: 5px;
	padding: 5px;
	background-color: white;
	box-shadow: 0px 6px 9px #00000008;
	height: 40px;
}
  
  form.woo_search button#mybtn {
    display: grid;
    padding: 4px;
    cursor: pointer;
    background: none;
    align-items: center;
    border: none;
  }
  
  form.woo_search input#keyword {
    border: none;
  }
  
div#datafetch {
	background: white;
	z-index: 10;
	position: absolute;
	max-height: 425px;
	overflow: auto;
	box-shadow: 0px 15px 15px #00000075;
	right: 0;
	left: 0;
	top: 50px;
	border-radius: 5px;
}
  
div.woo_search_bar {
 width: clamp(175px, (100% - 175px)*10000, 35vw); 
  
	position: relative;
}
  
  div.search_result ul a li {
    display: flex;
    margin: 0px;
    padding: 0px 0px 0px 0px;
    color: #3f3f3f;
    font-weight: bold;
    flex-direction: column;
    justify-content: space-evenly;
  }
  
  div.search_result li {
    margin-inline-start: 20px;
    list-style: none;
  }
  
  div.search_result ul {
    padding: 13px 0px 0px 0px !important;
    list-style: none;
    margin: auto;
  }
  
  div.search_result ul a {
    display: grid;
    grid-template-columns: 70px 1fr minmax(70px, min-content);
    margin-bottom: 10px;
    gap: 5px;
  }
  
  div.search_result ul a h5 {
    font-size: 1em;
    padding: 0;
    margin: 0;
    font-weight: bold;
  }
  
  div.search_result ul a p.des {
    font-weight: normal;
    font-size: 0.9em;
    color: #676767;
    padding: 0;
    margin: 0;
    line-height: 1.3em;
  }
  
  div.search_result ul a h5.sku {
    font-weight: normal;
    font-size: 0.85em;
    color: #676767;
    padding: 0 !important;
    margin: 0 !important;
  }
  
  div.search_result ul a span.title_r_1 {
    display: flex;
    flex-direction: row;
    gap: 9px;
  }
  
  div.search_result ul a:hover {
    background-color: #f3f3f3;
  }
  
  .woo_search input#keyword {
    outline: none;
    width: 100%;
    background-color: white;
  }
  
  span.loading {
    display: grid;
    align-items: center;
  }
  
  @-webkit-keyframes rotating {
    from {
      -webkit-transform: rotate(0deg);
    }
  
    to {
      -webkit-transform: rotate(360deg);
    }
  }
  
  .hds-flight-icon--animation-loading {
    -webkit-animation: rotating 1s linear infinite;
  }
  
  span.loading {
    visibility: hidden;
  }
  
  span.price p {
    padding: 0;
    margin: 0;
  }
  
  span.price {
    display: flex;
    margin-inline-end: 5px;
    align-items: center;
    color: #535353;
  }
  
  span.price .sale-price {
    justify-content: flex-start;
  
  }
  
  div#datafetch a {
    text-decoration: none;
  }
  
  ul.cat_ul.woo_bar_el {
    display: flex;
    flex-wrap: wrap;
    gap: 0px;
  }
  
  a.cat_a.woo_bar_el {
    display: block;
    color: #5a5a5a;
    padding: 4px 15px;
    border-radius: 10vh;
    border: 1px solid #5a5a5a;
  }
  
  a.cat_a.woo_bar_el:hover {
    background-color: #5a5a5a;
    color: white;
  }
  
  p.search_title {
    margin: 10px 0px 10px 8px;
    line-height: normal;
    color: #676767;
    font-size: 0.9em;
    font-weight: normal;
    padding: 0;
    text-align: center;
  }
  
  hr.search_title {
    background-color: #cccccc;
    margin: 2px 8px 0px 8px;
  }

  .show_all{
    text-align: center; 
    background: white;
    width: 100%;
    padding: 5px;
    color: #666464; 
    cursor: pointer; 
    font-size: 0.95em;
    border: none;
    }
</style>';
