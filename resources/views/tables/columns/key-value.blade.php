<div class="my-2 text-sm font-medium tracking-tight">
    @foreach($getState() as $key => $value)
        <span class="inline-block p-1 mr-1 rounded-md whitespace-normal text-gray-700 bg-gray-500/10">
            {{ $key }}
        </span>
        {{ $value }}
    @endforeach
</div>
