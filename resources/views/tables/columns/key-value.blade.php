@php
    $data = isset($state) ? $state : $getState()
@endphp

<div class="my-2 text-sm font-medium tracking-tight">
    <ul>
        @foreach($data ?? [] as $key => $value)
            <li>
                <span class="inline-block rounded-md whitespace-normal text-gray-700 dark:text-gray-200 bg-gray-500/10">
                    {{ Str::title($key) }}:
                </span>
                <span class="font-semibold">
                    @unless(is_array($value))
                        {{ $value }}
                    @else
                        <span class="divide-x divide-solid divide-gray-200 dark:divide-gray-700">
                            @foreach ($value as $nestedValue)
                                {{$nestedValue['id']}}
                            @endforeach
                        </span>
                    @endunless
                </span>
            </li>
        @endforeach
    </ul>
</div>
