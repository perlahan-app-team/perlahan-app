@props(['name', 'size' => 19])
<span {{ $attributes->class('ui-icon') }} aria-hidden="true"
      style="--icon-url: url('{{ asset('icons/lucide/' . $name . '.svg') }}'); --icon-size: {{ (int) $size }}px"></span>
