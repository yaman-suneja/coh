(function($){$.fn.lwsReadForm=function(){var obj={};function lwsAddToObj(name,value){if(name.endsWith("[]")){name=name.substr(0,name.length-2);if(obj[name]!=undefined)
obj[name].push(value);else obj[name]=[value]}else obj[name]=value}
$(this).find("span[data-name]").each(function(index,span){var name=$(span).data("name");if(name!=undefined&&name.length>0)
lwsAddToObj(name,$(span).html());});$(this).find("input:not([type='radio'], [type='checkbox']), select, textarea").each(function(index,input){var name=$(input).attr("name");if(name!=undefined&&name.length>0){var tag=input.tagName.toLowerCase();if(tag=="select"){lwsAddToObj(name,$(input).find("option:selected").val())}else if(tag=="textarea"&&$(input).hasClass('wp-editor-area')&&typeof tinyMCE!="undefined"&&input.id.length){let editor=tinyMCE.get(input.id);if(editor!==null){editor.save()}
lwsAddToObj(name,$(input).val())}else{lwsAddToObj(name,$(input).val())}}});$(this).find("input[type='radio']").each(function(index,input){var name=$(input).attr("name");if($(input).prop('checked')&&name!=undefined&&name.length>0)
lwsAddToObj(name,$(input).val());});$(this).find("input[type='checkbox']").each(function(index,input){var name=$(input).attr("name");if(name!=undefined&&name.length>0){lwsAddToObj(name,($(input).prop('checked'))?$(input).val():'')}});return obj}
$.fn.lwsWriteForm=function(obj,resetUnknown,resetAnonymous){function setInputAndTrigger(input,value,dName){if(dName!=undefined){input.data('value',('object'==typeof(value))?lwsBase64.fromObj(value):value);try{input.trigger('change')}catch(error){console.log("Fail to trigger change on element below");console.log(input)}}else{input.val(value).trigger('change')}}
$(this).find("input:not([type='radio'], [type='checkbox']), select, textarea").each(function(index,input){var dName=$(input).data('lw_name');var name=(dName!=undefined?dName:$(input).attr("name"));if(name!=undefined&&name.length>0){if(obj[name]!=undefined){let value='';if('textarea'==input.tagName.toLowerCase()){value=obj[name];if($(input).hasClass('wp-editor-area')&&typeof tinyMCE!="undefined"&&input.id.length){let editor=tinyMCE.get(input.id);if(editor!==null)
editor.setContent(value);}}else if('ignore'===$(input).data('escape')){value=obj[name]}else{value=$("<div>").html(obj[name]).text()}
setInputAndTrigger($(input),value,dName)}else if(resetUnknown===!0)
setInputAndTrigger($(input),'',dName);}else if(resetAnonymous===!0)
setInputAndTrigger($(input),'',dName);});$(this).find("span[data-name]").each(function(index,span){var name=$(span).data("name");if(name!=undefined&&name.length>0){if(obj[name]!=undefined)
$(span).html(obj[name]).trigger('change');else if(resetUnknown===!0)
$(span).text('').trigger('change');}else if(resetAnonymous===!0)
$(span).text('').trigger('change');});$(this).find("input[type='radio']").each(function(index,input){var name=$(input).attr("name");if((name!=undefined&&name.length>0)||resetAnonymous===!0){if(obj[name]!=undefined||resetAnonymous===!0||resetUnknown===!0)
$(input).prop('checked',!1);}});$(this).find("input[type='radio']").each(function(index,input){var name=$(input).attr("name");if(name!=undefined&&name.length>0){if(obj[name]!=undefined&&obj[name]==$(input).val()){$(input).prop('checked',!0).trigger('change')}}});$(this).find("input[type='checkbox']").each(function(index,input){var name=$(input).attr("name");if(name!=undefined&&name.length>0){if(obj[name]!=undefined)
$(input).prop('checked',obj[name]==$(input).val()).trigger('change');else if(resetUnknown===!0)
$(input).prop('checked',!1).trigger('change');}else if(resetAnonymous===!0)
$(input).prop('checked',!1).trigger('change');});return $(this)}
$.fn.lwsIsDark=function(cssKey){if(typeof(cssKey)==='undefined')cssKey='background-color';var rgb=this.css(cssKey).match(/\d+/g);if(rgb!=null){var y=(0.2126*rgb[0])+(0.7152*rgb[1])+(0.0722*rgb[2]);return(y<100.0)}else return!1}
$.fn.lwsMatchPattern=function(){var match=!0;function testMatch(index,element){var input=$(element);var reg=new RegExp(input.data('pattern'),input.data('pattern-flags'));if(!reg.test(input.val())){match=!1;if(input.data('pattern-title')!=undefined&&input.data('pattern-title').length>0)
alert(input.data('pattern-title'));return!1}}
if($(this).prop("tagName").toUpperCase()=='INPUT'){if($(this).data('pattern')!=undefined)
testMatch(0,this);}else $(this).find('input[data-pattern]').each(testMatch);return match}
$.fn.lwsSlugify=function(){var elt=$(this);var tag=elt.prop("tagName").toLowerCase();var value='';if('input'==tag||'textarea'==tag||'select'==tag)
value=elt.val();else value=elt.text();value=value.toString().normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase().trim().replace(/\s+/g,'-').replace(/[^\w\-]+/g,'').replace(/\-\-+/g,'-');if('input'==tag||'textarea'==tag||'select'==tag)
elt.val(value);else elt.text(value)}})(jQuery)