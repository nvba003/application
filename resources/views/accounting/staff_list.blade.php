@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Danh Sách Nhân Viên</h1>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên</th>
                <th>Ký tự</th>
                <th>Mã KH</th>
                <th>Tham số</th>
                <th>Sửa</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($staff as $member)
            <tr>
                <td>{{ $member->id }}</td>
                <td>{{ $member->name }}</td>
                <td>{{ $member->final_char }}</td>
                <td>{{ $member->customer_code }}</td>
                <td>{{ $member->parameter }}</td>
                <td>
                    <button type="button" class="btn btn-primary btn-sm edit-btn" data-toggle="modal" data-target="#editModal"
                        data-id="{{ $member->id }}"
                        data-name="{{ $member->name }}"
                        data-final_char="{{ $member->final_char }}"
                        data-customer_code="{{ $member->customer_code }}"
                        data-parameter="{{ $member->parameter }}"
                        data-email="{{ $member->email }}"
                    >Sửa</button>
                </td>

            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<!-- Modal Chỉnh Sửa -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Chỉnh Sửa Nhân Viên</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" id="editId" name="id">
                    <div class="form-group">
                        <label for="editName">Tên:</label>
                        <input type="text" class="form-control" id="editName" name="name">
                    </div>
                    <div class="form-group">
                        <label for="editFinalChar">Ký tự:</label>
                        <input type="text" class="form-control" id="editFinalChar" name="final_char">
                    </div>
                    <div class="form-group">
                        <label for="editCustomerCode">Mã KH:</label>
                        <input type="text" class="form-control" id="editCustomerCode" name="customer_code">
                    </div>
                    <div class="form-group">
                        <label for="editParameter">Tham số:</label>
                        <input type="text" class="form-control" id="editParameter" name="parameter">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Lưu Thay Đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.edit-btn').on('click', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var finalChar = $(this).data('final_char');
        var customerCode = $(this).data('customer_code');
        var parameter = $(this).data('parameter');

        $('#editId').val(id);
        $('#editName').val(name);
        $('#editFinalChar').val(finalChar);
        $('#editCustomerCode').val(customerCode);
        $('#editParameter').val(parameter);

        // Cập nhật action của form
        $('#editForm').attr('action', 'sale-staff/' + id);
    });

});
</script>
@endpush