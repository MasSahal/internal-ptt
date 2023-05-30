$(document).ready(function () {
	$('[data-plugin="select_hrm"]').select2($(this).attr("data-options"));
	$('[data-plugin="select_hrm"]').select2({ width: "100%" });
	// listing
	// On page load:

	// update 9-5-2023
	// $("#xin_table_php").dataTable({
	// 	bDestroy: true,
	// 	iDisplayLength: 10,
	// 	aLengthMenu: [
	// 		[5, 10, 30, 50, 100, -1],
	// 		[5, 10, 30, 50, 100, "All"],
	// 	],
	// });
	var ms_table_project_costs = $("#xin_table_project_costs").dataTable({
		bDestroy: true,
		iDisplayLength: 10,
		aLengthMenu: [
			[5, 10, 30, 50, 100, -1],
			[5, 10, 30, 50, 100, "All"],
		],
		ajax: {
			url: site_url + "project_costs/get_ajax_table_transactions",
			type: "GET",
		},
		fnDrawCallback: function (settings) {
			$('[data-toggle="tooltip"]').tooltip();
		},
	});

	var ms_detail_trans = $("#xin_table_project_cost_detail").dataTable({
		bDestroy: true,
		bFilter: false,
		bLengthChange: false,
		iDisplayLength: 5,
		aLengthMenu: [
			[5, 10, 30, 50, 100, -1],
			[5, 10, 30, 50, 100, "All"],
		],
		ajax: {
			url:
				site_url +
				"project_costs/get_ajax_table_transaction_detail/" +
				$("#project_cost_id").val(),
			type: "GET",
		},
		fnDrawCallback: function (settings) {
			$('[data-toggle="tooltip"]').tooltip();
		},
	});

	// update 9-5-2023
	jQuery("#transactions").submit(function (e) {
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
				"&is_ajax=471&data=transaction&type=transaction&form=" +
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
					ms_table_project_costs.api().ajax.reload(function () {
						toastr.success(JSON.result);
					}, true);
					$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
					$(".icon-spinner3").hide();
					jQuery("#vendors")[0].reset(); // To reset form fields
					jQuery(".save").prop("disabled", false);
					Ladda.stopAll();
				}
			},
		});
	});

	/* Delete data dari del_dialog.php */
	$("#delete_record").submit(function (e) {
		var tk_type = $("#token_type").val();
		$(".icon-spinner3").show();
		if (tk_type == "vendors") {
			var field_add = "&is_ajax=473&data=delete_vendors&type=delete_record&";
			var tb_name = "xin_table_" + tk_type; // nama id tabel view record
			//
		}

		/*Form Submit*/
		e.preventDefault();
		var obj = $(this),
			action = obj.attr("name");
		$.ajax({
			url: e.target.action,
			type: "post",
			data: "?" + obj.serialize() + field_add + "form=" + action,
			success: function (JSON) {
				if (JSON.error != "") {
					toastr.error(JSON.error);
					$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
					$(".icon-spinner3").hide();
					Ladda.stopAll();
				} else {
					$(".delete-modal").modal("toggle");
					$(".icon-spinner3").hide();
					$("#" + tb_name)
						.dataTable()
						.api()
						.ajax.reload(function () {
							toastr.success(JSON.result);
						}, true);
					$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
					Ladda.stopAll();
				}
			},
		});
	});
});

// delete record dari button tabel
$(document).on("click", ".delete", function () {
	$("input[name=_token]").val($(this).data("record-id"));
	$("input[name=token_type]").val($(this).data("token_type"));
	$("#delete_record").attr(
		"action",
		site_url +
			"settings/delete_" +
			$(this).data("token_type") +
			"/" +
			$(this).data("record-id")
	) + "/";
});

function addFormRow() {
	var table = document.getElementById("item_product");
	var newRow = document.createElement("tr");
	newRow.innerHTML =
		'<td><input type="text" name="name[]" /></td>' +
		'<td><input type="email" name="email[]" /></td>';
	table.appendChild(newRow);
}

var rowCounter = $("#item_product >tbody >tr").length;
function addRow() {
	const table = document.getElementById("item_product");
	// let rowCounter = 1;
	const rowId = "row-" + rowCounter;

	const newRow = document.createElement("tr");
	newRow.setAttribute("class", "item-row");
	newRow.id = rowId;
	newRow.innerHTML =
		"</td > " +
		'<td><input type="text" class="form-control form-control-sm item_name" name="item_name[]" id="item_name" placeholder="Item Name"></td>' +
		"<td>" +
		form_select_tax +
		"</td>" +
		'<td><input type="number" readonly="readonly" class="form-control form-control-sm tax-rate-item" name="tax_rate_item[]" value="0" /></td>' +
		'<td><input type="hidden" name="item_tax[]" value="1"><input type="text" class="form-control form-control-sm qty" name="qty[]" id="qty" value="1"></td>' +
		'<td><input type="number" name="price[]" class="form-control form-control-sm price" value="0" id="price" /></td>' +
		'<td><input type="number" class="form-control form-control-sm sub_total" readonly="readonly" name="sub_total[]" value="0" /></td>' +
		'<td style="text-align:center"><button type="button" class="btn icon-btn btn-xs btn-danger waves-effect waves-light remove-item" data-repeater-delete="" onclick="removeRow(' +
		rowId +
		')"> <span class="fa fa-trash"></span></button></td>';
	table.appendChild(newRow);

	rowCounter++;
}

// Fungsi untuk menghapus baris tabel
$(document).on("click", ".remove-item", function () {
	$(this).closest("tr").remove();
	// updateFormCount();
});

// Fungsi untuk memperbarui nomor urutan form
function updateFormCount() {
	$(".row").each(function (index) {
		let formCount = index + 1;
		$(this).closest("tr").find("td:first-child").text(formCount);
	});
}

// $("#item_product").on("input", "input", function () {
// 	var row = $(this).closest("tr");
// 	var jumlah = row.find(".qty").val();
// 	var hargaSatuan = row.find(".amount_item").val();
// 	var pajak = row.find(".tax-rate-item").val();

// 	var subTotal = parseFloat(jumlah) * parseFloat(hargaSatuan);
// 	var totalPajak = subTotal * (parseFloat(pajak) / 100);
// 	var grandTotal = subTotal + totalPajak;

// 	row.find(".sub-total").val(grandTotal.toFixed(2));
// });

function update_total() {
	var sub_total = 0;
	var st_tax = 0;
	var grand_total = 0;
	var gdTotal = 0;
	var rdiscount = 0;

	i = 1;

	//
	$(".sub_total").each(function (i) {
		var total = $(this).val();

		total = parseFloat(total);

		sub_total = total + sub_total;
	});
	$(".tax-rate-item").each(function (i) {
		var tax_rate = $(this).val();

		tax_rate = parseFloat(tax_rate);

		st_tax = tax_rate + st_tax;
	});
	$(".tax_total").html(st_tax.toFixed(2));
	jQuery(".items-tax-total").val(st_tax.toFixed(2));
	$(".sub_total").html(sub_total.toFixed(2));

	var item_sub_total = sub_total;

	var discount_figure = $(".discount_figure").val();
	//var fsub_total = item_sub_total - discount_figure;
	//alert(st_tax);
	//$('.items-tax-total').val(st_tax.toFixed(2));
	$(".items-sub-total").val(item_sub_total.toFixed(2));

	//var discount_type = $('.discount_type').val();
	//var sub_total = $('.items-sub-total').val();

	if ($(".discount_type").val() == "1") {
		var fsub_total = item_sub_total - discount_figure;
		//var discount_amval = discount_figure;//.toFixed(2);
		$(".discount_amount").val(discount_figure);
		//$('.grand_total').html(grand_total.toFixed(2));
	} else {
		var discount_percent = (item_sub_total / 100) * discount_figure;
		var fsub_total = item_sub_total - discount_percent;
		// var discount_amval = discount_percent.toFixed(2);
		$(".discount_amount").val(discount_percent.toFixed(2));
		//$('.grand_total').html(grand_total.toFixed(2));
	}

	$(".fgrand_total").val(fsub_total.toFixed(2));
	$(".grand_total").html(fsub_total.toFixed(2));
} //Update total function ends here.
jQuery(document).on("click", ".remove-invoice-item", function () {
	$(this)
		.closest(".item-row")
		.fadeOut(300, function () {
			$(this).remove();
			update_total();
		});
});

jQuery(document).on("click", ".eremove-item", function () {
	var record_id = $(this).data("record-id");
	var invoice_id = $(this).data("invoice-id");
	$(this)
		.closest(".item-row")
		.fadeOut(300, function () {
			jQuery.get(
				base_url + "/delete_item/" + record_id + "/isajax/",
				function (data, status) {}
			);
			$(this).remove();
			update_total();
		});
});
// for qty
jQuery(document).on("click keyup change", ".qty,.price", function () {
	var qty = 0;
	var price = 0;
	var tax_rate = 0;
	var qty = $(this).closest(".item-row").find(".qty").val();
	var price = $(this).closest(".item-row").find(".price").val();
	var tax_rate = $(this).closest(".item-row").find(".tax_type").val();
	var element = $(this)
		.closest(".item-row")
		.find(".tax_type")
		.find("option:selected");
	var tax_type = element.attr("tax-type");
	var tax_rate = element.attr("tax-rate");
	if (qty == "") {
		var qty = 0;
	}
	if (price == "") {
		var price = 0;
	}
	if (tax_rate == "") {
		var tax_rate = 0;
	}
	// calculation
	var sbT = qty * price;
	if (tax_type === "fixed") {
		var taxPP = (1 / 1) * tax_rate;
		var singleTax = taxPP;
		var subTotal = sbT + taxPP;
		var sub_total = subTotal.toFixed(2);
		jQuery(this)
			.closest(".item-row")
			.find(".tax-rate-item")
			.val(singleTax.toFixed(2));
	} else {
		var taxPP = (sbT / 100) * tax_rate;
		var singleTax = taxPP;
		var subTotal = sbT + taxPP;
		var sub_total = subTotal.toFixed(2);
		jQuery(this)
			.closest(".item-row")
			.find(".tax-rate-item")
			.val(singleTax.toFixed(2));
	}
	jQuery(this).closest(".item-row").find(".items-tax-total").val(tax_rate);
	jQuery(this).closest(".item-row").find(".amount_item").val(sub_total);
	jQuery(this).closest(".item-row").find(".amount_item").val(sub_total);
	update_total();
	//$('.tax-rate-item').html(taxPP.toFixed(2));
});
jQuery(document).on("change click", ".tax_type", function () {
	var qty = 0;
	var price = 0;
	var tax_rate = 0;
	var qty = $(this).closest(".item-row").find(".qty").val();
	var price = $(this).closest(".item-row").find(".price").val();
	var tax_rate = $(this).closest(".item-row").find(".tax_type").val();
	var element = $(this)
		.closest(".item-row")
		.find(".tax_type")
		.find("option:selected");
	var tax_type = element.attr("tax-type");
	var tax_rate = element.attr("tax-rate");
	if (qty == "") {
		var qty = 0;
	}
	if (price == "") {
		var price = 0;
	}
	if (tax_rate == "") {
		var tax_rate = 0;
	}
	// calculation
	var sbT = qty * price;
	if (tax_type === "fixed") {
		var taxPP = (1 / 1) * tax_rate;
		var singleTax = taxPP;
		var subTotal = sbT + taxPP;
		var sub_total = subTotal.toFixed(2);
		jQuery(this)
			.closest(".item-row")
			.find(".tax-rate-item")
			.val(singleTax.toFixed(2));
		jQuery(this).closest(".item-row").find(".amount_item").val(sub_total);
		update_total();
	} else {
		var taxPP = (sbT / 100) * tax_rate;
		var singleTax = taxPP;
		var subTotal = sbT + taxPP;
		var sub_total = subTotal.toFixed(2);
		jQuery(this)
			.closest(".item-row")
			.find(".tax-rate-item")
			.val(singleTax.toFixed(2));
		jQuery(this).closest(".item-row").find(".amount_item").val(sub_total);
		update_total();
	}

	jQuery(this).closest(".item-row").find(".amount_item").val(sub_total);
	update_total();
});
jQuery(document).on("click keyup change", ".discount_figure", function () {
	var qty = 0;
	var price = 0;
	var tax_rate = 0;
	var discount_figure = $(".discount_figure").val();
	var discount_type = $(".discount_type").val();
	var sub_total = $(".items-sub-total").val();

	if (parseFloat(discount_figure) <= parseFloat(sub_total)) {
		if ($(".discount_type").val() == "1") {
			var grand_total = sub_total - discount_figure;
			var discount_amval = discount_figure; //.toFixed(2);
			$(".discount_amount").val(discount_amval);
			$(".grand_total").html(grand_total.toFixed(2));
		} else {
			var discount_percent = (sub_total / 100) * discount_figure;
			var grand_total = sub_total - discount_percent;
			var discount_amval = discount_percent.toFixed(2);
			$(".discount_amount").val(discount_amval);
			$(".grand_total").html(grand_total.toFixed(2));
		}
	} else {
		//
		$(".discount_amount").val(0);
		$(".discount_figure").val(0);
		//	var grand_total = sub_total;
		$(".grand_total").html(sub_total);
		alert("Discount price should be less than Sub Total.");
	}
	update_total();
});
jQuery(document).on("change click", ".discount_type", function () {
	var qty = 0;
	var price = 0;
	var tax_rate = 0;
	var discount_figure = $(".discount_figure").val();
	var discount_type = $(".discount_type").val();
	var sub_total = $(".items-sub-total").val();

	if ($(".discount_type").val() == "1") {
		var grand_total = sub_total - discount_figure;
		var discount_amval = discount_figure; //.toFixed(2);
		$(".discount_amount").val(discount_amval);
		$(".grand_total").html(grand_total.toFixed(2));
	} else {
		var discount_percent = (sub_total / 100) * discount_figure;
		var grand_total = sub_total - discount_percent;
		var discount_amval = discount_percent.toFixed(2);
		$(".discount_amount").val(discount_amval);
		$(".grand_total").html(grand_total.toFixed(2));
	}

	update_total();
});
