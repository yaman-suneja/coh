"use strict";

"use strict";

function wobefOpenTab(item) {
    let wobefTabItem = item;
    let wobefParentContent = wobefTabItem.closest(".wobef-tabs-list");
    let wobefParentContentID = wobefParentContent.attr("data-content-id");
    let wobefDataBox = wobefTabItem.attr("data-content");
    wobefParentContent.find("li a.selected").removeClass("selected");
    wobefTabItem.addClass("selected");
    jQuery("#" + wobefParentContentID).children("div.selected").removeClass("selected");
    jQuery("#" + wobefParentContentID + " div[data-content=" + wobefDataBox + "]").addClass("selected");
    if (item.attr("data-type") === "main-tab") {
        wobefFilterFormClose();
    }
}

function wobefCloseModal() {
    let lastModalOpened = jQuery('#wobef-last-modal-opened');
    if (lastModalOpened.val() !== '') {
        jQuery(lastModalOpened.val() + ' .wobef-modal-box').fadeOut();
        jQuery(lastModalOpened.val()).fadeOut();
        lastModalOpened.val('');
    } else {
        jQuery('.wobef-modal-box').fadeOut();
        jQuery('.wobef-modal').fadeOut();
    }
}

function wobefReInitColorPicker() {
    if (jQuery('.wobef-color-picker').length > 0) {
        jQuery('.wobef-color-picker').wpColorPicker();
    }
    if (jQuery('.wobef-color-picker-field').length > 0) {
        jQuery('.wobef-color-picker-field').wpColorPicker();
    }
}

function wobefReInitDatePicker() {
    if (jQuery.fn.datetimepicker) {
        jQuery('.wobef-datepicker').datetimepicker('destroy');
        jQuery('.wobef-timepicker').datetimepicker('destroy');
        jQuery('.wobef-datetimepicker').datetimepicker('destroy');

        jQuery('.wobef-datepicker').datetimepicker({
            timepicker: false,
            format: 'Y/m/d',
            scrollMonth: false,
            scrollInput: false
        });


        jQuery('.wobef-timepicker').datetimepicker({
            datepicker: false,
            format: 'H:i',
            scrollMonth: false,
            scrollInput: false
        });

        jQuery('.wobef-datetimepicker').datetimepicker({
            format: 'Y/m/d H:i',
            scrollMonth: false,
            scrollInput: false
        });
    }

}

function wobefPaginationLoadingStart() {
    jQuery('.wobef-pagination-loading').show();
}

function wobefPaginationLoadingEnd() {
    jQuery('.wobef-pagination-loading').hide();
}

function wobefLoadingStart() {
    jQuery('#wobef-loading').removeClass('wobef-loading-error').removeClass('wobef-loading-success').text('Loading ...').slideDown(300);
}

function wobefLoadingSuccess(message = 'Success !') {
    jQuery('#wobef-loading').removeClass('wobef-loading-error').addClass('wobef-loading-success').text(message).delay(1500).slideUp(200);
}

function wobefLoadingError(message = 'Error !') {
    jQuery('#wobef-loading').removeClass('wobef-loading-success').addClass('wobef-loading-error').text(message).delay(1500).slideUp(200);
}

function wobefSetColorPickerTitle() {
    jQuery('.wobef-column-manager-right-item .wp-picker-container').each(function () {
        let title = jQuery(this).find('.wobef-column-manager-color-field input').attr('title');
        jQuery(this).attr('title', title);
        wobefSetTipsyTooltip();
    });
}

function wobefFilterFormClose() {
    if (jQuery('#wobef-filter-form-content').attr('data-visibility') === 'visible') {
        jQuery('.wobef-filter-form-icon').addClass('lni-chevron-down').removeClass('lni lni-chevron-up');
        jQuery('#wobef-filter-form-content').slideUp(200).attr('data-visibility', 'hidden');
    }
}

function wobefFilterFormOpen() {
    if (jQuery('#wobef-filter-form-content').attr('data-visibility') === 'hidden') {
        jQuery('.wobef-filter-form-icon').removeClass('lni lni-chevron-down').addClass('lni lni-chevron-up');
        jQuery('#wobef-filter-form-content').slideDown(200).attr('data-visibility', 'visible');
    }
}

function wobefSetTipsyTooltip() {
    jQuery('[title]').tipsy({
        html: true,
        arrowWidth: 10, //arrow css border-width * 2, default is 5 * 2
        attr: 'data-tipsy',
        cls: null,
        duration: 150,
        offset: 7,
        position: 'top-center',
        trigger: 'hover',
        onShow: null,
        onHide: null
    });
}

function wobefHideSelectionTools() {
    jQuery('.wobef-bulk-edit-form-selection-tools').hide();
}

function wobefShowSelectionTools() {
    jQuery('.wobef-bulk-edit-form-selection-tools').show();
}

function wobefSetColorPickerTitle() {
    jQuery('.wobef-column-manager-right-item .wp-picker-container').each(function () {
        let title = jQuery(this).find('.wobef-column-manager-color-field input').attr('title');
        jQuery(this).attr('title', title);
        wobefSetTipsyTooltip();
    });
}

function wobefAddMetaKeysManual(meta_key_name) {
    wobefLoadingStart();
    jQuery.ajax({
        url: WOBEF_DATA.ajax_url,
        type: 'post',
        dataType: 'html',
        data: {
            action: 'wobef_add_meta_keys_manual',
            meta_key_name: meta_key_name,
        },
        success: function (response) {
            jQuery('#wobef-meta-fields-items').append(response);
            wobefLoadingSuccess();
        },
        error: function () {
            wobefLoadingError();
        }
    })
}

function wobefAddACFMetaField(field_name, field_label, field_type) {
    wobefLoadingStart();
    jQuery.ajax({
        url: WOBEF_DATA.ajax_url,
        type: 'post',
        dataType: 'html',
        data: {
            action: 'wobef_add_acf_meta_field',
            field_name: field_name,
            field_label: field_label,
            field_type: field_type
        },
        success: function (response) {
            jQuery('#wobef-meta-fields-items').append(response);
            wobefLoadingSuccess();
        },
        error: function () {
            wobefLoadingError();
        }
    })
}

function wobefCheckFilterFormChanges() {
    let isChanged = false;
    jQuery('#wobef-filter-form-content [data-field=value]').each(function () {
        if (jQuery(this).val() && jQuery(this).val() != '') {
            isChanged = true;
        }
    });
    jQuery('#wobef-filter-form-content [data-field=from]').each(function () {
        if (jQuery(this).val() && jQuery(this).val() != '') {
            isChanged = true;
        }
    });
    jQuery('#wobef-filter-form-content [data-field=to]').each(function () {
        if (jQuery(this).val() && jQuery(this).val() != '') {
            isChanged = true;
        }
    });

    jQuery('#filter-form-changed').val(isChanged);

    if (isChanged === true) {
        jQuery('#wobef-bulk-edit-reset-filter').show();
    }
}

function wobefGetCheckedItem() {
    let itemIds;
    let itemsChecked = jQuery("input.wobef-check-item:checkbox:checked");
    if (itemsChecked.length > 0) {
        itemIds = itemsChecked.map(function (i) {
            return jQuery(this).val();
        }).get();
    }

    return itemIds;
}

function wobefInlineEdit(ordersIDs, field, value, reload = false) {
    wobefLoadingStart();
    jQuery.ajax({
        url: WOBEF_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wobef_inline_edit',
            orders_ids: ordersIDs,
            field: field,
            value: value
        },
        success: function (response) {
            if (response.success) {
                if (reload === true) {
                    wobefReloadOrders(response.edited_ids);
                } else {
                    wobefLoadingSuccess('Success !')
                }
                jQuery('.wobef-history-items tbody').html(response.history_items);
            } else {
                wobefLoadingError();
            }
        },
        error: function () {
            wobefLoadingError();
        }
    })
}

function wobefReloadOrders(edited_ids = [], current_page = wobefGetCurrentPage()) {
    let data = wobefGetCurrentFilterData();
    wobefOrdersFilter(data, 'pro_search', edited_ids, current_page);
}

function wobefOrdersFilter(data, action, edited_ids = null, page = wobefGetCurrentPage()) {
    // clear selected orders in export tab
    jQuery('#wobef-export-items-selected').html('');

    if (action === 'pagination') {
        wobefPaginationLoadingStart();
    } else {
        wobefLoadingStart();
    }
    jQuery.ajax({
        url: WOBEF_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wobef_orders_filter',
            filter_data: data,
            current_page: page,
            search_action: action,
        },
        success: function (response) {
            if (response.success) {
                wobefLoadingSuccess();
                wobefSetOrdersList(response, edited_ids)
            } else {
                wobefLoadingError();
            }
        },
        error: function () {
            wobefLoadingError();
        }
    });
}

function wobefSetOrdersList(response, edited_ids = null) {
    jQuery('#wobef-items-table').html(response.orders_list);
    jQuery('.wobef-items-pagination').html(response.pagination);
    jQuery('.wobef-top-nav-status-filter').html(response.status_filters);

    let currentPage = wobefGetCurrentPage();
    let countPerPage = jQuery('#wobef-quick-per-page').val();
    let showingTo = parseInt(currentPage * countPerPage);
    let showingFrom = parseInt(showingTo - countPerPage) + 1;
    showingTo = (showingTo < response.orders_count) ? showingTo : response.orders_count;
    jQuery('.wobef-items-count').html("Showing " + showingFrom + " to " + showingTo + " of " + response.orders_count + " entries");

    jQuery('.wobef-bulk-edit-status-filter-item').removeClass('active');
    let statusFilter = (jQuery('#wobef-filter-form-order-status').val()) ? jQuery('#wobef-filter-form-order-status').val() : 'all';
    if (jQuery.isArray(statusFilter)) {
        statusFilter.forEach(function (val) {
            jQuery('.wobef-bulk-edit-status-filter-item[data-status="' + val + '"]').addClass('active');
        });
    } else {
        jQuery('.wobef-bulk-edit-status-filter-item[data-status="' + statusFilter + '"]').addClass('active');
    }

    wobefReInitDatePicker();
    wobefReInitColorPicker();

    if (edited_ids && edited_ids.length > 0) {
        jQuery('tr').removeClass('wobef-item-edited');
        edited_ids.forEach(function (orderID) {
            jQuery('tr[data-item-id=' + orderID + ']').addClass('wobef-item-edited');
            jQuery('input[value=' + orderID + ']').prop('checked', true);
        });
        wobefShowSelectionTools();
    }

    wobefSetTipsyTooltip();
    setTimeout(function () {
        let maxHeightScrollWrapper = jQuery('.scroll-wrapper > .scroll-content').css('max-height');
        jQuery('.scroll-wrapper > .scroll-content').css({
            'max-height': (parseInt(maxHeightScrollWrapper) + 5)
        });

        let actionColumn = jQuery('td.wobef-action-column');
        if (actionColumn.length > 0) {
            actionColumn.each(function () {
                jQuery(this).css({
                    "min-width": (parseInt(jQuery(this).find('a').length) * 45)
                })
            });
        }
    }, 500);
}

function wobefGetOrderData(orderID) {
    jQuery.ajax({
        url: WOBEF_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wobef_get_order_data',
            order_id: orderID
        },
        success: function (response) {
            if (response.success) {
                wobefSetOrderDataBulkEditForm(response.order_data);
            } else {

            }
        },
        error: function () {

        }
    });
}

function wobefDeleteOrder(orderIDs, deleteType) {
    wobefLoadingStart();
    jQuery.ajax({
        url: WOBEF_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wobef_delete_orders',
            order_ids: orderIDs,
            delete_type: deleteType,
        },
        success: function (response) {
            if (response.success) {
                wobefReloadOrders(response.edited_ids, wobefGetCurrentPage());
                wobefHideSelectionTools();
                jQuery('.wobef-history-items tbody').html(response.history_items);
            } else {
                wobefLoadingError();
            }
        },
        error: function () {
            wobefLoadingError();
        }
    });
}

function wobefDuplicateOrder(orderIDs, duplicateNumber) {
    wobefLoadingStart();
    jQuery.ajax({
        url: WOBEF_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wobef_duplicate_order',
            order_ids: orderIDs,
            duplicate_number: duplicateNumber
        },
        success: function (response) {
            if (response.success) {
                wobefReloadOrders([], wobefGetCurrentPage());
                wobefCloseModal();
                wobefHideSelectionTools();
            } else {
                wobefLoadingError();
            }
        },
        error: function () {
            wobefLoadingError();
        }
    });
}

function wobefCreateNewOrder(count = 1) {
    wobefLoadingStart();
    jQuery.ajax({
        url: WOBEF_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wobef_create_new_order',
            count: count
        },
        success: function (response) {
            if (response.success) {
                wobefReloadOrders(response.order_ids, 1);
                wobefCloseModal();
            } else {
                wobefLoadingError();
            }
        },
        error: function () {
            wobefLoadingError();
        }
    });
}

function wobefSaveColumnProfile(presetKey, items, type) {
    wobefLoadingStart();
    jQuery.ajax({
        url: WOBEF_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wobef_save_column_profile',
            preset_key: presetKey,
            items: items,
            type: type
        },
        success: function (response) {
            if (response.success) {
                wobefLoadingSuccess('Success !');
                location.href = location.href.replace(location.hash, "");
            } else {
                wobefLoadingError();
            }
        },
        error: function () {
            wobefLoadingError();
        }
    });
}

function wobefLoadFilterProfile(presetKey) {
    wobefLoadingStart();
    jQuery.ajax({
        url: WOBEF_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wobef_load_filter_profile',
            preset_key: presetKey,
        },
        success: function (response) {
            if (response.success) {
                wobefResetFilterForm();
                setFilterValues(response.filter_data);
                wobefLoadingSuccess();
                wobefSetOrdersList(response);
                wobefCloseModal();
            } else {
                wobefLoadingError();
            }
        },
        error: function () {
            wobefLoadingError();
        }
    });
}

function wobefDeleteFilterProfile(presetKey) {
    wobefLoadingStart();
    jQuery.ajax({
        url: WOBEF_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wobef_delete_filter_profile',
            preset_key: presetKey,
        },
        success: function (response) {
            if (response.success) {
                wobefLoadingSuccess();
            } else {
                wobefLoadingError();
            }
        },
        error: function () {
            wobefLoadingError();
        }
    });
}

function wobefFilterProfileChangeUseAlways(presetKey) {
    wobefLoadingStart();
    jQuery.ajax({
        url: WOBEF_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wobef_filter_profile_change_use_always',
            preset_key: presetKey,
        },
        success: function (response) {
            if (response.success) {
                wobefLoadingSuccess();
            } else {
                wobefLoadingError()
            }
        },
        error: function () {
            wobefLoadingError();
        }
    });
}

function wobefGetCurrentFilterData() {
    return (jQuery('#wobef-quick-search-text').val()) ? wobefGetQuickSearchData() : wobefGetProSearchData()
}

function wobefResetQuickSearchForm() {
    jQuery('.wobef-top-nav-filters-search input').val('');
    jQuery('.wobef-top-nav-filters-search select').prop('selectedIndex', 0);
    jQuery('#wobef-quick-search-reset').hide();
}

function wobefResetFilterForm() {
    jQuery('#wobef-filter-form-content input').val('');
    jQuery('#wobef-filter-form-content select').prop('selectedIndex', 0);
    jQuery('#wobef-filter-form-content .wobef-select2').val(null).trigger('change');
    jQuery('#wobef-filter-form-content .wobef-select2-products').val(null).trigger('change');
    jQuery('#wobef-filter-form-content .wobef-select2-tags').val(null).trigger('change');
    jQuery('#wobef-filter-form-content .wobef-select2-categories').val(null).trigger('change');
    jQuery('#wobef-filter-form-content .wobef-select2-taxonomies').val(null).trigger('change');
    jQuery('.wobef-bulk-edit-status-filter-item').removeClass('active');
    jQuery('.wobef-bulk-edit-status-filter-item[data-status="all"]').addClass('active');
}

function wobefResetFilters() {
    wobefResetFilterForm();
    wobefResetQuickSearchForm();
    jQuery(".wobef-filter-profiles-items tr").removeClass("wobef-filter-profile-loaded");
    jQuery('input.wobef-filter-profile-use-always-item[value="default"]').prop("checked", true).closest("tr");
    jQuery("#wobef-bulk-edit-reset-filter").hide();
    wobefFilterProfileChangeUseAlways("default");
    let data = wobefGetCurrentFilterData();
    wobefOrdersFilter(data, "pro_search");
    jQuery('#wobef-bulk-edit-reset-filter').hide();
}

function wobefChangeCountPerPage(countPerPage) {
    wobefLoadingStart();
    jQuery.ajax({
        url: WOBEF_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wobef_change_count_per_page',
            count_per_page: countPerPage,
        },
        success: function (response) {
            if (response.success) {
                wobefReloadOrders([], 1);
            } else {
                wobefLoadingError();
            }
        },
        error: function () {
            wobefLoadingError();
        }
    });
}

function wobefUpdateOrderTaxonomy(order_ids, field, data, reload) {
    wobefLoadingStart();
    jQuery.ajax({
        url: WOBEF_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wobef_update_order_taxonomy',
            order_ids: order_ids,
            field: field,
            values: data
        },
        success: function (response) {
            if (response.success) {
                if (reload === true) {
                    wobefReloadOrders(order_ids);
                } else {
                    wobefLoadingSuccess();
                }
                jQuery('.wobef-history-items tbody').html(response.history_items);
            } else {
                wobefLoadingError();
            }
        },
        error: function () {
            wobefLoadingError();
        }
    });
}

function wobefAddOrderTaxonomy(taxonomyInfo, taxonomyName, taxonomy_id) {
    wobefLoadingStart();
    jQuery.ajax({
        url: WOBEF_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wobef_add_order_taxonomy',
            taxonomy_info: taxonomyInfo,
            taxonomy_name: taxonomyName,
        },
        success: function (response) {
            if (response.success) {
                taxonomy_id = (taxonomyInfo.modal_id) ? taxonomyInfo.modal_id : 'wobef-modal-taxonomy-' + taxonomyName + '-' + taxonomyInfo.order_id;
                jQuery('#' + taxonomy_id + ' .wobef-order-items-list').html(response.taxonomy_items);
                wobefLoadingSuccess();
                wobefCloseModal()
            } else {
                wobefLoadingError();
            }
        },
        error: function () {
            wobefLoadingError();
        }
    });
}

function wobefAddNewFileItem() {
    jQuery.ajax({
        url: WOBEF_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wobef_add_new_file_item',
        },
        success: function (response) {
            if (response.success) {
                jQuery('#wobef-modal-select-files .wobef-inline-select-files').prepend(response.file_item);
                wobefSetTipsyTooltip();
            }
        },
        error: function () {

        }
    });
}

function wobefGetOrderFiles(orderID) {
    jQuery.ajax({
        url: WOBEF_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wobef_get_order_files',
            order_id: orderID,
        },
        success: function (response) {
            if (response.success) {
                jQuery('#wobef-modal-select-files .wobef-inline-select-files').html(response.files);
                wobefSetTipsyTooltip();
            } else {
                jQuery('#wobef-modal-select-files .wobef-inline-select-files').html('');
            }
        },
        error: function () {
            jQuery('#wobef-modal-select-files .wobef-inline-select-files').html('');
        }
    });
}

function changedTabs(item) {
    let change = false;
    let tab = jQuery('nav.wobef-tabs-navbar a[data-content=' + item.closest('.wobef-tab-content-item').attr('data-content') + ']');
    item.closest('.wobef-tab-content-item').find('[data-field=operator]').each(function () {
        if (jQuery(this).val() === 'text_remove_duplicate') {
            change = true;
            return false;
        }
    });
    item.closest('.wobef-tab-content-item').find('[data-field=value]').each(function () {
        if (jQuery(this).val()) {
            change = true;
            return false;
        }
    });
    if (change === true) {
        tab.addClass('wobef-tab-changed');
    } else {
        tab.removeClass('wobef-tab-changed');
    }
}

function wobefGetQuickSearchData() {
    return {
        search_type: 'quick_search',
        quick_search_text: jQuery('#wobef-quick-search-text').val(),
        quick_search_field: jQuery('#wobef-quick-search-field').val(),
        quick_search_operator: jQuery('#wobef-quick-search-operator').val(),
    };
}

function wobefSortByColumn(columnName, sortType) {
    wobefLoadingStart();
    jQuery.ajax({
        url: WOBEF_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wobef_sort_by_column',
            filter_data: wobefGetCurrentFilterData(),
            column_name: columnName,
            sort_type: sortType,
        },
        success: function (response) {
            if (response.success) {
                wobefLoadingSuccess();
                wobefSetOrdersList(response)
            } else {
                wobefLoadingError();
            }
        },
        error: function () {
            wobefLoadingError();
        }
    });
}

function wobefAddMetaKeysByOrderID(orderID) {
    wobefLoadingStart();
    jQuery.ajax({
        url: WOBEF_DATA.ajax_url,
        type: 'post',
        dataType: 'html',
        data: {
            action: 'wobef_add_meta_keys_by_order_id',
            order_id: orderID,
        },
        success: function (response) {
            jQuery('#wobef-meta-fields-items').append(response);
            wobefLoadingSuccess();
        },
        error: function () {
            wobefLoadingError();
        }
    })
}

function wobefGetCurrentPage() {
    return jQuery('.wobef-top-nav-filters .wobef-top-nav-filters-paginate a.current').attr('data-index');
}

function wobefGetDefaultFilterProfileOrders() {
    jQuery.ajax({
        url: WOBEF_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wobef_get_default_filter_profile_orders',
        },
        success: function (response) {
            if (response.success) {
                setFilterValues(response);
                wobefSetOrdersList(response)
            }
        },
        error: function () {
        }
    });
}

function setFilterValues(response) {
    let filterData = response.filter_data;
    if (filterData) {
        jQuery('.wobef-top-nav-status-filter a').removeClass('active');
        jQuery.each(filterData, function (key, values) {
            if (values instanceof Object) {
                if (values.operator) {
                    jQuery('#wobef-filter-form .wobef-form-group[data-name=' + key + ']').find('[data-field=operator]').val(values.operator).change();
                }
                if (values.value) {
                    switch (key) {
                        case 'post_status':
                            if (values.value[0]) {
                                jQuery('.wobef-top-nav-status-filter a[data-status="' + values.value[0] + '"]').addClass('active');
                            } else {
                                jQuery('.wobef-top-nav-status-filter a[data-status="all"]').addClass('active');
                            }
                            break;
                        case 'products':
                            if (values.value.length > 0) {
                                values.value.forEach(function (key) {
                                    if (response.products[key]) {
                                        jQuery('#wobef-filter-form-order-products').append("<option value='" + key + "' selected='selected'>" + response.products[key] + "</option>");
                                    }
                                });
                            }
                            break;
                        case 'categories':
                            if (values.value.length > 0) {
                                values.value.forEach(function (key) {
                                    if (response.categories[key]) {
                                        jQuery('#wobef-filter-form-order-categories').append("<option value='" + key + "' selected='selected'>" + response.categories[key] + "</option>");
                                    }
                                });
                            }
                            break;
                        case 'tags':
                            if (values.value.length > 0) {
                                values.value.forEach(function (key) {
                                    if (response.tags[key]) {
                                        jQuery('#wobef-filter-form-order-tags').append("<option value='" + key + "' selected='selected'>" + response.tags[key] + "</option>");
                                    }
                                });
                            }
                            break;
                        case 'taxonomies':
                            if (values.value.length > 0) {
                                values.value.forEach(function (key) {
                                    if (response.taxonomies[key]) {
                                        jQuery('#wobef-filter-form-order-taxonomies').append("<option value='" + key + "' selected='selected'>" + response.taxonomies[key] + "</option>");
                                    }
                                });
                            }
                            break;
                        default:
                            jQuery('#wobef-filter-form .wobef-form-group[data-name=' + key + ']').find('[data-field=value]').val(values.value).change();
                    }
                }
                if (values.from) {
                    jQuery('#wobef-filter-form .wobef-form-group[data-name=' + key + ']').find('[data-field=from]').val(values.from).change();
                }
                if (values.to) {
                    jQuery('#wobef-filter-form .wobef-form-group[data-name=' + key + ']').find('[data-field=to]').val(values.to);
                }
            } else {
                jQuery('#wobef-filter-form .wobef-form-group[data-name=' + key + ']').find('[data-field=value]').val(values);
            }
        });
        wobefCheckFilterFormChanges();
    }
}

function checkedCurrentCategory(id, categoryIds) {
    categoryIds.forEach(function (value) {
        jQuery(id + ' input[value=' + value + ']').prop('checked', 'checked');
    });
}

function wobefSaveFilterPreset(data, presetName) {
    wobefLoadingStart();
    jQuery.ajax({
        url: WOBEF_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wobef_save_filter_preset',
            filter_data: data,
            preset_name: presetName
        },
        success: function (response) {
            if (response.success) {
                wobefLoadingSuccess();
                jQuery('#wobef-modal-filter-profiles').find('tbody').append(response.new_item);
            } else {
                wobefLoadingError();
            }
        },
        error: function () {
            wobefLoadingError();
        }
    });
}

function wobefResetBulkEditForm() {
    jQuery('#wobef-modal-bulk-edit input').val('').change();
    jQuery('#wobef-modal-bulk-edit select').prop('selectedIndex', 0).change();
    jQuery('#wobef-modal-bulk-edit textarea').val('');
    jQuery('#wobef-modal-bulk-edit .wobef-select2').val(null).trigger('change');
}

function wobefGetProSearchData() {
    let data;
    let custom_fields = [];
    let j = 0;
    jQuery('.wobef-tab-content-item[data-content=filter_custom_fields] .wobef-form-group').each(function () {
        if (jQuery(this).find('input').length === 2) {
            let dataFieldType;
            let values = jQuery(this).find('input').map(function () {
                dataFieldType = jQuery(this).attr('data-field-type');
                if (jQuery(this).val()) {
                    return jQuery(this).val()
                }
            }).get();
            custom_fields[j++] = {
                type: 'from-to-' + dataFieldType,
                taxonomy: jQuery(this).attr('data-name'),
                value: values
            }
        } else if (jQuery(this).find('input[data-field=value]').length === 1) {
            if (jQuery(this).find('input[data-field=value]').val() != null) {
                custom_fields[j++] = {
                    type: 'text',
                    taxonomy: jQuery(this).attr('data-name'),
                    operator: jQuery(this).find('select[data-field=operator]').val(),
                    value: jQuery(this).find('input[data-field=value]').val()
                }
            }
        } else if (jQuery(this).find('select[data-field=value]').length === 1) {
            if (jQuery(this).find('select[data-field=value]').val() != null) {
                custom_fields[j++] = {
                    type: 'select',
                    taxonomy: jQuery(this).attr('data-name'),
                    value: jQuery(this).find('select[data-field=value]').val()
                }
            }
        }
    });

    data = {
        search_type: 'pro_search',
        order_ids: {
            operator: jQuery('#wobef-filter-form-order-ids-operator').val(),
            value: jQuery('#wobef-filter-form-order-ids').val(),
        },
        post_date: {
            from: jQuery('#wobef-filter-form-order-created-date-from').val(),
            to: jQuery('#wobef-filter-form-order-created-date-to').val(),
        },
        post_modified: {
            from: jQuery('#wobef-filter-form-order-modified-date-from').val(),
            to: jQuery('#wobef-filter-form-order-modified-date-to').val(),
        },
        _paid_date: {
            from: jQuery('#wobef-filter-form-order-paid-date-from').val(),
            to: jQuery('#wobef-filter-form-order-paid-date-to').val(),
        },
        _customer_ip_address: {
            operator: jQuery('#wobef-filter-form-order-customer-ip-address-operator').val(),
            value: jQuery('#wobef-filter-form-order-customer-ip-address').val(),
        },
        post_status: {
            value: jQuery('#wobef-filter-form-order-status').val(),
        },
        _billing_address_1: {
            operator: jQuery('#wobef-filter-form-order-billing-address-1-operator').val(),
            value: jQuery('#wobef-filter-form-order-billing-address-1').val(),
        },
        _billing_address_2: {
            operator: jQuery('#wobef-filter-form-order-billing-address-2-operator').val(),
            value: jQuery('#wobef-filter-form-order-billing-address-2').val(),
        },
        _billing_city: {
            operator: jQuery('#wobef-filter-form-order-billing-city-operator').val(),
            value: jQuery('#wobef-filter-form-order-billing-city').val(),
        },
        _billing_company: {
            operator: jQuery('#wobef-filter-form-order-billing-company-operator').val(),
            value: jQuery('#wobef-filter-form-order-billing-company').val(),
        },
        _billing_country: {
            value: jQuery('#wobef-filter-form-order-billing-country').val(),
        },
        _billing_state: {
            value: (jQuery('select.wobef-filter-form-order-billing-state').val()) ? jQuery('select.wobef-filter-form-order-billing-state').val() : jQuery('input.wobef-filter-form-order-billing-state').val()
        },
        _billing_email: {
            operator: jQuery('#wobef-filter-form-order-billing-email-operator').val(),
            value: jQuery('#wobef-filter-form-order-billing-email').val(),
        },
        _billing_phone: {
            operator: jQuery('#wobef-filter-form-order-billing-phone-operator').val(),
            value: jQuery('#wobef-filter-form-order-billing-phone').val(),
        },
        _billing_first_name: {
            operator: jQuery('#wobef-filter-form-order-billing-first-name-operator').val(),
            value: jQuery('#wobef-filter-form-order-billing-first-name').val(),
        },
        _billing_last_name: {
            operator: jQuery('#wobef-filter-form-order-billing-last-name-operator').val(),
            value: jQuery('#wobef-filter-form-order-billing-last-name').val(),
        },
        _billing_postcode: {
            operator: jQuery('#wobef-filter-form-order-billing-postcode-operator').val(),
            value: jQuery('#wobef-filter-form-order-billing-postcode').val(),
        },
        _shipping_address_1: {
            operator: jQuery('#wobef-filter-form-order-shipping-address-1-operator').val(),
            value: jQuery('#wobef-filter-form-order-shipping-address-1').val(),
        },
        _shipping_address_2: {
            operator: jQuery('#wobef-filter-form-order-shipping-address-2-operator').val(),
            value: jQuery('#wobef-filter-form-order-shipping-address-2').val(),
        },
        _shipping_city: {
            operator: jQuery('#wobef-filter-form-order-shipping-city-operator').val(),
            value: jQuery('#wobef-filter-form-order-shipping-city').val(),
        },
        _shipping_company: {
            operator: jQuery('#wobef-filter-form-order-shipping-company-operator').val(),
            value: jQuery('#wobef-filter-form-order-shipping-company').val(),
        },
        _shipping_country: {
            value: jQuery('#wobef-filter-form-order-shipping-country').val(),
        },
        _shipping_state: {
            value: (jQuery('select.wobef-filter-form-order-shipping-state').val()) ? jQuery('select.wobef-filter-form-order-shipping-state').val() : jQuery('input.wobef-filter-form-order-shipping-state').val(),
        },
        _shipping_first_name: {
            operator: jQuery('#wobef-filter-form-order-shipping-first-name-operator').val(),
            value: jQuery('#wobef-filter-form-order-shipping-first-name').val(),
        },
        _shipping_last_name: {
            operator: jQuery('#wobef-filter-form-order-shipping-last-name-operator').val(),
            value: jQuery('#wobef-filter-form-order-shipping-last-name').val(),
        },
        _shipping_postcode: {
            operator: jQuery('#wobef-filter-form-order-shipping-postcode-operator').val(),
            value: jQuery('#wobef-filter-form-order-shipping-postcode').val(),
        },
        _order_currency: {
            value: jQuery('#wobef-filter-form-order-currency').val(),
        },
        _order_discount: {
            from: jQuery('#wobef-filter-form-order-discount-from').val(),
            to: jQuery('#wobef-filter-form-order-discount-to').val(),
        },
        _order_discount_tax: {
            from: jQuery('#wobef-filter-form-order-discount-tax-from').val(),
            to: jQuery('#wobef-filter-form-order-discount-tax-to').val(),
        },
        _order_total: {
            from: jQuery('#wobef-filter-form-order-total-from').val(),
            to: jQuery('#wobef-filter-form-order-total-to').val(),
        },
        products: {
            operator: jQuery('#wobef-filter-form-order-products-operator').val(),
            value: jQuery('#wobef-filter-form-order-products').val(),
        },
        categories: {
            operator: jQuery('#wobef-filter-form-order-categories-operator').val(),
            value: jQuery('#wobef-filter-form-order-categories').val(),
        },
        tags: {
            operator: jQuery('#wobef-filter-form-order-tags-operator').val(),
            value: jQuery('#wobef-filter-form-order-tags').val(),
        },
        taxonomies: {
            operator: jQuery('#wobef-filter-form-order-taxonomies-operator').val(),
            value: jQuery('#wobef-filter-form-order-taxonomies').val(),
        },
        _created_via: {
            value: jQuery('#wobef-filter-form-order-create-via').val(),
        },
        _payment_method: {
            value: jQuery('#wobef-filter-form-order-payment-method').val(),
        },
        _shipping_tax: {
            value: jQuery('#wobef-filter-form-order-shipping-tax').val(),
        },
        _order_tax: {
            value: jQuery('#wobef-filter-form-order-tax').val(),
        },
        _order_shipping: {
            value: jQuery('#wobef-filter-form-order-shipping').val(),
        },
        _recorded_coupon_usage_counts: {
            value: jQuery('#wobef-filter-form-order-recorder-coupon-usage-counts').val(),
        },
        _order_stock_reduced: {
            value: jQuery('#wobef-filter-form-order-stock-reduced').val(),
        },
        _prices_include_tax: {
            value: jQuery('#wobef-filter-form-order-prices-index-tax').val(),
        },
        _recorded_sales: {
            value: jQuery('#wobef-filter-form-order-recorded-sales').val(),
        },
        custom_fields: custom_fields,
    };
    return data;
}

function wobefOrdersBulkEdit(orderIDs, data, filterData) {
    wobefLoadingStart();
    jQuery.ajax({
        url: WOBEF_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wobef_orders_bulk_edit',
            order_ids: orderIDs,
            new_data: data,
            current_page: wobefGetCurrentPage(),
            filter_data: filterData
        },
        success: function (response) {
            if (response.success) {
                wobefReloadOrders(response.order_ids);
                jQuery('.wobef-history-items tbody').html(response.history_items);
                wobefReInitDatePicker();
                wobefReInitColorPicker();
                let wobefTextEditors = jQuery('input[name="wobef-editors[]"]');
                if (wobefTextEditors.length > 0) {
                    wobefTextEditors.each(function () {
                        tinymce.execCommand('mceRemoveEditor', false, jQuery(this).val());
                        tinymce.execCommand('mceAddEditor', false, jQuery(this).val());
                    })
                }
            } else {
                wobefLoadingError();
            }
        },
        error: function () {
            wobefLoadingError();
        }
    });
}

function wobefGetTaxonomyParentSelectBox(taxonomy) {
    jQuery.ajax({
        url: WOBEF_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wobef_get_taxonomy_parent_select_box',
            taxonomy: taxonomy,
        },
        success: function (response) {
            if (response.success) {
                jQuery('#wobef-new-order-taxonomy-parent').html(response.options);
            }
        },
        error: function () {
        }
    });
}

function wobefGetOrderBilling(orderId) {
    wobGetOrderById(orderId, wobefSetOrderFieldsToBilling);
}

function wobefSetOrderFieldsToBilling(order) {
    if (order) {
        let element = jQuery('#wobef-modal-order-billing');
        element.find('[data-order-field="customer-user-id"]').attr('data-customer-id', order.customer_user_id);
        element.find('[data-order-field="first-name"]').val(order.billing_first_name);
        element.find('[data-order-field="last-name"]').val(order.billing_last_name);
        element.find('[data-order-field="address-1"]').val(order.billing_address_1);
        element.find('[data-order-field="address-2"]').val(order.billing_address_2);
        element.find('[data-order-field="city"]').val(order.billing_city);
        element.find('[data-order-field="phone"]').val(order.billing_phone);
        element.find('[data-order-field="email"]').val(order.billing_email);
        element.find('[data-order-field="postcode"]').val(order.billing_postcode);
        element.find('[data-order-field="company"]').val(order.billing_company);
        element.find('[data-order-field="transaction-id"]').val(order.transaction_id);
        element.find('[data-order-field="country"]').val(order.billing_country).change();
        element.find('[data-order-field="payment-method"]').val(order.payment_method).change();
        element.find('[data-order-field="state"]').val(order.billing_state).change();
    }
}

function wobefGetOrderShipping(orderId) {
    wobGetOrderById(orderId, wobefSetOrderFieldsToShipping);
}

function wobefSetOrderFieldsToShipping(order) {
    if (order) {
        let element = jQuery('#wobef-modal-order-shipping');
        element.find('[data-order-field="customer-user-id"]').attr('data-customer-id', order.customer_user_id);
        element.find('[data-order-field="first-name"]').val(order.shipping_first_name);
        element.find('[data-order-field="last-name"]').val(order.shipping_last_name);
        element.find('[data-order-field="address-1"]').val(order.shipping_address_1);
        element.find('[data-order-field="address-2"]').val(order.shipping_address_2);
        element.find('[data-order-field="city"]').val(order.shipping_city);
        element.find('[data-order-field="postcode"]').val(order.shipping_postcode);
        element.find('[data-order-field="company"]').val(order.shipping_company);
        element.find('[data-order-field="customer-note"]').val(order.customer_note);
        element.find('[data-order-field="country"]').val(order.shipping_country).change();
        element.find('[data-order-field="state"]').val(order.shipping_state).change();
    }
}

function wobefGetOrderDetails(orderId) {
    wobGetOrderById(orderId, wobefSetOrderFieldsToDetails);
}

function wobefSetOrderFieldsToDetails(order) {
    let element = jQuery('#wobef-modal-order-details');
    // clear form
    element.find('[data-order-field="status"]').text('');
    element.find('[data-order-field="billing-address-index"]').html('');
    element.find('[data-order-field="shipping-address-index"]').html('');
    element.find('[data-order-field="billing-email"]').html('');
    element.find('[data-order-field="billing-phone"]').html('');
    element.find('[data-order-field="payment-via"]').html('');
    element.find('[data-order-field="shipping-method"]').html('');
    element.find('.wobef-order-details-items tbody').html('');  

    if (order) {
        // set values
        element.find('[data-order-field="status"]').text(wobefGetOrderStatusName(order.post_status));
        element.find('[data-order-field="billing-address-index"]').html(order.billing_address_index);
        element.find('[data-order-field="shipping-address-index"]').html(order.shipping_address_index);
        element.find('[data-order-field="billing-email"]').html(order.billing_email);
        element.find('[data-order-field="billing-phone"]').html(order.billing_phone);
        element.find('[data-order-field="payment-via"]').html(order.payment_method_title);
        element.find('[data-order-field="shipping-method"]').html(order.shipping_method);
        if (order.order_items_array.length > 0) {
            order.order_items_array.forEach(function (item) {
                element.find('.wobef-order-details-items tbody').append("<tr><td><a target='_bland' href='" + item.product_link + "'>" + item.product_name + "</a></td><td>" + item.quantity + "</td><td>" + item.tax + " " + item.currency + "</td><td>" + item.total + " " + item.currency + "</td></tr>")
            });
        }
    }
}

function wobGetOrderById(orderId, handler) {
    jQuery.ajax({
        url: WOBEF_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wobef_get_order_details',
            order_id: orderId,
        },
        success: function (response) {
            if (response.success) {
                handler(response.order);
            }
        },
        error: function () {
        }
    });
}

function wobefGetOrderStatusName(orderStatus) {
    let status;
    switch (orderStatus) {
        case 'wc-cancelled':
            status = 'Cancelled';
            break;
        case 'wc-pending':
            status = 'Pending payment';
            break;
        case 'wc-completed':
            status = 'Completed';
            break;
        case 'wc-processing':
            status = 'Processing';
            break;
        case 'wc-on-hold':
            status = 'On hold';
            break;
        case 'wc-refunded':
            status = 'Refunded';
            break;
        case 'wc-failed':
            status = 'Failed';
            break;
    }

    return status;
}

function wobefOrderBillingUpdate(orderId, billingData) {
    wobefLoadingStart();
    jQuery.ajax({
        url: WOBEF_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wobef_order_billing_update',
            order_id: orderId,
            billing_data: billingData,
        },
        success: function (response) {
            if (response.success) {
                wobefLoadingSuccess();
            } else {
                wobefLoadingError();
            }
        },
        error: function () {
            wobefLoadingError();
        }
    });
}

function wobefOrderShippingUpdate(orderId, shippingData) {
    wobefLoadingStart();
    jQuery.ajax({
        url: WOBEF_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wobef_order_shipping_update',
            order_id: orderId,
            shipping_data: shippingData,
        },
        success: function (response) {
            if (response.success) {
                wobefLoadingSuccess();
            } else {
                wobefLoadingError();
            }
        },
        error: function () {
            wobefLoadingError();
        }
    });
}

function wobefClearInputs(element) {
    element.find('input').val('');
    element.find('textarea').val('');
    element.find('select option:first').prop('selected', true);
}

function wobefLoadCustomerDetails(customerId, target) {
    jQuery.ajax({
        url: WOBEF_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wobef_get_customer_billing_address',
            customer_id: customerId,
        },
        success: function (response) {
            if (response.success) {
                wobefSetCustomerDetails(response.billing_address, target);
            }
        },
        error: function () {
        }
    });
}

function wobefSetCustomerDetails(billingAddress, target) {
    let element = jQuery(target);
    if (element) {
        element.find('.wobef-customer-details-items span').text('');
        if (billingAddress.billing_address_1) {
            element.find('[data-customer-field="address-1"]').text(billingAddress.billing_address_1 + ', ');
        }
        if (billingAddress.billing_address_2) {
            element.find('[data-customer-field="address-2"]').text(billingAddress.billing_address_2 + ', ');
        }
        if (billingAddress.billing_city) {
            element.find('[data-customer-field="city"]').text(billingAddress.billing_city + ', ');
        }

        element.find('[data-customer-field="country"]').text(billingAddress.billing_country_name);
        element.find('[data-customer-field="phone"]').text(billingAddress.billing_phone);
        element.find('[data-customer-field="email"]').text(billingAddress.billing_email);
    }
}

function wobefLoadCustomerBillingAddress(customerId, target) {
    jQuery.ajax({
        url: WOBEF_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wobef_get_customer_billing_address',
            customer_id: customerId,
        },
        success: function (response) {
            if (response.success) {
                wobefSetCustomerBillingAddress(response.billing_address, target);
            }
        },
        error: function () {
        }
    });
}

function wobefLoadCustomerShippingAddress(customerId, target) {
    jQuery.ajax({
        url: WOBEF_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wobef_get_customer_shipping_address',
            customer_id: customerId,
        },
        success: function (response) {
            if (response.success) {
                wobefSetCustomerShippingAddress(response.shipping_address, target);
            }
        },
        error: function () {
        }
    });
}

function wobefSetCustomerBillingAddress(billingAddress, target) {
    let element = jQuery(target);
    if (element) {
        element.find('[data-order-field="first-name"]').val(billingAddress.billing_first_name);
        element.find('[data-order-field="last-name"]').val(billingAddress.billing_last_name);
        element.find('[data-order-field="address-1"]').val(billingAddress.billing_address_1);
        element.find('[data-order-field="address-2"]').val(billingAddress.billing_address_2);
        element.find('[data-order-field="city"]').val(billingAddress.billing_city);
        element.find('[data-order-field="phone"]').val(billingAddress.billing_phone);
        element.find('[data-order-field="email"]').val(billingAddress.billing_email);
        element.find('[data-order-field="postcode"]').val(billingAddress.billing_postcode);
        element.find('[data-order-field="company"]').val(billingAddress.billing_company);
        element.find('[data-order-field="country"]').val(billingAddress.billing_country).change();
        element.find('[data-order-field="state"]').val(billingAddress.billing_state).change();
    }
}

function wobefSetCustomerShippingAddress(shippingAddress, target) {
    let element = jQuery(target);
    if (element) {
        element.find('[data-order-field="first-name"]').val(shippingAddress.shipping_first_name);
        element.find('[data-order-field="last-name"]').val(shippingAddress.shipping_last_name);
        element.find('[data-order-field="address-1"]').val(shippingAddress.shipping_address_1);
        element.find('[data-order-field="address-2"]').val(shippingAddress.shipping_address_2);
        element.find('[data-order-field="city"]').val(shippingAddress.shipping_city);
        element.find('[data-order-field="postcode"]').val(shippingAddress.shipping_postcode);
        element.find('[data-order-field="company"]').val(shippingAddress.shipping_company);
        element.find('[data-order-field="country"]').val(shippingAddress.shipping_country).change();
        element.find('[data-order-field="state"]').val(shippingAddress.shipping_state).change();
    }
}

function wobefGetProducts() {
    let query;
    jQuery(".wobef-select2-products").select2({
        ajax: {
            type: "post",
            delay: 200,
            url: WOBEF_DATA.ajax_url,
            dataType: "json",
            data: function (params) {
                query = {
                    action: "wobef_get_products",
                    search: params.term,
                };
                return query;
            },
        },
        minimumInputLength: 1
    });
}

function wobefGetTaxonomies() {
    let query;
    jQuery(".wobef-select2-taxonomies").select2({
        ajax: {
            type: "post",
            delay: 200,
            url: WOBEF_DATA.ajax_url,
            dataType: "json",
            data: function (params) {
                query = {
                    action: "wobef_get_taxonomies",
                    search: params.term,
                };
                return query;
            },
        },
        minimumInputLength: 1
    });
}

function wobefGetTags() {
    let query;
    jQuery(".wobef-select2-tags").select2({
        ajax: {
            type: "post",
            delay: 200,
            url: WOBEF_DATA.ajax_url,
            dataType: "json",
            data: function (params) {
                query = {
                    action: "wobef_get_tags",
                    search: params.term,
                };
                return query;
            },
        },
        minimumInputLength: 1
    });
}

function wobefGetCategories() {
    let query;
    jQuery(".wobef-select2-categories").select2({
        ajax: {
            type: "post",
            delay: 200,
            url: WOBEF_DATA.ajax_url,
            dataType: "json",
            data: function (params) {
                query = {
                    action: "wobef_get_categories",
                    search: params.term,
                };
                return query;
            },
        },
        minimumInputLength: 1
    });
}

function wobefGetOrderNotes(orderId) {
    jQuery.ajax({
        url: WOBEF_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wobef_get_order_notes',
            order_id: orderId,
        },
        success: function (response) {
            if (response.success) {
                jQuery('#wobef-modal-order-notes-items').html(response.order_notes);
            }
        },
        error: function () {
        }
    });
}

function wobefAddOrderNote(data) {
    wobefLoadingStart();
    jQuery.ajax({
        url: WOBEF_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wobef_add_order_note',
            order_id: data.order_id,
            content: data.content,
            type: data.type,
        },
        success: function (response) {
            jQuery('#wobef-modal-order-notes-content').val('');
            jQuery('#wobef-modal-order-notes-type').val('private').change();
            jQuery('#wobef-modal-order-notes .wobef-modal-body').scrollTop(0);
            wobefLoadingSuccess('Success !');
            jQuery('#wobef-modal-order-notes-items').html(response.order_notes);
        },
        error: function () {
            wobefLoadingError('Error !');
        }
    });
}

function wobefDeleteOrderNote(noteId) {
    wobefLoadingStart();
    jQuery.ajax({
        url: WOBEF_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wobef_delete_order_note',
            note_id: noteId
        },
        success: function () {
            jQuery('#wobef-modal-order-notes .delete-note[data-note-id="' + noteId + '"]').closest('.wobef-order-note-item').remove();
            wobefLoadingSuccess('Success !');
        },
        error: function () {
            wobefLoadingError('Error !');
        }
    });
}

function wobefGetAddress(orderId, field) {
    jQuery.ajax({
        url: WOBEF_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wobef_get_order_address',
            order_id: orderId,
            field: field
        },
        success: function (response) {
            jQuery('textarea.wobef-modal-order-address-text').val(response.address);
            jQuery('div.wobef-modal-order-address-text').html(response.address);
        },
        error: function () {
        }
    });
}

function wobefGetOrderItems(orderId) {
    jQuery.ajax({
        url: WOBEF_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wobef_get_order_items',
            order_id: orderId
        },
        success: function (response) {
            if (response.order_items.length > 0) {
                response.order_items.forEach(function (item) {
                    jQuery('#wobef-modal-order-items .wobef-order-items-table tbody').append("<tr><td><a target='_blank' href='" + item.product_link + "'>" + item.product_name + "</a></td><td>" + item.quantity + "</td></tr>")
                });
            } else {
                jQuery('#wobef-modal-order-items .wobef-order-items-table tbody').append("<tr><td class='wobef-red-text'>There is not any order item</td></tr>")
            }
        },
        error: function () {
        }
    });
}

function wobefOrderEdit(orderIds, orderData) {
    wobefLoadingStart();
    jQuery.ajax({
        url: WOBEF_DATA.ajax_url,
        type: 'post',
        dataType: 'json',
        data: {
            action: 'wobef_order_edit',
            order_ids: orderIds,
            order_data: orderData,
            filter_data: wobefGetCurrentFilterData(),
            current_page: wobefGetCurrentPage(),
        },
        success: function (response) {
            if (response.success) {
                wobefReloadRows(response.orders, response.order_statuses);
                wobefSetStatusFilter(response.status_filters);
                jQuery('.wobef-history-items tbody').html(response.history_items);
                wobefReInitDatePicker();
                wobefReInitColorPicker();
                let wobefTextEditors = jQuery('input[name="wobef-editors[]"]');
                if (wobefTextEditors.length > 0) {
                    wobefTextEditors.each(function () {
                        tinymce.execCommand('mceRemoveEditor', false, jQuery(this).val());
                        tinymce.execCommand('mceAddEditor', false, jQuery(this).val());
                    })
                }
                wobefLoadingSuccess();
            } else {
                wobefLoadingError();
            }
        },
        error: function () {
            wobefLoadingError();
        }
    });
}

function wobefSetStatusFilter(statusFilters) {
    jQuery('.wobef-top-nav-status-filter').html(statusFilters);

    jQuery('.wobef-bulk-edit-status-filter-item').removeClass('active');
    let statusFilter = (jQuery('#wobef-filter-form-order-status').val()) ? jQuery('#wobef-filter-form-order-status').val() : 'all';
    if (jQuery.isArray(statusFilter)) {
        statusFilter.forEach(function (val) {
            jQuery('.wobef-bulk-edit-status-filter-item[data-status="' + val + '"]').addClass('active');
        });
    } else {
        jQuery('.wobef-bulk-edit-status-filter-item[data-status="' + statusFilter + '"]').addClass('active');
    }
}

function wobefReloadRows(orders, statuses) {
    let currentStatus = (jQuery('#wobef-filter-form-product-status').val());

    jQuery('tr').removeClass('wobef-item-edited').find('.wobef-check-item').prop('checked', false);
    if (Object.keys(orders).length > 0) {
        jQuery.each(orders, function (key, val) {
            if (statuses[key] === currentStatus || (!currentStatus && statuses[key] !== 'trash')) {
                jQuery('#wobef-items-list').find('tr[data-item-id="' + key + '"]').replaceWith(val);
                jQuery('tr[data-item-id="' + key + '"]').addClass('wobef-item-edited').find('.wobef-check-item').prop('checked', true);
            } else {
                jQuery('#wobef-items-list').find('tr[data-item-id="' + key + '"]').remove();
            }
        });
        wobefShowSelectionTools();
    } else {
        wobefHideSelectionTools();
    }
}

function wobefGetOrdersChecked() {
    let orderIds = [];
    let ordersChecked = jQuery("input.wobef-check-item:checkbox:checked");
    if (ordersChecked.length > 0) {
        orderIds = ordersChecked.map(function (i) {
            return jQuery(this).val();
        }).get();
    }
    return orderIds;
}