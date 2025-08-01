@if(method_exists($data, 'hasPages') && $data->hasPages())
    <div class="{{ $class }} border-t-[1px] border-solid border-gray-200 w-full bg-gray-200 px-8 py-[4px] absolute bottom-0 rounded-br-md">
        {{ $data->links('vendor.livewire.tailwind') }}
    </div>
@endif
