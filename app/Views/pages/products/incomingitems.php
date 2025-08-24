<?= $this->extend('pages/layouts/index') ?>
<?= $this->section('content') ?>

<div class="main-content">
    <div class="container-fluid">
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>List Data Incoming Items</h3>
                <div>
                    <button class="btn btn-primary btn-sm mr-2" id="btn-pdf">
                        <i class="fa fa-file-pdf"></i> Download PDF
                    </button>
                    <button class="btn btn-success btn-sm" id="btn-add"><i class="fa fa-plus"></i> Tambah Incoming Item</button>
                </div>
            </div>
            <div class="card-body">
                <!-- Filter Section -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="filter_product">Filter by Product:</label>
                        <select class="form-control" id="filter_product">
                            <option value="">All Products</option>
                            <!-- Options will be populated by JavaScript -->
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filter_start_date">Start Date:</label>
                        <input type="date" class="form-control" id="filter_start_date">
                    </div>
                    <div class="col-md-3">
                        <label for="filter_end_date">End Date:</label>
                        <input type="date" class="form-control" id="filter_end_date">
                    </div>
                    <div class="col-md-3">
                        <label>&nbsp;</label>
                        <button class="btn btn-primary btn-block" id="btn-filter">Apply Filter</button>
                    </div>
                </div>

                <div class="dt-responsive">
                    <table id="incoming-items-table"
                        class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Product</th>
                                <th>Date</th>
                                <th>Quantity</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot>
                            <tr>
                                <th>ID</th>
                                <th>Product</th>
                                <th>Date</th>
                                <th>Quantity</th>
                                <th>Action</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div class="modal fade" id="IncomingItemsModal" tabindex="-1" role="dialog" aria-labelledby="IncomingItemsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="IncomingItemsForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="IncomingItemsModalLabel">
                        <span id="modalTitleText">Tambah Incoming Item</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="IncomingItems_id" name="id">

                    <div class="form-group">
                        <label for="IncomingItems_product_id">Product *</label>
                        <select class="form-control" id="IncomingItems_product_id" name="product_id" required>
                            <option value="">Select Product</option>
                            <!-- Options will be populated by JavaScript -->
                        </select>
                        <div class="invalid-feedback" id="product_id_error"></div>
                    </div>

                    <div class="form-group">
                        <label for="IncomingItems_date">Date *</label>
                        <input type="datetime-local" class="form-control" id="IncomingItems_date" name="date" required>
                        <div class="invalid-feedback" id="date_error"></div>
                    </div>

                    <div class="form-group">
                        <label for="IncomingItems_quantity">Quantity *</label>
                        <input type="number" class="form-control" id="IncomingItems_quantity" name="quantity" required min="1" step="1">
                        <div class="invalid-feedback" id="quantity_error"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="saveBtn">
                        <i class="fa fa-save"></i> Simpan
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-times"></i> Batal
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Use a self-invoking function to avoid global variables
    (function($) {
        "use strict";
        
        var table;
        var products = [];

        function loadProducts(selectElementId = null) {
            $.get('/api/v1/products', function(res) {
                if (res.data) {
                    products = res.data;
                    var options = '<option value="">Select Product</option>';
                    products.forEach(function(product) {
                        options += `<option value="${product.id}">${product.name} (${product.code || 'N/A'})</option>`;
                    });

                    // Update product dropdown in modal
                    if (selectElementId) {
                        $(selectElementId).html(options);
                    } else {
                        $('#IncomingItems_product_id').html(options);
                        $('#filter_product').html('<option value="">All Products</option>' + options.substring(options.indexOf('<option value="') + 15));
                    }
                }
            }).fail(function(xhr) {
                console.error('Failed to load products:', xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load products list'
                });
            });
        }

        function formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleString('id-ID', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function formatDateForInput(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            // Format to YYYY-MM-DDTHH:MM
            return date.toISOString().slice(0, 16);
        }

        function resetForm() {
            $('#IncomingItemsForm')[0].reset();
            $('#IncomingItems_id').val('');
            $('#IncomingItems_date').val('');
            $('.invalid-feedback').text('');
            $('.form-control').removeClass('is-invalid');
        }

        function initializeDataTable() {
            // Check if DataTable is already initialized
            if ($.fn.DataTable.isDataTable('#incoming-items-table')) {
                $('#incoming-items-table').DataTable().destroy();
                $('#incoming-items-table').empty();
            }

            table = $('#incoming-items-table').DataTable({
                scrollY: '50vh',
                scrollCollapse: true,
                paging: true,
                processing: true,
                serverSide: false,
                ajax: {
                    url: '/api/v1/incoming-items',
                    dataSrc: 'data'
                },
                columns: [{
                        data: 'id'
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return `${row.product_name || 'N/A'}`;
                        }
                    },
                    {
                        data: 'date',
                        render: function(data, type, row) {
                            return formatDate(data);
                        }
                    },
                    {
                        data: 'quantity',
                        render: function(data, type, row) {
                            return parseInt(data).toLocaleString('id-ID');
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return `
                                <button class="btn btn-primary btn-sm btn-edit" 
                                    data-id="${row.id}" 
                                    data-product_id="${row.product_id}" 
                                    data-date="${row.date}" 
                                    data-quantity="${row.quantity}">
                                    <i class="fa fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-danger btn-sm btn-delete" data-id="${row.id}">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            `;
                        }
                    }
                ]
            });
        }

        $(document).ready(function() {
            // Initialize the page
            loadProducts();
            initializeDataTable();

            // Apply filter
            $('#btn-filter').on('click', function() {
                var productId = $('#filter_product').val();
                var startDate = $('#filter_start_date').val();
                var endDate = $('#filter_end_date').val();

                var url = '/api/v1/incoming-items?';
                if (productId) url += 'product_id=' + productId + '&';
                if (startDate) url += 'start_date=' + startDate + '&';
                if (endDate) url += 'end_date=' + endDate;

                table.ajax.url(url).load();
            });

            // Show modal for add
            $('#btn-add').on('click', function() {
                resetForm();
                loadProducts('#IncomingItems_product_id');
                $('#IncomingItemsModalLabel').text('Tambah Incoming Item');
                $('#IncomingItemsModal').modal('show');
            });

            // Show modal for edit
            $(document).on('click', '.btn-edit', function() {
                resetForm();
                var id = $(this).data('id');
                var product_id = $(this).data('product_id');
                var date = $(this).data('date');
                var quantity = $(this).data('quantity');

                // Fill form with existing data
                $('#IncomingItems_id').val(id);
                $('#IncomingItems_date').val(formatDateForInput(date));
                $('#IncomingItems_quantity').val(parseInt(quantity));

                // Load products and set selected value after a short delay
                loadProducts('#IncomingItems_product_id');
                setTimeout(function() {
                    $('#IncomingItems_product_id').val(product_id);
                }, 300);

                $('#IncomingItemsModalLabel').text('Edit Incoming Item');
                $('#IncomingItemsModal').modal('show');
            });

            // Save (insert/update)
            $('#IncomingItemsForm').on('submit', function(e) {
                e.preventDefault();
                var id = $('#IncomingItems_id').val();
                var product_id = $('#IncomingItems_product_id').val();
                var date = $('#IncomingItems_date').val();
                var quantity = $('#IncomingItems_quantity').val();

                // Reset validation errors
                $('.invalid-feedback').text('');
                $('.form-control').removeClass('is-invalid');

                // Basic validation
                var isValid = true;
                if (!product_id) {
                    $('#product_id_error').text('Product is required');
                    $('#IncomingItems_product_id').addClass('is-invalid');
                    isValid = false;
                }
                if (!date) {
                    $('#date_error').text('Date is required');
                    $('#IncomingItems_date').addClass('is-invalid');
                    isValid = false;
                }
                if (!quantity || quantity < 1) {
                    $('#quantity_error').text('Quantity must be at least 1');
                    $('#IncomingItems_quantity').addClass('is-invalid');
                    isValid = false;
                }

                if (!isValid) return;

                var method = id ? 'PUT' : 'POST';
                var url = id ? `/api/v1/incoming-items/${id}` : '/api/v1/incoming-items';

                // Format date properly for the API
                var formattedDate = '';
                if (date) {
                    // Convert from "YYYY-MM-DDTHH:MM" to "YYYY-MM-DD HH:MM:SS"
                    formattedDate = date.replace('T', ' ') + ':00';
                }

                $.ajax({
                    url: url,
                    method: method,
                    contentType: 'application/json',
                    data: JSON.stringify({
                        product_id: parseInt(product_id),
                        date: formattedDate,
                        quantity: parseFloat(quantity)
                    }),
                    success: function(res) {
                        $('#IncomingItemsModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: id ? 'Berhasil diupdate!' : 'Berhasil ditambahkan!',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        table.ajax.reload(null, false);
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.messages) {
                            // Handle validation errors
                            if (xhr.responseJSON.messages.error) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: xhr.responseJSON.messages.error
                                });
                            } else {
                                $.each(xhr.responseJSON.messages, function(key, val) {
                                    $('#' + key + '_error').text(val);
                                    $('#IncomingItems_' + key).addClass('is-invalid');
                                });
                            }
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: xhr.responseJSON.message
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Terjadi kesalahan pada server'
                            });
                        }
                        console.error('Error details:', xhr.responseJSON);
                    }
                });
            });

            // Delete
            $(document).on('click', '.btn-delete', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/api/v1/incoming-items/${id}`,
                            method: 'DELETE',
                            success: function(res) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil dihapus!',
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan'
                                });
                            }
                        });
                    }
                });
            });
            // Download PDF
            $('#btn-pdf').on('click', function() {
                var productId = $('#filter_product').val();
                var startDate = $('#filter_start_date').val();
                var endDate = $('#filter_end_date').val();
                var url = '/api/v1/incoming-items/pdf?';
                if (productId) url += 'product_id=' + productId + '&';
                if (startDate) url += 'start_date=' + startDate + '&';
                if (endDate) url += 'end_date=' + endDate;
                window.open(url, '_blank');
            });
        });
    })(jQuery);
</script>
<?= $this->endSection() ?>