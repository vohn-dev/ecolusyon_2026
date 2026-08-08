@props(['name' => 'photo', 'required' => true])
 
<div class="camera-capture" data-input-name="{{ $name }}">
    <div class="camera-capture__preview mb-2" style="display:none;">
        <img class="camera-capture__image img-fluid rounded" alt="Captured photo preview">
    </div>
 
    <video class="camera-capture__video w-100 rounded mb-2" style="display:none;" autoplay playsinline muted></video>
    <canvas class="camera-capture__canvas" style="display:none;"></canvas>
 
    <div class="camera-capture__controls d-flex gap-2 justify-content-center mb-2">
        <button type="button" class="btn btn-outline-secondary btn-sm camera-capture__start">
            <i class="bi bi-camera"></i> Open camera
        </button>
        <button type="button" class="btn btn-outline-secondary btn-sm camera-capture__shoot" style="display:none;">
            <i class="bi bi-camera-fill"></i> Take photo
        </button>
        <button type="button" class="btn btn-outline-secondary btn-sm camera-capture__retake" style="display:none;">
            <i class="bi bi-arrow-counterclockwise"></i> Retake
        </button>
    </div>
 
    <input type="file" name="{{ $name }}" accept="image/*" capture="environment"
           class="form-control camera-capture__fileinput" @if($required) required @endif>
 
    @error($name) <div class="text-danger small mt-1">{{ $message }}</div> @enderror
</div>
