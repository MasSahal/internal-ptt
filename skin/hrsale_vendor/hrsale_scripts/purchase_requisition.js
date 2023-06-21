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

	var xin_table_purchase_requisitions = $(
		"#xin_table_purchase_requisitions"
	).dataTable({
		bDestroy: true,
		iDisplayLength: 10,
		aLengthMenu: [
			[10, 30, 50, 100, -1],
			[10, 30, 50, 100, "All"],
		],
		ajax: {
			url: site_url + "purchase_requisitions/get_ajax_table/",
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

	/* Delete data sub category*/
	$("#delete_record").submit(function (e) {
		var tk_type = $("#token_type").val();
		$(".icon-spinner3").show();

		if (tk_type == "product_categories") {
			var field_add =
				"&is_ajax=9&data=delete_product_categories&type=delete_record&";
			var tb_name = "xin_table_" + tk_type;
		} else if (tk_type == "product_sub_categories") {
			var field_add =
				"&is_ajax=10&data=delete_product_sub_categories&type=delete_record&";
			var tb_name = "xin_table_" + tk_type;
		}

		/*Form Submit*/
		e.preventDefault();
		var obj = $(this),
			action = obj.attr("name");
		$.ajax({
			url: e.target.action,
			type: "post",
			data: "?" + obj.serialize() + field_add + "form=" + action,
			cache: false,
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

		/*Form Submit*/
		// e.preventDefault();
		// var obj = $(this),
		// 	action = obj.attr("name");
		// $.ajax({
		// 	type: "POST",
		// 	url: e.target.action,
		// 	data: obj.serialize() + "&is_ajax=2&type=delete&form=" + action,
		// 	cache: false,
		// 	success: function (JSON) {
		// 		if (JSON.error != "") {
		// 			toastr.error(JSON.error);
		// 			$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
		// 			Ladda.stopAll();
		// 		} else {
		// 			$(".delete-modal").modal("toggle");
		// 			product_sub_categories.api().ajax.reload(function () {
		// 				toastr.success(JSON.result);
		// 			}, true);
		// 			$('input[name="csrf_hrsale"]').val(JSON.csrf_hash);
		// 			Ladda.stopAll();
		// 		}
		// 	},
		// });
	});

	// $("#edit_setting_datail").on("show.bs.modal", function (event) {
	$(".edit-modal-data").on("show.bs.modal", function (event) {
		var button = $(event.relatedTarget);
		var field_id = button.data("field_id");
		var field_type = button.data("field_type");

		$(".icon-spinner3").show();

		var modal = $(this);
		$.ajax({
			url: base_url + "/read_" + field_type,
			type: "GET",
			data: "jd=1&field_id=" + field_id,
			success: function (response) {
				if (response) {
					$(".icon-spinner3").hide();
					$("#ajax_modal").html(response);
				}
			},
		});
	});
});

$(document).on("click", ".delete", function () {
	$("input[name=_token]").val($(this).data("record-id"));
	$("input[name=token_type]").val($(this).data("token_type"));
	$("#delete_record").attr(
		"action",
		site_url +
			"product_categories/delete_" +
			$(this).data("token_type") +
			"/" +
			$(this).data("record-id")
	) + "/";
});

function set_project(id) {
	$.ajax({
		url: site_url + "ajax_request/get_projects?completed=true", // Ubah 'get_data.php' sesuai dengan URL yang sesuai untuk memperoleh data dari PHP
		type: "GET",
		dataType: "json",
		success: function (data) {
			var res = "";
			// Melakukan perulangan untuk setiap data yang diperoleh
			$.each(data, function (key, val) {
				// Membuat opsi baru dengan menggunakan data
				res +=
					'<option value="' + parseInt(val.id) + '">' + val.value + "</option>";
			});
			var i = "#project-" + id;
			$(i).append(res);
		},
		error: function (xhr, status, error) {
			console.log(error); // Menampilkan pesan kesalahan jika terjadi error
		},
	});
}

var formatter = new Intl.NumberFormat("id-ID", {
	style: "currency",
	currency: type_currency,
	minimumFractionDigits: 2,
});

function formatCurrency(number) {
	return formatter.format(number);
}

function formatCurrencyNumber(number) {
	return number.toLocaleString("id-ID", { minimumFractionDigits: 2 });
}

function update_total() {
	var total = 0;

	// $(".currency").prepend(formatCurrency(value));

	$(".row_amount").each(function () {
		var sub_total = $(this).val();
		sub_total = parseInt(sub_total);
		total = parseInt(total) + parseInt(sub_total);

		$(".amount").val(total);
		$(".amount_show").text(formatCurrency(total));
	});

	// $(".currency").prepend(formatCurrency(value));

	// $(".currency").each(function () {
	// 	var value = $(this).text();
	// 	var formattedValue = formatCurrency(value);
	// 	$(this).text(formattedValue);
	// });
}

//call
update_total();

var rowCounter = $("#item_product >tbody >tr").length;

function addRow() {
	const table = document.getElementById("item_product");
	// let rowCounter = 1;
	const rowId = "row-" + rowCounter;

	const newRow = document.createElement("tr");
	newRow.setAttribute("class", "item-row");
	newRow.id = rowId;
	newRow.innerHTML =
		'<td><input type="text" class="form-control item_name" name="row_item_name[]" id="row_item_name" placeholder="Item Name" required><span class="product_number font-weight-bold" style="font-size:10px;margin-top:2px"></span></td>' +
		"<td>" +
		"<select class='form-control' data-plugin='select_hrm' name='row_project_id[]' id='project-" +
		rowCounter +
		"'></select>" +
		"</td>" +
		'<td><input type="number" class="form-control row_qty" name="row_qty[]" id="row_qty" min="1" value="1" required></td>' +
		'<td><input type="number" class="form-control row_ref_price" name="row_ref_price[]" id="row_ref_price" min="1" value="0"></td>' +
		'<td class="text-center align-middle"><input type="hidden" class="row_amount" name="row_amount[]" id="row_amount" value="0"><strong class="row_amount_show currency">0</strong></td>' +
		'<td style="text-align:center"><button type="button" class="btn icon-btn btn-danger waves-effect waves-light remove-item" data-repeater-delete="" onclick="removeRow(' +
		rowId +
		')"> <span class="fa fa-minus"></span></button></td>';
	table.appendChild(newRow);
	set_project(rowCounter);
	rowCounter++;
	$('[data-plugin="select_hrm"]').select2({ width: "100%" });
	var value = $(".currency").text();
	$(".currency").text(formatCurrency(value));

	update_total();
}

$(document).ready(function () {
	$("#item_product >tbody").html(addRow());
});

$(document).on("click", ".remove-item", function () {
	$(this)
		.closest(".item-row")
		.fadeOut(300, function () {
			$(this).remove();
		});
});

$(document).ready(function () {
	var span = '<span class="text-danger">*</span>';
	$("label[required]").append(span);
});

// $(document).ready(function () {
// 	// Format all numbers with the currency formatter
// 	$(".currency").each(function () {
// 		var value = $(this).text();
// 		var formattedValue = formatCurrency(value);
// 		$(this).text(formattedValue);
// 	});
// });

// Calculate subtotal whenever row_qty or row_ref_price is changed
$(document).on("change", ".row_qty, .row_ref_price", function () {
	var row = $(this).closest("tr");
	var qty = parseFloat(row.find(".row_qty").val());
	var refPrice = parseFloat(row.find(".row_ref_price").val());
	var subtotal = qty * refPrice;
	row.find(".row_amount").val(subtotal);
	row.find(".row_amount_show").text(formatCurrency(subtotal));

	update_total();
	// alert(formatCurrencyNumber(subtotal));
	// row.find(".currency").text(formatCurrency(subtotal.toFixed(2)));
});

// Load the inputmask plugin
$.getScript(
	"https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.3.4/jquery.inputmask.bundle.min.js",
	function () {
		// Set the input to currency format
		$(".currency-input").inputmask({
			alias: "currency",
			prefix: "",
			suffix: "",
			autoGroup: true,
			digits: 2,
			radixPoint: ".",
			groupSeparator: ",",
			allowMinus: false,
			rightAlign: false,
			unmaskAsNumber: true,
		});
	}
);

function convert_currency(number) {
	const settings = {
		async: true,
		crossDomain: true,
		url:
			"https://currency-exchange.p.rapidapi.com/exchange?from=IDR&to=" +
			type_currency +
			"&q=" +
			number,
		method: "GET",
		headers: {
			"X-RapidAPI-Key": "ad1d5ccf06mshe1ee62e89fb09a2p12c4f3jsnf0ecb1364fef",
			"X-RapidAPI-Host": "currency-exchange.p.rapidapi.com",
		},
	};
	$.ajax(settings).done(function (response) {
		console.log(response);
		return response;
	});
}
