$(document).ready(function () {
	$('[data-plugin="select_hrm"]').select2($(this).attr("data-options"));
	$('[data-plugin="select_hrm"]').select2({ width: "100%" });
	// listing
	// On page load:

	// update 9-5-2023
	var product_categories = $("#xin_table_product_categories").dataTable({
		bDestroy: true,
		iDisplayLength: 10,
		aLengthMenu: [
			[10, 30, 50, 100, -1],
			[10, 30, 50, 100, "All"],
		],
		ajax: {
			url: site_url + "product_categories/get_ajax_table/",
			type: "GET",
		},
		fnDrawCallback: function (settings) {
			$('[data-toggle="tooltip"]').tooltip();
		},
	});

	// update 9-5-2023
	jQuery("#product_categories").submit(function (e) {
		/*Form Submit*/
		e.preventDefault();
		var obj = jQuery(this),
			action = obj.attr("name");
		jQuery(".save").prop("disabled", true);
		$(".icon-spinner3").show();
		jQuery.ajax({
			type: "POST",
			url: e.target.action,
			data:
				obj.serialize() +
				"&is_ajax=471&data=product_categories&type=create&form=" +
				action,
			cache: false,
			success: function (JSON) {
				if (JSON.error != "") {
					toastr.error(JSON.error);
					$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
					jQuery(".save").prop("disabled", false);
					$(".icon-spinner3").hide();
					Ladda.stopAll();
				} else {
					product_categories.api().ajax.reload(function () {
						toastr.success(JSON.result);
					}, true);
					$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
					$(".icon-spinner3").hide();
					jQuery("#product_categories")[0].reset(); // To reset form fields
					jQuery(".save").prop("disabled", false);
					Ladda.stopAll();
				}
			},
		});
	});

	/* Delete data */
	$("#delete_record").submit(function (e) {
		/*Form Submit*/
		e.preventDefault();
		var obj = $(this),
			action = obj.attr("name");
		$.ajax({
			type: "POST",
			url: e.target.action,
			data: obj.serialize() + "&is_ajax=2&type=delete&form=" + action,
			cache: false,
			success: function (JSON) {
				if (JSON.error != "") {
					toastr.error(JSON.error);
					$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
					Ladda.stopAll();
				} else {
					$(".delete-modal").modal("toggle");
					product_categories.api().ajax.reload(function () {
						toastr.success(JSON.result);
					}, true);
					$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
					Ladda.stopAll();
				}
			},
		});
	});

	$("#edit_setting_datail").on("show.bs.modal", function (event) {
		var button = $(event.relatedTarget);
		var field_id = button.data("field_id");
		var field_type = button.data("field_type");
		$(".icon-spinner3").show();
		if (field_type == "document_type") {
			var field_add = "&data=ed_document_type&type=ed_document_type&";
		} else if (field_type == "contract_type") {
			var field_add = "&data=ed_contract_type&type=ed_contract_type&";
		} else if (field_type == "payment_method") {
			var field_add = "&data=ed_payment_method&type=ed_payment_method&";
		} else if (field_type == "education_level") {
			var field_add = "&data=ed_education_level&type=ed_education_level&";
		} else if (field_type == "qualification_language") {
			var field_add =
				"&data=ed_qualification_language&type=ed_qualification_language&";
		} else if (field_type == "qualification_skill") {
			var field_add =
				"&data=ed_qualification_skill&type=ed_qualification_skill&";
		} else if (field_type == "award_type") {
			var field_add = "&data=ed_award_type&type=ed_award_type&";
		} else if (field_type == "leave_type") {
			var field_add = "&data=ed_leave_type&type=ed_leave_type&";
		} else if (field_type == "warning_type") {
			var field_add = "&data=ed_warning_type&type=ed_warning_type&";
		} else if (field_type == "termination_type") {
			var field_add = "&data=ed_termination_type&type=ed_termination_type&";
		} else if (field_type == "expense_type") {
			var field_add = "&data=ed_expense_type&type=ed_expense_type&";
		} else if (field_type == "job_type") {
			var field_add = "&data=ed_job_type&type=ed_job_type&";
		} else if (field_type == "exit_type") {
			var field_add = "&data=ed_exit_type&type=ed_exit_type&";
		} else if (field_type == "travel_arr_type") {
			var field_add = "&data=ed_travel_arr_type&type=ed_travel_arr_type&";
		} else if (field_type == "currency_type") {
			var field_add = "&data=ed_currency_type&type=ed_currency_type&";
		} else if (field_type == "company_type") {
			var field_add = "&data=ed_company_type&type=ed_company_type&";
		} else if (field_type == "job_category") {
			var field_add = "&data=ed_job_category&type=ed_job_category&";
		} else if (field_type == "ethnicity_type") {
			var field_add = "&data=ed_ethnicity_type&type=ed_ethnicity_type&";
		} else if (field_type == "income_type") {
			var field_add = "&data=ed_income_type&type=ed_income_type&";
		} else if (field_type == "security_level") {
			var field_add = "&data=ed_security_level&type=ed_security_level&";

			// update feature 9-5-2023
		} else if (field_type == "vendor") {
			var field_add = "&data=ed_product_categories&type=ed_product_categories&";
		}

		var modal = $(this);
		$.ajax({
			url: site_url + "settings/constants_read/",
			type: "GET",
			data: "jd=1" + field_add + "field_id=" + field_id,
			success: function (response) {
				if (response) {
					$(".icon-spinner3").hide();
					$("#ajax_setting_info").html(response);
				}
			},
		});
	});
});

$(document).on("click", ".delete", function () {
	$("input[name=_token]").val($(this).data("record-id"));
	$("#delete_record").attr(
		"action",
		site_url + "product_categories/delete/" + $(this).data("record-id")
	);
});
