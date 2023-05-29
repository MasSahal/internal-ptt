$(document).ready(function () {
	$('[data-plugin="select_hrm"]').select2($(this).attr("data-options"));
	$('[data-plugin="select_hrm"]').select2({ width: "100%" });
	// listing
	// On page load:

	// update 9-5-2023
	var ms_vendor_list = $("#xin_table_project_costs").dataTable({
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
	jQuery("#vendors").submit(function (e) {
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
				"&is_ajax=471&data=vendors&type=vendors&form=" +
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
					ms_vendor_list.api().ajax.reload(function () {
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

// $(document).ready(function() {
// 	$('#add-item').click(function() {
// 		var invoice_items = '<div class="row item-row">' +
// 			'<hr>' +
// 			'<div class="form-group mb-1 col-sm-12 col-md-3">' +
// 			'<label for="item_name"><?php echo $this->lang->line('xin_title_item'); ?></label>' +
// 			'<br>' +
// 			'<input type="text" class="form-control item_name" name="item_name[]" id="item_name" placeholder="Item Name">' +
// 			'</div>' +
// 			'<div class="form-group mb-1 col-sm-12 col-md-2">' +
// 			'<label for="tax_type"><?php echo $this->lang->line('xin_invoice_tax_type'); ?></label>' +
// 			'<br>' +
// 			'<select class="form-control tax_type" name="tax_type[]" id="tax_type">'
// 		<?php foreach ($all_taxes as $_tax) { ?>
// 			<?php
// 			if ($_tax->type == 'percentage') {
// 				$_tax_type = $_tax->rate . '%';
// 			} else {
// 				$_tax_type = $this->Xin_model->currency_sign($_tax->rate);
// 			}
// 			?>
// 				+
// 				'<option tax-type="<?php echo $_tax->type; ?>" tax-rate="<?php echo $_tax->rate; ?>" value="<?php echo $_tax->tax_id; ?>"> <?php echo $_tax->name; ?> (<?php echo $_tax_type; ?>)</option>'
// 		<?php } ?>
// 			+
// 			'</select>' +
// 			'</div>' +
// 			'<div class="form-group mb-1 col-sm-12 col-md-1">' +
// 			'<label for="tax_type"><?php echo $this->lang->line('xin_title_tax_rate'); ?></label>' +
// 			'<br>' +
// 			'<input type="text" readonly="readonly" class="form-control tax-rate-item" name="tax_rate_item[]" value="0" />' +
// 			'</div>' +
// 			'<div class="form-group mb-1 col-sm-12 col-md-1">' +
// 			'<label for="qty_hrs" class="cursor-pointer"><?php echo $this->lang->line('xin_title_qty_hrs'); ?></label>' +
// 			'<br>' +
// 			'<input type="text" class="form-control qty_hrs" name="qty_hrs[]" id="qty_hrs" value="1">' +
// 			'</div>' +
// 			'<div class="skin skin-flat form-group mb-1 col-sm-12 col-md-2">' +
// 			'<label for="unit_price"><?php echo $this->lang->line('xin_title_unit_price'); ?></label>' +
// 			'<br>' +
// 			'<input class="form-control unit_price" type="text" name="unit_price[]" value="0" id="unit_price" />' +
// 			'</div>' +
// 			'<div class="form-group mb-1 col-sm-12 col-md-2">' +
// 			'<label for="profession"><?php echo $this->lang->line('xin_title_sub_total'); ?></label>' +
// 			'<input type="text" class="form-control amount-item" readonly="readonly" name="sub_total_item[]" value="0" />' +
// 			'<p style="display:none" class="form-control-static"><span class="amount-html">0</span></p>' +
// 			'</div>' +
// 			'<div class="form-group col-sm-12 col-md-1 text-xs-center mt-2">' +
// 			'<label for="profession">&nbsp;</label><br><button type="button" class="btn icon-btn btn-xs btn-danger waves-effect waves-light remove-invoice-item" data-repeater-delete=""> <span class="fa fa-trash"></span></button>' +
// 			'</div>' +
// 			'</div>'

// 		$('#item-list').append(invoice_items).fadeIn(500);

// 	});
// });

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
	newRow.id = rowId;
	newRow.innerHTML =
		"</td > " +
		'<td><input type="text" class="form-control form-control-sm item_name" name="item_name[]" id="item_name" placeholder="Item Name"></td>' +
		"<td>" +
		form_select_tax +
		"</td>" +
		'<td><input type="number" readonly="readonly" class="form-control form-control-sm tax-rate-item" name="tax_rate_item[]" value="0" /></td>' +
		'<td><input type="hidden" name="qty[]" value="1"><input type="text" class="form-control form-control-sm qty" name="qty[]" id="qty" value="1"></td>' +
		'<td><input type="number" name="price[]" class="form-control form-control-sm price" value="0" id="price" /></td>' +
		'<td><input type="number" class="form-control form-control-sm amount_item" readonly="readonly" name="amount_item[]" value="0" /></td>' +
		'<button type="button" class="btn icon-btn btn-xs btn-danger waves-effect waves-light remove-item" data-repeater-delete="" onclick="removeRow(' +
		rowId +
		')"> <span class="fa fa-trash"></span></button>';
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

$("#item_product").on("input", "input", function () {
	var row = $(this).closest("tr");
	var jumlah = row.find(".qty").val();
	var hargaSatuan = row.find(".amount_item").val();
	var pajak = row.find(".tax-rate-item").val();

	var subTotal = parseFloat(jumlah) * parseFloat(hargaSatuan);
	var totalPajak = subTotal * (parseFloat(pajak) / 100);
	var grandTotal = subTotal + totalPajak;

	row.find(".sub-total").val(grandTotal.toFixed(2));
});
