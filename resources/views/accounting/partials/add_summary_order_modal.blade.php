<!-- Modal -->
<div class="modal fade" id="addSummaryOrderModal" tabindex="-1" aria-labelledby="addSummaryOrderModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addSummaryOrderModalLabel">Thêm đơn tổng hợp</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="addSummaryOrderForm">
        @csrf
          <input type="hidden" id="modalOrderId" name="order_id">
          <div class="form-group">
            <label>Mã Đơn Hàng:</label>
            <p id="modalOrderCode"></p>
          </div>
          <div class="form-group">
            <label>NVBH:</label>
            <p id="modalStaff"></p>
          </div>
          <div class="form-group">
            <label for="invoice_code">Số hóa đơn:</label>
            <input type="text" class="form-control" id="invoice_code" name="invoice_code" required>
          </div>
          <div class="form-group">
            <label for="report_date">Ngày Báo Cáo:</label>
            <input type="date" class="form-control" id="report_date" name="report_date" required>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
        <button type="button" class="btn btn-primary" onclick="submitSummaryOrder()">Lưu</button>
      </div>
    </div>
  </div>
</div>
