<div class="modal fade" id="addTransactionModalAd" tabindex="-1" aria-labelledby="addTransactionModalAdLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form id="addTransactionFormAd" method="POST" action="{{ url('store-transaction-ad') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addTransactionModalAdLabel">Add AD Transaction</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <label for="adDealerSelect" class="form-label">Select Dealer</label>
            <select id="adDealerSelect" name="dealer" class="form-select" required>
              <option value="">Search Dealer</option>
              @foreach($dealers as $dealer)
                <option value="{{ $dealer->user_id }}" data-center="{{ $dealer->center }}">
                  {{ $dealer->name }}{{ $dealer->center ? ' - '.$dealer->center : '' }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label for="adCustomerSelect" class="form-label">Select Customer</label>
            <select id="adCustomerSelect" name="customer_id" class="form-select" required disabled>
              <option value="">Select dealer first</option>
              @foreach($customers as $customer)
                @php
                  $serial = $customer->serial->serial_number ?? '';
                @endphp
                <option value="{{ $customer->id }}" data-center="{{ $customer->center }}">
                  {{ $customer->name }}{{ $serial ? ' - '.$serial : '' }}{{ $customer->center ? ' - '.$customer->center : '' }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Select Item</label>
            <br>
            @foreach($items as $key => $item)
              <input type="radio" id="ad_item_{{ $item->id }}" name="item_id" @if($key == 0) checked @endif value="{{ $item->id }}" required>
              <label for="ad_item_{{ $item->id }}" class="mr-3">{{ $item->item }}</label>
              <br>
            @endforeach
          </div>

          <div class="mb-3">
            <label class="form-label">Quantity</label>
            <div style="max-width: 140px;">
              <div class="input-group">
                <button type="button" class="btn btn-outline-secondary btn-sm" id="adQtyMinus">-</button>
                <input type="number" name="qty" id="adQtyInput" class="form-control form-control-sm text-center" value="1" min="1" required>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="adQtyPlus">+</button>
              </div>
            </div>
          </div>

          <div class="mb-3">
            <label for="adTransactionDate" class="form-label">Date</label>
            <input type="date" id="adTransactionDate" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" name="date" class="form-control form-control-sm" required>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn bg-danger-subtle text-danger" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn bg-info-subtle text-info">Save</button>
        </div>
      </div>
    </form>
  </div>
</div>
