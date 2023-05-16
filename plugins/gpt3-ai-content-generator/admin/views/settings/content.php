<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div id="tabs-2">
    <div class="wpcgai_form_row">
        <p><b>Language, Style and Tone</b></p>
        <label class="wpcgai_label">Language:</label>
        <select class="regular-text" id="label_wpai_language"  name="wpaicg_settings[wpai_language]" >
            <option value="en" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'en' ? 'selected' : '' ) ;
?>>English</option>
            <option value="af" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'af' ? 'selected' : '' ) ;
?>>Afrikaans</option>
            <option value="ar" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'ar' ? 'selected' : '' ) ;
?>>Arabic</option>
            <option value="an" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'an' ? 'selected' : '' ) ;
?>>Armenian</option>
            <option value="bs" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'bs' ? 'selected' : '' ) ;
?>>Bosnian</option>
            <option value="bg" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'bg' ? 'selected' : '' ) ;
?>>Bulgarian</option>
            <option value="zh" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'zh' ? 'selected' : '' ) ;
?>>Chinese (Simplified)</option>
            <option value="zt" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'zt' ? 'selected' : '' ) ;
?>>Chinese (Traditional)</option>
            <option value="hr" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'hr' ? 'selected' : '' ) ;
?>>Croatian</option>
            <option value="cs" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'cs' ? 'selected' : '' ) ;
?>>Czech</option>
            <option value="da" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'da' ? 'selected' : '' ) ;
?>>Danish</option>
            <option value="nl" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'nl' ? 'selected' : '' ) ;
?>>Dutch</option>
            <option value="et" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'et' ? 'selected' : '' ) ;
?>>Estonian</option>
            <option value="fil" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'fil' ? 'selected' : '' ) ;
?>>Filipino</option>
            <option value="fi" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'fi' ? 'selected' : '' ) ;
?>>Finnish</option>
            <option value="fr" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'fr' ? 'selected' : '' ) ;
?>>French</option>
            <option value="de" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'de' ? 'selected' : '' ) ;
?>>German</option>
            <option value="el" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'el' ? 'selected' : '' ) ;
?>>Greek</option>
            <option value="he" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'he' ? 'selected' : '' ) ;
?>>Hebrew</option>
            <option value="hi" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'hi' ? 'selected' : '' ) ;
?>>Hindi</option>
            <option value="hu" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'hu' ? 'selected' : '' ) ;
?>>Hungarian</option>
            <option value="id" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'id' ? 'selected' : '' ) ;
?>>Indonesian</option>
            <option value="it" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'it' ? 'selected' : '' ) ;
?>>Italian</option>
            <option value="ja" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'ja' ? 'selected' : '' ) ;
?>>Japanese</option>
            <option value="ko" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'ko' ? 'selected' : '' ) ;
?>>Korean</option>
            <option value="lv" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'lv' ? 'selected' : '' ) ;
?>>Latvian</option>
            <option value="lt" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'lt' ? 'selected' : '' ) ;
?>>Lithuanian</option>
            <option value="ms" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'ms' ? 'selected' : '' ) ;
?>>Malay</option>
            <option value="no" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'no' ? 'selected' : '' ) ;
?>>Norwegian</option>
            <option value="pl" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'pl' ? 'selected' : '' ) ;
?>>Polish</option>
            <option value="pt" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'pt' ? 'selected' : '' ) ;
?>>Portuguese</option>
            <option value="ro" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'ro' ? 'selected' : '' ) ;
?>>Romanian</option>
            <option value="ru" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'ru' ? 'selected' : '' ) ;
?>>Russian</option>
            <option value="sr" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'sr' ? 'selected' : '' ) ;
?>>Serbian</option>
            <option value="sk" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'sk' ? 'selected' : '' ) ;
?>>Slovak</option>
            <option value="sl" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'sl' ? 'selected' : '' ) ;
?>>Slovenian</option>
            <option value="es" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'es' ? 'selected' : '' ) ;
?>>Spanish</option>
            <option value="sv" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'sv' ? 'selected' : '' ) ;
?>>Swedish</option>
            <option value="th" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'th' ? 'selected' : '' ) ;
?>>Thai</option>
            <option value="tr" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'tr' ? 'selected' : '' ) ;
?>>Turkish</option>
            <option value="uk" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'uk' ? 'selected' : '' ) ;
?>>Ukranian</option>
            <option value="vi" <?php 
echo  ( esc_html( $existingValue['wpai_language'] ) == 'vi' ? 'selected' : '' ) ;
?>>Vietnamese</option>
        </select>
        <a class="wpcgai_help_link" href="https://gptaipower.com/supported-languages/" target="_blank">?</a>
    </div>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label">Writing Style:</label>
        <select class="regular-text" id="label_wpai_writing_style" name="wpaicg_settings[wpai_writing_style]" >
            <option value="infor" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'infor' ? 'selected' : '' ) ;
?>>Informative</option>
            <option value="acade" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'acade' ? 'selected' : '' ) ;
?>>Academic</option>
            <option value="analy" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'analy' ? 'selected' : '' ) ;
?>>Analytical</option>
            <option value="anect" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'anect' ? 'selected' : '' ) ;
?>>Anecdotal</option>
            <option value="argum" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'argum' ? 'selected' : '' ) ;
?>>Argumentative</option>
            <option value="artic" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'artic' ? 'selected' : '' ) ;
?>>Articulate</option>
            <option value="biogr" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'biogr' ? 'selected' : '' ) ;
?>>Biographical</option>
            <option value="blog" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'blog' ? 'selected' : '' ) ;
?>>Blog</option>
            <option value="casua" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'casua' ? 'selected' : '' ) ;
?>>Casual</option>
            <option value="collo" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'collo' ? 'selected' : '' ) ;
?>>Colloquial</option>
            <option value="compa" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'compa' ? 'selected' : '' ) ;
?>>Comparative</option>
            <option value="conci" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'conci' ? 'selected' : '' ) ;
?>>Concise</option>
            <option value="creat" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'creat' ? 'selected' : '' ) ;
?>>Creative</option>
            <option value="criti" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'criti' ? 'selected' : '' ) ;
?>>Critical</option>
            <option value="descr" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'descr' ? 'selected' : '' ) ;
?>>Descriptive</option>
            <option value="detai" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'detai' ? 'selected' : '' ) ;
?>>Detailed</option>
            <option value="dialo" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'dialo' ? 'selected' : '' ) ;
?>>Dialogue</option>
            <option value="direct" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'direct' ? 'selected' : '' ) ;
?>>Direct</option>
            <option value="drama" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'drama' ? 'selected' : '' ) ;
?>>Dramatic</option>
            <option value="evalu" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'evalu' ? 'selected' : '' ) ;
?>>Evaluative</option>
            <option value="emoti" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'emoti' ? 'selected' : '' ) ;
?>>Emotional</option>
            <option value="expos" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'expos' ? 'selected' : '' ) ;
?>>Expository</option>
            <option value="ficti" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'ficti' ? 'selected' : '' ) ;
?>>Fiction</option>
            <option value="histo" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'histo' ? 'selected' : '' ) ;
?>>Historical</option>
            <option value="journ" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'journ' ? 'selected' : '' ) ;
?>>Journalistic</option>
            <option value="lette" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'lette' ? 'selected' : '' ) ;
?>>Letter</option>
            <option value="lyric" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'lyric' ? 'selected' : '' ) ;
?>>Lyrical</option>
            <option value="metaph" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'metaph' ? 'selected' : '' ) ;
?>>Metaphorical</option>
            <option value="monol" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'monol' ? 'selected' : '' ) ;
?>>Monologue</option>
            <option value="narra" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'narra' ? 'selected' : '' ) ;
?>>Narrative</option>
            <option value="news" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'news' ? 'selected' : '' ) ;
?>>News</option>
            <option value="objec" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'objec' ? 'selected' : '' ) ;
?>>Objective</option>
            <option value="pasto" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'pasto' ? 'selected' : '' ) ;
?>>Pastoral</option>
            <option value="perso" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'perso' ? 'selected' : '' ) ;
?>>Personal</option>
            <option value="persu" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'persu' ? 'selected' : '' ) ;
?>>Persuasive</option>
            <option value="poeti" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'poeti' ? 'selected' : '' ) ;
?>>Poetic</option>
            <option value="refle" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'refle' ? 'selected' : '' ) ;
?>>Reflective</option>
            <option value="rheto" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'rheto' ? 'selected' : '' ) ;
?>>Rhetorical</option>
            <option value="satir" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'satir' ? 'selected' : '' ) ;
?>>Satirical</option>
            <option value="senso" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'senso' ? 'selected' : '' ) ;
?>>Sensory</option>
            <option value="simpl" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'simpl' ? 'selected' : '' ) ;
?>>Simple</option>
            <option value="techn" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'techn' ? 'selected' : '' ) ;
?>>Technical</option>
            <option value="theore" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'theore' ? 'selected' : '' ) ;
?>>Theoretical</option>
            <option value="vivid" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'vivid' ? 'selected' : '' ) ;
?>>Vivid</option>
            <option value="busin" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'busin' ? 'selected' : '' ) ;
?>>Business</option>
            <option value="repor" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'repor' ? 'selected' : '' ) ;
?>>Report</option>
            <option value="resea" <?php 
echo  ( esc_html( $existingValue['wpai_writing_style'] ) == 'resea' ? 'selected' : '' ) ;
?>>Research</option>
        </select>
        <a class="wpcgai_help_link" href="https://gptaipower.com/selecting-a-writing-style/" target="_blank">?</a>
    </div>

    <div class="wpcgai_form_row">
        <label class="wpcgai_label">Writing Tone:</label>
        <select class="regular-text" id="label_wpai_writing_tone" name="wpaicg_settings[wpai_writing_tone]" >
            <option value="formal" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'formal' ? 'selected' : '' ) ;
?>>Formal</option>
            <option value="asser" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'asser' ? 'selected' : '' ) ;
?>>Assertive</option>
            <option value="authoritative" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'authoritative' ? 'selected' : '' ) ;
?>>Authoritative</option>
            <option value="cheer" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'cheer' ? 'selected' : '' ) ;
?>>Cheerful</option>
            <option value="confident" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'confident' ? 'selected' : '' ) ;
?>>Confident</option>
            <option value="conve" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'conve' ? 'selected' : '' ) ;
?>>Conversational</option>
            <option value="factual" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'factual' ? 'selected' : '' ) ;
?>>Factual</option>
            <option value="friendly" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'friendly' ? 'selected' : '' ) ;
?>>Friendly</option>
            <option value="humor" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'humor' ? 'selected' : '' ) ;
?>>Humorous</option>
            <option value="informal" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'informal' ? 'selected' : '' ) ;
?>>Informal</option>
            <option value="inspi" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'inspi' ? 'selected' : '' ) ;
?>>Inspirational</option>
            <option value="neutr" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'neutr' ? 'selected' : '' ) ;
?>>Neutral</option>
            <option value="nostalgic" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'nostalgic' ? 'selected' : '' ) ;
?>>Nostalgic</option>
            <option value="polite" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'polite' ? 'selected' : '' ) ;
?>>Polite</option>
            <option value="profe" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'profe' ? 'selected' : '' ) ;
?>>Professional</option>
            <option value="romantic" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'romantic' ? 'selected' : '' ) ;
?>>Romantic</option>
            <option value="sarca" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'sarca' ? 'selected' : '' ) ;
?>>Sarcastic</option>
            <option value="scien" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'scien' ? 'selected' : '' ) ;
?>>Scientific</option>
            <option value="sensit" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'sensit' ? 'selected' : '' ) ;
?>>Sensitive</option>
            <option value="serious" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'serious' ? 'selected' : '' ) ;
?>>Serious</option>
            <option value="sincere" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'sincere' ? 'selected' : '' ) ;
?>>Sincere</option>
            <option value="skept" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'skept' ? 'selected' : '' ) ;
?>>Skeptical</option>
            <option value="suspenseful" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'suspenseful' ? 'selected' : '' ) ;
?>>Suspenseful</option>
            <option value="sympathetic" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'sympathetic' ? 'selected' : '' ) ;
?>>Sympathetic</option>
                <option value="curio" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'curio' ? 'selected' : '' ) ;
?>>Curious</option>
                <option value="disap" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'disap' ? 'selected' : '' ) ;
?>>Disappointed</option>
                <option value="encou" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'encou' ? 'selected' : '' ) ;
?>>Encouraging</option>
                <option value="optim" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'optim' ? 'selected' : '' ) ;
?>>Optimistic</option>
                <option value="surpr" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'surpr' ? 'selected' : '' ) ;
?>>Surprised</option>
                <option value="worry" <?php 
echo  ( esc_html( $existingValue['wpai_writing_tone'] ) == 'worry' ? 'selected' : '' ) ;
?>>Worried</option>
        </select>
        <a class="wpcgai_help_link" href="https://gptaipower.com/selecting-a-writing-tone/" target="_blank">?</a>
    </div>
    <hr>
    <p><b>Headings</b></p>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label">Number of Headings:</label>
        <input type="number" min="1" max="15" class="regular-text" id="label_wpai_number_of_heading"  name="wpaicg_settings[wpai_number_of_heading]" value="<?php 
echo  esc_html( $existingValue['wpai_number_of_heading'] ) ;
?>" placeholder="e.g. 5" >
        <a class="wpcgai_help_link" href="https://gptaipower.com/number-of-headings/" target="_blank">?</a>
    </div>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label">Heading Tag:</label>
        <select class="regular-text" id="label_wpai_heading_tag" name="wpaicg_settings[wpai_heading_tag]" >
            <option value="h1" <?php 
echo  ( esc_html( $existingValue['wpai_heading_tag'] ) == 'h1' ? 'selected' : '' ) ;
?>>h1</option>
            <option value="h2" <?php 
echo  ( esc_html( $existingValue['wpai_heading_tag'] ) == 'h2' ? 'selected' : '' ) ;
?>>h2</option>
            <option value="h3" <?php 
echo  ( esc_html( $existingValue['wpai_heading_tag'] ) == 'h3' ? 'selected' : '' ) ;
?>>h3</option>
            <option value="h4" <?php 
echo  ( esc_html( $existingValue['wpai_heading_tag'] ) == 'h4' ? 'selected' : '' ) ;
?>>h4</option>
            <option value="h5" <?php 
echo  ( esc_html( $existingValue['wpai_heading_tag'] ) == 'h5' ? 'selected' : '' ) ;
?>>h5</option>
            <option value="h6" <?php 
echo  ( esc_html( $existingValue['wpai_heading_tag'] ) == 'h6' ? 'selected' : '' ) ;
?>>h6</option>
        </select>
        <a class="wpcgai_help_link" href="https://gptaipower.com/adding-heading-tags/" target="_blank">?</a>
    </div>

    <div class="wpcgai_form_row">
        <label class="wpcgai_label">Modify Headings:</label>
        <input type="checkbox" id="label_wpai_modify_headings" name="wpaicg_settings[wpai_modify_headings]"
               value="<?php 
echo  esc_html( $existingValue['wpai_modify_headings'] ) ;
?>"
            <?php 
echo  ( esc_html( $existingValue['wpai_modify_headings'] ) == 1 ? " checked" : "" ) ;
?>
        />
        <a class="wpcgai_help_link" href="https://gptaipower.com/modifying-headings/" target="_blank">?</a>
    </div>
    <hr>
    <p><b>Additional Content</b></p>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label">Add Tagline:</label>
        <input type="checkbox" id="label_wpai_add_tagline" name="wpaicg_settings[wpai_add_tagline]"
               value="<?php 
echo  esc_html( $existingValue['wpai_add_tagline'] ) ;
?>"
            <?php 
echo  ( esc_html( $existingValue['wpai_add_tagline'] ) == 1 ? " checked" : "" ) ;
?>
        />
        <a class="wpcgai_help_link" href="https://gptaipower.com/adding-a-tagline/" target="_blank">?</a>
    </div>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label">Add Q&A:</label>
        <?php 
?>
            <input type="checkbox" value="0" disabled>Available in Pro
            <?php 
?>
        <a class="wpcgai_help_link" href="https://gptaipower.com/adding-a-qa/" target="_blank">?</a>
    </div>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label">Make Keywords Bold:</label>
        <?php 
?>
            <input type="checkbox" value="0" disabled class="pro_chk">Available in Pro
            <?php 
?>
        <a class="wpcgai_help_link" href="https://gptaipower.com/adding-keywords/" target="_blank">?</a>
    </div>

    <div class="wpcgai_form_row">
        <label class="wpcgai_label">Call-to-Action Position:</label>
        <select class="regular-text" id="label_wpai_cta_pos" name="wpaicg_settings[wpai_cta_pos]" >
            <option value="beg" <?php 
echo  ( esc_html( $existingValue['wpai_cta_pos'] ) == 'beg' ? 'selected' : '' ) ;
?>>Beginning</option>
            <option value="end" <?php 
echo  ( esc_html( $existingValue['wpai_cta_pos'] ) == 'end' ? 'selected' : '' ) ;
?>>End</option>
        </select>
        <a class="wpcgai_help_link" href="https://gptaipower.com/adding-links-and-call-to-action/" target="_blank">?</a>
    </div>
    <hr>
    <p><strong>Table of Contents</strong></p>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label">Add ToC?:</label>
        <?php 
$wpaicg_toc = get_option( 'wpaicg_toc', false );
?>
        <input<?php 
echo  ( $wpaicg_toc ? ' checked' : '' ) ;
?> type="checkbox" value="1" name="wpaicg_toc">
    </div>

    <div class="wpcgai_form_row">
        <label class="wpcgai_label">ToC Title:</label>
        <?php 
$wpaicg_toc_title = get_option( 'wpaicg_toc_title', 'Table of Contents' );
?>
        <input type="text" class="regular-text" value="<?php 
echo  esc_html( $wpaicg_toc_title ) ;
?>" name="wpaicg_toc_title">
    </div>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label">ToC Tag:</label>
        <?php 
$wpaicg_toc_title_tag = get_option( 'wpaicg_toc_title_tag', 'h2' );
?>
        <select class="regular-text" name="wpaicg_toc_title_tag">
            <option value="h1" <?php 
echo  ( esc_html( $wpaicg_toc_title_tag ) == 'h1' ? 'selected' : '' ) ;
?>>h1</option>
            <option value="h2" <?php 
echo  ( esc_html( $wpaicg_toc_title_tag ) == 'h2' ? 'selected' : '' ) ;
?>>h2</option>
            <option value="h3" <?php 
echo  ( esc_html( $wpaicg_toc_title_tag ) == 'h3' ? 'selected' : '' ) ;
?>>h3</option>
            <option value="h4" <?php 
echo  ( esc_html( $wpaicg_toc_title_tag ) == 'h4' ? 'selected' : '' ) ;
?>>h4</option>
            <option value="h5" <?php 
echo  ( esc_html( $wpaicg_toc_title_tag ) == 'h5' ? 'selected' : '' ) ;
?>>h5</option>
            <option value="h6" <?php 
echo  ( esc_html( $wpaicg_toc_title_tag ) == 'h6' ? 'selected' : '' ) ;
?>>h6</option>
        </select>
    </div>
    <hr>
    <p><b>Introduction</b></p>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label">Add Introduction:</label>
        <input type="checkbox" id="label_wpai_add_intro" name="wpaicg_settings[wpai_add_intro]"
               value="<?php 
echo  esc_html( $existingValue['wpai_add_intro'] ) ;
?>"
            <?php 
echo  ( esc_html( $existingValue['wpai_add_intro'] ) == 1 ? " checked" : "" ) ;
?>
        />
        <a class="wpcgai_help_link" href="https://gptaipower.com/adding-an-introduction/" target="_blank">?</a>
    </div>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label">Intro Title Tag:</label>
        <?php 
$wpaicg_intro_title_tag = get_option( 'wpaicg_intro_title_tag', 'h2' );
?>
        <select class="regular-text" name="wpaicg_intro_title_tag">
            <option value="h1" <?php 
echo  ( esc_html( $wpaicg_intro_title_tag ) == 'h1' ? 'selected' : '' ) ;
?>>h1</option>
            <option value="h2" <?php 
echo  ( esc_html( $wpaicg_intro_title_tag ) == 'h2' ? 'selected' : '' ) ;
?>>h2</option>
            <option value="h3" <?php 
echo  ( esc_html( $wpaicg_intro_title_tag ) == 'h3' ? 'selected' : '' ) ;
?>>h3</option>
            <option value="h4" <?php 
echo  ( esc_html( $wpaicg_intro_title_tag ) == 'h4' ? 'selected' : '' ) ;
?>>h4</option>
            <option value="h5" <?php 
echo  ( esc_html( $wpaicg_intro_title_tag ) == 'h5' ? 'selected' : '' ) ;
?>>h5</option>
            <option value="h6" <?php 
echo  ( esc_html( $wpaicg_intro_title_tag ) == 'h6' ? 'selected' : '' ) ;
?>>h6</option>
        </select>
    </div>
    <hr>
    <p><strong>Conclusion</strong></p>
    <div class="wpcgai_form_row">
        <label class="wpcgai_label">Add Conclusion:</label>
        <input type="checkbox" id="label_wpai_add_conclusion" name="wpaicg_settings[wpai_add_conclusion]"
               value="<?php 
echo  esc_html( $existingValue['wpai_add_conclusion'] ) ;
?>"
            <?php 
echo  ( esc_html( $existingValue['wpai_add_conclusion'] ) == 1 ? " checked" : "" ) ;
?>
        />
        <a class="wpcgai_help_link" href="https://gptaipower.com/adding-a-conclusion/" target="_blank">?</a>
    </div>
    <!-- wpaicg_conclusion_title_tag -->
    <div class="wpcgai_form_row">
        <label class="wpcgai_label">Conclusion Title Tag:</label>
        <?php 
$wpaicg_conclusion_title_tag = get_option( 'wpaicg_conclusion_title_tag', 'h2' );
?>
        <select class="regular-text" name="wpaicg_conclusion_title_tag">
            <option value="h1" <?php 
echo  ( esc_html( $wpaicg_conclusion_title_tag ) == 'h1' ? 'selected' : '' ) ;
?>>h1</option>
            <option value="h2" <?php 
echo  ( esc_html( $wpaicg_conclusion_title_tag ) == 'h2' ? 'selected' : '' ) ;
?>>h2</option>
            <option value="h3" <?php 
echo  ( esc_html( $wpaicg_conclusion_title_tag ) == 'h3' ? 'selected' : '' ) ;
?>>h3</option>
            <option value="h4" <?php 
echo  ( esc_html( $wpaicg_conclusion_title_tag ) == 'h4' ? 'selected' : '' ) ;
?>>h4</option>
            <option value="h5" <?php 
echo  ( esc_html( $wpaicg_conclusion_title_tag ) == 'h5' ? 'selected' : '' ) ;
?>>h5</option>
            <option value="h6" <?php 
echo  ( esc_html( $wpaicg_conclusion_title_tag ) == 'h6' ? 'selected' : '' ) ;
?>>h6</option>
        </select>
    </div>
    <hr>
    <p><strong>Custom Prompt for Express Mode</strong></p>
    <div class="wpcgai_form_row">
        <?php 
$wpaicg_content_custom_prompt_enable = get_option( 'wpaicg_content_custom_prompt_enable', false );
$wpaicg_content_custom_prompt = get_option( 'wpaicg_content_custom_prompt', '' );
if ( empty($wpaicg_content_custom_prompt) ) {
    $wpaicg_content_custom_prompt = \WPAICG\WPAICG_Custom_Prompt::get_instance()->wpaicg_default_custom_prompt;
}
?>
        <div class="mb-5">
            <label><input<?php 
echo  ( $wpaicg_content_custom_prompt_enable ? ' checked' : '' ) ;
?> type="checkbox" class="wpaicg_meta_custom_prompt_enable" name="wpaicg_content_custom_prompt_enable">&nbsp;Enable</label>
        </div>
        <div class="wpaicg_meta_custom_prompt_box" style="<?php 
echo  ( isset( $wpaicg_content_custom_prompt_enable ) && $wpaicg_content_custom_prompt_enable ? '' : 'display:none' ) ;
?>">
            <label>Custom Prompt</label>
            <textarea rows="20" class="wpaicg_meta_custom_prompt" name="wpaicg_content_custom_prompt"><?php 
echo  esc_html( str_replace( "\\", '', $wpaicg_content_custom_prompt ) ) ;
?></textarea>
            <?php 

if ( \WPAICG\wpaicg_util_core()->wpaicg_is_pro() ) {
    ?>
                <div>Make sure to include <code>[title]</code> in your prompt. You can also incorporate <code>[keywords_to_include]</code> and <code>[keywords_to_avoid]</code> to further customize your prompt.</div>
            <?php 
} else {
    ?>
                <div>Ensure <code>[title]</code> is included in your prompt.</div>
            <?php 
}

?>
            <button style="color: #fff;background: #df0707;border-color: #df0707;" data-prompt="<?php 
echo  esc_html( \WPAICG\WPAICG_Custom_Prompt::get_instance()->wpaicg_default_custom_prompt ) ;
?>" class="button wpaicg_meta_custom_prompt_reset" type="button">Reset</button>
            <div class="wpaicg_meta_custom_prompt_auto_error"></div>
        </div>
    </div>
</div>
