<p class="mt-8 text-center text-xs text-80">
     {{__("labels.system.type")}}

    <span class="px-1">&middot;</span>

    v{{ config('app.version') }}

    <span class="px-1">&middot;</span>
    &copy; {{ date('Y') }}
    <a target="_blank" href="{{__("labels.system.copyright_url")}}" class="text-primary dim no-underline">{{__("labels.system.copyright")}}</a>
</p>
