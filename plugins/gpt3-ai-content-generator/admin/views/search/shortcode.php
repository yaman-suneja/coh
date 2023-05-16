<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$wpaicg_pinecone_api = get_option('wpaicg_pinecone_api','');
$wpaicg_pinecone_environment = get_option('wpaicg_pinecone_environment','');
$wpaicg_search_placeholder = get_option('wpaicg_search_placeholder','Search anything..');
$wpaicg_search_no_result = get_option('wpaicg_search_no_result','');
$wpaicg_search_font_size = get_option('wpaicg_search_font_size','13');
$wpaicg_search_font_color = get_option('wpaicg_search_font_color','#000');
$wpaicg_search_border_color = get_option('wpaicg_search_border_color','#ccc');
$wpaicg_search_bg_color = get_option('wpaicg_search_bg_color','');
$wpaicg_search_width = get_option('wpaicg_search_width','100%');
$wpaicg_search_height = get_option('wpaicg_search_height','45px');
$wpaicg_search_result_font_size = get_option('wpaicg_search_result_font_size','13');
$wpaicg_search_result_font_color = get_option('wpaicg_search_result_font_color','#000');
$wpaicg_search_result_bg_color = get_option('wpaicg_search_result_bg_color','');
$wpaicg_search_loading_color = get_option('wpaicg_search_loading_color','#ccc');
if(empty($wpaicg_pinecone_api) || empty($wpaicg_pinecone_environment)):
    ?>
<p>Seems like you haven't entered your keys, therefore this feature is disabled.</p>
<?php
else:
?>
<style>
    .wpaicg-search{
        width: <?php echo esc_html($wpaicg_search_width)?>;
    }
    .wpaicg-search .wpaicg-search-form{}
    .wpaicg-search .wpaicg-search-form .wpaicg-search-input{
        height: <?php echo esc_html($wpaicg_search_height)?>;
        color: <?php echo esc_html($wpaicg_search_font_color)?>;
        position: relative;
        width: 100%;
        font-size: <?php echo esc_html($wpaicg_search_font_size)?>px;
    }
    .wpaicg-search .wpaicg-search-form .wpaicg-search-input .wpaicg-search-field{
        height: <?php echo esc_html($wpaicg_search_height)?>;
        color: <?php echo esc_html($wpaicg_search_font_color)?>;
        font-size: <?php echo esc_html($wpaicg_search_font_size)?>px;
        width: 100%;
        <?php
        if(!empty($wpaicg_search_bg_color)):
        ?>
        background-color: <?php echo esc_html($wpaicg_search_bg_color)?>;
        <?php
        endif;
        ?>
        border-color: <?php echo esc_html($wpaicg_search_border_color)?>;
        border-style: solid;
        border-width: 1px;
        border-radius: 5px;
        box-shadow: none;
    }
    .wpaicg-search .wpaicg-search-form .wpaicg-search-input svg{
        fill: currentColor;
        width: 25px;
        height: 25px;
        cursor: pointer;
        position: absolute;
        right: 10px;
        top: calc(50% - 12.5px);
    }
    .wpaicg-search-result{
        position: relative;
        min-height: 100px;
        margin-top: 20px;
        <?php
        if(!empty($wpaicg_search_result_bg_color)):
        ?>
        padding: 10px;
        <?php
        endif;
        ?>
        border-radius: 8px;
        color: <?php echo esc_html($wpaicg_search_result_font_color)?>;

    }
    .wpaicg-search-result.wpaicg-has-item{
    <?php
    if(!empty($wpaicg_search_result_bg_color)){
    ?>
        background-color: <?php echo esc_html($wpaicg_search_result_bg_color)?>;
    <?php
    }
    ?>
    }
    .wpaicg-search-loading{
        display: flex;
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
        justify-content: center;
        align-items: center;
        <?php
        if(empty($wpaicg_search_loading_color)):
        ?>
        background: rgb(0 0 0 / 25%);
        <?php
        else:
        ?>
        background: <?php echo esc_html($wpaicg_search_loading_color)?>;
        <?php
        endif;
        ?>
    }
    .wpaicg-lds-dual-ring {
        display: inline-block;
        width: 80px;
        height: 80px;
    }
    .wpaicg-lds-dual-ring:after {
        content: " ";
        display: block;
        width: 64px;
        height: 64px;
        margin: 8px;
        border-radius: 50%;
        border: 6px solid #fff;
        border-color: #fff transparent #fff transparent;
        animation: wpaicg-lds-dual-ring 1.2s linear infinite;
    }
    @keyframes wpaicg-lds-dual-ring {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }
    .wpaicg-search-item{
        padding-bottom: 20px;
    }
    .wpaicg-search-item-title{
        font-weight: bold;
        font-size: 20px;
    }
    .wpaicg-search-item-content{
        font-size: <?php echo esc_html($wpaicg_search_result_font_size)?>px;
        color: <?php echo esc_html($wpaicg_search_result_font_color)?>;
    }
    .wpaicg-search-source{}
    .wpaicg-search-source h3{
        margin: 10px 0;
    }
    .wpaicg-search-source a{
        display: inline-block;
        margin-right: 10px;
        color: <?php echo esc_html($wpaicg_search_result_font_color)?>;
    }
    .wpaicg-search-item-date{
        font-size: 13px;
        margin-bottom: 5px;
    }
</style>
<div class="wpaicg-search">
    <form class="wpaicg-search-form" action="" method="post">

        <div class="wpaicg-search-input">
            <input autocomplete="off" type="text" name="search" class="wpaicg-search-field" placeholder="<?php echo esc_attr($wpaicg_search_placeholder)?>">
            <svg class="wpaicg-search-submit" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"/></svg>
        </div>
    </form>
    <div class="wpaicg-search-result">

    </div>
    <div class="wpaicg-search-source"></div>
</div>
<script>
    var wpaicg_nonce = '<?php echo esc_html(wp_create_nonce( 'wpaicg-chatbox' ))?>';
    var wpaicgSearch = document.getElementsByClassName('wpaicg-search')[0];
    var wpaicgSearchForm = wpaicgSearch.getElementsByClassName('wpaicg-search-form')[0];
    var wpaicgSearchField = wpaicgSearch.getElementsByClassName('wpaicg-search-field')[0];
    var wpaicgSearchResult = wpaicgSearch.getElementsByClassName('wpaicg-search-result')[0];
    var wpaicgSearchSource = wpaicgSearch.getElementsByClassName('wpaicg-search-source')[0];
    var wpaicgSearchBtn = wpaicgSearch.getElementsByClassName('wpaicg-search-submit')[0];
    wpaicgSearchBtn.addEventListener('click', function (){
        wpaicgSearchData();
    });
    function wpaicgExpand(el){
        var wpaicgSearchItem = el.closest('.wpaicg-search-item');
        wpaicgSearchItem.getElementsByClassName('wpaicg-search-item-excerpt')[0].style.display = 'none';
        wpaicgSearchItem.getElementsByClassName('wpaicg-search-item-full')[0].style.display = 'block';
    }
    function wpaicgSearchData(){
        var search = wpaicgSearchField.value;
        if(search !== '') {
            wpaicgSearchResult.innerHTML = '<div class="wpaicg-search-loading"><div class="wpaicg-lds-dual-ring"></div></div>';
            wpaicgSearchSource.innerHTML = '';
            wpaicgSearchResult.classList.remove('wpaicg-has-item');
            const xhttp = new XMLHttpRequest();
            xhttp.open('POST', '<?php echo admin_url('admin-ajax.php')?>');
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send('action=wpaicg_search_data&_wpnonce='+wpaicg_nonce+'&search='+encodeURIComponent(search));
            xhttp.onreadystatechange = function(oEvent) {
                if (xhttp.readyState === 4) {
                    if (xhttp.status === 200) {
                        wpaicgSearchResult.classList.add('wpaicg-has-item');
                        var wpaicg_response = this.responseText;
                        if (wpaicg_response !== '') {
                            wpaicg_response = JSON.parse(wpaicg_response);
                            wpaicgSearchResult.innerHTML = '';
                            if (wpaicg_response.status === 'success') {
                                if(wpaicg_response.data.length){
                                    for(var i = 0; i < wpaicg_response.data.length; i++){
                                        var item = wpaicg_response.data[i];
                                        wpaicgSearchResult.innerHTML += item;
                                    }
                                    if(wpaicg_response.source.length){
                                        wpaicgSearchSource.innerHTML = '<h3><?php echo esc_html(__('Sources:','gpt3-ai-content-generator'))?></h3>';
                                        for(var i = 0; i < wpaicg_response.source.length; i++){
                                            var item = wpaicg_response.source[i];
                                            wpaicgSearchSource.innerHTML += item;
                                        }
                                    }
                                }
                                else{
                                    wpaicgSearchResult.innerHTML = '<p><?php echo esc_html(__('No result found','gpt3-ai-content-generator'))?></p>';
                                }
                            }
                            else{
                                wpaicgSearchResult.innerHTML = '<p class="wpaicg-search-error">'+wpaicg_response.msg+'</p>';
                            }
                        }
                        else{
                            wpaicgSearchResult.innerHTML = '<p class="wpaicg-search-error">Something went wrong</p>';
                        }
                    }
                    else{
                        wpaicgSearchResult.innerHTML = '<p class="wpaicg-search-error">Something went wrong</p>';
                    }
                }
            }

        }
    }
    wpaicgSearchForm.addEventListener('submit', function (e){
        wpaicgSearchData();
        e.preventDefault();
        return false;
    })
</script>
<?php
endif;
