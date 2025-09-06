<?= $this->extend('pages/layouts/index') ?>
<?= $this->section('content') ?>

<div class="main-content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>List data categories</h3>
                <button class="btn btn-success btn-sm" id="btn-add"><i class="fa fa-plus"></i> Tambah Categories</button>
            </div>
            <div class="card-body">
                <div class="dt-responsive">
                    <table id="tabelCategories"
                        class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
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
                                <th>Name</th>
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
<div class="modal fade" id="CategoriesModal" tabindex="-1" role="dialog" aria-labelledby="CategoriesModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="CategoriesForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="CategoriesModalLabel">
                        <span id="modalTitleText">Tambah Categories</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="Categories_id" name="id">
                    <div class="form-group">
                        <label for="Categories_name">Nama Categories</label>
                        <input type="text" class="form-control" id="Categories_name" name="name" required>
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
    $(document).ready(function() {

        table = $('#tabelCategories').DataTable({
            scrollY: '50vh',
            scrollCollapse: true,
            paging: false,
            processing: true,
            ajax: {
                url: '/api/v1/categories',
                dataSrc: 'data'
            },
            columns: [{
                    data: 'id'
                },
                {
                    data: 'name'
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
                    <button class="btn btn-primary btn-sm btn-edit" data-id="${row.id}" data-name="${row.name}">
                        <i class="fa fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-danger btn-sm btn-delete" data-id="${row.id}">
                        <i class="fa fa-trash"></i> Hapus
                    </button>
                `;
                    }
                }
            ]

        });

        // Show modal for add
        $('#btn-add').on('click', function() {
            $('#CategoriesForm')[0].reset();
            $('#Categories_id').val('');
            $('#CategoriesModalLabel').text('Tambah Categories');
            $('#CategoriesModal').modal('show');
        });

        // Show modal for edit
        $('#tabelCategories').on('click', '.btn-edit', function() {
            $('#CategoriesForm')[0].reset();
            $('#Categories_id').val($(this).data('id'));
            $('#Categories_name').val($(this).data('name'));
            $('#CategoriesModalLabel').text('Edit Categories');
            $('#CategoriesModal').modal('show');
        });

        // Save (insert/update)
        $('#CategoriesForm').on('submit', function(e) {
            e.preventDefault();
            var id = $('#Categories_id').val();
            var name = $('#Categories_name').val();
            var method = id ? 'PUT' : 'POST';
            var url = id ? `/api/v1/categories/${id}` : '/api/v1/categories';

            // Remove previous error
            $('#Categories_name').removeClass('is-invalid');
            $('#Categories_name').next('.invalid-feedback').remove();

            $.ajax({
                url: url,
                method: method,
                contentType: 'application/json',
                data: JSON.stringify({
                    name: name
                }),
                success: function(res) {
                    $('#CategoriesModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: id ? 'Berhasil diupdate!' : 'Berhasil ditambah!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    table.ajax.reload(null, false);
                },
                error: function(xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.messages && xhr.responseJSON.messages.name) {
                        $('#Categories_name').addClass('is-invalid');
                        if ($('#Categories_name').next('.invalid-feedback').length === 0) {
                            $('#Categories_name').after('<div class="invalid-feedback">' + xhr.responseJSON.messages.name + '</div>');
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan',
                        });
                    }
                }
            });
        });

        // Delete
        $('#tabelCategories').on('click', '.btn-delete', function() {
            var id = $(this).data('id');
            Swal.fire({
                title: 'Yakin hapus data?',
                text: "Data yang dihapus tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/api/v1/categories/${id}`,
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
                                title: 'Gagal!',
                                text: xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan',
                            });
                        }
                    });
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>