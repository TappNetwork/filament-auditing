@php
    $values = isset($data) ? $data : $getState();
@endphp

<div {{ $getExtraAttributeBag() }} class="fi-ta-col">
    <div class="fi-size-sm fi-ta-text-item fi-ta-text">
        <ul>
            @foreach($values ?? [] as $key => $value)
                <li>
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
