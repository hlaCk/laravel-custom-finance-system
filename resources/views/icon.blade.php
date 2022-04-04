@isset($icon)
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" class="sidebar-icon">
    <path fill="{{$color??'currentColor'}}" class="heroicon-ui"
          d="{{getSVGPath($icon)}}"/>
</svg>
@endisset
