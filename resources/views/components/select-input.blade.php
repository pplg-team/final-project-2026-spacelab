@props(['disabled' => false])

<select @disabled($disabled)
    {{ $attributes->merge(['class' => 'block w-full md border-gray-300    shadow-sm focus:border-gray-500 focus:ring-gray-500 sm:text-sm']) }}>
    {{ $slot }}
</select>
