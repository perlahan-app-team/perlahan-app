@props(['variant' => 'default'])
<div {{ $attributes->class(['logo', 'logo-' . $variant]) }}>
    <img src="{{ asset('images/perlahan-logo.png') }}"
         width="1195" height="1097"
         alt="PERLAHAN — Platform Pengelolaan dan Pemanfaatan Lahan"
         decoding="async">
</div>
