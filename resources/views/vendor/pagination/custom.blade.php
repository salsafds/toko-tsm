@if ($paginator->hasPages())
    <nav class="flex justify-center" aria-label="Pagination">
        <ul class="inline-flex -space-x-px text-sm">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li>
                    <span class="flex items-center justify-center px-3 py-2 leading-tight text-gray-400 bg-gray-100 rounded-l-lg cursor-not-allowed">
                        &lsaquo;
                    </span>
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}"
                       class="flex items-center justify-center px-3 py-2 leading-tight text-gray-600 bg-white border border-gray-300 rounded-l-lg hover:bg-gray-100 hover:text-gray-700">
                        &lsaquo;
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li>
                        <span class="flex items-center justify-center px-3 py-2 leading-tight text-gray-500 bg-white border border-gray-300">
                            {{ $element }}
                        </span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li>
                                <span class="flex items-center justify-center px-3 py-2 leading-tight text-white bg-blue-600 border border-blue-600">
                                    {{ $page }}
                                </span>
                            </li>
                        @else
                            <li>
                                <a href="{{ $url }}"
                                   class="flex items-center justify-center px-3 py-2 leading-tight text-gray-600 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700">
                                    {{ $page }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}"
                       class="flex items-center justify-center px-3 py-2 leading-tight text-gray-600 bg-white border border-gray-300 rounded-r-lg hover:bg-gray-100 hover:text-gray-700">
                        &rsaquo;
                    </a>
                </li>
            @else
                <li>
                    <span class="flex items-center justify-center px-3 py-2 leading-tight text-gray-400 bg-gray-100 rounded-r-lg cursor-not-allowed">
                        &rsaquo;
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif