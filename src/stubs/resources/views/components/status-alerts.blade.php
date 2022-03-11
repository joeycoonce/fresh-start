@props([
    'errors',
])

@if ($errors->any())
    @foreach ($errors->all() as $error)
        <x-alert type="danger">
            {{ $error }}
        </x-alert>
    @endforeach
@endif

@if (session()->has('success'))
    @foreach(as_array(session('success')) as $message)
        <x-alert type="success">
            {{ $message }}
        </x-alert>
    @endforeach
@endif

@if (session()->has('warning'))
    @foreach(as_array(session('warning')) as $message)
        <x-alert type="warning">
            {{ $message }}
        </x-alert>
    @endforeach
@endif

@if (session()->has('danger'))
    @foreach(as_array(session('danger')) as $message)
        <x-alert type="danger">
            {{ $message }}
        </x-alert>
    @endforeach
@endif

@if (session()->has('info'))
    @foreach(as_array(session('info')) as $message)
        <x-alert type="info">
            {{ $message }}
        </x-alert>
    @endforeach
@endif
