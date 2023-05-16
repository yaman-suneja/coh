<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<style>
    /* Container */
    .wpaicg_form_container {
        padding: 30px;
        max-width: auto;
    }

    /* Form elements */
    .wpaicg_form_container select,
    .wpaicg_form_container textarea {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #d1d1d1;
        border-radius: 4px;
        font-size: 14px;
        margin-bottom: 20px;
    }

    /* Buttons */
    .wpaicg_form_container button {
        padding: 10px 15px;
        font-size: 14px;
        border-radius: 4px;
        cursor: pointer;
        margin-right: 10px;
    }

    .wpaicg_form_container .wpaicg_generator_button {
        background-color: #2271B1;
        color: #ffffff;
        border: none;
    }

    .wpaicg_form_container .wpaicg_generator_stop {
        background-color: #dc3232;
        color: #ffffff;
        border: none;
        display: none;
    }

    /* Spinner */
    .wpaicg_form_container .spinner {
        display: inline-block;
        visibility: hidden;
        vertical-align: middle;
        margin-left: 5px;
    }

    /* Textarea */
    .wpaicg_prompt {
        height: auto !important;
        min-height: 100px;
        resize: vertical;
    }

    /* Notice text */
    .wpaicg_notice_text_pg {
        padding: 10px;
        background-color: #F8DC6F;
        text-align: left;
        margin-bottom: 12px;
        color: #000;
        box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
    }
    /* add border for table */
    .wpaicg_playground_table {
    max-width: auto;
    width: 100%;
}


</style>
<div class="wpaicg-grid-three" style="margin-top: 20px;">
    <div class="wpaicg-grid-1">
        <table>
        <tbody>
        <tr>
            <td>
                <h3>Category</h3>
                <select id="category_select" class="regular-text">
                    <option value="">Select a category</option>
                    <option value="wordpress">WordPress</option>
                    <option value="blogging">Blogging</option>
                    <option value="writing">Writing</option>
                    <option value="ecommerce">E-commerce</option>
                    <option value="online_business">Online Business</option>
                    <option value="entrepreneurship">Entrepreneurship</option>
                    <option value="seo">SEO</option>
                    <option value="social_media">Social Media</option>
                    <option value="digital_marketing">Digital Marketing</option>
                    <option value="woocommerce">WooCommerce</option>
                    <option value="content_creation">Content Creation</option>
                </select>
            </td>
        </tr>
        <tr class="sample_prompts_row" style="display: none;">
            <td>
                <h3>Prompt</h3>
                <select id="sample_prompts" class="regular-text">
                    <option value="">Select a prompt</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>
                <h3>Custom Prompt</h3>
                <textarea type="text" class="regular-text wpaicg_prompt">Write a blog post on how to effectively monetize a blog, discussing various methods such as affiliate marketing, sponsored content, and display advertising, as well as tips for maximizing revenue.</textarea>
                &nbsp;<button class="button wpaicg_generator_button"><span class="spinner"></span>Generate</button>
                &nbsp;<button class="button button-primary wpaicg_generator_stop">Stop</button>
            </td>
        </tr>
        </tbody>
        </table>
    </div>
    <div class="wpaicg-grid-2">
    <?php
                wp_editor('','wpaicg_generator_result', array('media_buttons' => true, 'textarea_name' => 'wpaicg_generator_result'));
                ?>
                <p class="wpaicg-playground-buttons">
                    <button class="button button-primary wpaicg-playground-save">Save as Draft</button>
                    <button class="button wpaicg-playground-clear">Clear</button>
                </p>
        
    
    </div>
</div>

<script>
    jQuery(document).ready(function ($){
        // Define the prompts
        var prompts = [
            {category: 'wordpress', prompt: 'Write a beginner-friendly tutorial on how to set up a secure and optimized WordPress website, focusing on security measures, performance enhancements, and best practices.'},
            {category: 'wordpress', prompt: 'Create a list of essential WordPress plugins for various niches, explaining their features, use cases, and benefits for website owners.'},
            {category: 'wordpress', prompt: 'Develop an in-depth guide on how to improve the loading speed of a WordPress website, covering hosting, caching, image optimization, and more.'},
            {category: 'wordpress', prompt: 'Write an article on how to choose the perfect WordPress theme for a specific business niche, taking into account design, functionality, and customization options.'},
            {category: 'wordpress', prompt: 'Create a comprehensive guide on managing a WordPress website, including updating themes and plugins, performing backups, and monitoring site health.'},
            {category: 'wordpress', prompt: 'Write a tutorial on how to create a custom WordPress theme from scratch, covering design principles, template hierarchy, and best practices for coding.'},
            {category: 'wordpress', prompt: 'Develop a resource guide on how to leverage WordPress Multisite to manage multiple websites efficiently, including setup, management, and use cases.'},
            {category: 'wordpress', prompt: 'Write an article on the benefits of using WooCommerce for e-commerce websites, including features, extensions, and comparisons to other e-commerce platforms.'},
            {category: 'wordpress', prompt: 'Create a guide on how to optimize a WordPress website for search engines, focusing on SEO-friendly themes, plugins, and on-page optimization techniques.'},
            {category: 'wordpress', prompt: 'Write a case study on a successful WordPress website, detailing its design, growth strategies, and the impact of its content on its target audience.'},
            {category: 'blogging', prompt: 'Write a blog post on how to effectively monetize a blog, discussing various methods such as affiliate marketing, sponsored content, and display advertising, as well as tips for maximizing revenue.'},
            {category: 'blogging', prompt: 'Write a blog post about the importance of networking and collaboration in the blogging community, including practical tips for building relationships and partnering with other bloggers and influencers.'},
            {category: 'blogging', prompt: 'Create a blog post that explores various content formats for blogging, such as written articles, podcasts, and videos, and discusses their pros and cons, as well as strategies for selecting the best format for a specific audience.'},
            {category: 'blogging', prompt: 'Write a blog post detailing the essential elements of a successful blog design and layout, focusing on user experience and visual appeal.'},
            {category: 'blogging', prompt: 'Write a blog post discussing the importance of authentic storytelling in blogging and how it can enhance audience engagement and brand loyalty.'},
            {category: 'blogging', prompt: 'Write a blog post about leveraging social media for blog promotion, including tips on cross-platform marketing and strategies for increasing blog visibility.'},
            {category: 'blogging', prompt: 'Write a blog post exploring the role of search engine optimization in blogging success, with a step-by-step guide on optimizing blog content for improved search rankings.'},
            {category: 'blogging', prompt: 'Write a blog post about the value of developing a consistent posting schedule and editorial calendar, sharing strategies for maintaining productivity and audience interest.'},
            {category: 'blogging', prompt: 'Write a blog post about the benefits and challenges of embracing a lean startup methodology, with actionable tips for implementing this approach in a new business venture.'},
            {category: 'writing', prompt: 'Write an article discussing the benefits of incorporating mindfulness and meditation practices into daily routines for improved mental health.'},
            {category: 'writing', prompt: 'Write an article exploring the impact of sustainable agriculture practices on global food security and the environment.'},
            {category: 'writing', prompt: 'Write an article analyzing the role of renewable energy sources in combating climate change and reducing global carbon emissions.'},
            {category: 'writing', prompt: 'Write an article examining the history and cultural significance of traditional art forms from around the world.'},
            {category: 'writing', prompt: 'Write an article discussing the importance of financial literacy and practical tips for managing personal finances.'},
            {category: 'writing', prompt: 'Write an article highlighting advancements in telemedicine and its potential to transform healthcare access and delivery.'},
            {category: 'writing', prompt: 'Write an article discussing the ethical implications of artificial intelligence and its potential effects on society.'},
            {category: 'writing', prompt: 'Write an article exploring the benefits of lifelong learning and its impact on personal and professional growth.'},
            {category: 'writing', prompt: 'Write an article analyzing the role of urban planning and design in creating sustainable and livable cities.'},
            {category: 'writing', prompt: 'Write an article discussing the influence of technology on modern communication and its effect on human relationships.'},
            {category: 'ecommerce', prompt: 'Design a digital marketing campaign for an online fashion store, focusing on customer engagement and boosting sales.'},
            {category: 'ecommerce', prompt: 'Create a step-by-step guide for optimizing an e-commerce websites user experience, including navigation, product presentation, and checkout process.'},
            {category: 'ecommerce', prompt: 'Write a persuasive email sequence for a cart abandonment campaign, aimed at encouraging customers to complete their purchases.'},
            {category: 'ecommerce', prompt: 'Develop a content strategy for an e-commerce blog, focusing on topics that will educate, inform, and entertain potential customers.'},
            {category: 'ecommerce', prompt: 'Outline the benefits and features of a new e-commerce platform designed to simplify the process of setting up and managing an online store.'},
            {category: 'ecommerce', prompt: 'Create a video script for a product demonstration that highlights the unique selling points of an innovative kitchen gadget.'},
            {category: 'ecommerce', prompt: 'Design a customer loyalty program for an e-commerce business, focusing on rewards, incentives, and strategies to drive repeat purchases.'},
            {category: 'ecommerce', prompt: 'Write a case study showcasing the successful implementation of an e-commerce solution for a small brick-and-mortar retailer.'},
            {category: 'ecommerce', prompt: 'Develop an infographic that illustrates the growth of e-commerce, including key statistics, trends, and milestones in the industry.'},
            {category: 'ecommerce', prompt: 'Create a series of social media posts for an e-commerce brand that showcases their products and engages their target audience.'},
            {category: 'online_business', prompt: 'Create a comprehensive guide on selecting the best e-commerce platform for a new online business, considering features, pricing, and scalability.'},
            {category: 'online_business', prompt: 'Develop a social media marketing plan for a small online business, focusing on choosing the right platforms, content creation, and audience engagement.'},
            {category: 'online_business', prompt: 'Write an in-depth article on utilizing search engine optimization (SEO) strategies to drive organic traffic to an online business website.'},
            {category: 'online_business', prompt: 'Design a webinar series that teaches aspiring entrepreneurs the essentials of building and managing a successful online business.'},
            {category: 'online_business', prompt: 'Create a resource guide on the top tools and software solutions for managing an online business, covering inventory management, marketing, and customer service.'},
            {category: 'online_business', prompt: 'Write a case study about a successful online business that pivoted during challenging times and thrived through innovation and adaptability.'},
            {category: 'online_business', prompt: 'Develop a list of best practices for creating an engaging and visually appealing online business website that attracts customers and drives sales.'},
            {category: 'online_business', prompt: 'Outline a customer support strategy for an online business, focusing on communication channels, response times, and customer satisfaction.'},
            {category: 'online_business', prompt: 'Write an article on the importance of branding and visual identity for an online business, including tips for creating a consistent and memorable brand.'},
            {category: 'online_business', prompt: 'Create a guide on using email marketing to nurture leads and convert them into loyal customers for an online business.'}, 
            {category: 'entrepreneurship', prompt: 'Develop a step-by-step guide on how to identify and validate a profitable niche for a new business venture, including market research and competitor analysis.'},
            {category: 'entrepreneurship', prompt: 'Write an article on the most effective funding options for startups, exploring crowdfunding, angel investors, venture capital, and bootstrapping.'},
            {category: 'entrepreneurship', prompt: 'Create a comprehensive guide on building a strong team for a startup, focusing on hiring strategies, team culture, and effective communication.'},
            {category: 'entrepreneurship', prompt: 'Design a video tutorial series on creating a successful business plan, covering executive summary, market analysis, marketing strategy, and financial projections.'},
            {category: 'entrepreneurship', prompt: 'Write a case study on a successful entrepreneur who overcame significant challenges and setbacks on their journey to building a thriving business.'},
            {category: 'entrepreneurship', prompt: 'Develop a list of essential legal considerations for starting a new business, including business structure, licensing, permits, and intellectual property protection.'},
            {category: 'entrepreneurship', prompt: 'Outline a guide on how to develop and maintain a healthy work-life balance as an entrepreneur, with a focus on time management, delegation, and self-care.'},
            {category: 'entrepreneurship', prompt: 'Write an article on the importance of networking for entrepreneurs, including strategies for building connections, maintaining relationships, and leveraging partnerships.'},
            {category: 'entrepreneurship', prompt: 'Create a resource guide on top tools and technologies for startups, covering project management, communication, financial management, and customer relationship management.'},
            {category: 'entrepreneurship', prompt: 'Develop an in-depth guide on how to effectively pivot a business when faced with unexpected challenges, including recognizing the need for change and implementing a new strategy.'},
            {category: 'seo', prompt: 'Write an in-depth guide on conducting comprehensive keyword research for website content, focusing on understanding user intent, search volume, and competition.'},
            {category: 'seo', prompt: 'Develop a blog post on the essential on-page SEO factors that every website owner should know, including proper URL structures, title tags, header tags, and meta descriptions.'},
            {category: 'seo', prompt: 'Create a comprehensive guide on link-building strategies for improving website authority and search rankings, covering techniques such as guest blogging, broken link building, and outreach.'},
            {category: 'seo', prompt: 'Write an article about the impact of website speed on SEO and user experience, discussing tools and techniques for analyzing and improving site performance.'},
            {category: 'seo', prompt: 'Develop a tutorial on how to create SEO-friendly content that appeals to both search engines and human readers, focusing on readability, keyword usage, and information value.'},
            {category: 'seo', prompt: 'Write a blog post about the importance of mobile-first indexing and responsive web design in modern SEO, including tips for optimizing websites for mobile devices.'},
            {category: 'seo', prompt: 'Create a guide on how to use Google Search Console effectively for monitoring and improving website SEO performance, including features such as index coverage reports, sitemaps, and search analytics.'},
            {category: 'seo', prompt: 'Write an article discussing the role of voice search in SEO, highlighting strategies for optimizing website content for voice search queries and emerging trends in voice search technology.'},
            {category: 'seo', prompt: 'Develop a blog post about the significance of user experience (UX) in SEO, including tips for enhancing website navigation, layout, and overall user satisfaction to improve search rankings.'},
            {category: 'seo', prompt: 'Create an article on the importance of local SEO for small businesses, focusing on strategies such as Google My Business optimization, citation building, and local content creation.'},
            {category: 'social_media', prompt: 'Write an article on the most effective strategies for growing a brands presence on social media platforms, including content creation, engagement, and advertising.'},
            {category: 'social_media', prompt: 'Develop a blog post about the benefits of using social media analytics to improve marketing efforts, with tips on interpreting data and making data-driven decisions.'},
            {category: 'social_media', prompt: 'Create a guide on how to create compelling visual content for social media platforms, focusing on elements such as color, typography, and composition.'},
            {category: 'social_media', prompt: 'Craft an in-depth article on leveraging user-generated content to boost brand authenticity and increase engagement on social media platforms.'},
            {category: 'social_media', prompt: 'Write a comprehensive tutorial on optimizing social media profiles for search engines, highlighting the importance of keywords, descriptions, and profile images.'},
            {category: 'social_media', prompt: 'Develop an informative blog post about the role of social media influencers in brand promotion, and outline the process of selecting and collaborating with the right influencers for a specific target audience.'},
            {category: 'social_media', prompt: 'Create a guide on how to effectively use social media scheduling tools to streamline content creation and posting, ensuring consistency and maximizing reach.'},
            {category: 'social_media', prompt: 'Write an article discussing the best practices for managing online communities on social media platforms, focusing on fostering positive interactions and handling negative feedback.'},
            {category: 'social_media', prompt: 'Develop a blog post about the importance of storytelling in social media marketing, with tips on creating engaging narratives that resonate with audiences and generate brand loyalty.'},
            {category: 'social_media', prompt: 'Create a guide on how to measure and analyze the return on investment (ROI) for social media advertising campaigns, including the key performance indicators (KPIs) to track and optimize.'},
            {category: 'digital_marketing', prompt: 'Write a comprehensive guide on creating and executing a successful content marketing strategy, including planning, creation, distribution, and measurement.'},
            {category: 'digital_marketing', prompt: 'Develop a blog post about the benefits of using marketing automation tools, with examples of popular platforms and use cases for different business sizes and industries.'},
            {category: 'digital_marketing', prompt: 'Create an article discussing the role of influencer marketing in modern advertising, with tips on selecting the right influencers, developing campaigns, and measuring success.'},
            {category: 'digital_marketing', prompt: 'Write an in-depth guide on utilizing search engine optimization (SEO) techniques for improving website visibility, including keyword research, on-page optimization, and off-page strategies.'},
            {category: 'digital_marketing', prompt: 'Craft a detailed article on the importance of social media marketing, highlighting effective platform-specific strategies, content planning, engagement techniques, and performance analysis.'},
            {category: 'digital_marketing', prompt: 'Develop a comprehensive guide on email marketing best practices, covering list building, segmentation, email design, personalization, automation, and metrics tracking.'},
            {category: 'digital_marketing', prompt: 'Write an informative blog post about the advantages of data-driven marketing, including insights on collecting, analyzing, and applying data to enhance targeting, personalization, and campaign effectiveness.'},
            {category: 'digital_marketing', prompt: 'Create an article exploring the benefits of video marketing, with tips on producing engaging content, optimizing for search engines, and leveraging various distribution channels.'},
            {category: 'digital_marketing', prompt: 'Write a guide on implementing effective pay-per-click (PPC) advertising campaigns, discussing budget allocation, keyword targeting, ad copywriting, landing page optimization, and performance analysis.'},
            {category: 'digital_marketing', prompt: 'Develop a blog post on the role of content repurposing in digital marketing, providing strategies for transforming existing content into different formats and leveraging multiple distribution channels.'},
            {category: 'woocommerce', prompt: 'Write a comprehensive guide on optimizing WooCommerce stores for maximum performance, discussing topics such as caching, image optimization, database cleaning, and choosing the right hosting environment.'},
            {category: 'woocommerce', prompt: 'Create an in-depth tutorial on setting up a successful WooCommerce store from scratch, covering aspects like choosing the right theme, setting up payment gateways, configuring shipping options, and managing inventory.'},
            {category: 'woocommerce', prompt: 'Develop an article on the top WooCommerce plugins that can enhance an online store’s functionality, covering areas such as analytics, email marketing, product recommendations, and customer support.'},
            {category: 'woocommerce', prompt: 'Write a detailed guide on implementing effective WooCommerce SEO strategies to improve search engine visibility, discussing on-page optimization, product schema markup, permalink structure, and sitemaps.'},
            {category: 'woocommerce', prompt: 'Craft an article on enhancing the user experience of a WooCommerce store, focusing on design principles, seamless navigation, product presentation, mobile responsiveness, and checkout optimization.'},
            {category: 'woocommerce', prompt: 'Write an in-depth article on maximizing sales and conversions for WooCommerce stores, covering strategies such as abandoned cart recovery, personalized product recommendations, and utilizing customer reviews.'},
            {category: 'woocommerce', prompt: 'Create a comprehensive guide on managing and scaling a WooCommerce store, discussing topics like inventory management, order fulfillment, automating processes, and expanding into new markets.'},
            {category: 'woocommerce', prompt: 'Develop an article on the importance of security for WooCommerce stores and best practices to protect against threats, including SSL certificates, secure hosting, regular backups, and security plugins.'},
            {category: 'woocommerce', prompt: 'Write a detailed tutorial on how to create and implement a successful marketing strategy for a WooCommerce store, covering email marketing, social media advertising, content marketing, and influencer partnerships.'},
            {category: 'woocommerce', prompt: 'Craft an article on the benefits of integrating third-party services and APIs with a WooCommerce store, focusing on areas such as payment processing, shipping solutions, marketing automation, and customer relationship management.'},
            {category: 'content_creation', prompt: 'Write an in-depth guide on brainstorming and developing unique content ideas, covering various research methods, mind mapping, and using audience feedback to inform content creation.'},
            {category: 'content_creation', prompt: 'Develop a comprehensive article on the principles of effective copywriting, focusing on techniques such as writing compelling headlines, utilizing storytelling, and creating persuasive calls to action.'},
            {category: 'content_creation', prompt: 'Create a detailed tutorial on how to structure and format long-form content for maximum readability and engagement, discussing elements like headings, lists, images, and content flow.'},
            {category: 'content_creation', prompt: 'Craft a blog post about the role of visual storytelling in content creation, with tips on using images, videos, and infographics to enhance the impact of written content and engage diverse audiences.'},
            {category: 'content_creation', prompt: 'Write an informative guide on optimizing content for search engines, including keyword research, proper use of headings and meta tags, internal and external linking, and image optimization.'},
            {category: 'content_creation', prompt: 'Develop an article discussing the importance of editorial calendars in content creation, covering aspects like planning, organization, collaboration, and ensuring consistent content output.'},
            {category: 'content_creation', prompt: 'Write a comprehensive guide on using various multimedia formats in content creation, such as podcasts, webinars, and interactive content, to cater to different audience preferences and enhance engagement.'},
            {category: 'content_creation', prompt: 'Create a blog post about the role of user-generated content in content marketing, with tips on encouraging audience participation, curating submissions, and leveraging this content for promotional purposes.'},
            {category: 'content_creation', prompt: 'Craft a detailed article on repurposing existing content for different platforms and formats, such as transforming blog posts into infographics, videos, or social media snippets to maximize reach and engagement.'},
            {category: 'content_creation', prompt: 'Write an in-depth tutorial on incorporating storytelling techniques into content creation, including character development, conflict resolution, and narrative structure to create engaging and memorable content.'}
        ];
        // Function to handle category selection
        $('#category_select').on('change', function() {
            var selectedCategory = $(this).val();
            if (selectedCategory) {
                // Clear and populate the prompts dropdown
                $('#sample_prompts').html('<option value="">Select a prompt</option>');
                prompts.forEach(function(promptObj) {
                    if (promptObj.category === selectedCategory) {
                        $('#sample_prompts').append('<option value="' + promptObj.prompt + '">' + promptObj.prompt + '</option>');
                    }
                });
                $('.sample_prompts_row').show();
            } else {
                // Hide the prompts dropdown and clear its value
                $('.sample_prompts_row').hide();
                $('#sample_prompts').val('');
            }
        });

        // Function to handle sample prompt selection
        $('#sample_prompts').on('change', function() {
            var selectedPrompt = $(this).val();
            if (selectedPrompt) {
                // Clear the textarea and set the selected prompt
                $('.wpaicg_prompt').val(selectedPrompt);
            }
        });
        var wpaicg_generator_working = false;
        var eventGenerator = false;
        var wpaicg_limitLines = 1;
        function stopOpenAIGenerator(){
            $('.wpaicg-playground-buttons').show();
            $('.wpaicg_generator_stop').hide();
            wpaicg_generator_working = false;
            $('.wpaicg_generator_button .spinner').hide();
            $('.wpaicg_generator_button').removeAttr('disabled');
            eventGenerator.close();
        }
        $('.wpaicg_generator_button').click(function(){
            var btn = $(this);
            var title = $('.wpaicg_prompt').val();
            if(!wpaicg_generator_working && title !== ''){
                var count_line = 0;
                var wpaicg_generator_result = $('.wpaicg_generator_result');
                btn.attr('disabled','disabled');
                btn.find('.spinner').show();
                btn.find('.spinner').css('visibility','unset');
                wpaicg_generator_result.val('');
                wpaicg_generator_working = true;
                $('.wpaicg_generator_stop').show();
                eventGenerator = new EventSource('<?php echo esc_html(add_query_arg('wpaicg_stream','yes',site_url().'/index.php'));?>&title='+title+'&nonce=<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>');
                var editor = tinyMCE.get('wpaicg_generator_result');
                var basicEditor = true;
                if ( $('#wp-wpaicg_generator_result-wrap').hasClass('tmce-active') && editor ) {
                    basicEditor = false;
                }
                var currentContent = '';
                var wpaicg_newline_before = false;
                var wpaicg_response_events = 0;
                eventGenerator.onmessage = function (e) {
                    if(basicEditor){
                        currentContent = $('#wpaicg_generator_result').val();
                    }
                    else{
                        currentContent = editor.getContent();
                        currentContent = currentContent.replace(/<\/?p(>|$)/g, "");
                    }
                    if(e.data === "[DONE]"){
                        count_line += 1;
                        if(basicEditor) {
                            $('#wpaicg_generator_result').val(currentContent+'\n\n');
                        }
                        else{
                            editor.setContent(currentContent+'\n\n');
                        }
                        wpaicg_response_events = 0;
                    }
                    else{
                        var result = JSON.parse(e.data);
                        if(result.error !== undefined){
                            var content_generated = result.error.message;
                        }
                        else{
                            var content_generated = result.choices[0].delta !== undefined ? (result.choices[0].delta.content !== undefined ? result.choices[0].delta.content : '') : result.choices[0].text;
                        }
                        if((content_generated === '\n' || content_generated === ' \n' || content_generated === '.\n' || content_generated === '\n\n' || content_generated === '.\n\n') && wpaicg_response_events > 0 && currentContent !== ''){
                            if(!wpaicg_newline_before) {
                                wpaicg_newline_before = true;
                                if(basicEditor){
                                    $('#wpaicg_generator_result').val(currentContent+'<br /><br />');
                                }
                                else{
                                    editor.setContent(currentContent+'<br /><br />');
                                }
                            }
                        }
                        else if(content_generated === '\n' && wpaicg_response_events === 0  && currentContent === ''){

                        }
                        else{
                            wpaicg_newline_before = false;
                            wpaicg_response_events += 1;
                            if(basicEditor){
                                $('#wpaicg_generator_result').val(currentContent+content_generated);
                            }
                            else{
                                editor.setContent(currentContent+content_generated);
                            }
                        }
                    }
                    if(count_line === wpaicg_limitLines){
                        stopOpenAIGenerator();
                    }
                };
                eventGenerator.onerror = function (e) {
                };
            }
        });
        $('.wpaicg_generator_stop').click(function (){
            stopOpenAIGenerator();
        });
        $('.wpaicg-playground-clear').click(function (){
            // $('.wpaicg_prompt').val('');
            var editor = tinyMCE.get('wpaicg_generator_result');
            var basicEditor = true;
            if ( $('#wp-wpaicg_generator_result-wrap').hasClass('tmce-active') && editor ) {
                basicEditor = false;
            }
            if(basicEditor){
                $('#wpaicg_generator_result').val('');
            }
            else{
                editor.setContent('');
            }
        });
        $('.wpaicg-playground-save').click(function (){
            var wpaicg_draft_btn = $(this);
            var title = $('.wpaicg_prompt').val();
            var editor = tinyMCE.get('wpaicg_generator_result');
            var basicEditor = true;
            if ( $('#wp-wpaicg_generator_result-wrap').hasClass('tmce-active') && editor ) {
                basicEditor = false;
            }
            var content = '';
            if (basicEditor){
                content = $('#wpaicg_generator_result').val();
            }
            else{
                content = editor.getContent();
            }
            if(title === ''){
                alert('Please enter title');
            }
            else if(content === ''){
                alert('Please wait content generated');
            }
            else{
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php')?>',
                    data: {title: title, content: content, action: 'wpaicg_save_draft_post_extra','nonce': '<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>'},
                    dataType: 'json',
                    type: 'POST',
                    beforeSend: function (){
                        wpaicg_draft_btn.attr('disabled','disabled');
                        wpaicg_draft_btn.append('<span class="spinner"></span>');
                        wpaicg_draft_btn.find('.spinner').css('visibility','unset');
                    },
                    success: function (res){
                        wpaicg_draft_btn.removeAttr('disabled');
                        wpaicg_draft_btn.find('.spinner').remove();
                        if(res.status === 'success'){
                            window.location.href = '<?php echo admin_url('post.php')?>?post='+res.id+'&action=edit';
                        }
                        else{
                            alert(res.msg);
                        }
                    },
                    error: function (){
                        wpaicg_draft_btn.removeAttr('disabled');
                        wpaicg_draft_btn.find('.spinner').remove();
                        alert('Something went wrong');
                    }
                });
            }
        })
    })
</script>
