@props(['name'])
{{ collect(preg_split('/\s+/u', trim($name)))->filter()->take(2)->map(fn($word) => mb_strtoupper(mb_substr($word,0,1)))->implode('') }}
