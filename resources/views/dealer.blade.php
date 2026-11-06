@extends('layouts.header')
@php
  $isMegaDealer = $isMegaDealer ?? false;
  $profileLabel = $isMegaDealer ? 'Mega Dealer' : 'Dealer';
@endphp
@section('css')
{{-- <style>
    /* Custom styling */
    .transaction-table th {
        text-align: center;
    }
    .btn-view {
        width: 100px;
        font-size: 14px;
    }
    .dashboard-stats {
        display: flex;
        justify-content: space-around;
    }
    .dashboard-stats div {
        text-align: center;
        padding: 20px;
        background-color: #f8f9fa;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 30%;
    }
    /* Welcome section styling */
    .welcome {
        margin-top: 20px;
    }
    .card-header {
        font-size: 1.25rem;
        font-weight: bold;
    }
    .card-body {
        padding: 20px;
    }
    .filter-container {
        margin-bottom: 20px;
    }
    
</style> --}}

<style>
  .dealer-detail-page { padding: 8px 0 26px; }
  .dealer-detail-grid { display:grid; grid-template-columns:minmax(280px,.82fr) minmax(0,1.8fr); gap:18px; align-items:start; }
  .dealer-detail-grid > .dealer-profile-col,.dealer-detail-grid > .dealer-detail-main { width:auto; max-width:none; min-width:0; padding:0; }
  .dealer-profile-card,.dealer-summary-card .card,.dealer-transactions-card { height:100%; overflow:hidden; border:1px solid #dfe8f0!important; border-radius:13px!important; background:#fff; box-shadow:0 10px 26px rgba(25,57,85,.055)!important; }
  .dealer-profile-card .card-header,.dealer-transactions-card .card-header { padding:17px 20px; border-bottom:1px solid #edf1f4; background:linear-gradient(135deg,#fff,#f8fbfc); }
  .dealer-profile-card .card-header h5,.dealer-transactions-card .card-header h5 { margin:0; color:#172f4a; font-size:16px; font-weight:850; }
  .dealer-profile-card .card-body { padding:22px; }
  .profile-avatar { width:104px; height:104px; object-fit:cover; border:4px solid #e8f4f8; border-radius:50%; background:#f2f8fb; box-shadow:0 4px 14px rgba(18,89,120,.1); }
  .profile-name { margin-top:12px; color:#163b58; font-size:17px; font-weight:850; line-height:1.35; overflow-wrap:anywhere; }
  .dealer-store-name { margin-top:3px; color:#8191a1; font-size:12px; font-weight:650; }
  .dealer-profile-actions { display:flex; justify-content:center; gap:8px; margin:16px 0 18px; }
  .dealer-profile-actions .btn { width:36px; height:36px; display:inline-flex; align-items:center; justify-content:center; border:0; border-radius:9px; box-shadow:none; }
  .dealer-profile-divider { margin:0 0 16px; border-color:#e8eef3; opacity:1; }
  .profile-info { display:grid; gap:2px; }
  .profile-info p { display:flex; align-items:flex-start; gap:10px; margin:0; padding:9px 0; color:#40566c; font-size:12px; line-height:1.45; overflow-wrap:anywhere; }
  .profile-info .bi { flex:0 0 16px; margin:1px 0 0; color:#1688b3; font-size:15px; text-align:center; }
  #qrcode { display:flex; justify-content:center; max-width:100%; overflow:hidden; }
  #qrcode img,#qrcode canvas { max-width:100%; height:auto!important; }
  .dealer-detail-main { display:grid; gap:18px; }
  .dealer-summary-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:18px; }
  .dealer-summary-grid > .dealer-summary-card { width:auto; max-width:none; min-width:0; padding:0; }
  .dealer-summary-grid > .dealer-transactions-card-wrap { grid-column:1/-1; width:auto; max-width:none; min-width:0; padding:0; }
  .dealer-summary-card .card,.dealer-transactions-card { margin:0; }
  .dealer-summary-card .card-body { padding:19px; }
  .dealer-summary-title { display:flex; align-items:center; gap:9px; margin:0; color:#203a54; font-size:14px; font-weight:850; }
  .dealer-summary-title > i { color:#1688b3; font-size:17px; }
  .dealer-id-badge { display:inline-flex; width:30px; height:30px; align-items:center; justify-content:center; margin-left:auto; border-radius:8px; background:#e7f6fb; color:#1688b3; }
  .dealer-summary-divider { margin:13px 0; border-color:#e8eef3; opacity:1; }
  .dealer-summary-copy { margin:7px 0 0; color:#65788b; font-size:12px; line-height:1.5; overflow-wrap:anywhere; }
  .dealer-summary-copy strong { color:#415a70; font-weight:750; }
  .dealer-summary-action { margin-top:12px; }
  .dealer-summary-action .btn { display:inline-flex; align-items:center; gap:6px; border-radius:8px; font-size:12px; font-weight:750; }
  .dealer-contract-card .card-body { display:flex; min-height:108px; align-items:center; justify-content:center; flex-direction:column; gap:10px; text-align:center; }
  .dealer-contract-card .card-title { margin:0; color:#203a54; font-size:14px; font-weight:850; }
  .dealer-contract-card .btn { display:inline-flex; align-items:center; gap:6px; border-radius:8px; font-size:12px; font-weight:750; }
  .dealer-contract-card .text-muted { max-width:260px; margin:0; font-size:12px; }
  .dealer-transactions-card .card-body { padding:0; }
  .dealer-transactions-head { display:flex; align-items:center; justify-content:space-between; gap:12px; }
  .dealer-transactions-count { display:inline-flex; padding:6px 9px; border-radius:99px; background:#e4f7fd; color:#18799b; font-size:11px; font-weight:800; white-space:nowrap; }
  .dealer-transactions-scroll { width:100%; overflow-x:auto; -webkit-overflow-scrolling:touch; }
  .dealer-transactions-table { min-width:690px; margin:0!important; }
  .dealer-transactions-table thead th { padding:12px!important; border-color:#e8eef3!important; background:#f7fafc; color:#718196; font-size:10px; font-weight:900; letter-spacing:.04em; text-transform:uppercase; white-space:nowrap; }
  .dealer-transactions-table tbody td { padding:11px 12px!important; border-color:#edf2f5; color:#40566c; font-size:12px; vertical-align:middle; white-space:nowrap; }
  .dealer-transactions-table tbody tr:hover { background:#f7fcfe; }
  .dealer-transactions-table .points-earned { color:#16834a; font-weight:800; }
  @media(max-width:1050px) { .dealer-detail-grid { grid-template-columns:minmax(250px,.75fr) minmax(0,1.5fr); gap:14px; }.dealer-detail-main,.dealer-summary-grid { gap:14px; } }
  @media(max-width:820px) { .dealer-detail-grid { grid-template-columns:minmax(0,1fr); }.dealer-profile-card .card-body { padding:18px; }.dealer-detail-main { gap:14px; } }
  @media(max-width:560px) { .dealer-detail-page { padding-top:0; }.dealer-summary-grid { grid-template-columns:minmax(0,1fr); gap:12px; }.dealer-summary-grid > .dealer-transactions-card-wrap { grid-column:auto; }.dealer-profile-card .card-header,.dealer-transactions-card .card-header { padding:15px; }.dealer-profile-card .card-body,.dealer-summary-card .card-body { padding:15px; }.dealer-transactions-table { min-width:650px; }.dealer-transactions-count { font-size:10px; } }
</style>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.6/dist/signature_pad.umd.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
@endsection

@section('content')
<section class="welcome dealer-detail-page">
  <div class="dealer-detail-grid">
    <aside class="dealer-profile-col">
      <div class="card dealer-profile-card">
        <div class="card-header" style="padding-bottom: 0px">
          <h5>{{ $profileLabel }} Information</h5>
        </div>
        <div class="card-body">
          <div class='text-center'>
            {{-- <img src="{{$dealer->avatar ? asset($dealer->avatar) : asset('design/assets/images/profile/user-1.png')}}" alt="Avatar Image" class="img-fluid rounded-circle" style="width: 100px; height: 100px;"> --}}
            <img src="{{$dealer->avatar ? asset($dealer->avatar) : asset('design/assets/images/profile/user-1.png')}}" onerror="this.onerror=null;this.src='{{ asset('design/assets/images/profile/user-1.png') }}';" alt="{{ $profileLabel }} profile" class="profile-avatar mx-auto">
            {{-- <div class="profile-name">{{ trim(strtoupper($dealer->user->first_name ?? '')) . ' ' . strtoupper(($dealer->user->last_name ?? '')) ?: ( strtoupper($dealer->name ?? '')) }}</div> --}}
            <div class="profile-name">{{ strtoupper($dealer->name) }}</div>
            <div class="dealer-store-name">{{ strtoupper($dealer->store_name) }}</div>
          </div>  
          <div class='dealer-profile-actions'>
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadAvatarModal" title="Upload Avatar">
              <i class="fas fa-camera"></i>
              <span class="sr-only">Upload Avatar</span>
            </button>
            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editDealerModal">
              <i class="fas fa-edit"></i>
            </button>
          </div>
          <hr class="dealer-profile-divider">
          <!-- Customer Personal Details -->
          <div class="profile-info text-start">
            @if($isMegaDealer)<p><i class="bi bi-upc-scan"></i> {{ strtoupper($dealer->partner_code ?: '-') }}</p>@endif
            <p><i class="bi bi-telephone"></i> {{ strtoupper($dealer->number) }}</p>
            <p><i class="bi bi-geo-alt"></i> {{ strtoupper($dealer->address) }}</p>
            <p><i class="bi bi-shop"></i> {{ strtoupper($dealer->store_type) }}</p>
            <p><i class="bi bi-facebook"></i> {{ strtoupper($dealer->facebook) }}</p>
            <p><i class="bi bi-envelope"></i> {{ strtoupper($dealer->email_address) }}</p>
            <p><i class="bi bi-map"></i> {{ strtoupper($dealer->area) }}</p>
          </div>
          <!-- QR Code Generation -->
          <div id="qrcode" class="mt-4 text-center"></div>
        </div>
      </div>
    </aside>
    
    <div class="dealer-detail-main">
      <div class='dealer-summary-grid'>
        <div class="dealer-summary-card">
          <div class="card shadow-sm stretch">
            @if($dealer->valid_id)
              <div class="card-body">
                <h5 class="dealer-summary-title">
                  <i class="bi bi-person-vcard-fill me-2"></i> Valid ID Information  &nbsp;
                  <button type="button" data-bs-toggle="modal"  data-bs-target="#viewValidId" class="btn btn-primary btn-sm btn-radius">
                    <i class="bi bi-file-earmark"></i>
                  </button>
                </h5>
                <hr>
                <p class="dealer-summary-copy">
                  <strong><i class="bi bi-card-text me-2"></i>ID Type:</strong> {{$dealer->valid_id}}
                </p>
                <p class="dealer-summary-copy">
                  <strong><i class="bi bi-hash me-2"></i>ID Number:</strong> {{$dealer->valid_id_number}}
                </p>
              </div>
            @else
              <div class="card-body">
                <h5 class="dealer-summary-title"><i class="bi bi-person-vcard"></i> Valid ID</h5>
                <p class="dealer-summary-copy">No valid ID uploaded yet. Add an ID to complete this dealer profile.</p>
                <div class="dealer-summary-action"><button class="btn btn-outline-primary" type='button' data-bs-toggle="modal"  data-bs-target="#uploadIdModal">
                  <i class="bi bi-upload"></i> Upload ID
                </button></div>
              </div>
            @endif
          </div>
        </div>

        <div class="dealer-summary-card dealer-contract-card">
          @if($dealer->signature)
            <div class="card shadow-sm stretch" >
              <div class="card-body text-center">
                <h6 class="card-title"><i class="mdi mdi-file-document-check-outline"></i> Signed Contract</h6>
                @if($dealer->signature)
                  <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#contractView">
                    <i class="bi bi-file-text"></i> View Signed Contract
                  </button>
                @else
                  <p class="text-muted"><i class="mdi mdi-close-circle-outline"></i> No contract uploaded.</p>
                @endif
              </div>
            </div>
          @else
            <div class="card shadow-sm">
              <div class="card-body text-center">
                <h5 class="card-title"><i class="bi bi-file-earmark-text"></i> Contract Signing</h5>
                <p class="card-text">Review and sign the contract.</p>
                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#contractModal">
                  <i class="bi bi-pencil-square"></i> Sign Contract
                </button>
              </div>
            </div>
          @endif
        </div>

        <div class="dealer-transactions-card-wrap">
          <div class="card dealer-transactions-card">
            <div class="card-header dealer-transactions-head">
                <div><h5>Transactions</h5><small class="text-muted">Purchase history and points earned.</small></div>
                <span class="dealer-transactions-count">{{ number_format($transactions->count()) }} records</span>
            </div>
            <div class="card-body">
              <div class="dealer-transactions-scroll" role="region" aria-label="Dealer transactions" tabindex="0">
              <table class="table dealer-transactions-table">
                <thead>
                  <tr>
                    <th>Transaction No.</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Points Earned</th>
                    <th>Amount</th>
                    <th>Date</th>
                  </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $transaction)
                    <tr>
                        <td>{{$transaction->id}}</td>
                        <td>{{$transaction->item}}</td>
                        <td>{{$transaction->qty}}</td>
                        <td><span class='points-earned'>{{$transaction->points_client}}</span></td>
                        <td>{{number_format($transaction->qty*$transaction->price,2)}}</td>
                        <td>{{date('M d, Y',strtotime($transaction->created_at))}}</td>
                    </tr>
                    @endforeach
                </tbody>
              </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@include('change_avatar_dealer')
@include('upload_valid_id_dealer')
@include('viewValidIdDealer')
@include('sign_contract_dealer')
@include('view_contract_signed_dealer')
@include('edit_dealer')
@endsection

@section('javascript')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  const canvas = document.getElementById('signatureCanvas');
  const ctx = canvas.getContext('2d');
  let drawing = false;

  canvas.addEventListener('mousedown', () => drawing = true);
  canvas.addEventListener('mouseup', () => {
    drawing = false;
    ctx.beginPath();
    saveSignatureAsFile(); // Save after drawing
  });
  canvas.addEventListener('mouseout', () => drawing = false);
  canvas.addEventListener('mousemove', draw);

  function draw(e) {
    if (!drawing) return;
    const rect = canvas.getBoundingClientRect();
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';
    ctx.strokeStyle = '#000';
    ctx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
    ctx.stroke();
    ctx.beginPath();
    ctx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
  }

  function clearSignature() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    document.getElementById('contract_signature').value = '';
  }

  function saveSignatureAsFile() {
    canvas.toBlob(function (blob) {
      const file = new File([blob], "signature.png", { type: "image/png" });

      const dataTransfer = new DataTransfer();
      dataTransfer.items.add(file);

      const input = document.getElementById('contract_signature');
      input.files = dataTransfer.files;
    }, 'image/png');
  }
</script>
<script>
  const video = document.getElementById('video');
  const preview = document.getElementById('preview');
  const imageInput = document.getElementById('image_data');
  const cameraSection = document.getElementById('cameraSection');

  function handleFileUpload(event) {
    stopCamera();
    const file = event.target.files[0];
    if (file) {
      if (!file.type.startsWith('image/')) {
        alert('Please select a valid image file.');
        return;
      }
      
      const maxSize = 5 * 1024 * 1024;
      if (file.size > maxSize) {
        alert('File size is too large. Please select an image smaller than 5MB.');
        return;
      }
      
      const reader = new FileReader();
      reader.onload = function(e) {
        const img = new Image();
        img.onload = function() {
          const canvas = document.createElement('canvas');
          const ctx = canvas.getContext('2d');
          
          const maxWidth = 800;
          const maxHeight = 800;
          let { width, height } = img;
          
          if (width > height) {
            if (width > maxWidth) {
              height = (height * maxWidth) / width;
              width = maxWidth;
            }
          } else {
            if (height > maxHeight) {
              width = (width * maxHeight) / height;
              height = maxHeight;
            }
          }
          
          canvas.width = width;
          canvas.height = height;
          
          ctx.drawImage(img, 0, 0, width, height);
          const compressedDataUrl = canvas.toDataURL('image/png', 0.8);
          
          preview.src = compressedDataUrl;
          imageInput.value = compressedDataUrl;
        };
        img.src = e.target.result;
      };
      reader.readAsDataURL(file);
    }
  }

  function enableCamera() {
    cameraSection.style.display = 'block';
    navigator.mediaDevices.getUserMedia({ 
      video: { 
        width: { ideal: 1280 }, 
        height: { ideal: 720 } 
      } 
    })
      .then(stream => {
        video.srcObject = stream;
      })
      .catch(err => {
        console.error("Camera access error:", err);
        alert("Camera access denied: " + err.message);
      });
  }

  function captureImage() {
    const canvas = document.createElement('canvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0);
    
    const imageData = canvas.toDataURL('image/png', 0.8);
    preview.src = imageData;
    imageInput.value = imageData;
    stopCamera();
  }

  function stopCamera() {
    const stream = video.srcObject;
    if (stream) {
      stream.getTracks().forEach(track => track.stop());
    }
    video.srcObject = null;
    if (cameraSection) {
      cameraSection.style.display = 'none';
    }
  }

  document.addEventListener('DOMContentLoaded', function() {
  const uploadModal = document.getElementById('uploadAvatarModal');
  if (uploadModal) {
    uploadModal.addEventListener('hidden.bs.modal', function() {
      stopCamera();
    });
  }
});
</script>
<script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
<script>
    // Valid data for QR code generation
    const customerData = {
        customerId: 'ST12345',
    };

    // Create a JSON string of the customer data
    const customerDataString = JSON.stringify(customerData);

    // Generate QR code for the customer data
    QRCode.toCanvas(document.getElementById('qrcode'), customerDataString, function(error) {
        if (error) {
            console.error(error);
        } else {
            console.log('QR code generated!');
        }
    });

  $('#editDealerModal').on('shown.bs.modal', function () {
    initSelect2(this);
    initSelect3(this);
  });

  function initSelect3(parent = document) {
    if (!$.fn.select2) return;

    $(parent).find('.select2-area').each(function () {
        const $this = $(this);

        // ✅ ALWAYS destroy first (important)
        if ($this.hasClass('select2-hidden-accessible')) {
            $this.select2('destroy');
        }

        const $modal = $this.closest('.modal');

        $this.select2({
            width: '100%',
            dropdownParent: $modal,
            placeholder: $this.data('placeholder') || 'Select Area',
            allowClear: true,

            templateResult: formatArea,
            templateSelection: formatArea,

            escapeMarkup: function (markup) {
                return markup;
            }
        });

        // ✅ FIX: re-set selected value properly
        let selectedVal = $this.find('option:selected').val();
        if (selectedVal) {
            $this.val(selectedVal).trigger('change.select2');
        }
    });
  }
  $('#editDealerModal').on('shown.bs.modal', function () {
    initMap();

    // 🔥 If dealer already has address → sync map
    if ($('#complete_address').val()) {
        geocodeAddressToMap();
    }
  });
</script>

@endsection
