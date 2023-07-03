$(document).ready(function () {
	$('[data-plugin="select_hrm"]').select2({ width: "100%" });

	$("#select_due_date").change(function () {
		var duration = parseInt($("#select_due_date").val());
		var startDate = new Date($("#transaction_date").val());
		var durationType = $("#select_due_date").find(":selected").data("type");

		if (isNaN(duration) || isNaN(startDate.getTime())) {
			alert("Please enter valid input");
			return;
		}

		var dueDate = calculateDueDate(startDate, duration, durationType);
		$("#due_date").val(dueDate.toISOString().split("T")[0]);
	});
	$("#due_date").change(function () {
		$("#select_due_date").val(0).change();
	});
});

function calculateDueDate(startDate, duration, durationType) {
	var dueDate = new Date(startDate);

	if (durationType === "days") {
		dueDate.setDate(dueDate.getDate() + duration);
	} else if (durationType === "months") {
		dueDate.setMonth(dueDate.getMonth() + duration);
	} else if (durationType === "years") {
		dueDate.setFullYear(dueDate.getFullYear() + duration);
	}

	return dueDate;
}

function set_project(id, val_project = false) {
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

			if (val_project) {
				$(i).val(val_project);
				$(i).select2().trigger("change");
			}
		},
		error: function (xhr, status, error) {
			console.log(error); // Menampilkan pesan kesalahan jika terjadi error
		},
	});
}
function set_tax(id) {
	$.ajax({
		url: site_url + "ajax_request/get_taxs", // Ubah 'get_data.php' sesuai dengan URL yang sesuai untuk memperoleh data dari PHP
		type: "GET",
		dataType: "json",
		success: function (data) {
			var res = "";
			// Melakukan perulangan untuk setiap data yang diperoleh
			$.each(data, function (key, value) {
				// Membuat opsi baru dengan menggunakan data
				res +=
					'<option value="' +
					parseInt(value.tax_id) +
					'" tax-type="' +
					value.type +
					'" tax-rate="' +
					parseInt(value.rate) +
					'">' +
					value.name +
					"</option>";
			});
			var i = "#tax-" + id;
			$(i).append(res);
		},
		error: function (xhr, status, error) {
			console.log(error); // Menampilkan pesan kesalahan jika terjadi error
		},
	});
}
function set_discount(id) {
	$.ajax({
		url: site_url + "ajax_request/get_discounts", // Ubah 'get_data.php' sesuai dengan URL yang sesuai untuk memperoleh data dari PHP
		type: "GET",
		dataType: "json",
		success: function (data) {
			var res = "";
			// Melakukan perulangan untuk setiap data yang diperoleh
			$.each(data, function (key, value) {
				// Membuat opsi baru dengan menggunakan data
				res +=
					'<option value="' +
					parseInt(value.discount_id) +
					'" discount-type="' +
					value.discount_type +
					'" discount-rate="' +
					parseInt(value.discount_value) +
					'">' +
					value.discount_name +
					"</option>";
			});
			var i = "#discount-" + id;
			$(i).append(res);
		},
		error: function (xhr, status, error) {
			console.log(error); // Menampilkan pesan kesalahan jika terjadi error
		},
	});
}
function set_product(id, val_item = false) {
	$.ajax({
		url: site_url + "ajax_request/get_products", // Ubah 'get_data.php' sesuai dengan URL yang sesuai untuk memperoleh data dari PHP
		type: "GET",
		dataType: "json",
		success: function (data) {
			var res = "";
			// "<option value='0' selected disabled>" + ms_select_item + "</option>";
			// Melakukan perulangan untuk setiap data yang diperoleh
			$.each(data, function (key, value) {
				// Membuat opsi baru dengan menggunakan data
				res +=
					'<option value="' +
					parseInt(value.product_id) +
					'" product-number="' +
					value.product_number +
					'" product-price="' +
					parseInt(value.price) +
					'">' +
					value.label +
					"</option>";
			});
			var i = "#item-" + id; // cause name product -> item
			$(i)
				.append(res)
				.then(function () {
					if (val_item) {
						if ($.isNumeric(val_item)) {
							$(i).val(val_item).trigger("change");
						} else {
							var opt = new Option(val_item, val_item, true, true);
							// "<option value='" + val_item + "'> " + val_item + "</option>";
							$(i).append(opt);
							$(i).val(val_item).trigger("change");
						}
					}
				});
		},
		error: function (xhr, status, error) {
			console.log(error); // Menampilkan pesan kesalahan jika terjadi error
		},
	});
}
/* ----------------------- CALCULATE ITEMS ----------------------------- */

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

$("#item_product").on("change", "[name='row_discount_id[]']", function () {
	var selectedRow = $(this).closest("tr");

	var discount_rate = $(this).find(":selected").attr("discount-rate");
	var discount_type = $(this).find(":selected").attr("discount-type");

	var item_price = selectedRow.find(".row_item_price").val();
	var item_qty = selectedRow.find(".row_qty").val();

	// tipe 0 adalah flat
	if (discount_type == 0) {
		var discount = parseFloat(discount_rate);
	} else {
		var total_item = parseFloat(item_price) * parseFloat(item_qty);
		var discount = (discount_rate / 100) * total_item; // get nilai diskon
		// var discount = total_item - parseFloat(proses_1);
	}

	selectedRow.find(".row_discount_rate").val(discount);
	selectedRow.find(".row_discount_rate_show").text(formatCurrency(discount));
});

$("#item_product").on("change", "[name='row_tax_id[]']", function () {
	var selectedRow = $(this).closest("tr");

	var tax_rate = $(this).find(":selected").attr("tax-rate");
	var tax_type = $(this).find(":selected").attr("tax-type");

	var item_price = parseFloat(selectedRow.find(".row_item_price").val());
	var item_qty = parseFloat(selectedRow.find(".row_qty").val());
	var discount = parseFloat(selectedRow.find(".row_discount").val());

	// tipe 0 adalah flat
	var proses_1 = item_price * item_qty;
	var proses_2 = proses_1 - discount;

	if (tax_type == "fixed") {
		var tax = parseFloat(tax_rate);
	} else {
		var proses_3 = (tax_rate / 100) * proses_2; // get nilai tax
		var tax = proses_2 - parseFloat(proses_3);
	}

	selectedRow.find(".row_tax_rate").val(tax);
	selectedRow.find(".row_tax_rate_show").text(formatCurrency(tax));
});

function update_total() {
	var total = 0;
	$(".row_amount").each(function () {
		var sub_total = $(this).val();
		sub_total = parseFloat(sub_total);
		total = parseFloat(total) + parseFloat(sub_total);
	});

	var delivery_fee = $("#delivery_fee").val();
	total = parseFloat(total) + parseFloat(delivery_fee);

	$("#amount").val(total);
	$("#amount_show").text(formatCurrency(total));
}

// Fungsi edit otomatic kaluasi saat load
$(document).on("load", function () {
	update_total();
});

// Calculate subtotal whenever row_qty or row_item_price is changed
$(document).on(
	"change click keyup load",
	".row_qty, .row_item_price, .delivery_fee, .row_item_price, .row_tax_price, .row_discount_price, [name='row_tax_id[]'], [name='row_discount_id[]'], [data-plugin='select_item']",
	function () {
		var row = $(this).closest("tr");

		var row_item_price = parseFloat(row.find(".row_item_price").val());
		var row_qty = parseFloat(row.find(".row_qty").val());
		var row_discount_rate = parseFloat(row.find(".row_discount_rate").val());
		var row_tax_rate = parseFloat(row.find(".row_tax_rate").val());

		var subtotal_1 = row_qty * row_item_price; // cari harga x jumlah

		var discount_rate = row
			.find("[name='row_discount_id[]'] :selected")
			.attr("discount-rate");
		parseFloat();
		var discount_type = row
			.find("[name='row_discount_id[]'] :selected")
			.attr("discount-type");

		var tax_rate = parseFloat(
			row.find("[name='row_tax_id[]'] :selected").attr("tax-rate")
		);
		var tax_type = row.find("[name='row_tax_id[]'] :selected").attr("tax-type");

		// kalkulasi

		if (tax_type == "fixed") {
			row_tax_rate = tax_rate;
			// subtotal_1 = subtotal_1 - row_discount_rate + row_tax_rate;
		} else {
			row_tax_rate = (tax_rate / 100) * (subtotal_1 - row_discount_rate); // get nilai tax
			// subtotal_1 = subtotal_1 + row_tax_rate;
		}
		row.find(".row_tax_rate").val(row_tax_rate);
		row.find(".row_tax_rate_show").text(formatCurrency(row_tax_rate));

		if (discount_type == 0) {
			row_discount_rate = discount_rate;
		} else {
			row_discount_rate = (discount_rate / 100) * subtotal_1; // get nilai diskon
		}
		row.find(".row_discount_rate").val(row_discount_rate);
		row.find(".row_discount_rate_show").text(formatCurrency(row_discount_rate));

		subtotal_1 = subtotal_1 - row_discount_rate + row_tax_rate;
		row.find(".row_amount").val(subtotal_1);
		row.find(".row_amount_show").text(formatCurrency(subtotal_1));

		update_total();
	}
);

/* ----------------------- END CALCULATE ITEMS ----------------------------- */

/* ----------------------- ADD ITEMS ----------------------------- */

// Function to add a new row to the table body
function addRow(val_item = false, val_project = false) {
	// Get the table body element
	var tbody = $("#item_product > tbody");

	// Get the number of rows in the table body
	var rowCount = tbody.children().length;

	var rowId = "row-" + rowCount;
	// Create a new row element with a unique id attribute
	var newRow =
		'<tr class="item-row" id="' +
		rowCount +
		'">' +
		// '"><td><input type="text" class="form-control item_name" name="row_item_name[]" id="row_item_name" placeholder="Item Name" required><span class="product_number font-weight-bold" style="font-size:10px;margin-top:2px"></span></td>' +
		"<td>" +
		"<select class='form-control' data-plugin='select_item' data-placeholder='" +
		ms_select_item +
		"'name='row_item_id[]' id='item-" +
		rowCount +
		"'></select><br><strong class='product_number' style='font-size:10px'></strong>" +
		"</td>" +
		"<td>" +
		"<select class='form-control select-item' data-plugin='select_project' data-placeholder='" +
		ms_select_project +
		"' name='row_project_id[]' id='project-" +
		rowCount +
		"'></select>" +
		"</td>" +
		"<td>" +
		"<select class='form-control' data-plugin='select_hrm2' name='row_tax_id[]' id='tax-" +
		rowCount +
		"'></select><input type='hidden' class='row_tax_rate' name='row_tax_rate[]' id='row_tax_rate_" +
		rowCount +
		"' value='0'><br><strong class='row_tax_rate_show currency' style='font-size:10px'></strong>" +
		"</td>" +
		"<td>" +
		"<select class='form-control' data-plugin='select_hrm2' name='row_discount_id[]' id='discount-" +
		rowCount +
		"'></select> <input type='hidden' class='row_discount_rate' name='row_discount_rate[]' id='row_discount_rate_" +
		rowCount +
		"' value='0'><br><strong class='row_discount_rate_show currency' style='font-size:10px'></strong>" +
		"</td>" +
		'<td><input type="number" class="form-control row_qty" name="row_qty[]" id="row_qty" min="1" value="1" required></td>' +
		'<td><input type="number" class="form-control row_item_price" name="row_item_price[]" data-type="currency" id="row_item_price" min="1" value="0" step="0.01"></td>' +
		'<td class="text-right align-middle"><input type="hidden" class="row_amount" step="0.01" name="row_amount[]" id="row_amount_' +
		rowCount +
		'" value="0"><strong class="row_amount_show currency">0</strong></td>' +
		'<td style="text-align:center"><button type="button" class="btn icon-btn btn-danger waves-effect waves-light remove-item"> <span class="fa fa-minus"></span></button></td></tr>';
	// Add the row to the table body
	tbody.append(newRow);

	set_project(rowCount, val_project);
	set_tax(rowCount);
	set_discount(rowCount);
	// set_product(rowCount, val_item);
	// rowCount++;
	// $('[data-plugin="select_item"]').select2({ width: "150px", tags: true });
	$('[data-plugin="select_hrm2"]').select2({ width: "100px" });
	$('[data-plugin="select_project"]').select2({ width: "150px" });

	var rowAmountSelect = $("#row_amount_" + rowCount);
	rowAmountSelect
		.closest("td")
		.find(".currency")
		.text(formatCurrency(rowAmountSelect.val()));

	update_total();
}

$(document).on("click", ".remove-item", function () {
	$(this)
		.closest(".item-row")
		.fadeOut(300, function () {
			$(this).remove();
			update_total();
		});
});

// set selected item
// $(document).ready(function () {
// 	console.log("halo");
// 	$('[data-plugin="select_item"]').each(function (key, value) {
// 		var select = $(this);
// 		var url = $select.attr("");
// 		select.select2({
// 			ajax: {
// 				url: site_url + "ajax_request/get_products",
// 				dataType: "json",
// 				data: function (params) {
// 					console.log(params);
// 					var query = {
// 						query: params.term,
// 					};
// 					return query;
// 				},
// 				processResults: function (data) {
// 					console.log(data);
// 					return {
// 						results: data,
// 					};
// 				},
// 				cache: true,
// 			},
// 		});
// 	});
// });

// buat label berbintang required
$(document).ready(function () {
	var span = '<span class="text-danger">*</span>';
	var required = $(".form-control[required]");
	// required.closest(".form-group").find("label").append(span);

	$(window).on("load", function () {
		if ($("#pr_number").val() != 0) {
			$.ajax({
				type: "GET",
				url: base_url + "/get_ajax_pr",
				data: "pr_number=" + $("#pr_number").val(),
				dataType: "JSON",
				success: function (response) {
					if (response) {
						// console.log(response.items[0].amount);
						$.each(response.items, function (key, value) {
							addRow();
							// addRow(value.item_name, value.ref_item);
							// var row = $("#item-" + key).closest("tr");
							// row.find(".row_qty").val(value.quantity);
							// row.find(".row_item_price").val(value.ref_price);
							// row.find(".row_amount").val(value.amount);
							// row.find(".row_amount_show").text(formatCurrency(value.amount));
							// if (value.ref_item == 0) {
							// 	$("#item-" + key).append(
							// 		"<option value='" +
							// 			value.item_name +
							// 			"'>" +
							// 			value.item_name +
							// 			"</option>"
							// 	);
							// 	$("#item-" + key).val(value.item_name);
							// } else {
							// 	$("#item-" + key).val(value.ref_item);
							// 	$("#item-" + key)
							// 		.select2()
							// 		.trigger("change");
							// }

							// console.log(value.project_id);
							// set selected project
							$("#project-" + key).val(value.project_id);
						});

						// set another
						$("#delivery_fee").val(response.data.ref_delivery_fee);
						$("#amount").val(response.data.amount);
						$("#amount_show").text(formatCurrency(response.data.amount));
						// var span2 =
						// 	'<i class="fa fa-question-circle" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Data from ' +
						// 	response.data.expedition +
						// 	"'></i>" +
						// 	span;
						// required.closest(".form-group").find("label").append(span2);
					}
				},
			});
			// var span2 =
			// 	'<i class="fa fa-question-circle" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="Data from ref"></i>';
			// required.closest(".form-group").find("label").append(span2);
		} else {
			// Call the addRow function to add a new row to the table body
			addRow();
			required.closest(".form-group").find("label").append(span);
		}
	});
});

$(function () {
	$("#item_product").on("change", '[data-plugin="select_item"]', function () {
		var selectedRow = $(this).closest("tr");
		var query = $(this).val();
		if ($.isNumeric(query)) {
			selectedRow
				.find(".product_number")
				.text(selectedRow.find(":selected").attr("product-number"));
			selectedRow
				.find(".row_item_price")
				.val(selectedRow.find(":selected").attr("product-price"));
		} else {
			selectedRow.find(".product_number").text("");
		}
	});
});
/* ----------------------- END ADD ITEMS ----------------------------- */

$(function () {
	$('[data-plugin="select_vendor"]').select2({
		ajax: {
			delay: 250,
			url: site_url + "ajax_request/find_vendor",
			data: function (params) {
				var queryParameters = {
					query: params.term,
				};
				return queryParameters;
			},

			processResults: function (data) {
				return {
					results: data,
				};
			},
			cache: true,
			transport: function (params, success, failure) {
				var $request = $.ajax(params);

				$request.then(success);
				$request.fail(failure);

				return $request;
			},
		},
	});
});

$(function () {
	console.log($(".select-item"));
	$(".select-item").each(function (key, value) {
		var id = $(this).data("id");
		$(this).select2({
			ajax: {
				delay: 250,
				url: site_url + "ajax_request/find_product",
				data: function (params) {
					var queryParameters = {
						query: params.term,
					};
					return queryParameters;
				},

				processResults: function (data) {
					return {
						results: data,
					};
				},
				cache: true,
				transport: function (params, success, failure) {
					var $request = $.ajax(params);

					$request.then(success);
					$request.fail(failure);

					return $request;
				},
			},
			width: "150px",
			tags: true,
		});
	});
});
