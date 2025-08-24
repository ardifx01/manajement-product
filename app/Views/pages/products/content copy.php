<?= $this->extend('pages/layouts/index') ?>
<?= $this->section('content') ?>

<div class="main-content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>List data products</h3>
                <button class="btn btn-success btn-sm" id="btn-add"><i class="fa fa-plus"></i> Tambah Product</button>
            </div>
            <div class="card-body">
                <div class="dt-responsive">
                    <table id="scr-vtr-dynamic"
                        class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>unit</th>
                                <th>Stock</th>
                                <th>Created</th>
                                <th>Updated</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot>
                            <tr>
                                <th>ID</th>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>unit</th>
                                <th>Stock</th>
                                <th>Created</th>
                                <th>Updated</th>
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
<div class="modal fade" id="ProductsModal" tabindex="-1" role="dialog" aria-labelledby="ProductsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="ProductsForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ProductsModalLabel">
                        <span id="modalTitleText">Tambah Product</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="Products_id" name="id">
                    <div class="form-group">
                        <label for="Products_code">Product Code</label>
                        <input type="text" class="form-control" id="Products_code" name="code" readonly>
                    </div>
                    <div class="form-group">
                        <label for="Products_name">Nama Product</label>
                        <input type="text" class="form-control" id="Products_name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="Products_category">Category</label>
                        <select class="form-control" id="Products_category" name="category_id" required>
                            <!-- Option categories, fill with PHP or AJAX -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="Products_unit">unit</label>
                        <input type="text" class="form-control" id="Products_unit" name="unit" required>
                    </div>
                    <div class="form-group">
                        <label for="Products_stock">Stock</label>
                        <input type="number" class="form-control" id="Products_stock" name="stock" required>
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

    function loadCategories() {
        $.get('/api/v1/categories', function(res) {
            var options = '';
            if (res.data) {
                res.data.forEach(function(cat) {
                    options += `<option value="${cat.id}">${cat.name}</option>`;
                });
            }
            $('#Products_category').html(options);
        });
    }

    function generateProductCode() {
        // Example: PRC-001-2025
        // You can customize the logic as needed
        var year = new Date().getFullYear();
        var randomNum = Math.floor(100 + Math.random() * 900); // 3 digit random number
        return `PRC-${randomNum}-${year}`;
    }
    $(document).ready(function() {
        loadCategories();
        table = $('#scr-vtr-dynamic').DataTable({
            scrollY: '50vh',
            scrollCollapse: true,
            paging: false,
            ajax: {
                url: '/api/v1/products',
                dataSrc: 'data'
            },
            columns: [{
                    data: 'id'
                },
                {
                    data: 'code'
                },
                {
                    data: 'name'
                },
                {
                    data: 'category_name'
                },
                {
                    data: 'unit'
                },
                {
                    data: 'stock'
                },
                {
                    data: 'created_at'
                },
                {
                    data: 'updated_at'
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return `
                    <button class="btn btn-primary btn-sm btn-edit" 
                        data-id="${row.id}" 
                        data-code="${row.code}"
                        data-name="${row.name}" 
                        data-category_id="${row.category_id}" 
                        data-unit="${row.unit}" 
                        data-stock="${row.stock}">
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

        // Show modal for add
        $('#btn-add').on('click', function() {
            $('#ProductsForm')[0].reset();
            $('#Products_id').val('');
            $('#Products_code').val(generateProductCode());
            $('#ProductsModalLabel').text('Add Product');
            loadCategories();
            $('#ProductsModal').modal('show');
        });

        // Show modal for edit
        $('#scr-vtr-dynamic').on('click', '.btn-edit', function() {
            $('#ProductsForm')[0].reset();
            var id = $(this).data('id');
            // Fetch latest data from API to ensure correct values
            $.get(`/api/v1/products/${id}`, function(res) {
                if (res.data) {
                    $('#Products_id').val(res.data.id);
                    $('#Products_name').val(res.data.name);
                    $('#Products_unit').val(res.data.unit);
                    $('#Products_stock').val(res.data.stock);
                    $('#Products_code').val(res.data.code);
                    //loadCategories();
                    $('#Products_category').val(res.data.category_id);
                    $('#ProductsModalLabel').text('Edit Product');
                    $('#ProductsModal').modal('show');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed!',
                        text: 'Product data not found.',
                    });
                }
            });
        });

        // Save (insert/update)
        $('#ProductsForm').on('submit', function(e) {
            e.preventDefault();
            var id = $('#Products_id').val();
            var name = $('#Products_name').val();
            var category_id = $('#Products_category').val();
            var unit = $('#Products_unit').val();
            var stock = $('#Products_stock').val();
            var code = $('#Products_code').val();
            var method = id ? 'PUT' : 'POST';
            var url = id ? `/api/v1/products/${id}` : '/api/v1/products';

            $('#ProductsForm .form-control').removeClass('is-invalid');
            $('#ProductsForm .invalid-feedback').remove();

            $.ajax({
                url: url,
                method: method,
                contentType: 'application/json',
                data: JSON.stringify({
                    code,
                    name,
                    category_id,
                    unit,
                    stock
                }),
                success: function(res) {
                    $('#ProductsModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: id ? 'Successfully updated!' : 'Successfully added!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    table.ajax.reload(null, false);
                },
                error: function(xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.messages) {
                        $.each(xhr.responseJSON.messages, function(key, val) {
                            var input = $('#Products_' + key);
                            input.addClass('is-invalid');
                            if (input.next('.invalid-feedback').length === 0) {
                                input.after('<div class="invalid-feedback">' + val + '</div>');
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed!',
                            text: xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred',
                        });
                    }
                }
            });
        });

        // Delete
        $('#scr-vtr-dynamic').on('click', '.btn-delete', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Are you sure you want to delete this data?',
                text: "Deleted data cannot be restored!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/api/v1/products/${id}`,
                        method: 'DELETE',
                        success: function(res) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Successfully deleted!',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            table.ajax.reload();
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed!',
                                text: xhr.responseJSON ? xhr.responseJSON.message : 'An error occurred',
                            });
                        }
                    });
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>