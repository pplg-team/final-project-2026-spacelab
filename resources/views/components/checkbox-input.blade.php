@props(['disabled' => false, 'checked' => false, 'value' => '1'])

<input type="checkbox" value="{{ $value }}" @checked($checked) @disabled($disabled)
    {{ $attributes->merge(['class' => ' border-gray-300  text-gray-600 shadow-sm focus:ring-gray-500 ']) }}>
