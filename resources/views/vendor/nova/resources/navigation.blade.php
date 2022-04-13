@if (count(Nova::availableResources(request())))
    <ul class="sidemenu">
        @foreach(navigationRenderCallback($navigation) as $group => $resources)

            @if (count($groups) > 1)
                <li class="sidebar-dropdown mb-2 {{navigationItemIsHidden($group) ? ' hidden navigation_hidden_toggle' : ''}}">
                    <input
                        type="checkbox"
                        {{ isActiveNavigationItem($resources) ? 'checked="checked"' : '' }}
                    />
                    <a href="#" data-toggle="dropdown">
                        <span class="sidebar-label ml-4">{{ $group }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @foreach($resources as $resource)
                            <li class="nav_inner_li">
                                @if(is_string($resource))
                                    <router-link :to="{
                                        name: 'index',
                                        params: {
                                            resourceName: '{{ $resource::uriKey() }}'
                                        }
                                    }" class="flex items-center font-normal text-black text-base no-underline dim">
                                        @if(property_exists($resource, 'icon'))
                                            {!! $resource::$icon !!}
                                        @elseif(method_exists($resource, 'icon'))
                                            {!! $resource::icon() !!}
                                        @else
                                            <img src="{{ asset('images/circle_copy.svg') }}" alt="">
                                        @endif
                                        <span class="sidebar-label">{{ $resource::navigationLabel() }}</span>
                                    </router-link>
                                @else
                                    @isset($resource['class'])
                                        {!! app($resource['class'])->renderNavigation() !!}
                                    @elseif(isset($resource['name']))
                                        <router-link :to="{
                                            name: '{{$resource['name']}}'
                                        }" class="flex items-center font-normal text-black text-base no-underline dim">
                                            @isset($resource['icon'])
                                                {!! data_get($resource, 'icon') !!}
                                            @else
                                                <img src="{{ asset('images/circle_copy.svg') }}" alt="">
                                            @endif
                                            <span class="sidebar-label">{{ data_get($resource, 'navigationLabel', $resource['name']) }}</span>
                                        </router-link>
                                    @else

                                    @endisset
                                @endif
                            </li>
                        @endforeach

                        @if (config('navigation.translation_editor.active') && ($group == __(config('navigation.translation_editor.group'))))
                            <li class="nav_inner_li">
                                <router-link
                                    tag="a"
                                    :to="{name: 'nova-translation-editor'}"
                                    class="flex items-center font-normal text-black mb-6 text-base no-underline dim">
                                    <img src='/images/circle_copy.svg' class='inner-nav-icon'>
                                    <span class="sidebar-label">
                                        {{ __('Translation Editor') }}
                                    </span>
                                </router-link>
                            </li>
                        @endif

                        @if (config('navigation.menu_builder.active') && ($group == __(config('navigation.menu_builder.group'))))
                            <li class="nav_inner_li">
                                @if (OptimistDigital\MenuBuilder\MenuBuilder::getMenuResource()::authorizedToViewAny(request()))
                                    <router-link tag="a" :to="{ name: 'menus' }"
                                                 class="cursor-pointer flex items-center font-normal dim text-white mb-6 text-base no-underline">
                                        <img src='/images/circle_copy.svg' class='inner-nav-icon'>
                                        <span class="sidebar-label">

                                        @{{ __('Menus') }}
                                    </span>
                                    </router-link>
                                @endif
                            </li>
                        @endif

                    </ul>
                </li>

            @else
                @foreach($resources as $resource)
                    <li class="sidebar-dropdown">
                        <router-link :to="{
                name: 'index',
                params: {
                    resourceName: '{{ $resource::uriKey() }}'
                }
            }" class="flex items-center font-normal text-black mb-6 text-base no-underline dim">
                            @if(property_exists($resource, 'icon'))
                                {!! $resource::$icon !!}
                            @elseif(method_exists($resource, 'icon'))
                                {!! $resource::icon() !!}
                            @else
                                <svg class="sidebar-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill="var(--sidebar-icon)"
                                          d="M3 1h4c1.1045695 0 2 .8954305 2 2v4c0 1.1045695-.8954305 2-2 2H3c-1.1045695 0-2-.8954305-2-2V3c0-1.1045695.8954305-2 2-2zm0 2v4h4V3H3zm10-2h4c1.1045695 0 2 .8954305 2 2v4c0 1.1045695-.8954305 2-2 2h-4c-1.1045695 0-2-.8954305-2-2V3c0-1.1045695.8954305-2 2-2zm0 2v4h4V3h-4zM3 11h4c1.1045695 0 2 .8954305 2 2v4c0 1.1045695-.8954305 2-2 2H3c-1.1045695 0-2-.8954305-2-2v-4c0-1.1045695.8954305-2 2-2zm0 2v4h4v-4H3zm10-2h4c1.1045695 0 2 .8954305 2 2v4c0 1.1045695-.8954305 2-2 2h-4c-1.1045695 0-2-.8954305-2-2v-4c0-1.1045695.8954305-2 2-2zm0 2v4h4v-4h-4z"
                                    />
                                </svg>
                            @endif
                            <span class="sidebar-label">{{ $resource::navigationLabel() }}</span>
                        </router-link>
                    </li>
                @endforeach
            @endif
        @endforeach
    </ul>
@endif
