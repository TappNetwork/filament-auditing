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
                            @unless(is_array($value))
                                {{ $value }}
                            @else
                                <span class="divide-x divide-solid divide-gray-200 dark:divide-gray-700">
                                    @foreach ($value as $nestedValue)
                                        {{ $nestedValue['id'] }}
                                    @endforeach
                                </span>
                            @endunless
                        </span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</x-dynamic-component>
