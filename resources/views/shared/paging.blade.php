<?php

$queryString = '';
if(session('filter_site_id')){
  $queryString .=  "site_id=".session('filter_site_id');
}

?>
@if ($paginator->lastPage() > 1)
<nav>
    <ul class="pagination pagination-gutter pagination-primary no-bg">
        <li class="{{ ($paginator->currentPage() == 1) ? ' disabled' : 'page-item page-indicator' }}">
            <a class="page-link" href="{{ $paginator->url(1) }}&{{$queryString}}">
                <i class="la la-angle-left"></i>
            </a>
        </li>

        @for ($i = 1; $i <= $paginator->lastPage(); $i++)
            <?php
            $half_total_links = floor(15 / 2);
            $from = $paginator->currentPage() - $half_total_links;
            $to = $paginator->currentPage() + $half_total_links;
            if ($paginator->currentPage() < $half_total_links) {
               $to += $half_total_links - $paginator->currentPage();
            }
            if ($paginator->lastPage() - $paginator->currentPage() < $half_total_links) {
                $from -= $half_total_links - ($paginator->lastPage() - $paginator->currentPage()) - 1;
            }
            ?>
            @if ($from < $i && $i < $to)
                <li class="{{ ($paginator->currentPage() == $i) ? 'page-item active' : 'page-item' }}">
                    <a class="page-link" href="{{ $paginator->url($i) }}&{{$queryString}}">{{ $i }}</a>
                </li>
            @endif
        @endfor

        <li class="{{ ($paginator->currentPage() == $paginator->lastPage()) ? ' disabled' : 'page-item page-indicator' }}">
            <a class="page-link" href="{{ $paginator->url($paginator->currentPage()+1) }}&{{$queryString}}">
                <i class="la la-angle-right"></i>
            </a>
        </li>
    </ul>
</nav>
@endif