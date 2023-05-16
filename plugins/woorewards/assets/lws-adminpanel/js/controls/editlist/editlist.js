(function($){$.widget("lws.lws_editlist",{_create:function(){this.editlistId=this.element.attr("id");this.currentTextArea='';this.rowKeyId=this.element.find(".lws_editlist_table").attr("uid");this._bindOn(this.element,"click",".lws_editlist_item_add",this._addLine);this._bindOn(this.element,"click",".editlist-btn.mod",this.editLine);this._bindOn(this.element,"click",".editlist-btn.dup",this._copyLine);this._bindOn(this.element,"click",".editlist-btn.del",this._removeLine);this._bindOn(this.element,"click",".select-all",this.checkAll);this._bindOn(this.element,"click",".lws_editlist_group_add",this._addGroup);this._bindOn(this.element,"click",".lws_editlist_group_del",this._removeGroup);this._bindOn(this.element,"click",".lws_editlist_group_head_edit",this._editGroup);this._bindOn(this.element,"click",".lws_editlist_group_form_submit",this._submitEditGroup);this._bindOn(this.element,"click",".lws_editlist_group_form_cancel",this._cancelEditGroup);this.itemForm=this.element.find(".lws_editlist_line_form");this._bindOn(this.itemForm,"click",".btn-save",this._saveForm);this._bindOn(this.itemForm,"click",".btn-cancel",this._cancelForm);this._bindOn(this.element,"click",".lws_editlist_line_form",this._outOfForm);this._bindOn(this.element,"click",".lws-editlist-action-button",this._showLineButtons);this._bindOn(this.element,"click",".editlist-btn",this.hideAllLinesButtons);this._bindOn(this.element,"mouseleave",".lws-editlist-groupby-header",this._hideLineButtons);this._bindOn(this.element,"mouseleave",".lws-editlist-cell",this._hideLineButtons);this.itemTemplate=this.element.find(".lws_editlist_row.template");setTimeout(this._bind(this._detachTemplate,this),0);this.tableTemplate=this.element.find(".lws_editlist_table").clone(!0);this.tableTemplate.find(".lws_editlist_row.editable").remove();this.addBtnTemplate=this.element.find(".lws_editlist_item_add").clone(!0);this.groupForm=this.element.find(".lws_editlist_groupby_settings>.lws_editlist_groupby_form").detach();this.element.removeClass("lws-editlist-renderer-grouped").addClass("lws-editlist-renderer-flatten");if(this.element.data("groupby")=="on"){setTimeout(this._bind(this.groupBy,this),1)}
this.isSmall=!1;this.switchWidth=-1;this.handleDisplay()},_detachTemplate:function(){this.itemTemplate.parent().detach()},_addGroup:function(e){e.preventDefault();if(!this.isFormVisible()){var ul=this.element.find(".lws_editlist_groups");var li=this._appendNewNode(ul);li.addClass("lws_editlist_node_trial_state");this._editGroup({target:li.find(".lws_editlist_groupby_head")})}
return!1},_removeGroup:function(e){e.preventDefault();if(!this.isFormVisible()&&confirm(lws_adminpanel.confirmDel)){var li=$(e.target).closest(".lws_editlist_group");var grp=li.closest(".lws_editlist_groups");var raw=li.data("line");var me=this;li.find(".lws_editlist_row.editable[data-line]").each(function(){me._removeItems(me._getRow($(this)),grp)});this.groupForm.detach();li.remove();this.element.trigger("group_deleted",lwsBase64.toObj(raw),this)}
return!1},_editGroup:function(e){if(undefined!=e.preventDefault)e.preventDefault();if(!this.isFormVisible()){if(this.groupForm.length>0){var node=$(e.target).closest(".lws_editlist_groupby_node");$(".lws_editlist_modal_edit_button").addClass("lws-editlist-btn-disabled");node.children(".lws_editlist_groupby_head").hide();node.append(this.groupForm);obj=lwsBase64.toObj(node.closest(".lws_editlist_group").data("line"));this.groupForm.lwsWriteForm(obj);this.resetBeforeUnload();this.groupForm.show().trigger("edit",obj,this);this.focusFirstField(this.groupForm)}}
return!1},_submitEditGroup:function(e){e.preventDefault();if(this.groupForm.is(":visible")&&this.groupForm.lwsMatchPattern()){var node=this.groupForm.closest(".lws_editlist_groupby_node");var disp=node.children(".lws_editlist_groupby_head");var obj=this.groupForm.lwsReadForm();this.groupForm.detach();this._spreadGroupedData(node.closest(".lws_editlist_group"),obj);disp.lwsWriteForm(obj).show();node.closest(".lws_editlist_group").data("line",lwsBase64.fromObj(obj)).removeClass("lws_editlist_node_trial_state").trigger("change",obj,this);this.resetBeforeUnload();$(".lws_editlist_modal_edit_button").removeClass("lws-editlist-btn-disabled")}
return!1},_cancelEditGroup:function(e){e.preventDefault();this.resetBeforeUnload();var node=$(e.target).closest(".lws_editlist_groupby_node");var li=node.closest(".lws_editlist_group");if(li.hasClass("lws_editlist_node_trial_state")&&li.find(".lws_editlist_row.editable[data-line]").length==0){this.groupForm.detach();li.remove()}else{li.removeClass("lws_editlist_node_trial_state");this.groupForm.detach();node.children(".lws_editlist_groupby_head").show()}
$(".lws_editlist_modal_edit_button").removeClass("lws-editlist-btn-disabled");return!1},_getFormData:function(){var local=this.itemForm.lwsReadForm();var grpData=this.itemForm.data("group-line");if(undefined!=grpData){$.each(lwsBase64.toObj(grpData),function(key,value){local[key]=value})}
return local},_spreadGroupedData:function(li,obj){var subs=li.find(".lws_editlist_row.editable").addClass("loading");if(this._mergeData(subs,obj)){var me=this;subs.each(function(){var tr=$(this);$.ajax({dataType:"json",url:lws_editlist_ajax.url,method:"POST",data:me._addPageToData({method:"put",id:me.editlistId,line:tr.data("line"),groupedBy:me.isGroupedBy()?"true":"",},"post"),success:function(response){subs.removeClass("loading");if(0!=response&&response.status&&response.line!=undefined){var obj=lwsBase64.toObj(response.line);me._updateItemCells(tr,obj)}else{alert(0!=response&&response.error!=undefined?response.error:lws_adminpanel.updateAlert)}},}).fail(function(d,textStatus,error){subs.removeClass("loading");me._alert("Update error, status: "+textStatus+", error: "+error,li,li)})})}},_addPageToData:function(data,method){if(undefined==method)method="get";else method=method.toLowerCase();var tab=$("input[name='tab']");if(tab.length>0){data.tab=tab.val()}
var page=$("input[name='option_page']");if(page.length>0){if("post"==method)data.option_page=page.val();else data.page=page.val()}
return data},_mergeData:function(trs,obj){if(obj==undefined){var li=trs.first().closest(".lws_editlist_group[data-line]");if(li.length==0)return!1;obj=lwsBase64.toObj(li.data("line"))}
var dirty=!1;trs.filter("[data-line]").each(function(){var local=lwsBase64.toObj($(this).data("line"));$.each(obj,function(key,value){if(local[key]!=value){dirty=!0;local[key]=value}});$(this).data("line",lwsBase64.fromObj(local))});return dirty},_filterGroupData:function(trData){if(this.groupDataTemplate==undefined){var obj={};this.element.find(".lws_editlist_groupby_settings").find("span[data-name],input[name],select[name],textarea[name]").each(function(){var input=$(this);if(input.prop("tagName").toUpperCase()=="SPAN")obj[input.data("name")]=input.html();else obj[input.attr("name")]=input.val()});this.groupDataTemplate=obj}
var obj={};$.each(this.groupDataTemplate,function(key,value){obj[key]=trData[key]==undefined?value:trData[key]});return obj},isGroupedBy:function(){return this.isGrouped===!0},_splitTable:function(index,element){var row=this._getRow($(element));var local=this._getRowData(row);var groupval=local[this.groupKey];groupval=groupval==undefined?"":groupval.replace(/\"/g,'\\"');var li=this.groups.find('.lws_editlist_group[data-groupval="'+groupval+'"]');if(li.length==0)li=this._appendNewNode(this.groups,local,groupval);this._appendRow(li.find(".lws_editlist_table"),row)},_appendNewNode:function(ul,local,groupval){var obj=this._filterGroupData(local==undefined?{}:local);li=$("<div>",{'class':"lws_editlist_group lws-editlist-group","data-groupval":groupval==undefined?"":groupval,"data-line":lwsBase64.fromObj(obj),});this.groupHead.clone(!0).removeClass("lws_editlist_groupby_settings").addClass("lws-editlist-groupby-head lws_editlist_groupby_node").appendTo(li).lwsWriteForm(obj).css("display","flex");var table=this.tableTemplate.clone(!0);li.append(table);if(this.addBtnTemplate.length>0){var btn=this.addBtnTemplate.clone(!0).data("table",table);table.data("button",btn);li.append(btn)}
ul.append(li);return li},groupBy:function(key){this.groupHead=this.element.find(".lws_editlist_groupby_settings");if(this.groupHead.length==0)return;if(key==undefined)key=this.groupHead.data("groupby");if(key==undefined)return;if(undefined==this.groupKey)this.groupKey=key;$(".lws_editlist_modal_edit_button").removeClass("lws-editlist-btn-disabled");this.flatten(!0);var addBtn=this.element.find(".lws_editlist_item_add");var table=this.element.find(".lws_editlist_table");this.groups=this.element.find(".lws_editlist_groups");if(this.groups.length==0)this.groups=$("<div>",{'class':"lws_editlist_groups lws-editlist-groups"}).insertBefore(table);table.find(".lws_editlist_row.editable").each(this._bind(this._splitTable,this));table.remove();addBtn.remove();if(this.groupHead.data("add")!=undefined&&this.groupForm.length>0){var button=$("<button>",{'class':"lws-adm-btn lws-editlist-group-add lws_editlist_modal_edit_button lws_editlist_group_add","data-id":this.editlistId,}).append($("<div>",{'class':"lws-group-add-icon lws-icon lws-icon-plus",})).append($("<div>",{'class':"lws-group-add-text",}).html(this.groupHead.data("add")));this.element.append(button)}
this.groups=undefined;this.isGrouped=!0;this.element.removeClass("lws-editlist-renderer-flatten").addClass("lws-editlist-renderer-grouped");this.element.trigger("grouped",this)},flatten:function(silent){this._cancelForm();var ul=this.element.find(".lws_editlist_groups");if(ul.length>0){var cp=this.tableTemplate.clone(!0);this.element.find(".lws_editlist_table .lws_editlist_row.editable").each(function(){cp.append($(this))});cp.insertAfter(ul);if(this.addBtnTemplate.length>0){var btn=this.addBtnTemplate.clone(!0);var bottom=this.element.find(".lws-editlist-bottom-line");if(bottom.length)bottom.prepend(btn);else cp.after(btn)}
$(".lws_editlist_modal_edit_button").removeClass("lws-editlist-btn-disabled");this.groupForm.detach();ul.remove();this.element.children(".lws_editlist_group_add").remove()}
this.isGrouped=!1;this.element.removeClass("lws-editlist-renderer-grouped").addClass("lws-editlist-renderer-flatten");if(silent!==!0)this.element.trigger("flatten",this);},checkAll:function(e){var checked=$(e.currentTarget).prop("checked");this.element.find("input.lws_editlist_check_selectitem").prop("checked",checked).trigger("change")},localTable:function(dom){if(dom==undefined)return this.element.find(".lws_editlist_table");else return $(dom).closest(".lws_editlist_table")},_bindOn:function(node,events,selector,handler){node.on(events,selector,this._bind(handler,this))},_bind:function(fn,me){return function(){return fn.apply(me,arguments)}},_showLineButtons:function(e){if(!this.isFormVisible()){this.hideAllLinesButtons();$(e.currentTarget).find(".editlist-actions-popup").toggleClass("hidden",!1)}},_hideLineButtons:function(e){$(e.currentTarget).find(".editlist-actions-popup").toggleClass("hidden",!0)},hideAllLinesButtons:function(){this.element.find(".editlist-actions-popup").toggleClass("hidden",!0)},isFormVisible:function(){return $(".lws_editlist .lws_editlist_modal_form:visible").length>0},editLine:function(e){if(!this.isFormVisible())this._showForm(this._getRow($(e.target)));return!1},_getRow:function(from){return from.closest(".lws_editlist_row.editable")},_getRowData:function(row){var data=this._getRowRawData(row);if(undefined!=data)data=lwsBase64.toObj(data);return data},_setRowData:function(row,data){this._setRowRawData(row,lwsBase64.fromObj(data))},_getRowRawData:function(row){return row.filter("[data-line]").data("line")},_setRowRawData:function(row,data){row.data("line",data);return row},_copyLine:function(e){if(!this.isFormVisible()){var row=this._getRow($(e.target));var obj=this._getRowData(row);obj[this.rowKeyId]="";var cp=row.clone(!0).removeAttr("data-id").data("template",1);this._setRowData(cp,obj);cp=cp.insertBefore(row.first());this._showForm(cp)}
return!1},_removeLine:function(e){if(!this.isFormVisible()&&confirm(lws_adminpanel.confirmDel)){var row=this._getRow($(e.target));if(row)this._removeItems(row);}
return!1},_removeItems:function(cells,origin){var me=this;var raw=this._getRowRawData(cells);cells.addClass("loading");$.ajax({dataType:"json",url:lws_editlist_ajax.url,method:"POST",data:me._addPageToData({method:"del",id:me.editlistId,line:raw,groupedBy:me.isGroupedBy()?"true":"",},"post"),success:function(response){if(0==response||!response.status){me._alert("Erase error.",cells,origin)}
cells.remove();me.element.trigger("deleted",lwsBase64.toObj(raw),this)},}).fail(function(d,textStatus,error){me._alert("Erase error, status: "+textStatus+", error: "+error,cells,origin)})},_alert:function(message,row,origin){var err=$("<p>",{'class':"lws-editlist-error"});if(undefined==origin){err.css("grid-column","1 / -1")}else{err.addClass("group")}
err.html(message).insertBefore(row);row.remove()},_addLine:function(e){e.preventDefault();if(!this.isFormVisible()){var dup=this.itemTemplate.clone(!0);dup.removeAttr("id").addClass("editable").removeClass("template");var body=$(e.currentTarget).data("table");if(undefined==body)body=this.element.find(".lws_editlist_table");this._appendRow(body,dup);this._mergeData(dup);this._showForm(dup)}
return!1},_appendRow:function(table,row){var target=table.find(".lws_editlist_row.editable:last-of-type");if(!target.length)target=table.find(".lws_editlist_row.head.top");if(target.length)target.after(row);else table.prepend(row);return row},_showForm:function(row){this.itemForm.find("button.btn-save").prop("disabled",!1);row.addClass("lws_editlist_tr_edited");var obj=this._getRowData(row);try{this.itemForm.lwsWriteForm(undefined==obj?{}:obj,!0)}catch(error){console.log(error)}
$(".lws_editlist_modal_edit_button").addClass("lws-editlist-btn-disabled");this.resetBeforeUnload();this.itemForm.removeClass("lws_editlist_form_hidden");var grp=row.closest(".lws_editlist_group[data-line]");if(grp.length>0)
this.itemForm.data("group-line",grp.data("line"));else this.itemForm.removeData("group-line");this.itemForm.trigger("edit",obj,this);this.focusFirstField(this.itemForm)},getRowData:function(rowDomElt){var row=this._getRow(rowDomElt);var data=this._getRowData(row);if(row.length&&undefined!=data)return{row:row,data:data};else return undefined},updateRowData:function(rowData){rowData.row.addClass("lws_editlist_tr_edited");var grp=rowData.row.closest(".lws_editlist_group[data-line]");if(grp.length){var grpData=grp.data("line");if(undefined!=grpData){$.each(lwsBase64.toObj(grpData),function(key,value){rowData.data[key]=value})}}
var me=this;$.ajax({dataType:"json",url:lws_editlist_ajax.url,method:"POST",data:this._addPageToData({method:"put",id:this.editlistId,line:lwsBase64.fromObj(rowData.data),groupedBy:this.isGroupedBy()?"true":"",},"post"),success:this._bind(this._savedCallback,this),}).fail(function(d,textStatus,error){rowData.row.removeClass("lws_editlist_tr_edited");me._alert("Update error, status: "+textStatus+", error: "+error,rowData.row)})},_saveForm:function(){this.itemForm.find("button.btn-save").prop("disabled",!0);var row=this.element.find(".lws_editlist_tr_edited");var me=this;$.ajax({dataType:"json",url:lws_editlist_ajax.url,method:"POST",data:this._addPageToData({method:"put",id:this.editlistId,line:lwsBase64.fromObj(this._getFormData()),groupedBy:this.isGroupedBy()?"true":"",},"post"),success:this._bind(this._savedCallback,this),}).fail(function(d,textStatus,error){row.removeClass("lws_editlist_tr_edited");me.itemForm.addClass("lws_editlist_form_hidden").removeData("group-line");$(".lws_editlist_modal_edit_button").removeClass("lws-editlist-btn-disabled");me._alert("Update error, status: "+textStatus+", error: "+error,row)});return!1},_savedCallback:function(response){if(0!=response&&response.status&&response.line!=undefined){var row=this.element.find(".lws_editlist_tr_edited").removeClass("lws_editlist_tr_edited").removeData("template").removeAttr("data-template");var obj=lwsBase64.toObj(response.line);this._setRowData(row,obj);this._updateItemCells(row,obj,!0);this.itemForm.addClass("lws_editlist_form_hidden").removeData("group-line");$(".lws_editlist_modal_edit_button").removeClass("lws-editlist-btn-disabled");this.showInfo(response.message);this.resetBeforeUnload();row.trigger("updated",obj,this)}else{alert(0!=response&&response.error!=undefined?response.error:lws_adminpanel.updateAlert);this.itemForm.find("button.btn-save").prop("disabled",!1)}},_updateItemCells:function(cells,obj,trigChanged,sel){if(undefined==sel)
sel='.td';cells.children(sel).each(function(index,td){cell=$(td);var key=cell.data("key");if(key!=undefined&&obj[key]!=undefined){cell.children(".cell-content").html(obj[key]);if(trigChanged===!0)cell.trigger("change",obj,this);}});let small=cells.children(".lws_deep_cell");if(small.length)
this._updateItemCells(small,obj,trigChanged,'.subtd');},showInfo:function(content){if(content!=undefined)alert(content);},_outOfForm:function(event){if(event.target===event.currentTarget){this._cancelForm();return!1}},_cancelForm:function(){var row=this.element.find(".lws_editlist_tr_edited").removeClass("lws_editlist_tr_edited");if(row.length>0&&row.data("template")=="1"){row.remove()}
$(".lws_editlist_modal_edit_button").removeClass("lws-editlist-btn-disabled");this.itemForm.addClass("lws_editlist_form_hidden").removeData("group-line");this.resetBeforeUnload();return!1},resetBeforeUnload:function(){if(window.lwsInputchanged!==!0)
window.onbeforeunload=undefined},focusFirstField:function(dom){setTimeout(function(){dom.find("input:visible, select:visible, textarea:visible").first().trigger('focus').trigger('select')},10)},handleDisplay:function(){if(this.isSmall==!0){if(this.element.outerWidth()>(this.switchWidth+50)){this.switchWidth=-1;this.isSmall=!1;this.element.find(".lws-editlist").removeClass('small-visible')}}
if(this.isSmall==!1){if(this.element.outerWidth()>this.element.closest(".fields-grid").outerWidth()){this.switchWidth=this.element.closest(".fields-grid").outerWidth();this.isSmall=!0;this.element.find(".lws-editlist").addClass('small-visible')}}},})})(jQuery)
jQuery(function($){$(".lws_editlist").lws_editlist();$(window).on('resize',function(){$(".lws_editlist").lws_editlist('handleDisplay')})})