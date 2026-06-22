@extends('layouts.header')
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
  .profile-card {
    border-radius: 16px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.06);
    border: none;
  }

  .profile-avatar {
    width: 120px;
    height: 120px;
    object-fit: cover;
    border-radius: 50%;
    border: 4px solid #f1f3f5;
  }

  .profile-name {
    font-size: 18px;
    font-weight: 600;
    margin-top: 10px;
    color: #5BC2E7;
  }

  .profile-info p {
    font-size: 15px;
    margin-bottom: 10px;
    color: #555;
  }

  .profile-info .bi {
    margin-right: 10px;
    color: #5BC2E7;
  }

  .info-card {
    border-radius: 14px;
    border: none;
    box-shadow: 0 6px 18px rgba(0,0,0,0.05);
    height: 100%;
  }

  .info-card .card-body {
    padding: 18px;
  }

  .icon-box {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f1f3f5;
  }

  .section-title {
    font-weight: 600;
    font-size: 14px;
  }

  .table-modern {
    border-radius: 12px;
    overflow: hidden;
  }

  .table-modern thead {
    background: #f8f9fa;
  }

  .table-modern tbody tr:hover {
    background: #f5f7fa;
    transition: 0.2s;
  }
</style>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.6/dist/signature_pad.umd.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
@endsection

@section('content')
<section class="welcome">
  <div class="row">
    <div class="col-md-4">
      <div class="card">
        <div class="card-header" style="padding-bottom: 0px">
          <h5>Dealer Information</h5>
        </div>
        <div class="card-body">
          <div class='text-center'>
            {{-- <img src="{{$dealer->avatar ? asset($dealer->avatar) : asset('design/assets/images/profile/user-1.png')}}" alt="Avatar Image" class="img-fluid rounded-circle" style="width: 100px; height: 100px;"> --}}
            <img src="{{$dealer->avatar ? asset($dealer->avatar) : asset('design/assets/images/profile/user-1.png')}}" class="profile-avatar mx-auto">
            <div class="profile-name">{{ trim(strtoupper($dealer->user->first_name ?? '')) . ' ' . strtoupper(($dealer->user->last_name ?? '')) ?: ( strtoupper($dealer->name ?? '')) }}</div>
            <div class="text-muted small">{{ strtoupper($dealer->store_name) }}</div>
          </div>  
          <br>
          <div class='text-center mb-3'>
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadAvatarModal" title="Upload Avatar">
              <i class="fas fa-camera"></i>
              <span class="sr-only">Upload Avatar</span>
            </button>
            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editDealerModal">
              <i class="fas fa-edit"></i>
            </button>
          </div>
          <hr>
          <!-- Customer Personal Details -->
          <div class="profile-info text-start">
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
    </div>
    
    <div class="col-xl-8 col-lg-8 col-md-12 col-sm-12 col-xs-12">
      <div class='row'>
        <div class="col-6">
          <div class="card shadow-sm stretch">
            @if($dealer->valid_id)
              <div class="card-body">
                <h5 class="card-title">
                  <i class="bi bi-person-vcard-fill me-2"></i> Valid ID Information  &nbsp;
                  <button type="button" data-bs-toggle="modal"  data-bs-target="#viewValidId" class="btn btn-primary btn-sm btn-radius">
                    <i class="bi bi-file-earmark"></i>
                  </button>
                </h5>
                <hr>
                <p class="mb-2">
                  <strong><i class="bi bi-card-text me-2"></i>ID Type:</strong> {{$dealer->valid_id}}
                </p>
                <p class="mb-0">
                  <strong><i class="bi bi-hash me-2"></i>ID Number:</strong> {{$dealer->valid_id_number}}
                </p>
              </div>
            @else
              <div class="card-body text-center">
                <h5 class="card-title"><i class="bi bi-person-vcard"></i> Upload Valid ID</h5>
                <p class="card-text">Submit a valid government-issued ID.</p>
                <button class="btn btn-danger" type='button' data-bs-toggle="modal"  data-bs-target="#uploadIdModal">
                  <i class="bi bi-upload"></i> Upload ID
                </button>
              </div>
            @endif
          </div>
        </div>

        <div class="col-6">
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

        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
                <h5>Transactions</h5>
            </div>
            <div class="card-body">
              <!-- Purchase History Table -->
              <table class="table table-bordered" style='font-size:12px;'>
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
                        <td><span class='text-success'>{{$transaction->points_client}}</span></td>
                        <td>{{number_format($transaction->qty*$transaction->price,2)}}</td>
                        <td>{{date('M d, Y',strtotime($transaction->created_at))}}</td>
                    </tr>
                    @endforeach
                    <!-- Sample Purchase 1 -->
                    {{-- <tr>
                        <td>123</td>
                        <td>330g LPG Cylinder</td>
                        <td>5</td>
                        <td>PHP XXX.00</td>
                        <td>March 1, 2025</td>
                    </tr> --}}
                </tbody>
              </table>
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
