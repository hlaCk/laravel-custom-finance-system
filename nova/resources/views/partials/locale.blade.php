<dropdown-trigger class="h-9 flex items-center">
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M4.06 13a8 8 0 0 0 5.18 6.51A18.5 18.5 0 0 1 8.02 13H4.06zm0-2h3.96a18.5 18.5 0 0 1 1.22-6.51A8 8 0 0 0 4.06 11zm15.88 0a8 8 0 0 0-5.18-6.51A18.5 18.5 0 0 1 15.98 11h3.96zm0 2h-3.96a18.5 18.5 0 0 1-1.22 6.51A8 8 0 0 0 19.94 13zm-9.92 0c.16 3.95 1.23 7 1.98 7s1.82-3.05 1.98-7h-3.96zm0-2h3.96c-.16-3.95-1.23-7-1.98-7s-1.82 3.05-1.98 7zM12 22a10 10 0 1 1 0-20 10 10 0 0 1 0 20z" class="heroicon-ui"></path></svg>
   <!--  <span class="text-90">
        {{ config( 'nova.locales.' . app()->getLocale() ) }}
    </span> -->
</dropdown-trigger>

<dropdown-menu slot="menu" width="200" direction="rtl">
    <ul class="list-reset">
      @foreach( config( 'nova.locales' ) AS $locale_code => $locale_title )
        <li>
          @if( $locale_code == app()->getLocale() )
            <a href="#" class="block no-underline text-90 hover:bg-30 p-3">
                {{ $locale_title }}
            </a>
          @else
            <a href="{{ route( 'nova-locale', [ 'locale' => $locale_code ] ) }}" class="block no-underline text-90 hover:bg-30 p-3">
                &nbsp;{{ $locale_title }}
            </a>
          @endif
        </li>
      @endforeach
    </ul>
</dropdown-menu>
