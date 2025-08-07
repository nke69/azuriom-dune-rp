```blade
@extends('admin.layouts.admin')

@section('title', trans('dune-rp::admin.houses.create'))

@push('styles')
<style>
.form-card {
    background: #fff;
    border-radius: 8px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 25px;
}

.form-section {
    border-bottom: 1px solid #e9ecef;
    padding-bottom: 30px;
    margin-bottom: 30px;
}

.form-section:last-child {
    border-bottom: none;
    padding-bottom: 0;
    margin-bottom: 0;
}

.section-title {
    color: #495057;
    font-size: 1.2rem;
    font-weight: bold;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.form-row.two-columns {
    grid-template-columns: 1fr 1fr;
}

.form-row.three-columns {
    grid-template-columns: 1fr 1fr 1fr;
}

.form-group-full {
    grid-column: 1 / -1;
}

.color-preview {
    display: flex;
    align-items: center;
    gap: 10px;
}

.color-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 3px solid #dee2e6;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    color: white;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
}

.color-presets {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: 10px;
}

.color-preset {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    border: 2px solid #dee2e6;
    cursor: pointer;
    transition: transform 0.2s ease, border-color 0.2s ease;
    position: relative;
}

.color-preset:hover {
    transform: scale(1.1);
    border-color: #007bff;
}

.color-preset.selected {
    border-color: #007bff;
    transform: scale(1.1);
}

.color-preset::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-weight: bold;
    font-size: 0.7rem;
    opacity: 0;
    transition: opacity 0.2s ease;
}

.color-preset.selected::after {
    opacity: 1;
}

.image-upload-area {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    padding: 30px;
    text-align: center;
    background: #f8f9fa;
    transition: border-color 0.3s ease;
    cursor: pointer;
}

.image-upload-area:hover {
    border-color: #007bff;
    background: #e3f2fd;
}

.image-upload-area.dragover {
    border-color: #007bff;
    background: #e3f2fd;
}

.image-preview {
    max-width: 200px;
    max-height: 200px;
    border-radius: 8px;
    border: 2px solid #dee2e6;
    margin: 10px auto;
}

.house-preview {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
    border: 1px solid #dee2e6;
    margin-top: 20px;
}

.preview-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 15px;
}

.preview-logo {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    border: 3px solid currentColor;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.preview-name {
    font-size: 1.5rem;
    font-weight: bold;
    margin: 0;
}

.preview-motto {
    font-style: italic;
    color: #6c757d;
    margin: 10px 0;
}

.preview-meta {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
    margin-top: 15px;
    font-size: 0.9rem;
}

.form-help {
    font-size: 0.875rem;
    color: #6c757d;
    margin-top: 5px;
}

.input-with-icon {
    position: relative;
}

.input-with-icon .form-control {
    padding-left: 35px;
}

.input-with-icon .input-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
}

@media (max-width: 768px) {
    .form-row,
    .form-row.two-columns,
    .form-row.three-columns {
        grid-template-columns: 1fr;
    }
    
    .preview-meta {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>
                    <i class="fas fa-plus-circle"></i>
                    {{ trans('dune-rp::admin.houses.create') }}
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.home') }}">{{ trans('admin.nav.dashboard') }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('dune-rp.admin.houses.index') }}">{{ trans('dune-rp::admin.houses.title') }}</a>
                    </li>
                    <li class="breadcrumb-item active">{{ trans('dune-rp::admin.houses.create') }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="form-card">
            @if($errors->any())
                <div class="alert alert-danger">
                    <h5><i class="fas fa-exclamation-triangle"></i> {{ trans('dune-rp::admin.errors.validation') }}</h5>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('dune-rp.admin.houses.store') }}" method="POST" enctype="multipart/form-data" id="createHouseForm">
                @csrf

                {{-- Basic Information --}}
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-info-circle"></i>
                        {{ trans('dune-rp::admin.houses.basic_info') }}
                    </h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="name" class="form-label">
                                {{ trans('dune-rp::admin.houses.fields.name') }} <span class="text-danger">*</span>
                            </label>
                            <div class="input-with-icon">
                                <i class="fas fa-shield-alt input-icon"></i>
                                <input type="text" id="name" name="name" class="form-control" 
                                       value="{{ old('name') }}" required maxlength="100"
                                       placeholder="{{ trans('dune-rp::admin.houses.placeholders.name') }}">
                            </div>
                            <small class="form-help">{{ trans('dune-rp::admin.houses.help.name') }}</small>
                        </div>

                        <div class="form-group">
                            <label for="leader_id" class="form-label">
                                {{ trans('dune-rp::admin.houses.fields.leader') }}
                            </label>
                            <div class="input-with-icon">
                                <i class="fas fa-crown input-icon"></i>
                                <select id="leader_id" name="leader_id" class="form-control">
                                    <option value="">{{ trans('dune-rp::admin.houses.no_leader') }}</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('leader_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <small class="form-help">{{ trans('dune-rp::admin.houses.help.leader') }}</small>
                        </div>

                        <div class="form-group">
                            <label for="homeworld" class="form-label">
                                {{ trans('dune-rp::admin.houses.fields.homeworld') }}
                            </label>
                            <div class="input-with-icon">
                                <i class="fas fa-globe input-icon"></i>
                                <input type="text" id="homeworld" name="homeworld" class="form-control" 
                                       value="{{ old('homeworld') }}" maxlength="100"
                                       placeholder="{{ trans('dune-rp::admin.houses.placeholders.homeworld') }}">
                            </div>
                            <small class="form-help">{{ trans('dune-rp::admin.houses.help.homeworld') }}</small>
                        </div>
                    </div>

                    <div class="form-group form-group-full">
                        <label for="motto" class="form-label">
                            {{ trans('dune-rp::admin.houses.fields.motto') }}
                        </label>
                        <div class="input-with-icon">
                            <i class="fas fa-quote-left input-icon"></i>
                            <input type="text" id="motto" name="motto" class="form-control" 
                                   value="{{ old('motto') }}" maxlength="255"
                                   placeholder="{{ trans('dune-rp::admin.houses.placeholders.motto') }}">
                        </div>
                        <small class="form-help">{{ trans('dune-rp::admin.houses.help.motto') }}</small>
                    </div>

                    <div class="form-group form-group-full">
                        <label for="description" class="form-label">
                            {{ trans('dune-rp::admin.houses.fields.description') }}
                        </label>
                        <textarea id="description" name="description" class="form-control" rows="5" maxlength="5000"
                                  placeholder="{{ trans('dune-rp::admin.houses.placeholders.description') }}">{{ old('description') }}</textarea>
                        <small class="form-help">{{ trans('dune-rp::admin.houses.help.description') }}</small>
                        <div id="description-counter" class="form-help text-right">0/5000</div>
                    </div>
                </div>

                {{-- Visual Identity --}}
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-palette"></i>
                        {{ trans('dune-rp::admin.houses.visual_identity') }}
                    </h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="color" class="form-label">
                                {{ trans('dune-rp::admin.houses.fields.color') }}
                            </label>
                            <div class="color-preview">
                                <input type="color" id="color" name="color" class="form-control" 
                                       value="{{ old('color', '#007bff') }}" style="width: 60px;">
                                <div class="color-circle" id="color-circle" style="background: {{ old('color', '#007bff') }};">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <div>
                                    <div style="font-weight: bold;">{{ trans('dune-rp::admin.houses.color_preview') }}</div>
                                    <div id="color-value" style="font-size: 0.9rem; color: #6c757d;">{{ old('color', '#007bff') }}</div>
                                </div>
                            </div>
                            
                            <div class="color-presets">
                                <div class="color-preset" data-color="#8B0000" style="background: #8B0000;" title="Harkonnen Rouge"></div>
                                <div class="color-preset" data-color="#4169E1" style="background: #4169E1;" title="Atreides Bleu"></div>
                                <div class="color-preset" data-color="#DAA520" style="background: #DAA520;" title="Corrino Or"></div>
                                <div class="color-preset" data-color="#228B22" style="background: #228B22;" title="Vert Fremen"></div>
                                <div class="color-preset" data-color="#800080" style="background: #800080;" title="Violet Noble"></div>
                                <div class="color-preset" data-color="#FF8C00" style="background: #FF8C00;" title="Orange Épice"></div>
                                <div class="color-preset" data-color="#DC143C" style="background: #DC143C;" title="Rouge Crimson"></div>
                                <div class="color-preset" data-color="#483D8B" style="background: #483D8B;" title="Bleu Slate"></div>
                                <div class="color-preset" data-color="#2F4F4F" style="background: #2F4F4F;" title="Gris Slate"></div>
                                <div class="color-preset" data-color="#8B4513" style="background: #8B4513;" title="Brun Désert"></div>
                            </div>
                            <small class="form-help">{{ trans('dune-rp::admin.houses.help.color') }}</small>
                        </div>

                        <div class="form-group">
                            <label for="sigil" class="form-label">
                                {{ trans('dune-rp::admin.houses.fields.sigil') }}
                            </label>
                            <div class="image-upload-area" onclick="document.getElementById('sigil').click()">
                                <i class="fas fa-cloud-upload-alt" style="font-size: 2rem; color: #6c757d; margin-bottom: 10px;"></i>
                                <div style="color: #6c757d;">
                                    {{ trans('dune-rp::admin.houses.upload_sigil') }}
                                </div>
                                <div style="font-size: 0.8rem; color: #adb5bd; margin-top: 5px;">
                                    {{ trans('dune-rp::admin.houses.sigil_formats') }}
                                </div>
                                <input type="file" id="sigil" name="sigil" class="d-none" accept="image/*">
                                <img id="image-preview" class="image-preview" style="display: none;" alt="{{ trans('dune-rp::admin.houses.sigil_preview') }}">
                            </div>
                            <small class="form-help">{{ trans('dune-rp::admin.houses.help.sigil') }}</small>
                        </div>
                    </div>
                </div>

                {{-- Resources & Status --}}
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-coins"></i>
                        {{ trans('dune-rp::admin.houses.resources_status') }}
                    </h3>

                    <div class="form-row three-columns">
                        <div class="form-group">
                            <label for="spice_reserves" class="form-label">
                                {{ trans('dune-rp::admin.houses.fields.spice_reserves') }}
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-coins"></i>
                                    </span>
                                </div>
                                <input type="number" id="spice_reserves" name="spice_reserves" class="form-control" 
                                       value="{{ old('spice_reserves', 1000) }}" min="0" max="999999999" step="0.01">
                                <div class="input-group-append">
                                    <span class="input-group-text">{{ trans('dune-rp::admin.spice.unit') }}</span>
                                </div>
                            </div>
                            <small class="form-help">{{ trans('dune-rp::admin.houses.help.spice_reserves') }}</small>
                        </div>

                        <div class="form-group">
                            <label for="influence_points" class="form-label">
                                {{ trans('dune-rp::admin.houses.fields.influence_points') }}
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-star"></i>
                                    </span>
                                </div>
                                <input type="number" id="influence_points" name="influence_points" class="form-control" 
                                       value="{{ old('influence_points', 0) }}" min="0" max="999999999">
                                <div class="input-group-append">
                                    <span class="input-group-text">pts</span>
                                </div>
                            </div>
                            <small class="form-help">{{ trans('dune-rp::admin.houses.help.influence_points') }}</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">{{ trans('dune-rp::admin.houses.fields.status') }}</label>
                            <div class="form-check" style="padding-top: 8px;">
                                <input type="checkbox" id="is_active" name="is_active" value="1" 
                                       {{ old('is_active', true) ? 'checked' : '' }} class="form-check-input">
                                <label for="is_active" class="form-check-label">
                                    <i class="fas fa-toggle-on"></i>
                                    {{ trans('dune-rp::admin.houses.fields.is_active') }}
                                </label>
                            </div>
                            <small class="form-help">{{ trans('dune-rp::admin.houses.help.is_active') }}</small>
                        </div>
                    </div>
                </div>

                {{-- Preview --}}
                <div class="house-preview" id="house-preview">
                    <h4 style="margin-bottom: 20px;">
                        <i class="fas fa-eye"></i>
                        {{ trans('dune-rp::admin.houses.preview') }}
                    </h4>
                    <div class="preview-content">
                        <div class="preview-header">
                            <div class="preview-logo" id="preview-logo" style="background: #007bff;">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div>
                                <h3 class="preview-name" id="preview-name">{{ trans('dune-rp::admin.houses.house_name') }}</h3>
                                <div class="preview-motto" id="preview-motto" style="display: none;"></div>
                            </div>
                        </div>
                        
                        <div class="preview-meta">
                            <div id="preview-leader" style="display: none;">
                                <i class="fas fa-crown"></i>
                                <span></span>
                            </div>
                            <div id="preview-homeworld" style="display: none;">
                                <i class="fas fa-globe"></i>
                                <span></span>
                            </div>
                            <div>
                                <i class="fas fa-coins"></i>
                                <span id="preview-spice">1,000</span> {{ trans('dune-rp::admin.spice.unit') }}
                            </div>
                            <div>
                                <i class="fas fa-star"></i>
                                <span id="preview-influence">0</span> pts d'influence
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="form-actions" style="text-align: center; padding-top: 30px; border-top: 1px solid #dee2e6;">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-save"></i>
                        {{ trans('dune-rp::admin.houses.create') }}
                    </button>
                    
                    <a href="{{ route('dune-rp.admin.houses.index') }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times"></i>
                        {{ trans('admin.cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('createHouseForm');
    const colorInput = document.getElementById('color');
    const colorCircle = document.getElementById('color-circle');
    const colorValue = document.getElementById('color-value');
    const colorPresets = document.querySelectorAll('.color-preset');
    const imageInput = document.getElementById('sigil');
    const imagePreview = document.getElementById('image-preview');
    const uploadArea = document.querySelector('.image-upload-area');
    
    // Color picker functionality
    colorInput.addEventListener('change', function() {
        updateColorPreview(this.value);
    });
    
    colorPresets.forEach(preset => {
        preset.addEventListener('click', function() {
            const color = this.dataset.color;
            colorInput.value = color;
            updateColorPreview(color);
            
            // Update selected state
            colorPresets.forEach(p => p.classList.remove('selected'));
            this.classList.add('selected');
        });
    });
    
    function updateColorPreview(color) {
        colorCircle.style.background = color;
        colorValue.textContent = color;
        document.getElementById('preview-logo').style.background = color;
        document.getElementById('preview-name').style.color = color;
    }
    
    // Image upload functionality
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                imagePreview.style.display = 'block';
                uploadArea.querySelector('i').style.display = 'none';
                uploadArea.querySelector('div').style.display = 'none';
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Drag & Drop functionality
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('dragover');
    });
    
    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
    });
    
    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            imageInput.files = files;
            imageInput.dispatchEvent(new Event('change'));
        }
    });
    
    // Character counter for description
    const descriptionTextarea = document.getElementById('description');
    const counter = document.getElementById('description-counter');
    
    descriptionTextarea.addEventListener('input', function() {
        const current = this.value.length;
        const max = 5000;
        counter.textContent = `${current}/${max}`;
        
        if (current > max * 0.9) {
            counter.classList.add('text-warning');
        } else {
            counter.classList.remove('text-warning');
        }
    });
    
    // Live preview updates
    function updatePreview() {
        const name = document.getElementById('name').value || 'Nom de la Maison';
        const motto = document.getElementById('motto').value;
        const leaderId = document.getElementById('leader_id').value;
        const homeworld = document.getElementById('homeworld').value;
        const spice = document.getElementById('spice_reserves').value || '0';
        const influence = document.getElementById('influence_points').value || '0';
        
        // Update preview elements
        document.getElementById('preview-name').textContent = name;
        
        const previewMotto = document.getElementById('preview-motto');
        if (motto) {
            previewMotto.textContent = `"${motto}"`;
            previewMotto.style.display = 'block';
        } else {
            previewMotto.style.display = 'none';
        }
        
        const previewLeader = document.getElementById('preview-leader');
        if (leaderId) {
            const leaderOption = document.querySelector(`#leader_id option[value="${leaderId}"]`);
            if (leaderOption) {
                previewLeader.querySelector('span').textContent = leaderOption.textContent;
                previewLeader.style.display = 'block';
            }
        } else {
            previewLeader.style.display = 'none';
        }
        
        const previewHomeworld = document.getElementById('preview-homeworld');
        if (homeworld) {
            previewHomeworld.querySelector('span').textContent = homeworld;
            previewHomeworld.style.display = 'block';
        } else {
            previewHomeworld.style.display = 'none';
        }
        
        document.getElementById('preview-spice').textContent = new Intl.NumberFormat().format(spice);
        document.getElementById('preview-influence').textContent = new Intl.NumberFormat().format(influence);
    }
    
    // Attach event listeners for live preview
    ['name', 'motto', 'leader_id', 'homeworld', 'spice_reserves', 'influence_points'].forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.addEventListener('input', updatePreview);
            element.addEventListener('change', updatePreview);
        }
    });
    
    // Initial preview update
    updatePreview();
    
    // Form validation
    form.addEventListener('submit', function(e) {
        let isValid = true;
        const errors = [];
        
        // Check required fields
        const name = document.getElementById('name').value.trim();
        if (!name) {
            errors.push('Le nom de la maison est requis');
            isValid = false;
        }
        
        // Check for duplicate name (basic client-side check)
        if (name.toLowerCase().includes('test') && name.length < 4) {
            errors.push('Le nom de la maison doit être plus descriptif');
            isValid = false;
        }
        
        // Validate spice reserves
        const spice = parseFloat(document.getElementById('spice_reserves').value);
        if (spice < 0 || spice > 999999999) {
            errors.push('Les réserves d\'épice doivent être entre 0 et 999,999,999');
            isValid = false;
        }
        
        // Validate influence points
        const influence = parseInt(document.getElementById('influence_points').value);
        if (influence < 0 || influence > 999999999) {
            errors.push('Les points d\'influence doivent être entre 0 et 999,999,999');
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
            alert('Erreurs de validation:\n' + errors.join('\n'));
        }
    });
    
    // Auto-save draft functionality
    let autoSaveTimeout;
    const formInputs = form.querySelectorAll('input, textarea, select');
    
    formInputs.forEach(input => {
        input.addEventListener('input', function() {
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = setTimeout(function() {
                const formData = new FormData(form);
                const draft = Object.fromEntries(formData.entries());
                localStorage.setItem('house_draft', JSON.stringify(draft));
                
                // Show saved indicator
                let indicator = document.getElementById('save-indicator');
                if (!indicator) {
                    indicator = document.createElement('div');
                    indicator.id = 'save-indicator';
                    indicator.style.cssText = 'position: fixed; top: 20px; right: 20px; background: #28a745; color: white; padding: 10px 15px; border-radius: 5px; z-index: 1000; opacity: 0; transition: opacity 0.3s;';
                    document.body.appendChild(indicator);
                }
                
                indicator.textContent = 'Brouillon sauvegardé';
                indicator.style.opacity = '1';
                
                setTimeout(() => {
                    indicator.style.opacity = '0';
                }, 2000);
            }, 1000);
        });
    });
    
    // Load draft on page load
    const savedDraft = localStorage.getItem('house_draft');
    if (savedDraft) {
        try {
            const draft = JSON.parse(savedDraft);
            
            if (confirm('Un brouillon de maison a été trouvé. Voulez-vous le restaurer ?')) {
                Object.keys(draft).forEach(key => {
                    const input = form.querySelector(`[name="${key}"]`);
                    if (input) {
                        if (input.type === 'checkbox') {
                            input.checked = draft[key] === '1';
                        } else {
                            input.value = draft[key];
                        }
                        
                        // Trigger events for proper updates
                        input.dispatchEvent(new Event('input'));
                        input.dispatchEvent(new Event('change'));
                    }
                });
            } else {
                localStorage.removeItem('house_draft');
            }
        } catch (e) {
            localStorage.removeItem('house_draft');
        }
    }
    
    // Clear draft on successful submit
    form.addEventListener('submit', function() {
        localStorage.removeItem('house_draft');
    });
});
</script>
@endpush
```
