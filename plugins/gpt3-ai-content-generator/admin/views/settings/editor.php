<?php
$wpaicg_editor_button_menus = get_option('wpaicg_editor_button_menus', []);
$wpaicg_editor_change_action = get_option('wpaicg_editor_change_action', 'below');
if(!is_array($wpaicg_editor_button_menus) || count($wpaicg_editor_button_menus) == 0){
    $wpaicg_editor_button_menus = \WPAICG\WPAICG_Editor::get_instance()->wpaicg_edit_default_menus;
}
?>
<style>
    .wpaicg_editor_menu{
        display: flex;
        justify-content: space-between;
        position: relative;
        background: #d7d7d7;
        margin-bottom: 10px;
        padding: 10px;
        border-radius: 3px;
    }
    .wpaicg_editor_menu > div{
        width: 48%;
    }
    .wpaicg_editor_menu label{
        display: block;
        font-weight: bold;
    }
    .wpaicg_editor_menu input{
        width: 100%
    }
    .wpaicg_editor_menu_close{
        position: absolute;
        top: 2px;
        right: 2px;
        width: 20px;
        height: 20px;
        border-radius: 2px;
        background: #c70000;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 20px;
        color: #fff;
        cursor: pointer;
    }
    .wpaicg_editor_menu_help{
        font-size: 12px;
        font-style: italic;
    }
    .wpaicg_editor_add_menu{
        display: block!important;
        margin-bottom: 10px!important;
        width: 100%;
    }
</style>
<div id="tabs-9">
    <p>AI Assistant is a feature that allows you to add a button to the WordPress editor that will help you to create content. You can add your own menus with your own prompts.</p>
    <p>AI Assistant is compatible with both Gutenberg and Classic Editor.</p>
    <p>Use the form below to add, modify, or remove menus as needed.</p>
    <div class="wpaicg_editor_menus">
        <?php
        if($wpaicg_editor_button_menus && is_array($wpaicg_editor_button_menus) && count($wpaicg_editor_button_menus)){
            $key = 0;
            foreach ($wpaicg_editor_button_menus as $wpaicg_editor_button_menu){
                if(isset($wpaicg_editor_button_menu['name']) && isset($wpaicg_editor_button_menu['prompt']) && $wpaicg_editor_button_menu['name'] != '' && $wpaicg_editor_button_menu['prompt'] != ''){
                ?>
                <div class="wpaicg_editor_menu">
                    <span class="wpaicg_editor_menu_close">&times;</span>
                    <div>
                        <label>Menu Item</label>
                        <input name="wpaicg_editor_button_menus[<?php echo esc_html($key)?>][name]" class="wpaicg_editor_menu_name" type="text" value="<?php echo esc_html($wpaicg_editor_button_menu['name'])?>">
                    </div>
                    <div>
                        <label>Prompt</label>
                        <input name="wpaicg_editor_button_menus[<?php echo esc_html($key)?>][prompt]" class="wpaicg_editor_menu_prompt" type="text" value="<?php echo esc_html($wpaicg_editor_button_menu['prompt'])?>">
                        <span class="wpaicg_editor_menu_help">Ensure <code>[text]</code> is included in your prompt.</span>
                    </div>
                </div>
                <?php
                    $key++;
                }
            }
        }
        ?>
    </div>
    <button class="button button-primary wpaicg_editor_add_menu" type="button">Add More</button>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label">Content Position</label>
        <select class="regular-text" name="wpaicg_editor_change_action">
            <option<?php echo $wpaicg_editor_change_action == 'below' ? ' selected':'';?> value="below">Below</option>
            <option<?php echo $wpaicg_editor_change_action == 'above' ? ' selected':'';?> value="above">Above</option>
        </select>
    </div>
</div>
<script>
    jQuery(document).ready(function ($){
        $(document).on('click','.wpaicg_editor_menu_close', function (e){
            $(e.currentTarget).closest('.wpaicg_editor_menu').remove();
            wpaicgSortMenu();
        });
        function wpaicgSortMenu(){
            $('.wpaicg_editor_menu').each(function (idx, item){
                $(item).find('.wpaicg_editor_menu_name').attr('name','wpaicg_editor_button_menus['+idx+'][name]');
                $(item).find('.wpaicg_editor_menu_prompt').attr('name','wpaicg_editor_button_menus['+idx+'][prompt]');
            })
        }
        $('.wpaicg_editor_add_menu').click(function (){
            let html = '<div class="wpaicg_editor_menu">';
            html += '<span class="wpaicg_editor_menu_close">&times;</span>';
            html += '<div>';
            html += '<label>Menu name</label>';
            html += '<input class="wpaicg_editor_menu_name" type="text">';
            html += '</div><div>';
            html += '<label>Prompt</label>';
            html += '<input class="wpaicg_editor_menu_prompt" type="text">';
            html += '<span class="wpaicg_editor_menu_help">Ensure <code>[text]</code> is included in your prompt.</span>';
            html += '</div></div>';
            $('.wpaicg_editor_menus').append(html);
            wpaicgSortMenu();
        });
    })
</script>
