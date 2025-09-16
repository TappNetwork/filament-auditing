<x-dynamic-component
    :component="$getEntryWrapperView()"
    :entry="$entry"
>
    <div {{ $getExtraAttributeBag() }}>
        <div>
            <ul>
                @foreach($getState() ?? [] as $key => $value)
                    <li class="mb-2">
                        <span class="inline-block rounded-md whitespace-normal text-gray-700 dark:text-gray-200">
                           {{ Str::title($key) }}:
                        </span>
                        <span class="font-semibold">
                            @if(is_array($value))
                                <span class="divide-x divide-solid divide-gray-200 dark:divide-gray-700">
                                    @foreach ($value as $nestedValue)
                                        {{ $nestedValue['id'] }}
                                    @endforeach
                                </span>
                            @elseif (is_bool($value))
                                {{ $value ? 'true' : 'false' }}
                            @else
                                {{ $value }}
                            @endif
                        </span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</x-dynamic-component>
