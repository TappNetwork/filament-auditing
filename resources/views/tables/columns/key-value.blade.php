<div class="my-2 text-sm font-medium tracking-tight">
    @foreach($getState() ?? [] as $key => $value)
        <span class="inline-block p-1 mr-1 rounded-md whitespace-normal text-gray-700 dark:text-gray-200 bg-gray-500/10">
            {{ $key }}
        </span>
        @unless(is_array($value))
            {{ $value }}
        @else
            <span class="divide-x divide-solid divide-gray-200 dark:divide-gray-700">
                @foreach ($value as $nestedValue)
                    {{$nestedValue['id']}}
                @endforeach
            </span>
        @endunless
    @endforeach
</div>
