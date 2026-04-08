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
        <a class="sub-menu-item" {{ wireNavigate() }}
            href="{{ route('project.service.container-info', $parameters) }}">
            <span class="menu-item-label">Container Info</span>
        </a>
        <a class="sub-menu-item menu-item-active" href="#"><span class="menu-item-label">Network</span></a>
    </div>

    <div class="pt-4">
        @if ($resource)
            <div class="flex flex-col gap-6">
                {{-- Connect to a new network --}}
                <div class="p-4 border dark:border-coolgray-300 rounded-xl">
                    <h3 class="mb-3">Connect to Network</h3>
                    <div class="flex items-end gap-2">
                        <x-forms.select wire:model="selectedNetwork" label="Select Network" class="flex-1">
                            <option value="">Select a network...</option>
                            @forelse ($availableNetworks as $network)
                                @if (! in_array($network, collect($connectedNetworks)->pluck('name')->toArray()))
                                    <option value="{{ $network }}">{{ $network }}</option>
                                @endif
                            @empty
                                <option disabled>No networks available</option>
                            @endforelse
                        </x-forms.select>
                        <x-forms.button wire:click="connectNetwork" :disabled="! $selectedNetwork">
                            Connect
                        </x-forms.button>
                    </div>
                </div>

                {{-- Connected Networks --}}
                <div>
                    <h3 class="mb-3">Connected Networks</h3>
                    <div class="border dark:border-coolgray-300 rounded-xl overflow-hidden">
                        <table class="w-full text-sm">
                            <thead class="bg-neutral-100 dark:bg-coolgray-200">
                                <tr>
                                    <th class="px-4 py-2 text-xs font-semibold text-left text-helper">Network Name</th>
                                    <th class="px-4 py-2 text-xs font-semibold text-left text-helper">IPv4 Address</th>
                                    <th class="px-4 py-2 text-xs font-semibold text-left text-helper">IPv6 Address</th>
                                    <th class="px-4 py-2 text-xs font-semibold text-left text-helper">Gateway</th>
                                    <th class="px-4 py-2 text-xs font-semibold text-left text-helper">MAC Address</th>
                                    <th class="px-4 py-2 text-xs font-semibold text-right text-helper">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($connectedNetworks as $network)
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
                                        <td class="px-4 py-2 font-mono text-xs">{{ $network['gateway'] ?: 'N/A' }}</td>
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
                                        <td class="px-4 py-2 text-right">
                                            <x-forms.button wire:click="disconnectNetwork('{{ $network['name'] }}')"
                                                class="py-0.5 px-2 text-xs hover:text-error" isError>
                                                Disconnect
                                            </x-forms.button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-3 text-center text-helper">
                                            No networks connected. Use the form above to connect to a network.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <x-callout type="info" title="No resource selected">
                Please select a container from the service to manage its network connections.
            </x-callout>
        @endif
    </div>
</div>
