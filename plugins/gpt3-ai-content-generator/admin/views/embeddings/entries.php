<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<style>
.wpaicg-faq-list:has(.wpaicg-faq-item){
    padding: 10px;
    background: #d5d5d5;
    border-radius: 5px;
    margin-bottom: 5px;
}
.wpaicg-faq-item{
    padding: 5px 10px;
    background: #fff;
    border-radius: 4px;
    position: relative;
    margin-bottom: 10px;
}
.wpaicg-faq-close{
    position: absolute;
    right: 5px;
    top: 5px;
    width: 20px;
    height: 20px;
    background: #c70000;
    border-radius: 4px;
    color: #fff;
    font-size: 20px;
    line-height: 15px;
    text-align: center;
    cursor: pointer;
}
.wpaicg-knowledge-list:has(.wpaicg-knowledge-item){
    padding: 10px;
    background: #d5d5d5;
    border-radius: 5px;
}
.wpaicg-knowledge-item{
    padding: 5px 10px;
    background: #fff;
    border-radius: 4px;
    position: relative;
    margin-bottom: 10px;
}
.wpaicg-knowledge-close{
    position: absolute;
    right: 5px;
    top: 5px;
    width: 20px;
    height: 20px;
    background: #c70000;
    border-radius: 4px;
    color: #fff;
    font-size: 20px;
    line-height: 15px;
    text-align: center;
    cursor: pointer;
}
.wpaicg-product-list:has(.wpaicg-product-item){
    padding: 10px;
    background: #d5d5d5;
    border-radius: 5px;
}
.wpaicg-product-item{
    padding: 5px 10px;
    background: #fff;
    border-radius: 4px;
    position: relative;
    margin-bottom: 10px;
}
.wpaicg-product-close{
    position: absolute;
    right: 5px;
    top: 5px;
    width: 20px;
    height: 20px;
    background: #c70000;
    border-radius: 4px;
    color: #fff;
    font-size: 20px;
    line-height: 15px;
    text-align: center;
    cursor: pointer;
}
.wpaicg-product-Product-Name{
    width: 100%;
}
.wpaicg-product-Product-Description{
    width: 100%;
    height: 100px;
}
.wpaicg-product-Product-Price{
    width: 100%;
}
.wpaicg-product-Product-URL{
    width: 100%;
}
.wpaicg-product-Product-Id{
    width: 100%;
}
.wpaicg-wooproduct-list:has(.wpaicg-wooproduct-item){
    padding: 10px;
    background: #d5d5d5;
    border-radius: 5px;
}
.wpaicg-wooproduct-item{
    padding: 5px 10px;
    background: #fff;
    border-radius: 4px;
    position: relative;
    margin-bottom: 10px;
}
.wpaicg-wooproduct-close{
    position: absolute;
    right: 5px;
    top: 5px;
    width: 20px;
    height: 20px;
    background: #c70000;
    border-radius: 4px;
    color: #fff;
    font-size: 20px;
    line-height: 15px;
    text-align: center;
    cursor: pointer;
}
.wpaicg-wooproduct-Product-Name{
    width: 100%;
}
.wpaicg-wooproduct-Product-Description{
    width: 100%;
    height: 100px;
}
.wpaicg-wooproduct-Product-RegularPrice{
    width: 100%;
}
.wpaicg-wooproduct-Product-URL{
    width: 100%;
}
.wpaicg-wooproduct-Product-SKU{
    width: 100%;
}
.wpaicg-wooproduct-Product-SalePrice{
    width: 100%;
}
.wpaicg-wooproduct-Product-StockStatus{
    width: 100%;
}
.wpaicg-link-list:has(.wpaicg-link-item){
    padding: 10px;
    background: #d5d5d5;
    border-radius: 5px;
}
.wpaicg-link-item{
    padding: 5px 10px;
    background: #fff;
    border-radius: 4px;
    position: relative;
    margin-bottom: 10px;
}
.wpaicg-link-close{
    position: absolute;
    right: 5px;
    top: 5px;
    width: 20px;
    height: 20px;
    background: #c70000;
    border-radius: 4px;
    color: #fff;
    font-size: 20px;
    line-height: 15px;
    text-align: center;
    cursor: pointer;
}
.wpaicg-company-list:has(.wpaicg-company-item){
    padding: 10px;
    background: #d5d5d5;
    border-radius: 5px;
}
.wpaicg-company-item{
    padding: 5px 10px;
    background: #fff;
    border-radius: 4px;
    position: relative;
    margin-bottom: 10px;
}
.wpaicg-company-close{
    position: absolute;
    right: 5px;
    top: 5px;
    width: 20px;
    height: 20px;
    background: #c70000;
    border-radius: 4px;
    color: #fff;
    font-size: 20px;
    line-height: 15px;
    text-align: center;
    cursor: pointer;
}
.wpaicg-company-Company-Name{
    width: 100%;
}
.wpaicg-company-Company-Description{
    width: 100%;
    height: 100px;
}
.wpaicg-company-Company-CEO{
    width: 100%;
}
.wpaicg-company-Company-Founded{
    width: 100%;
}
.wpaicg-company-Company-Location{
    width: 100%;
}
.wpaicg-company-Company-Employees{
    width: 100%;
}
.wpaicg-company-Company-Industry{
    width: 100%;
}
.wpaicg-company-Company-Products{
    width: 100%;
}
.wpaicg-company-Company-Website{
    width: 100%;
}
.wpaicg-company-Company-Email{
    width: 100%;
}
.wpaicg-company-Company-Phone{
    width: 100%;
}
.wpaicg-company-Company-Address{
    width: 100%;
}
.wpaicg-event-list:has(.wpaicg-event-item){
    padding: 10px;
    background: #d5d5d5;
    border-radius: 5px;
}
.wpaicg-event-item{
    padding: 5px 10px;
    background: #fff;
    border-radius: 4px;
    position: relative;
    margin-bottom: 10px;
}
.wpaicg-event-close{
    position: absolute;
    right: 5px;
    top: 5px;
    width: 20px;
    height: 20px;
    background: #c70000;
    border-radius: 4px;
    color: #fff;
    font-size: 20px;
    line-height: 15px;
    text-align: center;
    cursor: pointer;
}
.wpaicg-event-Event-Name{
    width: 100%;
}
.wpaicg-event-Event-Description{
    width: 100%;
    height: 100px;
}
.wpaicg-event-Event-Date{
    width: 100%;
}
.wpaicg-event-Event-Time{
    width: 100%;
}
.wpaicg-event-Event-Location{
    width: 100%;
}
.wpaicg-event-Event-Organizer{
    width: 100%;
}
.wpaicg-event-Event-URL{
    width: 100%;
}
.wpaicg-pricing-list:has(.wpaicg-pricingplans-item){
    padding: 10px;
    background: #d5d5d5;
    border-radius: 5px;
}
.wpaicg-pricing-item{
    padding: 5px 10px;
    background: #fff;
    border-radius: 4px;
    position: relative;
    margin-bottom: 10px;
}
.wpaicg-pricing-close{
    position: absolute;
    right: 5px;
    top: 5px;
    width: 20px;
    height: 20px;
    background: #c70000;
    border-radius: 4px;
    color: #fff;
    font-size: 20px;
    line-height: 15px;
    text-align: center;
    cursor: pointer;
}
.wpaicg-pricing-Pricing-Plan-Name{
    width: 100%;
}
.wpaicg-pricing-Pricing-Plan-Features{
    width: 100%;
    height: 100px;
}
.wpaicg-pricing-Pricing-Plan-Price{
    width: 100%;
}
.wpaicg-pricing-Pricing-Plan-URL{
    width: 100%;
}
.wpaicg-contact-list:has(.wpaicg-contact-item){
    padding: 10px;
    background: #d5d5d5;
    border-radius: 5px;
}
.wpaicg-contact-item{
    padding: 5px 10px;
    background: #fff;
    border-radius: 4px;
    position: relative;
    margin-bottom: 10px;
}
.wpaicg-contact-close{
    position: absolute;
    right: 5px;
    top: 5px;
    width: 20px;
    height: 20px;
    background: #c70000;
    border-radius: 4px;
    color: #fff;
    font-size: 20px;
    line-height: 15px;
    text-align: center;
    cursor: pointer;
}
.wpaicg-contact-Contact-Name{
    width: 100%;
}
.wpaicg-contact-Contact-Email{
    width: 100%;
}
.wpaicg-contact-Contact-Phone{
    width: 100%;
}
.wpaicg-contact-Contact-Address{
    width: 100%;
}
.wpaicg-contact-Contact-Description{
    width: 100%;
    height: 100px;
}
.wpaicg-contact-Contact-URL{
    width: 100%;
}
</style>
<div class="wpaicg-faq-item-default" style="display: none">
    <div class="wpaicg-faq-item">
        <span class="wpaicg-faq-close">&times;</span>
        <p>
            <label><strong>Question</strong></label>
            <textarea class="wpaicg-faq-Question"></textarea>
        </p>
        <p>
            <label><strong>Answer</strong></label>
            <textarea class="wpaicg-faq-Answer"></textarea>
        </p>
    </div>
</div>
<div class="wpaicg-knowledge-item-default" style="display: none">
    <div class="wpaicg-knowledge-item">
        <span class="wpaicg-knowledge-close">&times;</span>
        <p>
            <label><strong>Topic</strong></label>
            <textarea class="wpaicg-knowledge-Topic"></textarea>
        </p>
        <p>
            <label><strong>Description</strong></label>
            <textarea class="wpaicg-knowledge-Description"></textarea>
        </p>
    </div>
</div>
<div class="wpaicg-product-item-default" style="display: none">
    <div class="wpaicg-product-item">
        <span class="wpaicg-product-close">&times;</span>
        <p>
            <label><strong>Product ID</strong></label>
            <input type="text" class="wpaicg-product-Product-Id">
        </p>
        <p>
            <label><strong>Product Name</strong></label>
            <input type="text" class="wpaicg-product-Product-Name">
        </p>
        <p>
            <label><strong>Product Description</strong></label>
            <textarea class="wpaicg-product-Product-Description"></textarea>
        </p>
        <p>
            <label><strong>Product Price</strong></label>
            <input type="text" class="wpaicg-product-Product-Price">
        </p>
        <p>
            <label><strong>Product URL</strong></label>
            <input type="text" class="wpaicg-product-Product-URL">
        </p>
    </div>
</div>
<div class="wpaicg-wooproduct-item-default" style="display: none">
    <div class="wpaicg-wooproduct-item">
        <span class="wpaicg-wooproduct-close">&times;</span>
        <p>
            <label><strong>Product SKU</strong></label>
            <input type="text" class="wpaicg-wooproduct-Product-SKU">
        </p>
        <p>
            <label><strong>Product Name</strong></label>
            <input type="text" class="wpaicg-wooproduct-Product-Name">
        </p>
        <p>
            <label><strong>Product Description</strong></label>
            <textarea class="wpaicg-wooproduct-Product-Description"></textarea>
        </p>
        <p>
            <label><strong>Product Regular Price</strong></label>
            <input type="text" class="wpaicg-wooproduct-Product-RegularPrice">
        </p>
        <p>
            <label><strong>Product Sale Price</strong></label>
            <input type="text" class="wpaicg-wooproduct-Product-SalePrice">
        </p>
        <p>
            <label><strong>Product URL</strong></label>
            <input type="text" class="wpaicg-wooproduct-Product-URL">
        </p>
        <p>
            <label><strong>Product Stock Status</strong></label>
            <input type="text" class="wpaicg-wooproduct-Product-StockStatus">
        </p>
    </div>
</div>
<div class="wpaicg-link-item-default" style="display: none">
    <div class="wpaicg-link-item">
        <span class="wpaicg-link-close">&times;</span>
        <p>
            <label><strong>URL</strong></label>
            <textarea class="wpaicg-link-URL"></textarea>
        </p>
        <p>
            <label><strong>Description</strong></label>
            <textarea class="wpaicg-link-Description"></textarea>
        </p>
    </div>
</div>
<div class="wpaicg-company-item-default" style="display: none">
    <div class="wpaicg-company-item">
        <span class="wpaicg-company-close">&times;</span>
        <p>
            <label><strong>Company Name</strong></label>
            <textarea class="wpaicg-company-Company-Name"></textarea>
        </p>
        <p>
            <label><strong>Founder</strong></label>
            <textarea class="wpaicg-company-Company-Founder"></textarea>
        </p>
        <p>
            <label><strong>CEO</strong></label>
            <textarea class="wpaicg-company-Company-CEO"></textarea>
        </p>
        <p>
            <label><strong>Founded</strong></label>
            <textarea class="wpaicg-company-Company-Founded"></textarea>
        </p>
        <p>
            <label><strong>Location</strong></label>
            <textarea class="wpaicg-company-Company-Location"></textarea>
        </p>
        <p>
            <label><strong>Number of Employees</strong></label>
            <textarea class="wpaicg-company-Company-Employees"></textarea>
        </p>
        <p>
            <label><strong>Industry</strong></label>
            <textarea class="wpaicg-company-Company-Industry"></textarea>
        </p>
        <p>
            <label><strong>Products</strong></label>
            <textarea class="wpaicg-company-Company-Products"></textarea>
        </p>
        <p>
            <label><strong>Company Website</strong></label>
            <textarea class="wpaicg-company-Company-Website"></textarea>
        </p>
        <p>
            <label><strong>Company Email</strong></label>
            <textarea class="wpaicg-company-Company-Email"></textarea>
        </p>
        <p>
            <label><strong>Company Phone</strong></label>
            <textarea class="wpaicg-company-Company-Phone"></textarea>
        </p>
        <p>
            <label><strong>Company Address</strong></label>
            <textarea class="wpaicg-company-Company-Address"></textarea>
        </p>
        <p>
            <label><strong>Company Description</strong></label>
            <textarea class="wpaicg-company-Company-Description"></textarea>
        </p>
    </div>
</div>
<div class="wpaicg-event-item-default" style="display: none">
    <div class="wpaicg-event-item">
        <span class="wpaicg-event-close">&times;</span>
        <p>
            <label><strong>Event Name</strong></label>
            <textarea class="wpaicg-event-Event-Name"></textarea>
        </p>
        <p>
            <label><strong>Event Description</strong></label>
            <textarea class="wpaicg-event-Event-Description"></textarea>
        </p>
        <p>
            <label><strong>Event Date</strong></label>
            <textarea class="wpaicg-event-Event-Date"></textarea>
        </p>
        <p>
            <label><strong>Event Time</strong></label>
            <textarea class="wpaicg-event-Event-Time"></textarea>
        </p>
        <p>
            <label><strong>Event Organizer</strong></label>
            <textarea class="wpaicg-event-Event-Organizer"></textarea>
        <p>
            <label><strong>Event Location</strong></label>
            <textarea class="wpaicg-event-Event-Location"></textarea>
        </p>
        <p>
            <label><strong>Event URL</strong></label>
            <textarea class="wpaicg-event-Event-URL"></textarea>
        </p>
    </div>
</div>
<!-- pricing plans: name, features, price, url -->
<div class="wpaicg-pricing-item-default" style="display: none">
    <div class="wpaicg-pricing-item">
        <span class="wpaicg-pricing-close">&times;</span>
        <p>
            <label><strong>Pricing Plan Name</strong></label>
            <textarea class="wpaicg-pricing-Pricing-Plan-Name"></textarea>
        </p>
        <p>
            <label><strong>Pricing Plan Features</strong></label>
            <textarea class="wpaicg-pricing-Pricing-Plan-Features"></textarea>
        </p>
        <p>
            <label><strong>Pricing Plan Price</strong></label>
            <textarea class="wpaicg-pricing-Pricing-Plan-Price"></textarea>
        </p>
        <p>
            <label><strong>Pricing Plan URL</strong></label>
            <textarea class="wpaicg-pricing-Pricing-Plan-URL"></textarea>
        </p>
    </div>
</div>
<!-- contact list: name, email, phone, address, description, url -->
<div class="wpaicg-contact-item-default" style="display: none">
    <div class="wpaicg-contact-item">
        <span class="wpaicg-contact-close">&times;</span>
        <p>
            <label><strong>Contact Name</strong></label>
            <textarea class="wpaicg-contact-Contact-Name"></textarea>
        </p>
        <p>
            <label><strong>Contact Email</strong></label>
            <textarea class="wpaicg-contact-Contact-Email"></textarea>
        </p>
        <p>
            <label><strong>Contact Phone</strong></label>
            <textarea class="wpaicg-contact-Contact-Phone"></textarea>
        </p>
        <p>
            <label><strong>Contact Address</strong></label>
            <textarea class="wpaicg-contact-Contact-Address"></textarea>
        </p>
        <p>
            <label><strong>Contact Description</strong></label>
            <textarea class="wpaicg-contact-Contact-Description"></textarea>
        </p>
        <p>
            <label><strong>Contact URL</strong></label>
            <textarea class="wpaicg-contact-Contact-URL"></textarea>
        </p>
    </div>
</div>
<form action="" method="post" id="wpaicg_embeddings_form">
    <?php
    wp_nonce_field('wpaicg_embeddings_save');
    ?>
    <input type="hidden" name="action" value="wpaicg_embeddings">
    <div class="wpaicg-embeddings-success" style="padding: 10px;background: #fff;border-left: 2px solid #11ad6b;display: none">Record saved successfully</div>
    <div class="wpaicg-mb-10">
        <p><strong>Content Type</strong></p>
        <select name="type" class="regular-text wpaicg-select-entry-type">
            <option value="free">Free Text</option>
            <option value="company">Company Profile</option>
            <option value="contact">Contact List</option>
            <option value="event">Event</option>
            <option value="faq">FAQ</option>
            <option value="knowledge">KnowledgeBase</option>
            <option value="pricing">Pricing Plan</option>
            <option value="product">Product</option>
            <option value="link">URL</option>
            <option value="wooproduct">WooCommerce Product</option>
        </select>
    </div>
    <div class="wpaicg-mb-10 wpaicg-data-entry wpaicg-free">
        <p><strong>Content</strong></p>
        <textarea name="content" class="wpaicg-embeddings-content" rows="15"></textarea>
    </div>
    <div class="wpaicg-mb-10 wpaicg-data-entry wpaicg-faq" style="display: none">
        <div class="wpaicg-faq-list">
        </div>
        <button type="button" class="button button-primary btn-add-faq" style="width: 100%">Add More</button>
    </div>
    <div class="wpaicg-mb-10 wpaicg-data-entry wpaicg-knowledge" style="display: none">
        <div class="wpaicg-knowledge-list">
        </div>
        <button type="button" class="button button-primary btn-add-knowledge" style="width: 100%">Add More</button>
    </div>
    <div class="wpaicg-mb-10 wpaicg-data-entry wpaicg-product" style="display: none">
        <div class="wpaicg-product-list">
        </div>
        <button type="button" class="button button-primary btn-add-product" style="width: 100%">Add More</button>
    </div>
    <div class="wpaicg-mb-10 wpaicg-data-entry wpaicg-wooproduct" style="display: none">
        <div class="wpaicg-wooproduct-list">
        </div>
        <button type="button" class="button button-primary btn-add-wooproduct" style="width: 100%">Add More</button>
    </div>
    <div class="wpaicg-mb-10 wpaicg-data-entry wpaicg-link" style="display: none">
        <div class="wpaicg-link-list">
        </div>
        <button type="button" class="button button-primary btn-add-link" style="width: 100%">Add More</button>
    </div>
    <div class="wpaicg-mb-10 wpaicg-data-entry wpaicg-company" style="display: none">
        <div class="wpaicg-company-list">
        </div>
        <button type="button" class="button button-primary btn-add-company" style="width: 100%">Add More</button>
    </div>
    <div class="wpaicg-mb-10 wpaicg-data-entry wpaicg-event" style="display: none">
        <div class="wpaicg-event-list">
        </div>
        <button type="button" class="button button-primary btn-add-event" style="width: 100%">Add More</button>
    </div>
    <div class="wpaicg-mb-10 wpaicg-data-entry wpaicg-pricing" style="display: none">
        <div class="wpaicg-pricing-list">
        </div>
        <button type="button" class="button button-primary btn-add-pricing" style="width: 100%">Add More</button>
    </div>
    <div class="wpaicg-mb-10 wpaicg-data-entry wpaicg-contact" style="display: none">
        <div class="wpaicg-contact-list">
        </div>
        <button type="button" class="button button-primary btn-add-contact" style="width: 100%">Add More</button>
    </div>
    <button class="button button-primary">Save</button>
</form>
<script>
    jQuery(document).ready(function ($){
        var wpaicgFaqList = $('.wpaicg-faq-list');
        var wpaicgKnowledgeList = $('.wpaicg-knowledge-list');
        var wpaicgProductList = $('.wpaicg-product-list');
        var wpaicgWooProductList = $('.wpaicg-wooproduct-list');
        var wpaicgLinkList = $('.wpaicg-link-list');
        var wpaicgCompanyList = $('.wpaicg-company-list');
        var wpaicgEventList = $('.wpaicg-event-list');
        var wpaicgPricingList = $('.wpaicg-pricing-list');
        var wpaicgContactList = $('.wpaicg-contact-list');
        function wpaicgLoading(btn){
            btn.attr('disabled','disabled');
            if(!btn.find('spinner').length){
                btn.append('<span class="spinner"></span>');
            }
            btn.find('.spinner').css('visibility','unset');
        }
        function wpaicgRmLoading(btn){
            btn.removeAttr('disabled');
            btn.find('.spinner').remove();
        }
        $('.wpaicg-select-entry-type').on('change', function (){
            var type = $(this).val();
            $('.wpaicg-data-entry').hide();
            if(type !== 'free'){
                $('.wpaicg-'+type+'-list').empty();
                $('.wpaicg-'+type+'-list').append($('.wpaicg-'+type+'-item-default').html());
            }
            $('.wpaicg-'+$(this).val()).show();
        })
        $('.btn-add-faq').click(function (){
            wpaicgFaqList.append($('.wpaicg-faq-item-default').html());
        });
        $('.btn-add-knowledge').click(function (){
            wpaicgKnowledgeList.append($('.wpaicg-knowledge-item-default').html());
        });
        $('.btn-add-product').click(function (){
            wpaicgProductList.append($('.wpaicg-product-item-default').html());
        });
        $('.btn-add-wooproduct').click(function (){
            wpaicgWooProductList.append($('.wpaicg-wooproduct-item-default').html());
        });
        $('.btn-add-link').click(function (){
            wpaicgLinkList.append($('.wpaicg-link-item-default').html());
        });
        $('.btn-add-company').click(function (){
            wpaicgCompanyList.append($('.wpaicg-company-item-default').html());
        });
        $('.btn-add-event').click(function (){
            wpaicgEventList.append($('.wpaicg-event-item-default').html());
        });
        $('.btn-add-pricing').click(function (){
            wpaicgPricingList.append($('.wpaicg-pricing-item-default').html());
        });
        $('.btn-add-contact').click(function (){
            wpaicgContactList.append($('.wpaicg-contact-item-default').html());
        });
        $(document).on('click','.wpaicg-knowledge-close,.wpaicg-faq-close,.wpaicg-product-close,.wpaicg-wooproduct-close,.wpaicg-link-close,.wpaicg-company-close,.wpaicg-event-close,.wpaicg-pricing-close,.wpaicg-contact-close', function (e){
            var btn = $(e.currentTarget);
            btn.parent().remove();
        });
        var wpaicg_types = {
            faq: ['Question','Answer'],
            knowledge: ['Topic','Description'],
            product: ['Product-Id','Product-Name','Product-Description','Product-Price','Product-URL'],
            wooproduct: ['Product-SKU','Product-Name','Product-Description','Product-RegularPrice','Product-URL','Product-SalePrice','Product-StockStatus'],
            link: ['URL','Description'],
            company: ['Company-Name','Company-Founder','Company-CEO','Company-Founded','Company-Location','Company-Employees','Company-Industry','Company-Products','Company-Website','Company-Email','Company-Phone','Company-Address','Company-Description'],
            event: ['Event-Name','Event-Date','Event-Time','Event-Location','Event-Description','Event-URL', 'Event-Organizer'],
            pricing: ['Pricing-Plan-Name','Pricing-Plan-Features','Pricing-Plan-Price','Pricing-Plan-URL'],
            contact: ['Contact-Name','Contact-Email','Contact-Phone','Contact-Address','Contact-URL','Contact-Description']
        }
        $('#wpaicg_embeddings_form').on('submit', function (e){
            var form = $(e.currentTarget);
            var btn = form.find('button');
            var type = $('.wpaicg-select-entry-type').val();
            var has_empty = false;
            var content;
            if(type !== 'free'){
                var custom_content = '';
                $('.wpaicg-'+type+'-list .wpaicg-'+type+'-item').each(function (idx, item){
                    $.each(wpaicg_types[type], function (idy, name){
                        var input_name = $(item).find('.wpaicg-'+type+'-'+name);
                        if(input_name !== undefined){
                            if(input_name.val() !== '') {
                                custom_content += name + ': ' + input_name.val()+"\n";
                            }
                            else{
                                has_empty = true;
                            }
                        }
                        else{
                            has_empty = true;
                        }
                    })
                });
                content = custom_content;
            }
            else{
                content = $('.wpaicg-embeddings-content').val();
            }
            if(has_empty){
                alert('Ensure that all fields are filled in.');
                return false;
            }
            if(type !== 'free'){
                $('.wpaicg-embeddings-content').val(custom_content);
            }
            if(content === ''){
                alert('Please insert content')
            }
            else{
                var data = form.serialize();
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php')?>',
                    data: data,
                    dataType: 'JSON',
                    type: 'POST',
                    beforeSend: function (){
                        wpaicgLoading(btn)
                    },
                    success: function (res){
                        wpaicgRmLoading(btn);
                        if(res.status === 'success'){
                            $('.wpaicg-embeddings-success').show();
                            $('.wpaicg-embeddings-content').val('');
                            if(type !== 'free'){
                                $('.wpaicg-'+type+'-list').empty();
                            }
                            setTimeout(function (){
                                $('.wpaicg-embeddings-success').hide();
                            },2000)
                        }
                        else{
                            alert(res.msg)
                        }
                    },
                    error: function (){
                        wpaicgRmLoading(btn);
                        alert('Something went wrong');
                    }
                })
            }
            return false;
        })
    })
</script>
