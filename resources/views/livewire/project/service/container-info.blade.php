<div>
    <div class="sub-menu-wrapper">
        <a class="sub-menu-item" {{ wireNavigate() }}
            href="{{ route('project.service.index', $parameters) }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="sub-menu-item-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            <span class="menu-item-label">Back</span>
        </a>
        <a class="sub-menu-item" {{ wireNavigate() }}
            href="{{ route('project.service.index', $parameters) }}">
            <span class="menu-item-label">General</span>
        </a>
        <a class="sub-menu-item menu-item-active" href="#"><span class="menu-item-label">Container Info</span></a>
        <a class="sub-menu-item" {{ wireNavigate() }}
            href="{{ route('project.service.network', $parameters) }}">
            <span class="menu-item-label">Network</span>
        </a>
    </div>

    <div class="pt-4">
        @if ($resource)
            @if (! empty($containerInfo))
                <div class="flex flex-col gap-4">
                    {{-- Container Status --}}
                    <div class="flex items-center gap-3">
                        <h3>Container Status</h3>
                        <span class="px-2 py-0.5 rounded-md text-white text-xs font-bold
                            {{ $containerInfo['state_running'] ? 'bg-green-500' : 'bg-red-500' }}">
                            {{ $containerInfo['state'] }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 xl:grid-cols-3">
                        {{-- Container ID --}}
                        <div class="p-4 border dark:border-coolgray-300 rounded-xl">
                            <div class="text-xs text-helper">Container ID</div>
                            <div class="flex items-center gap-2 mt-1 font-mono text-sm break-all">
                                <span>{{ Str::limit($containerInfo['id'], 20) }}</span>
                                <x-forms.button
                                    @click="navigator.clipboard.writeText('{{ $containerInfo['id'] }}'); $dispatch('success', 'Copied to clipboard.')"
                                    class="py-0.5 px-1 text-xs" title="Copy">
                                    <x-heroicon-o-clipboard class="w-3.5 h-3.5" />
                                </x-forms.button>
                            </div>
                        </div>

                        {{-- Container Name --}}
                        <div class="p-4 border dark:border-coolgray-300 rounded-xl">
                            <div class="text-xs text-helper">Container Name</div>
                            <div class="flex items-center gap-2 mt-1 font-mono text-sm break-all">
                                <span>{{ Str::limit($containerInfo['name'], 30) }}</span>
                                <x-forms.button
                                    @click="navigator.clipboard.writeText('{{ $containerInfo['name'] }}'); $dispatch('success', 'Copied to clipboard.')"
                                    class="py-0.5 px-1 text-xs" title="Copy">
                                    <x-heroicon-o-clipboard class="w-3.5 h-3.5" />
                                </x-forms.button>
                            </div>
                        </div>

                        {{-- Image --}}
                        <div class="p-4 border dark:border-coolgray-300 rounded-xl">
                            <div class="text-xs text-helper">Image</div>
                            <div class="flex items-center gap-2 mt-1 font-mono text-sm break-all">
                                <span>{{ Str::limit($containerInfo['image'], 30) }}</span>
                                <x-forms.button
                                    @click="navigator.clipboard.writeText('{{ $containerInfo['image'] }}'); $dispatch('success', 'Copied to clipboard.')"
                                    class="py-0.5 px-1 text-xs" title="Copy">
                                    <x-heroicon-o-clipboard class="w-3.5 h-3.5" />
                                </x-forms.button>
                            </div>
                        </div>

                        {{-- IPv4 Address --}}
                        <div class="p-4 border dark:border-coolgray-300 rounded-xl">
                            <div class="text-xs text-helper">IPv4 Address</div>
                            <div class="flex items-center gap-2 mt-1 font-mono text-sm">
                                @if ($containerInfo['ip4_address'])
                                    <span>{{ $containerInfo['ip4_address'] }}</span>
                                    <x-forms.button
                                        @click="navigator.clipboard.writeText('{{ $containerInfo['ip4_address'] }}'); $dispatch('success', 'Copied to clipboard.')"
                                        class="py-0.5 px-1 text-xs" title="Copy">
                                        <x-heroicon-o-clipboard class="w-3.5 h-3.5" />
                                    </x-forms.button>
                                @else
                                    <span class="text-helper">N/A</span>
                                @endif
                            </div>
                        </div>

                        {{-- IPv6 Address --}}
                        <div class="p-4 border dark:border-coolgray-300 rounded-xl">
                            <div class="text-xs text-helper">IPv6 Address</div>
                            <div class="flex items-center gap-2 mt-1 font-mono text-sm">
                                @if ($containerInfo['ip6_address'])
                                    <span>{{ $containerInfo['ip6_address'] }}</span>
                                    <x-forms.button
                                        @click="navigator.clipboard.writeText('{{ $containerInfo['ip6_address'] }}'); $dispatch('success', 'Copied to clipboard.')"
                                        class="py-0.5 px-1 text-xs" title="Copy">
                                        <x-heroicon-o-clipboard class="w-3.5 h-3.5" />
                                    </x-forms.button>
                                @else
                                    <span class="text-helper">N/A</span>
                                @endif
                            </div>
                        </div>

                        {{-- Created --}}
                        <div class="p-4 border dark:border-coolgray-300 rounded-xl">
                            <div class="text-xs text-helper">Created</div>
                            <div class="mt-1 text-sm">
                                {{ $containerInfo['created'] && $containerInfo['created'] !== 'N/A' ? Str::limit($containerInfo['created'], 20) : 'N/A' }}
                            </div>
                        </div>

                        {{-- Started At --}}
                        <div class="p-4 border dark:border-coolgray-300 rounded-xl">
                            <div class="text-xs text-helper">Started At</div>
                            <div class="mt-1 text-sm">
                                {{ $containerInfo['started_at'] && $containerInfo['started_at'] !== '0001-01-01T00:00:00Z' ? Str::limit($containerInfo['started_at'], 20) : 'N/A' }}
                            </div>
                        </div>

                        {{-- Finished At --}}
                        <div class="p-4 border dark:border-coolgray-300 rounded-xl">
                            <div class="text-xs text-helper">Finished At</div>
                            <div class="mt-1 text-sm">
                                {{ $containerInfo['finished_at'] && $containerInfo['finished_at'] !== '0001-01-01T00:00:00Z' ? Str::limit($containerInfo['finished_at'], 20) : 'N/A' }}
                            </div>
                        </div>
                    </div>

                    {{-- Networks --}}
                    <div>
                        <h3 class="mb-2">Networks</h3>
                        <div class="border dark:border-coolgray-300 rounded-xl overflow-hidden">
                            <table class="w-full text-sm">
                                <thead class="bg-neutral-100 dark:bg-coolgray-200">
                                    <tr>
                                        <th class="px-4 py-2 text-xs font-semibold text-left text-helper">Network</th>
                                        <th class="px-4 py-2 text-xs font-semibold text-left text-helper">IPv4</th>
                                        <th class="px-4 py-2 text-xs font-semibold text-left text-helper">IPv6</th>
                                        <th class="px-4 py-2 text-xs font-semibold text-left text-helper">MAC Address</th>
                                        <th class="px-4 py-2 text-xs font-semibold text-left text-helper">Gateway</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($containerInfo['networks'] as $network)
                                        <tr class="border-t border-coolgray-300 dark:border-coolgray-300">
                                            <td class="px-4 py-2 font-mono text-xs">{{ $network['name'] }}</td>
                                            <td class="px-4 py-2 font-mono text-xs">
                                                @if ($network['ipv4'])
                                                    <button
                                                        @click="navigator.clipboard.writeText('{{ $network['ipv4'] }}'); $dispatch('success', 'Copied to clipboard.')"
                                                        class="hover:text-warning" title="Click to copy">
                                                        {{ $network['ipv4'] }}
                                                    </button>
                                                @else
                                                    <span class="text-helper">N/A</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-2 font-mono text-xs">
                                                @if ($network['ipv6'])
                                                    <button
                                                        @click="navigator.clipboard.writeText('{{ $network['ipv6'] }}'); $dispatch('success', 'Copied to clipboard.')"
                                                        class="hover:text-warning" title="Click to copy">
                                                        {{ Str::limit($network['ipv6'], 20) }}
                                                    </button>
                                                @else
                                                    <span class="text-helper">N/A</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-2 font-mono text-xs">
                                                @if ($network['mac'])
                                                    <button
                                                        @click="navigator.clipboard.writeText('{{ $network['mac'] }}'); $dispatch('success', 'Copied to clipboard.')"
                                                        class="hover:text-warning" title="Click to copy">
                                                        {{ $network['mac'] }}
                                                    </button>
                                                @else
                                                    <span class="text-helper">N/A</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-2 font-mono text-xs">{{ $network['gateway'] ?: 'N/A' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-4 py-3 text-center text-helper">No networks found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <x-callout type="warning" title="Container not found or not running">
                    The container may not be running or could not be found. Make sure the service is deployed.
                </x-callout>
            @endif
        @else
            <x-callout type="info" title="No resource selected">
                Please select a container from the service to view its information.
            </x-callout>
        @endif
    </div>
</div>
