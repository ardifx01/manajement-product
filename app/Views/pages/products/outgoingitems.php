<?= $this->extend('pages/layouts/index') ?>
<?= $this->section('content') ?>

<div class="main-content">
    <div class="container-fluid">
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>List Data Outgoing Products</h3>
                <div>
                    <button class="btn btn-primary btn-sm mr-2" id="btn-pdf">
                        <i class="fa fa-file-pdf"></i> Download PDF
                    </button>
                    <button class="btn btn-success btn-sm" id="btn-add"><i class="fa fa-plus"></i> Tambah Incoming Products</button>
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
                    <table id="outgoing-items-table"
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
<div class="modal fade" id="OutgoingItemsModal" tabindex="-1" role="dialog" aria-labelledby="OutgoingItemsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="OutgoingItemsForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="OutgoingItemsModalLabel">
                        <span id="modalTitleText">Tambah Outgoing Item</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="OutgoingItems_id" name="id">

                    <div class="form-group">
                        <label for="OutgoingItems_product_id">Product *</label>
                        <select class="form-control" id="OutgoingItems_product_id" name="product_id" required>
                            <option value="">Select Product</option>
                            <!-- Options will be populated by JavaScript -->
                        </select>
                        <div class="invalid-feedback" id="product_id_error"></div>
                    </div>

                    <div class="form-group">
                        <label for="OutgoingItems_date">Date *</label>
                        <input type="datetime-local" class="form-control" id="OutgoingItems_date" name="date" required>
                        <div class="invalid-feedback" id="date_error"></div>
                    </div>

                    <div class="form-group">
                        <label for="OutgoingItems_quantity">Quantity *</label>
                        <input type="number" class="form-control" id="OutgoingItems_quantity" name="quantity" required min="1" step="1">
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
                    $('#OutgoingItems_product_id').html(options);
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
        $('#OutgoingItemsForm')[0].reset();
        $('#OutgoingItems_id').val('');
        $('#OutgoingItems_date').val('');
        $('.invalid-feedback').text('');
        $('.form-control').removeClass('is-invalid');
    }

    $(document).ready(function() {
        loadProducts();

        table = $('#outgoing-items-table').DataTable({
            scrollY: '50vh',
            scrollCollapse: true,
            paging: false,
            processing: true,
            serverSide: false,
            ajax: {
                url: '/api/v1/outgoing-items',
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

        // Apply filter
        $('#btn-filter').on('click', function() {
            var productId = $('#filter_product').val();
            var startDate = $('#filter_start_date').val();
            var endDate = $('#filter_end_date').val();

            var url = '/api/v1/outgoing-items?';
            if (productId) url += 'product_id=' + productId + '&';
            if (startDate) url += 'start_date=' + startDate + '&';
            if (endDate) url += 'end_date=' + endDate;

            table.ajax.url(url).load();
        });

        // Show modal for add
        $('#btn-add').on('click', function() {
            resetForm();
            loadProducts('#OutgoingItems_product_id');
            $('#OutgoingItemsModalLabel').text('Tambah Outgoing Item');
            $('#OutgoingItemsModal').modal('show');
        });

        // Show modal for edit
        $('#outgoing-items-table').on('click', '.btn-edit', function() {
            resetForm();
            var id = $(this).data('id');
            var product_id = $(this).data('product_id');
            var date = $(this).data('date');
            var quantity = $(this).data('quantity');

            // Fill form with existing data
            $('#OutgoingItems_id').val(id);
            $('#OutgoingItems_date').val(formatDateForInput(date));
            $('#OutgoingItems_quantity').val(parseInt(quantity));

            // Load products and set selected value after a short delay
            loadProducts('#OutgoingItems_product_id');
            setTimeout(function() {
                $('#OutgoingItems_product_id').val(product_id);
            }, 300);

            $('#OutgoingItemsModalLabel').text('Edit Outgoing Item');
            $('#OutgoingItemsModal').modal('show');
        });

        // Save (insert/update)
        $('#OutgoingItemsForm').on('submit', function(e) {
            e.preventDefault();
            var id = $('#OutgoingItems_id').val();
            var product_id = $('#OutgoingItems_product_id').val();
            var date = $('#OutgoingItems_date').val();
            var quantity = $('#OutgoingItems_quantity').val();

            // Reset validation errors
            $('.invalid-feedback').text('');
            $('.form-control').removeClass('is-invalid');

            // Basic validation
            var isValid = true;
            if (!product_id) {
                $('#product_id_error').text('Product is required');
                $('#OutgoingItems_product_id').addClass('is-invalid');
                isValid = false;
            }
            if (!date) {
                $('#date_error').text('Date is required');
                $('#OutgoingItems_date').addClass('is-invalid');
                isValid = false;
            }
            if (!quantity || quantity < 1) {
                $('#quantity_error').text('Quantity must be at least 1');
                $('#OutgoingItems_quantity').addClass('is-invalid');
                isValid = false;
            }

            if (!isValid) return;

            var method = id ? 'PUT' : 'POST';
            var url = id ? `/api/v1/outgoing-items/${id}` : '/api/v1/outgoing-items';

            // Format date properly for the API
            var formattedDate = '';
            if (date) {
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
                    $('#OutgoingItemsModal').modal('hide');
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
                                $('#OutgoingItems_' + key).addClass('is-invalid');
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
        $('#outgoing-items-table').on('click', '.btn-delete', function() {
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
                        url: `/api/v1/outgoing-items/${id}`,
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
    });
</script>
<?= $this->endSection() ?>