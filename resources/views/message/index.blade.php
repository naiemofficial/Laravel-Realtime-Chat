@php
    $messageLocation = session()->get('messageLocation');
    $messages = !empty($messageLocation) ? session()->get($messageLocation) : [];
    $className = $className ?? null;
    $__CLASS__ = $__CLASS__ ?? null;

    $template = session()->get('messageTemplate') ?? [];
    $template_key = strtolower(!empty($template['key']) ? $template['key'] : 'default');

    $template_source = "message.$template_key.";
@endphp

@if(!empty($messageLocation) && ($messageLocation == $className || $messageLocation == $__CLASS__))
    @php
        $delays = [
            'default' => 300,
            'textonly' => 3500
        ];

        $delayBetween = $delays[$template_key];
    @endphp
    @foreach($messages as $index => $data)
        @include($template_source . $data['key'], [
            'message'   => $data['message'],
            'delay'     => $index * $delayBetween, // Add a delay for each subsequent message
            'template'  => $template
        ])
    @endforeach
@endif
