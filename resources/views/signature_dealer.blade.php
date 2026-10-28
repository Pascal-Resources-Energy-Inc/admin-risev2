<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Digital Signature Pad</title>
<style>
  html, body {
    margin: 0;
    padding: 0;
    height: 100%;
    overflow: hidden; /* Prevent scrolling */
    background: #f8f9fa;
    touch-action: none; /* Prevent touch gestures from scrolling */
  }
  #signature-pad {
    display: block;
    background: white;
    border: 2px solid #ccc;
    width: 100%;
    height: 90%;
    touch-action: none;
  }
  .buttons {
    text-align: center;
    height: 10%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f0f0f0;
    border-top: 1px solid #ccc;
  }
  button {
    padding: 8px 16px;
    margin: 0 5px;
    font-size: 14px;
    cursor: pointer;
  }
</style>
</head>
<body>
    <form method='POST' action='{{ ($isMegaDealer ?? false) ? route('mega-dealers.contract', $dealer->id) : url('submit-contract-dealer/'.$dealer->id) }}' onsubmit='show()' enctype="multipart/form-data" class="validation-wizard wizard-circle mt-5">
      @csrf
        <canvas id="signature-pad"></canvas>
        <input type="file" name="contract_signature" id="contract_signature" style="display: none;" required/>
        <div class="buttons">
        <a href='{{ ($isMegaDealer ?? false) ? route('mega-dealers.show', $dealer->id) : url("/view-dealer/".$dealer->id) }}'>Back to Contract</a>
        <button type='button'  onclick="clearPad()">Clear</button>
        <button type='submit'>Save</button>
        </div>
   </form>

<script>
const canvas = document.getElementById('signature-pad');
const ctx = canvas.getContext('2d');
let drawing = false;

function resizeCanvas() {
  canvas.width = window.innerWidth;
  canvas.height = window.innerHeight * 0.9; // 90% height
  drawPlaceholder();
}
window.addEventListener('resize', resizeCanvas);
resizeCanvas();

function drawPlaceholder() {
  ctx.font = '20px Arial';
  ctx.fillStyle = 'rgba(150, 150, 150, 0.6)';
  ctx.textAlign = 'center';
  ctx.fillText('Sign Here', canvas.width / 2, canvas.height - 20);
}

function startDraw(e) {
  drawing = true;
  ctx.beginPath();
  ctx.moveTo(getX(e), getY(e));
  
}
function draw(e) {
  if (!drawing) return;
  ctx.lineWidth = 2;
  ctx.lineCap = 'round';
  ctx.strokeStyle = '#000';
  ctx.lineTo(getX(e), getY(e));
  ctx.stroke();
}
function endDraw() {
  drawing = false;
  saveSignatureAsFile(); // Save after drawing
  ctx.closePath();
}
function getX(e) {
  return e.type.includes('touch') ? e.touches[0].clientX : e.clientX;
}
function getY(e) {
  return e.type.includes('touch') ? e.touches[0].clientY : e.clientY;
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

// Mouse events
canvas.addEventListener('mousedown', startDraw);
canvas.addEventListener('mousemove', draw);
canvas.addEventListener('mouseup', endDraw);
canvas.addEventListener('mouseleave', endDraw);

// Touch events
canvas.addEventListener('touchstart', startDraw);
canvas.addEventListener('touchmove', draw);
canvas.addEventListener('touchend', endDraw);

// Buttons
function clearPad() {
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  drawPlaceholder();
}
function saveSignature() {
  const dataURL = canvas.toDataURL('image/png');
  const a = document.createElement('a');
  a.href = dataURL;
  a.download = 'signature.png';
  a.click();
}
</script>

</body>
</html>
