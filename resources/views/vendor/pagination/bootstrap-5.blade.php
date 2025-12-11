@if ($paginator->hasPages())
    <nav aria-label="Page navigation" style="margin-top: 10px;margin-bottom: 10px;">
        <ul class="pagination justify-content-center mb-0" style="gap: 6px;">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link border-0 bg-light text-muted" style="
                        width: 38px; 
                        height: 38px; 
                        border-radius: 12px; 
                        display: flex; 
                        align-items: center; 
                        justify-content: center;
                        font-size: 14px;
                        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                    ">
                        <i class="mdi mdi-chevron-left"></i>
                    </span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link border-0 bg-white" href="{{ $paginator->previousPageUrl() }}" rel="prev" style="
                        width: 38px; 
                        height: 38px; 
                        border-radius: 12px; 
                        display: flex; 
                        align-items: center; 
                        justify-content: center;
                        font-size: 14px;
                        color: #20c997;
                        box-shadow: 0 2px 8px rgba(32,201,151,0.15);
                        transition: all 0.3s ease;
                        border: 2px solid #20c997;
                    " onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(32,201,151,0.25)'; this.style.borderColor='#17a2b8'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(32,201,151,0.15)'; this.style.borderColor='#20c997'">
                        <i class="mdi mdi-chevron-left"></i>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled">
                        <span class="page-link border-0 bg-light text-muted" style="
                            width: 45px; 
                            height: 45px; 
                            border-radius: 12px; 
                            display: flex; 
                            align-items: center; 
                            justify-content: center;
                            font-size: 14px;
                            font-weight: 500;
                        ">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active">
                                <span class="page-link border-0 text-white" style="
                                    width: 38px; 
                                    height: 38px; 
                                    border-radius: 12px; 
                                    display: flex; 
                                    align-items: center; 
                                    justify-content: center;
                                    font-size: 14px;
                                    font-weight: 600;
                                    background: linear-gradient(135deg, #20c997, #17a2b8);
                                    box-shadow: 0 4px 12px rgba(32,201,151,0.4);
                                    border: 2px solid #138496;
                                ">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link border-0 bg-white" href="{{ $url }}" style="
                                    width: 38px; 
                                    height: 38px; 
                                    border-radius: 12px; 
                                    display: flex; 
                                    align-items: center; 
                                    justify-content: center;
                                    font-size: 14px;
                                    font-weight: 500;
                                    color: #20c997;
                                    box-shadow: 0 2px 4px rgba(32,201,151,0.1);
                                    transition: all 0.3s ease;
                                    border: 2px solid #20c997;
                                " onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 8px rgba(32,201,151,0.15)'; this.style.backgroundColor='#f0fdfa'; this.style.borderColor='#17a2b8'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 4px rgba(32,201,151,0.1)'; this.style.backgroundColor='white'; this.style.borderColor='#20c997'">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link border-0 bg-white" href="{{ $paginator->nextPageUrl() }}" rel="next" style="
                        width: 38px; 
                        height: 38px; 
                        border-radius: 12px; 
                        display: flex; 
                        align-items: center; 
                        justify-content: center;
                        font-size: 14px;
                        color: #20c997;
                        box-shadow: 0 2px 8px rgba(32,201,151,0.15);
                        transition: all 0.3s ease;
                        border: 2px solid #20c997;
                    " onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(32,201,151,0.25)'; this.style.borderColor='#17a2b8'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(32,201,151,0.15)'; this.style.borderColor='#20c997'">
                        <i class="mdi mdi-chevron-right"></i>
                    </a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link border-0 bg-light text-muted" style="
                        width: 38px; 
                        height: 38px; 
                        border-radius: 12px; 
                        display: flex; 
                        align-items: center; 
                        justify-content: center;
                        font-size: 14px;
                        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                    ">
                        <i class="mdi mdi-chevron-right"></i>
                    </span>
                </li>
            @endif
        </ul>
        
        {{-- Pagination Info --}}
        <div class="text-center mt-3">
            <small class="text-muted">
                Showing {{ $paginator->firstItem() ?? 0 }} to {{ $paginator->lastItem() ?? 0 }} of {{ $paginator->total() }} results
            </small>
        </div>
    </nav>
@endif