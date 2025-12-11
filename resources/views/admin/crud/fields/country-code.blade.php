{{-- Country code picker field --}}
<div class="form-group">
    <label for="{{ $id ?? $name }}">
        {{ $label }} {!! !empty($required) ? '<span class="text-danger">*</span>' : '' !!}
    </label>
    
    <select class="form-select select2 {{ $className ?? '' }}" name="{{ $name }}" id="{{ $id ?? $name }}" {{ !empty($required) ? 'required' : '' }}>
        <!-- High Priority (Preferred) -->
        <option value="91" {{ old($name, $value ?? '91') == '91' ? 'selected' : '' }}>🇮🇳 +91 (India)</option>
        <option value="1" {{ old($name, $value ?? '') == '1' ? 'selected' : '' }}>🇺🇸 +1 (USA)</option>
        <option value="44" {{ old($name, $value ?? '') == '44' ? 'selected' : '' }}>🇬🇧 +44 (UK)</option>

        <!-- Middle East -->
        <option value="971" {{ old($name, $value ?? '') == '971' ? 'selected' : '' }}>🇦🇪 +971 (UAE)</option>
        <option value="974" {{ old($name, $value ?? '') == '974' ? 'selected' : '' }}>🇶🇦 +974 (Qatar)</option>
        <option value="966" {{ old($name, $value ?? '') == '966' ? 'selected' : '' }}>🇸🇦 +966 (Saudi)</option>
        <option value="968" {{ old($name, $value ?? '') == '968' ? 'selected' : '' }}>🇴🇲 +968 (Oman)</option>
        <option value="965" {{ old($name, $value ?? '') == '965' ? 'selected' : '' }}>🇰🇼 +965 (Kuwait)</option>
        <option value="973" {{ old($name, $value ?? '') == '973' ? 'selected' : '' }}>🇧🇭 +973 (Bahrain)</option>
        <option value="20" {{ old($name, $value ?? '') == '20' ? 'selected' : '' }}>🇪🇬 +20 (Egypt)</option>
        <option value="962" {{ old($name, $value ?? '') == '962' ? 'selected' : '' }}>🇯🇴 +962 (Jordan)</option>
        <option value="961" {{ old($name, $value ?? '') == '961' ? 'selected' : '' }}>🇱🇧 +961 (Lebanon)</option>

        <!-- Popular Global -->
        <option value="61" {{ old($name, $value ?? '') == '61' ? 'selected' : '' }}>🇦🇺 +61 (Australia)</option>
        <option value="81" {{ old($name, $value ?? '') == '81' ? 'selected' : '' }}>🇯🇵 +81 (Japan)</option>
        <option value="82" {{ old($name, $value ?? '') == '82' ? 'selected' : '' }}>🇰🇷 +82 (Korea)</option>
        <option value="86" {{ old($name, $value ?? '') == '86' ? 'selected' : '' }}>🇨🇳 +86 (China)</option>
        <option value="65" {{ old($name, $value ?? '') == '65' ? 'selected' : '' }}>🇸🇬 +65 (Singapore)</option>
        <option value="60" {{ old($name, $value ?? '') == '60' ? 'selected' : '' }}>🇲🇾 +60 (Malaysia)</option>
        <option value="63" {{ old($name, $value ?? '') == '63' ? 'selected' : '' }}>🇵🇭 +63 (Philippines)</option>
        <option value="66" {{ old($name, $value ?? '') == '66' ? 'selected' : '' }}>🇹🇭 +66 (Thailand)</option>
        <option value="84" {{ old($name, $value ?? '') == '84' ? 'selected' : '' }}>🇻🇳 +84 (Vietnam)</option>
        <option value="62" {{ old($name, $value ?? '') == '62' ? 'selected' : '' }}>🇮🇩 +62 (Indonesia)</option>

        <!-- Europe & Others -->
        <option value="49" {{ old($name, $value ?? '') == '49' ? 'selected' : '' }}>🇩🇪 +49 (Germany)</option>
        <option value="33" {{ old($name, $value ?? '') == '33' ? 'selected' : '' }}>🇫🇷 +33 (France)</option>
        <option value="39" {{ old($name, $value ?? '') == '39' ? 'selected' : '' }}>🇮🇹 +39 (Italy)</option>
        <option value="55" {{ old($name, $value ?? '') == '55' ? 'selected' : '' }}>🇧🇷 +55 (Brazil)</option>
        <option value="52" {{ old($name, $value ?? '') == '52' ? 'selected' : '' }}>🇲🇽 +52 (Mexico)</option>
        <option value="27" {{ old($name, $value ?? '') == '27' ? 'selected' : '' }}>🇿🇦 +27 (S. Africa)</option>

        <!-- Low Priority (South Asia - moved down) -->
        <option value="92" {{ old($name, $value ?? '') == '92' ? 'selected' : '' }}>🇵🇰 +92 (Pakistan)</option>
        <option value="880" {{ old($name, $value ?? '') == '880' ? 'selected' : '' }}>🇧🇩 +880 (Bangladesh)</option>
        <option value="94" {{ old($name, $value ?? '') == '94' ? 'selected' : '' }}>🇱🇰 +94 (Sri Lanka)</option>
        <option value="977" {{ old($name, $value ?? '') == '977' ? 'selected' : '' }}>🇳🇵 +977 (Nepal)</option>
        <option value="975" {{ old($name, $value ?? '') == '975' ? 'selected' : '' }}>🇧🇹 +975 (Bhutan)</option>
        <option value="960" {{ old($name, $value ?? '') == '960' ? 'selected' : '' }}>🇲🇻 +960 (Maldives)</option>
    </select>
    
    @error($name)
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>
