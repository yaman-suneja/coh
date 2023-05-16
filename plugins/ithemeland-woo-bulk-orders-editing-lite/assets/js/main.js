jQuery(document).ready(function ($) {
    "use strict";

    var navigationTopOffset;
    if ($('#wobef-bulk-edit-navigation').length) {
        navigationTopOffset = $("#wobef-bulk-edit-navigation").offset().top;
    }

    $(document).on('click', '.wobef-timepicker, .wobef-datetimepicker, .wobef-datepicker', function () {
        $(this).attr('data-val', $(this).val());
    });

    wobefReInitDatePicker();
    wobefReInitColorPicker();

    // Select2
    if ($.fn.select2) {
        let wobefSelect2 = $(".wobef-select2");
        if (wobefSelect2.length) {
            wobefSelect2.select2({
                placeholder: "Select ..."
            });
        }
    }

    if ($.fn.scrollbar) {
        $("#wobef-items-table").scrollbar({
            autoScrollSize: false,
            scrollx: $(".external-scroll_x"),
        });
    }

    let mainTabs = [
        'bulk-edit',
        'column-manager',
        'meta-fields',
        'history',
        'import-export',
        'settings',
        'activation',
    ]
    let currentTab = (window.location.hash && $.inArray(window.location.hash.split('#')[1], mainTabs) !== -1) ? window.location.hash.split('#')[1] : 'bulk-edit';
    window.location.hash = currentTab;
    wobefOpenTab($('.wobef-tabs-list li a[data-content="' + currentTab + '"]'));
    if ($("#wobef-bulk-edit-navigation").length > 0) {
        navigationTopOffset = ($("#wobef-bulk-edit-navigation").offset().top > 300) ? $("#wobef-bulk-edit-navigation").offset().top : 300;
    }

    // Tabs
    $(document).on("click", ".wobef-tabs-list li a", function (event) {
        if ($(this).attr('data-disabled') !== 'true') {
            event.preventDefault();
            window.location.hash = $(this).attr('data-content');
            wobefOpenTab($(this));
            if ($("#wobef-bulk-edit-navigation").length > 0) {
                navigationTopOffset = ($("#wobef-bulk-edit-navigation").offset().top > 300) ? $("#wobef-bulk-edit-navigation").offset().top : 300;
            }
        }
    });

    $(window).scroll(function () {
        if ($('a[data-content=bulk-edit]').hasClass('selected')) {
            let top = ($(window).width() > 768) ? "32px" : "0";
            if ($(window).scrollTop() >= navigationTopOffset) {
                $("#wobef-bulk-edit-navigation").css({
                    position: "fixed",
                    top: top,
                    "z-index": 15000,
                    width: $("#wobef-items-table").width()
                });
            } else {
                $("#wobef-bulk-edit-navigation").css({
                    position: "static",
                    width: "100%"
                });
            }
        }
    });

    // Filter Form (Show & Hide)
    $(".wobef-filter-form-toggle").on("click", function () {
        if ($("#wobef-filter-form-content").attr("data-visibility") === "visible") {
            wobefFilterFormClose();
        } else {
            wobefFilterFormOpen();
        }

        if ($("#wobef-filter-form").css("position") === "static") {
            setTimeout(function () {
                navigationTopOffset = $("#wobef-bulk-edit-navigation").offset().top;
            }, 300);
        }
    });

    // Modal
    $(document).on("click", "[data-toggle=modal]", function () {
        $($(this).attr("data-target")).fadeIn();
        $($(this).attr("data-target") + " .wobef-modal-box").fadeIn();
        $("#wobef-last-modal-opened").val($(this).attr("data-target"));

        // set height for modal body
        let titleHeight = $($(this).attr("data-target") + " .wobef-modal-box .wobef-modal-title").height();
        let footerHeight = $($(this).attr("data-target") + " .wobef-modal-box .wobef-modal-footer").height();
        $($(this).attr("data-target") + " .wobef-modal-box .wobef-modal-body").css({
            "max-height": parseInt($($(this).attr("data-target") + " .wobef-modal-box").height()) - parseInt(titleHeight + footerHeight + 150) + "px"
        });

        $($(this).attr("data-target") + " .wobef-modal-box-lg .wobef-modal-body").css({
            "max-height": parseInt($($(this).attr("data-target") + " .wobef-modal-box").height()) - parseInt(titleHeight + footerHeight + 120) + "px"
        });
    });

    $(document).on("click", "[data-toggle=modal-close]", function () {
        wobefCloseModal();
    });

    $(document).on("keyup", function (e) {
        if (e.keyCode === 27) {
            wobefCloseModal();
            $("[data-type=edit-mode]").each(function () {
                $(this).closest("span").html($(this).attr("data-val"));
            });
        }
    });

    // Color Picker Style
    $(document).on("change", "input[type=color]", function () {
        this.parentNode.style.backgroundColor = this.value;
    });

    $(document).on('click', '#wobef-full-screen', function () {
        if ($('#adminmenuback').css('display') === 'block') {
            $('#adminmenuback, #adminmenuwrap').hide();
            $('#wpcontent, #wpfooter').css({ "margin-left": 0 });
        } else {
            $('#adminmenuback, #adminmenuwrap').show();
            $('#wpcontent, #wpfooter').css({ "margin-left": "160px" });
        }
    });

    // Select Items (Checkbox) in table
    $(document).on("change", ".wobef-check-item-main", function () {
        let checkbox_items = $(".wobef-check-item");
        if ($(this).prop("checked") === true) {
            checkbox_items.prop("checked", true);
            $("#wobef-items-list tr").addClass("wobef-tr-selected");
            checkbox_items.each(function () {
                $("#wobef-export-items-selected").append("<input type='hidden' name='item_ids[]' value='" + $(this).val() + "'>");
            });
            wobefShowSelectionTools();
            $("#wobef-export-only-selected-items").prop("disabled", false);
        } else {
            checkbox_items.prop("checked", false);
            $("#wobef-items-list tr").removeClass("wobef-tr-selected");
            $("#wobef-export-items-selected").html("");
            wobefHideSelectionTools();
            $("#wobef-export-only-selected-items").prop("disabled", true);
            $("#wobef-export-all-items-in-table").prop("checked", true);
        }
    });

    $(document).on("change", ".wobef-check-item", function () {
        if ($(this).prop("checked") === true) {
            $("#wobef-export-items-selected").append("<input type='hidden' name='item_ids[]' value='" + $(this).val() + "'>");
            if ($(".wobef-check-item:checked").length === $(".wobef-check-item").length) {
                $(".wobef-check-item-main").prop("checked", true);
            }
            $(this).closest("tr").addClass("wobef-tr-selected");
        } else {
            $("#wobef-export-items-selected").find("input[value=" + $(this).val() + "]").remove();
            $(this).closest("tr").removeClass("wobef-tr-selected");
            $(".wobef-check-item-main").prop("checked", false);
        }

        // Disable and enable "Only Selected items" in "Import/Export"
        if ($(".wobef-check-item:checkbox:checked").length > 0) {
            $("#wobef-export-only-selected-items").prop("disabled", false);
            wobefShowSelectionTools();
        } else {
            wobefHideSelectionTools();
            $("#wobef-export-only-selected-items").prop("disabled", true);
            $("#wobef-export-all-items-in-table").prop("checked", true);
        }
    });

    $(document).on("click", "#wobef-bulk-edit-unselect", function () {
        $("input.wobef-check-item").prop("checked", false);
        $("input.wobef-check-item-main").prop("checked", false);
        wobefHideSelectionTools();
    });

    // Start "Column Profile"
    $(document).on("change", "#wobef-column-profiles-choose", function () {
        $('#wobef-column-profile-select-all').prop('checked', false).attr('data-profile-name', $(this).val());
        $('.wobef-column-profile-select-all span').text('Select All');
        $(".wobef-column-profile-fields").hide();
        $(".wobef-column-profile-fields[data-content=" + $(this).val() + "]").show();
        $("#wobef-column-profiles-apply").attr("data-preset-key", $(this).val());
        if (defaultPresets && $.inArray($(this).val(), defaultPresets) === -1) {
            $("#wobef-column-profiles-update-changes").show();
        } else {
            $("#wobef-column-profiles-update-changes").hide();
        }
    });

    $(document).on("keyup", "#wobef-column-profile-search", function () {
        let wobefSearchFieldValue = $(this).val().toLowerCase().trim();
        $(".wobef-column-profile-fields ul li").filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(wobefSearchFieldValue) > -1);
        });
    });

    $(document).on('change', '#wobef-column-profile-select-all', function () {
        if ($(this).prop('checked') === true) {
            $(this).closest('label').find('span').text('Unselect');
            $('.wobef-column-profile-fields[data-content=' + $(this).attr('data-profile-name') + '] input:checkbox:visible').prop('checked', true);
        } else {
            $(this).closest('label').find('span').text('Select All');
            $('.wobef-column-profile-fields[data-content=' + $(this).attr('data-profile-name') + '] input:checkbox').prop('checked', false);
        }
    });
    // End "Column Profile"

    // Calculator for numeric TD
    $(document).on(
        {
            mouseenter: function () {
                $(this)
                    .children(".wobef-calculator")
                    .show();
            },
            mouseleave: function () {
                $(this)
                    .children(".wobef-calculator")
                    .hide();
            }
        },
        "td[data-content-type=regular_price], td[data-content-type=sale_price], td[data-content-type=numeric]"
    );

    // delete items button
    $(document).on("click", ".wobef-bulk-edit-delete-item", function () {
        $(this).find(".wobef-bulk-edit-delete-item-buttons").slideToggle(200);
    });

    $('#wp-admin-bar-root-default').append('<li id="wp-admin-bar-wobef-col-view"></li>');

    $(document).on(
        {
            mouseenter: function () {
                $('#wp-admin-bar-wobef-col-view').html('#' + $(this).attr('data-item-id') + ' | ' + $(this).attr('data-item-title') + ' [<span class="wobef-col-title">' + $(this).attr('data-col-title') + '</span>] ');
            },
            mouseleave: function () {
                $('#wp-admin-bar-wobef-col-view').html('');
            }
        },
        "#wobef-items-list td"
    );

    $(document).on("click", ".wobef-open-uploader", function (e) {
        let target = $(this).attr("data-target");
        let element = $(this).closest('div');
        let type = $(this).attr("data-type");
        let mediaUploader;
        let wobefNewImageElementID = $(this).attr("data-id");
        let wobefProductID = $(this).attr("data-item-id");
        e.preventDefault();
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }
        if (type === "single") {
            mediaUploader = wp.media.frames.file_frame = wp.media({
                title: "Choose Image",
                button: {
                    text: "Choose Image"
                },
                multiple: false
            });
        } else {
            mediaUploader = wp.media.frames.file_frame = wp.media({
                title: "Choose Images",
                button: {
                    text: "Choose Images"
                },
                multiple: true
            });
        }

        mediaUploader.on("select", function () {
            let attachment = mediaUploader.state().get("selection").toJSON();
            switch (target) {
                case "inline-file":
                    $("#url-" + wobefNewImageElementID).val(attachment[0].url);
                    break;
                case "inline-file-custom-field":
                    $("#wobef-file-url").val(attachment[0].url);
                    $('#wobef-file-id').val(attachment[0].id)
                    break;
                case "inline-edit":
                    $("#" + wobefNewImageElementID).val(attachment[0].url);
                    $("[data-image-preview-id=" + wobefNewImageElementID + "]").html("<img src='" + attachment[0].url + "' alt='' />");
                    $("#wobef-modal-image button[data-item-id=" + wobefProductID + "][data-button-type=save]").attr("data-image-id", attachment[0].id).attr("data-image-url", attachment[0].url);
                    break;
                case "inline-edit-gallery":
                    attachment.forEach(function (item) {
                        $("div[data-gallery-id=wobef-gallery-items-" + wobefProductID + "]").append('<div class="wobef-inline-edit-gallery-item"><img src="' + item.url + '" alt=""><input type="hidden" class="wobef-inline-edit-gallery-image-ids" value="' + item.id + '"></div>');
                    });
                    break;
                case "bulk-edit-image":
                    element.find(".wobef-bulk-edit-form-item-image").val(attachment[0].id);
                    element.find(".wobef-bulk-edit-form-item-image-preview").html('<div><img src="' + attachment[0].url + '" width="43" height="43" alt=""><button type="button" class="wobef-bulk-edit-form-remove-image">x</button></div>');
                    break;
                case "bulk-edit-file":
                    element.find(".wobef-bulk-edit-form-item-file").val(attachment[0].id);
                    break;
                case "bulk-edit-gallery":
                    attachment.forEach(function (item) {
                        $("#wobef-bulk-edit-form-item-gallery").append('<input type="hidden" value="' + item.id + '">');
                        $("#wobef-bulk-edit-form-item-gallery-preview").append('<div><img src="' + item.url + '" width="43" height="43" alt=""><button type="button" data-id="' + item.id + '" class="wobef-bulk-edit-form-remove-gallery-item">x</button></div>');
                    });
                    break;
            }
        });
        mediaUploader.open();
    });

    $(document).on("change", ".wobef-column-manager-check-all-fields-btn input:checkbox", function () {
        if ($(this).prop("checked")) {
            $(this).closest("label").find("span").addClass("selected").text("Unselect");
            $(".wobef-column-manager-available-fields[data-action=" + $(this).closest("label").attr("data-action") + "] li:visible").each(function () {
                $(this).find("input:checkbox").prop("checked", true);
            });
        } else {
            $(this).closest("label").find("span").removeClass("selected").text("Select All");
            $(".wobef-column-manager-available-fields[data-action=" + $(this).closest("label").attr("data-action") + "] li:visible input:checked").prop("checked", false);
        }
    });

    $(document).on("keyup", ".wobef-column-manager-search-field", function () {
        let wobefSearchFieldValue = $(this).val().toLowerCase().trim();
        $(".wobef-column-manager-available-fields[data-action=" + $(this).attr("data-action") + "] ul li[data-added=false]").filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(wobefSearchFieldValue) > -1);
        });
    });

    if ($.fn.sortable) {
        let wobefMetaFieldItems = $(".wobef-meta-fields-right");
        wobefMetaFieldItems.sortable({
            handle: ".wobef-meta-field-item-sortable-btn",
            cancel: ""
        });
        wobefMetaFieldItems.disableSelection();
    }

    $(document).on("click", "#wobef-add-meta-field-manual", function () {
        $(".wobef-meta-fields-empty-text").hide();
        let input = $("#wobef-meta-fields-manual_key_name");
        wobefAddMetaKeysManual(input.val());
        input.val("");
    });

    $(document).on("click", "#wobef-add-acf-meta-field", function () {
        let input = $("#wobef-add-meta-fields-acf");
        if (input.val()) {
            $(".wobef-meta-fields-empty-text").hide();
            wobefAddACFMetaField(input.val(), input.find('option:selected').text(), input.find('option:selected').attr('data-type'));
            input.val("").change();
        }
    });

    $(document).on("click", ".wobef-meta-field-remove", function () {
        $(this).closest(".wobef-meta-fields-right-item").remove();
        if ($(".wobef-meta-fields-right-item").length < 1) {
            $(".wobef-meta-fields-empty-text").show();
        }
    });

    $(document).on('click', '.wobef-modal', function (e) {
        if ($(e.target).hasClass('wobef-modal') || $(e.target).hasClass('wobef-modal-container') || $(e.target).hasClass('wobef-modal-box')) {
            wobefCloseModal();
        }
    });

    $(document).on("change", 'select[data-field="operator"]', function () {
        if ($(this).val() === "number_formula") {
            $(this).closest("div").find("input[type=number]").attr("type", "text");
        }
    });

    $(document).on('change', '#wobef-filter-form-content [data-field=value], #wobef-filter-form-content [data-field=from], #wobef-filter-form-content [data-field=to]', function () {
        wobefCheckFilterFormChanges();
    });

    $(document).on('change', 'input[type=number][data-field=to]', function () {
        let from = $(this).closest('.wobef-form-group').find('input[type=number][data-field=from]');
        if (parseFloat($(this).val()) < parseFloat(from.val())) {
            from.val('').addClass('wobef-input-danger').focus();
        }
    });

    $(document).on('change', 'input[type=number][data-field=from]', function () {
        let to = $(this).closest('.wobef-form-group').find('input[type=number][data-field=to]');
        if (parseFloat($(this).val()) > parseFloat(to.val())) {
            $(this).val('').addClass('wobef-input-danger');
        } else {
            $(this).removeClass('wobef-input-danger')
        }
    });

    $(document).on('change', '#wobef-switcher', function () {
        wobefLoadingStart();
        $('#wobef-switcher-form').submit();
    });

    $(document).on('click', 'span[data-target="#wobef-modal-image"]', function () {
        let modal = $('#wobef-modal-image');
        let col_title = $(this).closest('td').attr('data-col-title');
        let id = $(this).attr('data-id');
        let image_id = $(this).attr('data-image-id');
        let item_id = $(this).closest('td').attr('data-item-id');
        let full_size_url = $(this).attr('data-full-image-src');
        let field = $(this).closest('td').attr('data-field');
        let field_type = $(this).closest('td').attr('data-field-type');

        $('#wobef-modal-image-item-title').text(col_title);
        modal.find('.wobef-open-uploader').attr('data-id', id).attr('data-item-id', item_id);
        modal.find('.wobef-inline-image-preview').attr('data-image-preview-id', id).html('<img src="' + full_size_url + '" />');
        modal.find('.wobef-image-preview-hidden-input').attr('id', id);
        modal.find('button[data-button-type="save"]').attr('data-item-id', item_id).attr('data-field', field).attr('data-image-url', full_size_url).attr('data-image-id', image_id).attr('data-field-type', field_type);
        modal.find('button[data-button-type="remove"]').attr('data-item-id', item_id).attr('data-field', field).attr('data-field-type', field_type);
    });

    $(document).on('click', 'button[data-target="#wobef-modal-file"]', function () {
        let modal = $('#wobef-modal-file');
        modal.find('#wobef-modal-select-file-item-title').text($(this).closest('td').attr('data-col-title'));
        modal.find('#wobef-modal-file-apply').attr('data-item-id', $(this).attr('data-item-id')).attr('data-field', $(this).attr('data-field')).attr('data-field-type', $(this).attr('data-field-type'));
        modal.find('#wobef-file-id').val($(this).attr('data-file-id'));
        modal.find('#wobef-file-url').val($(this).attr('data-file-url'));
    });

    $(document).on('click', '#wobef-modal-file-clear', function () {
        let modal = $('#wobef-modal-file');
        modal.find('#wobef-file-id').val(0).change();
        modal.find('#wobef-file-url').val('').change();
    });

    // Inline edit
    $(document).on("click", "td[data-action=inline-editable]", function (e) {
        if ($(e.target).attr("data-type") !== "edit-mode" && $(e.target).find("[data-type=edit-mode]").length === 0) {
            // Close All Inline Edit
            $("[data-type=edit-mode]").each(function () {
                $(this).closest("span").html($(this).attr("data-val"));
            });
            // Open Clicked Inline Edit
            switch ($(this).attr("data-content-type")) {
                case "text":
                case "select":
                case "password":
                case "url":
                case "email":
                    $(this).children("span").html("<textarea data-item-id='" + $(this).attr("data-item-id") + "' data-field='" + $(this).attr("data-field") + "' data-field-type='" + $(this).attr("data-field-type") + "' data-type='edit-mode' data-val='" + $(this).text().trim() + "'>" + $(this).text().trim() + "</textarea>").children("textarea").focus().select();
                    break;
                case "numeric":
                case "regular_price":
                case "sale_price":
                    $(this).children("span").html("<input type='number' min='-1' data-item-id='" + $(this).attr("data-item-id") + "' data-field='" + $(this).attr("data-field") + "' data-field-type='" + $(this).attr("data-field-type") + "' data-type='edit-mode' data-val='" + $(this).text().trim() + "' value='" + $(this).text().trim() + "'>").children("input[type=number]").focus().select();
                    break;
            }
        }
    });

    // Discard Save
    $(document).on("click", function (e) {
        if ($(e.target).attr("data-action") !== "inline-editable" && $(e.target).attr("data-type") !== "edit-mode") {
            $("[data-type=edit-mode]").each(function () {
                $(this).closest("span").html($(this).attr("data-val"));
            });
        }
    });

    // Save Inline Edit By Enter Key
    $(document).on("keypress", "[data-type=edit-mode]", function (event) {
        let wobefKeyCode = event.keyCode ? event.keyCode : event.which;
        if (wobefKeyCode === 13) {
            let orderData = [];
            let orderIds = [];
            let tdElement = $(this).closest('td');

            if ($('#wobef-inline-edit-bind').prop('checked') === true) {
                orderIds = wobefGetOrdersChecked();
            }
            orderIds.push($(this).attr("data-item-id"));

            orderData.push({
                name: tdElement.attr('data-name'),
                sub_name: (tdElement.attr('data-sub-name')) ? tdElement.attr('data-sub-name') : '',
                type: tdElement.attr('data-update-type'),
                value: $(this).val(),
                operation: 'inline_edit'
            });

            $(this).closest("span").html($(this).val());
            wobefOrderEdit(orderIds, orderData);
        }
    });

    // fetch order data by click to bulk edit button
    $(document).on("click", "#wobef-bulk-edit-bulk-edit-btn", function () {
        if ($(this).attr("data-fetch-order") === "yes") {
            let orderID = $("input.wobef-check-item:checkbox:checked");
            if (orderID.length === 1) {
                wobefGetOrderData(orderID.val());
            } else {
                wobefResetBulkEditForm();
            }
        }
    });

    $(document).on('click', '.wobef-inline-edit-color-action', function () {
        $(this).closest('td').find('input.wobef-inline-edit-action').trigger('change');
    });

    $(document).on("change", ".wobef-inline-edit-action", function (e) {
        let $this = $(this);
        setTimeout(function () {
            if ($('div.xdsoft_datetimepicker:visible').length > 0) {
                e.preventDefault();
                return false;
            }

            if ($this.hasClass('wobef-datepicker') || $this.hasClass('wobef-timepicker') || $this.hasClass('wobef-datetimepicker')) {
                if ($this.attr('data-val') == $this.val()) {
                    e.preventDefault();
                    return false;
                }
            }

            let orderData = [];
            let orderIds = [];
            let tdElement = $this.closest('td');
            if ($('#wobef-inline-edit-bind').prop('checked') === true) {
                orderIds = wobefGetOrdersChecked();
            }
            orderIds.push($this.attr("data-item-id"));
            let wobefValue;
            switch (tdElement.attr("data-content-type")) {
                case 'checkbox_dual_mode':
                    wobefValue = $this.prop("checked") ? "yes" : "no";
                    break;
                case 'checkbox':
                    let checked = [];
                    tdElement.find('input[type=checkbox]:checked').each(function () {
                        checked.push($(this).val());
                    });
                    wobefValue = checked;
                    break;
                default:
                    wobefValue = $this.val();
                    break;
            }

            orderData.push({
                name: tdElement.attr('data-name'),
                sub_name: (tdElement.attr('data-sub-name')) ? tdElement.attr('data-sub-name') : '',
                type: tdElement.attr('data-update-type'),
                value: wobefValue,
                operation: 'inline_edit'
            });

            wobefOrderEdit(orderIds, orderData);
        }, 250)
    });

    $(document).on("click", ".wobef-inline-edit-clear-date", function () {
        let orderData = [];
        let orderIds = [];
        let tdElement = $(this).closest('td');

        if ($('#wobef-inline-edit-bind').prop('checked') === true) {
            orderIds = wobefGetOrdersChecked();
        }
        orderIds.push($(this).attr("data-item-id"));
        orderData.push({
            name: tdElement.attr('data-name'),
            sub_name: (tdElement.attr('data-sub-name')) ? tdElement.attr('data-sub-name') : '',
            type: tdElement.attr('data-update-type'),
            value: '',
            operation: 'inline_edit'
        });

        wobefOrderEdit(orderIds, orderData);
    });

    $(document).on("click", ".wobef-edit-action-price-calculator", function () {
        let orderId = $(this).attr("data-item-id");
        let fieldName = $(this).attr("data-field");
        let orderIds = [];
        let orderData = [];

        if ($('#wobef-inline-edit-bind').prop('checked') === true) {
            orderIds = wobefGetOrdersChecked();
        }
        orderIds.push(orderId);
        orderData.push({
            name: fieldName,
            sub_name: '',
            type: $(this).attr('data-update-type'),
            operator: $("#wobef-" + fieldName + "-calculator-operator-" + orderId).val(),
            value: $("#wobef-" + fieldName + "-calculator-value-" + orderId).val(),
            operator_type: $("#wobef-" + fieldName + "-calculator-type-" + orderId).val(),
            round: $("#wobef-" + fieldName + "-calculator-round-" + orderId).val()
        });

        wobefOrderEdit(orderIds, orderData);
    });

    $(document).on("click", ".wobef-bulk-edit-delete-action", function () {
        let deleteType = $(this).attr('data-delete-type');
        let OrderIds;
        let ordersChecked = $("input.wobef-check-item:checkbox:checked");
        OrderIds = ordersChecked.map(function () {
            return $(this).val();
        }).get();
        swal({
            title: "Are you sure?",
            type: "warning",
            showCancelButton: true,
            cancelButtonClass: "wobef-button wobef-button-lg wobef-button-white",
            confirmButtonClass: "wobef-button wobef-button-lg wobef-button-green",
            confirmButtonText: "Yes, I'm sure !",
            closeOnConfirm: true
        }, function (isConfirm) {
            if (isConfirm) {
                if (OrderIds.length > 0) {
                    wobefDeleteOrder(OrderIds, deleteType);
                } else {
                    swal({
                        title: "Please Select Order !",
                        type: "warning"
                    });
                }
            }
        });
    });

    $(document).on("click", "#wobef-bulk-edit-duplicate-start", function () {
        let orderIDs = $("input.wobef-check-item:checkbox:checked").map(function () {
            if ($(this).attr('data-item-type') === 'variation') {
                swal({
                    title: "Duplicate for variations order is disabled!",
                    type: "warning"
                });
                return false;
            }
            return $(this).val();
        }).get();
        wobefDuplicateOrder(orderIDs, parseInt($("#wobef-bulk-edit-duplicate-number").val()));
    });

    $(document).on("click", "#wobef-create-new-item", function () {
        let count = $("#wobef-new-item-count").val();
        wobefCreateNewOrder(count);
    });

    $(document).on("click", "#wobef-column-profiles-save-as-new-preset", function () {
        swal({
            title: "Are you sure?",
            type: "warning",
            showCancelButton: true,
            cancelButtonClass: "wobef-button wobef-button-lg wobef-button-white",
            confirmButtonClass: "wobef-button wobef-button-lg wobef-button-green",
            confirmButtonText: "Yes, I'm sure !",
            closeOnConfirm: true
        }, function (isConfirm) {
            if (isConfirm) {
                let presetKey = $("#wobef-column-profiles-choose").val();
                let items = $(".wobef-column-profile-fields[data-content=" + presetKey + "] input:checkbox:checked").map(function () {
                    return $(this).val();
                }).get();
                wobefSaveColumnProfile(presetKey, items, "save_as_new");
            }
        });
    });

    $(document).on("click", "#wobef-column-profiles-update-changes", function () {
        swal({
            title: "Are you sure?",
            type: "warning",
            showCancelButton: true,
            cancelButtonClass: "wobef-button wobef-button-lg wobef-button-white",
            confirmButtonClass: "wobef-button wobef-button-lg wobef-button-green",
            confirmButtonText: "Yes, I'm sure !",
            closeOnConfirm: true
        }, function (isConfirm) {
            if (isConfirm) {
                let presetKey = $("#wobef-column-profiles-choose").val();
                let items = $(".wobef-column-profile-fields[data-content=" + presetKey + "] input:checkbox:checked").map(function () {
                    return $(this).val();
                }).get();
                wobefSaveColumnProfile(presetKey, items, "update_changes");
            }
        });
    });

    $(document).on("click", ".wobef-bulk-edit-filter-profile-load", function () {
        wobefLoadFilterProfile($(this).val());
        if ($(this).val() !== "default") {
            $("#wobef-bulk-edit-reset-filter").show();
        }
        $(".wobef-filter-profiles-items tr").removeClass("wobef-filter-profile-loaded");
        $(this).closest("tr").addClass("wobef-filter-profile-loaded");
    });

    $(document).on("click", ".wobef-bulk-edit-filter-profile-delete", function () {
        let presetKey = $(this).val();
        let item = $(this).closest("tr");
        swal({
            title: "Are you sure?",
            type: "warning",
            showCancelButton: true,
            cancelButtonClass: "wobef-button wobef-button-lg wobef-button-white",
            confirmButtonClass: "wobef-button wobef-button-lg wobef-button-green",
            confirmButtonText: "Yes, I'm sure !",
            closeOnConfirm: true
        }, function (isConfirm) {
            if (isConfirm) {
                wobefDeleteFilterProfile(presetKey);
                if (item.hasClass('wobef-filter-profile-loaded')) {
                    $('.wobef-filter-profiles-items tbody tr:first-child').addClass('wobef-filter-profile-loaded').find('input[type=radio]').prop('checked', true);
                    $('#wobef-bulk-edit-reset-filter').trigger('click');
                    item.remove();
                }
            }
        });
    });

    $(document).on("change", "input.wobef-filter-profile-use-always-item", function () {
        if ($(this).val() !== "default") {
            $("#wobef-bulk-edit-reset-filter").show();
        } else {
            $("#wobef-bulk-edit-reset-filter").hide();
        }
        wobefFilterProfileChangeUseAlways($(this).val());
    });

    $(document).on("click", ".wobef-filter-form-action", function (e) {
        let data = wobefGetCurrentFilterData();
        let page;
        let action = $(this).attr("data-search-action");
        if (action === "pagination") {
            page = $(this).attr("data-index");
        }
        if (action === "quick_search") {
            wobefResetFilterForm();
        }
        if (action === "pro_search") {
            $('#wobef-bulk-edit-reset-filter').show();
            wobefResetQuickSearchForm();
            $(".wobef-filter-profiles-items tr").removeClass("wobef-filter-profile-loaded");
            $('input.wobef-filter-profile-use-always-item[value="default"]').prop("checked", true).closest("tr");
            wobefFilterProfileChangeUseAlways("default");
        }
        wobefOrdersFilter(data, action, null, page);
    });

    $(document).on("click", "#wobef-filter-form-reset", function () {
        wobefResetFilters();
    });

    $(document).on("click", "#wobef-bulk-edit-reset-filter", function () {
        wobefResetFilters();
    });

    $(document).on("change", "#wobef-quick-search-field", function () {
        let options = $("#wobef-quick-search-operator option");
        switch ($(this).val()) {
            case "title":
                options.each(function () {
                    $(this).closest("select").prop("selectedIndex", 0);
                    $(this).prop("disabled", false);
                });
                break;
            case "id":
                options.each(function () {
                    $(this).closest("select").prop("selectedIndex", 1);
                    if ($(this).attr("value") === "exact") {
                        $(this).prop("disabled", false);
                    } else {
                        $(this).prop("disabled", true);
                    }
                });
                break;
        }
    });

    // Quick Per Page
    $(document).on("change", '#wobef-quick-per-page', function () {
        wobefChangeCountPerPage($(this).val());
    });

    $(document).on("click", ".wobef-edit-action-with-button", function () {
        let orderIds = [];
        let orderData = [];

        if ($('#wobef-inline-edit-bind').prop('checked') === true) {
            orderIds = wobefGetOrdersChecked();
        }
        orderIds.push($(this).attr("data-item-id"));

        let wobefValue;
        switch ($(this).attr("data-content-type")) {
            case "textarea":
                wobefValue = tinymce.get("wobef-text-editor").getContent();
                break;
            case "select_orders":
                wobefValue = $('#wobef-select-orders-value').val();
                break;
            case "select_files":
                let names = $('.wobef-inline-edit-file-name').map(function () {
                    return $(this).val();
                }).get();

                let urls = $('.wobef-inline-edit-file-url').map(function () {
                    return $(this).val();
                }).get();

                wobefValue = {
                    files_name: names,
                    files_url: urls,
                };
                break;
            case "file":
                wobefValue = $('#wobef-modal-file #wobef-file-id').val();
                break;
            case "image":
                wobefValue = $(this).attr("data-image-id");
                break;
            case "gallery":
                wobefValue = $("#wobef-modal-gallery-items input.wobef-inline-edit-gallery-image-ids").map(function () {
                    return $(this).val();
                }).get();
                break;
        }

        orderData.push({
            name: $(this).attr('data-name'),
            sub_name: ($(this).attr('data-sub-name')) ? $(this).attr('data-sub-name') : '',
            type: $(this).attr('data-update-type'),
            value: wobefValue,
            operation: 'inline_edit'
        });

        wobefOrderEdit(orderIds, orderData);
    });

    $(document).on("click", ".wobef-load-text-editor", function () {
        let orderId = $(this).attr("data-item-id");
        let field = $(this).attr("data-field");
        let fieldType = $(this).attr("data-field-type");
        $('#wobef-modal-text-editor-item-title').text($(this).attr('data-item-name'));
        $("#wobef-text-editor-apply").attr("data-field", field).attr("data-field-type", fieldType).attr("data-item-id", orderId);
        $.ajax({
            url: WOBEF_DATA.ajax_url,
            type: "post",
            dataType: "json",
            data: {
                action: "wobef_get_text_editor_content",
                order_id: orderId,
                field: field,
                field_type: fieldType
            },
            success: function (response) {
                if (response.success) {
                    tinymce.get("wobef-text-editor").setContent(response.content);
                    tinymce.execCommand('mceFocus', false, 'wobef-text-editor');
                }
            },
            error: function () { }
        });
    });

    $(document).on("click", "#wobef-create-new-order-taxonomy", function () {
        if ($("#wobef-new-order-category-name").val() !== "") {
            let taxonomyInfo = {
                name: $("#wobef-new-order-taxonomy-name").val(),
                slug: $("#wobef-new-order-taxonomy-slug").val(),
                parent: $("#wobef-new-order-taxonomy-parent").val(),
                description: $("#wobef-new-order-taxonomy-description").val(),
                order_id: $(this).attr("data-item-id"),
                modal_id: $(this).attr('data-closest-id')
            };
            wobefAddOrderTaxonomy(taxonomyInfo, $(this).attr("data-field"));
        } else {
            swal({
                title: "Taxonomy Name is required !",
                type: "warning"
            });
        }
    });

    //Search
    $(document).on("keyup", ".wobef-search-in-list", function () {
        let wobefSearchValue = this.value.toLowerCase().trim();
        $($(this).attr("data-id") + " .wobef-order-items-list li").filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(wobefSearchValue) > -1);
        });
    });

    $(document).on('click', 'button[data-target="#wobef-modal-select-orders"]', function () {
        let childrenIds = $(this).attr('data-children-ids').split(',');
        $('#wobef-modal-select-orders-item-title').text($(this).attr('data-item-name'));
        $('#wobef-modal-select-orders .wobef-edit-action-with-button').attr('data-item-id', $(this).attr('data-item-id')).attr('data-field', $(this).attr('data-field')).attr('data-field-type', $(this).attr('data-field-type'));
        let orders = $('#wobef-select-orders-value');
        if (orders.length > 0) {
            orders.val(childrenIds).change();
        }
    });

    $(document).on('click', '#wobef-modal-select-files-add-file-item', function () {
        wobefAddNewFileItem();
    });

    $(document).on('click', 'button[data-toggle=modal][data-target="#wobef-modal-select-files"]', function () {
        $('#wobef-modal-select-files-apply').attr('data-item-id', $(this).attr('data-item-id')).attr('data-field', $(this).attr(('data-field')));
        $('#wobef-modal-select-files-item-title').text($(this).closest('td').attr('data-col-title'));
        wobefGetOrderFiles($(this).attr('data-item-id'));
    });

    $(document).on('click', '.wobef-inline-edit-file-remove-item', function () {
        $(this).closest('.wobef-modal-select-files-file-item').remove();
    });

    if ($.fn.sortable) {
        let wobefSelectFiles = $(".wobef-inline-select-files");
        wobefSelectFiles.sortable({
            handle: ".wobef-select-files-sortable-btn",
            cancel: ""
        });
        wobefSelectFiles.disableSelection();
    }

    $(document).on("change", ".wobef-bulk-edit-form-variable", function () {
        let newVal = $(this).val() ? $(this).closest("div").find("input[type=text]").val() + "{" + $(this).val() + "}" : "";
        $(this).closest("div").find("input[type=text]").val(newVal).change();
    });

    $(document).on("change", "select[data-field=operator]", function () {
        let id = $(this).closest(".wobef-form-group").find("label").attr("for");
        if ($(this).val() === "text_replace") {
            $(this).closest(".wobef-form-group").append('<div class="wobef-bulk-edit-form-extra-field"><select id="' + id + '-sensitive"><option value="yes">Same Case</option><option value="no">Ignore Case</option></select><input type="text" id="' + id + '-replace" placeholder="Text ..."><select class="wobef-bulk-edit-form-variable" title="Select Variable" data-field="variable"><option value="">Variable</option><option value="title">Title</option><option value="id">ID</option><option value="sku">SKU</option><option value="menu_order">Menu Order</option><option value="parent_id">Parent ID</option><option value="parent_title">Parent Title</option><option value="parent_sku">Parent SKU</option><option value="regular_price">Regular Price</option><option value="sale_price">Sale Price</option></select></div>');
        } else if ($(this).val() === "number_round") {
            $(this).closest(".wobef-form-group").append('<div class="wobef-bulk-edit-form-extra-field"><select id="' + id + '-round-item"><option value="5">5</option><option value="10">10</option><option value="19">19</option><option value="29">29</option><option value="39">39</option><option value="49">49</option><option value="59">59</option><option value="69">69</option><option value="79">79</option><option value="89">89</option><option value="99">99</option></select></div>');
        } else {
            $(this).closest(".wobef-form-group").find(".wobef-bulk-edit-form-extra-field").remove();
        }
        if ($(this).val() === "number_clear") {
            $(this).closest(".wobef-form-group").find('input[data-field=value]').prop('disabled', true);
        } else {
            $(this).closest(".wobef-form-group").find('input[data-field=value]').prop('disabled', false);
        }
        changedTabs($(this));
    });

    $("#wobef-modal-bulk-edit .wobef-tab-content-item").on("change", "[data-field=value]", function () {
        changedTabs($(this));
    });

    $(document).on("change", ".wobef-date-from", function () {
        let field_to = $('#' + $(this).attr('data-to-id'));
        let datepicker = true;
        let timepicker = false;
        let format = 'Y/m/d';

        if ($(this).hasClass('wobef-datetimepicker')) {
            timepicker = true;
            format = 'Y/m/d H:i'
        }

        if ($(this).hasClass('wobef-timepicker')) {
            datepicker = false;
            timepicker = true;
            format = 'H:i'
        }

        field_to.val("");
        field_to.datetimepicker("destroy");
        field_to.datetimepicker({
            format: format,
            datepicker: datepicker,
            timepicker: timepicker,
            minDate: $(this).val(),
        });
    });

    $(document).on("click", ".wobef-bulk-edit-form-remove-image", function () {
        $(this).closest("div").remove();
        $("#wobef-bulk-edit-form-order-image").val("");
    });

    $(document).on("click", ".wobef-bulk-edit-form-remove-gallery-item", function () {
        $(this).closest("div").remove();
        $("#wobef-bulk-edit-form-order-gallery input[value=" + $(this).attr("data-id") + "]").remove();
    });

    var sortType = 'DESC'
    $(document).on('click', '.wobef-sortable-column', function () {
        if (sortType === 'DESC') {
            sortType = 'ASC';
            $(this).find('i.wobef-sortable-column-icon').text('d');
        } else {
            sortType = 'DESC';
            $(this).find('i.wobef-sortable-column-icon').text('u');
        }
        wobefSortByColumn($(this).attr('data-column-name'), sortType);
    });

    $(document).on("click", "#wobef-get-meta-fields-by-order-id", function () {
        $(".wobef-meta-fields-empty-text").hide();
        let input = $("#wobef-add-meta-fields-order-id");
        wobefAddMetaKeysByOrderID(input.val());
        input.val("");
    });

    $(document).on("change", ".wobef-meta-fields-main-type", function () {
        if ($(this).val() === "textinput") {
            $(".wobef-meta-fields-sub-type[data-id=" + $(this).attr("data-id") + "]").show();
        } else {
            $(".wobef-meta-fields-sub-type[data-id=" + $(this).attr("data-id") + "]").hide();
        }
    });

    $(document).on("click", "#wobef-bulk-edit-form-reset", function () {
        wobefResetBulkEditForm();
        $("nav.wobef-tabs-navbar li a").removeClass("wobef-tab-changed");
    });

    $(document).on("click", "#wobef-filter-form-save-preset", function () {
        let presetName = $("#wobef-filter-form-save-preset-name").val();
        if (presetName !== "") {
            let data = wobefGetProSearchData();
            wobefSaveFilterPreset(data, presetName);
        } else {
            swal({
                title: "Preset name is required !",
                type: "warning"
            });
        }
    });

    $(document).on("click", "#wobef-bulk-edit-form-do-bulk-edit", function (e) {
        let orderIds = wobefGetOrdersChecked();
        let orderData = [];

        $("#wobef-modal-bulk-edit .wobef-form-group").each(function () {
            let value;
            if ($(this).find("[data-field=value]").length > 1) {
                value = $(this).find("[data-field=value]").map(function () {
                    if ($(this).val() !== '') {
                        return $(this).val();
                    }
                }).get();
            } else {
                value = $(this).find("[data-field=value]").val();
            }

            if (($.isArray(value) && value.length > 0) || (!$.isArray(value) && value)) {
                let name = $(this).attr('data-name');
                let type = $(this).attr('data-type');

                orderData.push({
                    name: name,
                    sub_name: ($(this).attr('data-sub-name')) ? $(this).attr('data-sub-name') : '',
                    type: type,
                    operator: $(this).find("[data-field=operator]").val(),
                    value: value,
                    replace: $(this).find("[data-field=replace]").val(),
                    sensitive: $(this).find("[data-field=sensitive]").val(),
                    round: $(this).find("[data-field=round]").val(),
                    operation: 'bulk_edit'
                });
            }
        });

        if (orderIds.length > 0) {
            wobefCloseModal();
            wobefOrderEdit(orderIds, orderData);
        } else {
            swal({
                title: "Are you sure?",
                type: "warning",
                showCancelButton: true,
                cancelButtonClass: "wobef-button wobef-button-lg wobef-button-white",
                confirmButtonClass: "wobef-button wobef-button-lg wobef-button-green",
                confirmButtonText: "Yes, i'm sure",
                closeOnConfirm: true
            }, function (isConfirm) {
                if (isConfirm) {
                    wobefCloseModal();
                    wobefOrderEdit(orderIds, orderData);
                }
            });
        }
    });

    $(document).on('click', '[data-target="#wobef-modal-new-item"]', function () {
        $('#wobef-new-item-title').html("New Order");
        $('#wobef-new-item-description').html("Enter how many new order(s) to create!");
    });

    // keypress: Enter
    $(document).on("keypress", function (e) {
        if (e.keyCode === 13) {
            if ($("#wobef-filter-form-content").attr("data-visibility") === "visible" || ($('#wobef-quick-search-text').val() !== '' && $($('#wobef-last-modal-opened').val()).css('display') !== 'block' && $('.wobef-tabs-list a[data-content=bulk-edit]').hasClass('selected'))) {
                wobefReloadOrders();
                $("#wobef-bulk-edit-reset-filter").show();
            }
            if ($("#wobef-modal-new-order-taxonomy").css("display") === "block") {
                $("#wobef-create-new-order-taxonomy").trigger("click");
            }
            if ($("#wobef-modal-new-item").css("display") === "block") {
                $("#wobef-create-new-item").trigger("click");
            }
            if ($("#wobef-modal-item-duplicate").css("display") === "block") {
                $("#wobef-bulk-edit-duplicate-start").trigger("click");
            }

            let metaFieldManualInput = $("#wobef-meta-fields-manual_key_name");
            let metaFieldOrderId = $("#wobef-add-meta-fields-order-id");
            if (metaFieldManualInput.val() !== "") {
                $(".wobef-meta-fields-empty-text").hide();
                wobefAddMetaKeysManual(metaFieldManualInput.val());
                metaFieldManualInput.val("");
            }
            if (metaFieldOrderId.val() !== "") {
                $(".wobef-meta-fields-empty-text").hide();
                wobefAddMetaKeysByOrderID(metaFieldOrderId.val());
                metaFieldOrderId.val("");
            }
        }
    });

    let query;
    $(".wobef-get-orders-ajax").select2({
        ajax: {
            type: "post",
            delay: 800,
            url: WOBEF_DATA.ajax_url,
            dataType: "json",
            data: function (params) {
                query = {
                    action: "wobef_get_orders_name",
                    search: params.term
                };
                return query;
            }
        },
        placeholder: "Order Name ...",
        minimumInputLength: 3
    });

    $(document).on("change", "input:radio[name=create_variation_mode]", function () {
        if ($(this).attr("data-mode") === "all_combination") {
            $("#wobef-variation-bulk-edit-individual").hide();
            $("#wobef-variation-bulk-edit-generate").show();
        } else {
            $("#wobef-variation-bulk-edit-generate").hide();
            $("#wobef-variation-bulk-edit-individual").show();
        }
    }
    );

    $(document).on("select2:select", ".wobef-select2-ajax", function (e) {
        if ($(".wobef-variation-bulk-edit-individual-items div[data-id=" + $(this).attr("id") + "]").length === 0) {
            $(".wobef-variation-bulk-edit-individual-items").append('<div data-id="' + $(this).attr("id") + '"><select class="wobef-variation-bulk-edit-manual-item" data-attribute-name="' + $(this).attr("data-attribute-name") + '"></select></div>');
        }
        $(".wobef-variation-bulk-edit-individual-items div[data-id=" + $(this).attr("id") + "]").find("select").append('<option value="' + e.params.data.id + '">' + e.params.data.id + "</option>");
        $("#wobef-variation-bulk-edit-manual-add").prop("disabled", false);
        $("#wobef-variation-bulk-edit-generate").prop("disabled", false);
    });

    $(document).on("select2:unselect", ".wobef-select2-ajax", function (e) {
        $(".wobef-variation-bulk-edit-individual-items div[data-id=" + $(this).attr("id") + "]").find("option[value=" + e.params.data.id + "]").remove();
        if ($(".wobef-variation-bulk-edit-attribute-item").find(".select2-selection__choice").length === 0) {
            $("#wobef-variation-bulk-edit-manual-add").attr("disabled", "disabled");
            $("#wobef-variation-bulk-edit-generate").attr("disabled", "disabled");
        }
        if ($(this).val() === null) {
            $("div[data-id=wobef-variation-bulk-edit-attribute-item-" + $(this).attr("data-attribute-name") + "]").remove();
        }
    });

    $(document).on("change", "input:radio[name=delete_variation_mode]", function () {
        if ($(this).attr("data-mode") === "delete_all") {
            $("#wobef-variation-delete-single-delete").hide();
            $("#wobef-variation-delete-delete-all").show();
        } else {
            $("#wobef-variation-delete-delete-all").hide();
            $("#wobef-variation-delete-single-delete").show();
        }
    });

    $(document).on("click", ".wobef-inline-edit-taxonomy-save", function () {
        let reload = true;
        let OrderIds;
        let ordersChecked = $("input.wobef-check-item:checkbox:checked");
        let bindEdit = $("#wobef-inline-edit-bind");
        if (bindEdit.prop("checked") === true && ordersChecked.length > 0) {
            OrderIds = ordersChecked.map(function (i) {
                return $(this).val();
            }).get();
            OrderIds[ordersChecked.length] = $(this).attr("data-item-id");
        } else {
            OrderIds = [];
            OrderIds[0] = $(this).attr("data-item-id");
        }

        let field;
        if ($(this).attr("data-field-type")) {
            field = [$(this).attr("data-field-type"), $(this).attr("data-field")];
        } else {
            field = $(this).attr("data-field");
        }

        let data = $("#wobef-modal-taxonomy-" + $(this).attr("data-field") + "-" + $(this).attr("data-item-id") + " input:checkbox:checked").map(function () {
            return $(this).val();
        }).get();
        wobefUpdateOrderTaxonomy(OrderIds, field, data, reload);
    });


    $(document).on("click", ".wobef-inline-edit-add-new-taxonomy", function () {
        $("#wobef-create-new-order-taxonomy").attr("data-field", $(this).attr("data-field")).attr("data-item-id", $(this).attr("data-item-id")).attr('data-closest-id', $(this).attr('data-closest-id'));
        $('#wobef-modal-new-order-taxonomy-order-title').text($(this).attr('data-item-name'));
        wobefGetTaxonomyParentSelectBox($(this).attr("data-field"));
    });

    $(document).on("click", 'button.wobef-calculator[data-target="#wobef-modal-numeric-calculator"]', function () {
        let btn = $("#wobef-modal-numeric-calculator .wobef-edit-action-numeric-calculator");
        btn.attr("data-item-id", $(this).attr("data-item-id"));
        btn.attr("data-field", $(this).attr("data-field"));
        btn.attr("data-field-type", $(this).attr("data-field-type"));
        if ($(this).attr('data-field') === 'download_limit' || $(this).attr('data-field') === 'download_expiry') {
            $('#wobef-modal-numeric-calculator #wobef-numeric-calculator-type').val('n').change().hide();
            $('#wobef-modal-numeric-calculator #wobef-numeric-calculator-round').val('').change().hide();
        } else {
            $('#wobef-modal-numeric-calculator #wobef-numeric-calculator-type').show();
            $('#wobef-modal-numeric-calculator #wobef-numeric-calculator-round').show();
        }
        $('#wobef-modal-numeric-calculator-item-title').text($(this).attr('data-item-name'));
    });

    $(document).on("click", ".wobef-edit-action-numeric-calculator", function () {
        let orderID = $(this).attr("data-item-id");
        let OrderIds;
        let ordersChecked = $("input.wobef-check-item:checkbox:checked");
        let bindEdit = $("#wobef-inline-edit-bind");
        if (bindEdit.prop("checked") === true && ordersChecked.length > 0) {
            OrderIds = ordersChecked.map(function (i) {
                return $(this).val();
            }).get();
            OrderIds[ordersChecked.length] = orderID;
        } else {
            OrderIds = [];
            OrderIds[0] = orderID;
        }

        let wobefField;
        if ($(this).attr("data-field-type")) {
            wobefField = [$(this).attr("data-field-type"), $(this).attr("data-field")];
        } else {
            wobefField = $(this).attr("data-field");
        }

        let values = {
            operator: $("#wobef-numeric-calculator-operator").val(),
            value: $("#wobef-numeric-calculator-value").val(),
            operator_type: $("#wobef-numeric-calculator-type").val(),
            roundItem: $("#wobef-numeric-calculator-round").val()
        };

        wobefEditByCalculator(OrderIds, wobefField, values);
    });

    $(document).on('keyup', 'input[type=number][data-field=download_limit], input[type=number][data-field=download_expiry]', function () {
        if ($(this).val() < -1) {
            $(this).val(-1);
        }
    });

    $(document).on('click', '#wobef-quick-search-button', function () {
        $('#wobef-quick-search-reset').show();
    });

    $(document).on('click', '#wobef-quick-search-reset', function () {
        wobefResetFilters()
    });

    $(document).on('click', '.wobef-order-details-button', function () {
        $('#wobef-modal-order-details-item-title').text($(this).attr('data-item-name'));
        wobefGetOrderDetails($(this).attr('data-item-id'));
    });

    $(document).on('click', '.wobef-order-notes-button', function () {
        $('#wobef-modal-order-notes-item-title').text($(this).attr('data-item-name'));
        $('#wobef-modal-order-notes-add').attr('data-order-id', $(this).attr('data-item-id'));
        $('#wobef-modal-order-notes-items').html('');
        wobefGetOrderNotes($(this).attr('data-item-id'));
    });

    $(document).on('click', '.wobef-order-billing-button', function () {
        wobefClearInputs($('#wobef-modal-order-billing'));
        let orderId = $(this).attr('data-item-id');
        $('#wobef-modal-order-billing-item-title').text($(this).attr('data-item-name'));
        $('.wobef-modal-order-billing-save-changes-button').attr('data-order-id', orderId);
        wobefGetOrderBilling(orderId);
    });

    $(document).on('click', '.wobef-order-shipping-button', function () {
        wobefClearInputs($('#wobef-modal-order-shipping'));
        let orderId = $(this).attr('data-item-id');
        $('#wobef-modal-order-shipping-item-title').text($(this).attr('data-item-name'));
        $('.wobef-modal-order-shipping-save-changes-button').attr('data-order-id', orderId);
        wobefGetOrderShipping(orderId);
    });

    $(document).on('change', '.wobef-order-country', function () {
        if (wobefShippingStates) {
            let selectElement = $('select' + $(this).attr('data-state-target'));
            let textElement = $('input' + $(this).attr('data-state-target'));
            let country = $(this).val();
            selectElement.html('');
            selectElement.val('').change();
            textElement.val('');
            if (wobefShippingStates[country] && (typeof (wobefShippingStates[country].length) == undefined || wobefShippingStates[country].length !== 0)) {
                textElement.hide().prop('disabled', true);
                selectElement.show().prop('disabled', false);
                selectElement.append('<option value="">Select</option>');
                jQuery.each(wobefShippingStates[country], function (key, value) {
                    selectElement.append('<option value="' + key + '">' + value + '</option>');
                });
            } else {
                selectElement.val('').change().hide().prop('disabled', true);
                selectElement.html('');
                textElement.show().prop('disabled', false);
            }
        }
    });

    $(document).on('click', '.wobef-modal-order-billing-save-changes-button', function () {
        let billingModal = $(this).closest('#wobef-modal-order-billing');
        let billingData = {
            '_billing_first_name': billingModal.find('[data-order-field="first-name"]').val(),
            '_billing_last_name': billingModal.find('[data-order-field="last-name"]').val(),
            '_billing_email': billingModal.find('[data-order-field="email"]').val(),
            '_billing_phone': billingModal.find('[data-order-field="phone"]').val(),
            '_billing_postcode': billingModal.find('[data-order-field="postcode"]').val(),
            '_billing_company': billingModal.find('[data-order-field="company"]').val(),
            '_billing_address_1': billingModal.find('[data-order-field="address-1"]').val(),
            '_billing_address_2': billingModal.find('[data-order-field="address-2"]').val(),
            '_billing_city': billingModal.find('[data-order-field="city"]').val(),
            '_billing_country': billingModal.find('[data-order-field="country"]').val(),
            '_billing_state': billingModal.find('[data-order-field="state"]').val(),
            '_payment_method': billingModal.find('[data-order-field="payment-method"]').val(),
            '_transaction_id': billingModal.find('[data-order-field="transaction-id"]').val(),
        }
        wobefOrderBillingUpdate($(this).attr('data-order-id'), billingData);
    });

    $(document).on('click', '.wobef-modal-order-shipping-save-changes-button', function () {
        let shippingModal = $(this).closest('#wobef-modal-order-shipping');
        let shippingData = {
            '_shipping_first_name': shippingModal.find('[data-order-field="first-name"]').val(),
            '_shipping_last_name': shippingModal.find('[data-order-field="last-name"]').val(),
            '_shipping_postcode': shippingModal.find('[data-order-field="postcode"]').val(),
            '_shipping_company': shippingModal.find('[data-order-field="company"]').val(),
            '_shipping_address_1': shippingModal.find('[data-order-field="address-1"]').val(),
            '_shipping_address_2': shippingModal.find('[data-order-field="address-2"]').val(),
            '_shipping_city': shippingModal.find('[data-order-field="city"]').val(),
            '_shipping_country': shippingModal.find('[data-order-field="country"]').val(),
            '_shipping_state': shippingModal.find('[data-order-field="state"]').val(),
            '_payment_method': shippingModal.find('[data-order-field="payment-method"]').val(),
            'customer_note': shippingModal.find('[data-order-field="customer-note"]').val(),
        }
        wobefOrderShippingUpdate($(this).attr('data-order-id'), shippingData);
    });

    $(document).on('click', '.wobef-modal-load-billing-address', function () {
        wobefLoadCustomerBillingAddress($(this).attr('data-customer-id'), $(this).attr('data-target'));
    });

    $(document).on('click', '.wobef-modal-load-shipping-address', function () {
        wobefLoadCustomerShippingAddress($(this).attr('data-customer-id'), $(this).attr('data-target'));
    });

    $(document).on('change', '#wobef-bulk-edit-change-status', function () {
        let orderIds = wobefGetOrdersChecked();
        if ($(this).val() && orderIds.length > 0) {
            let orderData = [{
                name: 'order_status',
                type: 'woocommerce_field',
                value: $(this).val(),
                operation: 'inline_edit'
            }];
            wobefOrderEdit(orderIds, orderData);
        }
    });

    $(document).on('click', '.wobef-customer-details', function () {
        $('#wobef-modal-customer-details-item-title').text($(this).attr('data-item-name'));
        wobefLoadCustomerDetails($(this).attr('data-customer-id'), '#wobef-modal-customer-details');
    });

    $(document).on(
        {
            mouseenter: function () {
                $(this).addClass('wobef-disabled-column');
            },
            mouseleave: function () {
                $(this).removeClass('wobef-disabled-column');
            }
        },
        "td[data-editable=no]"
    );

    $(document).on('click', '#wobef-modal-order-notes-add', function () {
        if ($('#wobef-modal-order-notes-content').val()) {
            let data = {
                order_id: $(this).attr('data-order-id'),
                content: $('#wobef-modal-order-notes-content').val(),
                type: $('#wobef-modal-order-notes-type').val()
            };

            wobefAddOrderNote(data);
        } else {
            swal({
                title: "Note is required !",
                type: "warning"
            });
        }
    });

    $(document).on('click', '#wobef-modal-order-notes .delete-note', function () {
        let noteId = $(this).attr('data-note-id');
        swal({
            title: "Are you sure?",
            type: "warning",
            showCancelButton: true,
            cancelButtonClass: "wobef-button wobef-button-lg wobef-button-white",
            confirmButtonClass: "wobef-button wobef-button-lg wobef-button-green",
            confirmButtonText: "Yes, I'm sure !",
            closeOnConfirm: true
        }, function (isConfirm) {
            if (isConfirm) {
                wobefDeleteOrderNote(noteId);
            }
        });
    });

    $(document).on('click', '.wobef-order-address', function () {
        $('#wobef-modal-order-address-title').text($(this).attr('data-item-name'));
        $('div.wobef-modal-order-address-text').html('');
        $('textarea.wobef-modal-order-address-text').val('');
        $('.wobef-order-address-save-button').attr('data-order-id', $(this).attr('data-item-id')).attr('data-field', $(this).attr('data-field'));
        if ($(this).attr('data-field') == '_billing_address_index' || $(this).attr('data-field') == '_shipping_address_index') {
            $('.wobef-order-address-save-button').hide();
            $('textarea.wobef-modal-order-address-text').prop('disabled', true).hide();
            $('div.wobef-modal-order-address-text').show();
        } else {
            $('.wobef-order-address-save-button').show();
            $('textarea.wobef-modal-order-address-text').prop('disabled', false).show();
            $('div.wobef-modal-order-address-text').hide();
        }
        wobefGetAddress($(this).attr('data-item-id'), $(this).attr('data-field'));
    });

    $(document).on('click', '.wobef-order-items', function () {
        $('.wobef-order-items-table tbody').html('');
        $('#wobef-modal-order-items-title').text($(this).attr('data-item-name'));
        wobefGetOrderItems($(this).attr('data-item-id'));
    });

    $(document).on('click', '.wobef-bulk-edit-status-filter-item', function () {
        $('.wobef-bulk-edit-status-filter-item').removeClass('active');
        $(this).addClass('active');
        if ($(this).attr('data-status') === 'all') {
            $('#wobef-filter-form-reset').trigger('click');
        } else {
            $('#wobef-filter-form-order-status').val($(this).attr('data-status')).change();
            setTimeout(function () {
                $('#wobef-filter-form-get-orders').trigger('click');
            }, 250);
        }
    });

    $(document).on('click', '.wobef-order-address-save-button', function () {
        let orderIds = [];
        if ($('#wobef-inline-edit-bind').prop("checked") === true) {
            orderIds = wobefGetOrdersChecked();
        } else {
            orderIds.push($(this).attr("data-order-id"));
        }

        let orderData = [{
            name: $(this).attr('data-name'),
            type: $(this).attr('data-update-type'),
            value: $('input.wobef-modal-order-address-text').val(),
            operation: 'inline_edit'
        }];
        wobefOrderEdit(orderIds, orderData);
    });

    $(document).on('click', '#wobef-activation-activate', function () {
        $('#wobef-activation-type').val('activate');

        if ($('#wobef-activation-email').val() != '') {
            if ($('#wobef-activation-industry').val() != '') {
                setTimeout(function () {
                    $('#wobef-activation-form').first().submit();
                }, 200)
            } else {
                swal({
                    title: "Industry is required !",
                    type: "warning"
                });
            }
        } else {
            swal({
                title: "Email is required !",
                type: "warning"
            });
        }
    });

    $(document).on('click', '#wobef-activation-skip', function () {
        $('#wobef-activation-type').val('skip');

        setTimeout(function () {
            $('#wobef-activation-form').first().submit();
        }, 200)
    });

    wobefGetProducts();
    wobefGetTaxonomies();
    wobefGetTags();
    wobefGetCategories();
    wobefGetDefaultFilterProfileOrders();
    wobefSetTipsyTooltip();
});