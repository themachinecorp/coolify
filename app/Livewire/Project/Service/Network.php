<?php

namespace App\Livewire\Project\Service;

use App\Models\ServiceApplication;
use App\Models\ServiceDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;
use Livewire\Component;

class Network extends Component
{
    public ServiceApplication|ServiceDatabase|null $resource = null;

    public string $resourceType = '';

    public array $parameters = [];

    public array $connectedNetworks = [];

    public Collection $availableNetworks = [];

    public ?string $selectedNetwork = null;

    public ?string $ipv6Enabled = null;

    public function mount()
    {
        $this->parameters = get_route_parameters();
        $service = \App\Models\Service::whereUuid($this->parameters['service_uuid'])->first();
        if (! $service) {
            return;
        }

        $this->authorize('view', $service);

        $stackServiceUuid = $this->parameters['stack_service_uuid'] ?? null;
        if (! $stackServiceUuid) {
            return;
        }

        $application = $service->applications()->whereUuid($stackServiceUuid)->first();
        if ($application) {
            $this->resource = $application;
            $this->resourceType = 'application';
        } else {
            $this->resource = $service->databases()->whereUuid($stackServiceUuid)->first();
            $this->resourceType = 'database';
        }

        if ($this->resource) {
            $this->loadNetworks();
        }
    }

    private function loadNetworks(): void
    {
        $server = $this->resource->service->destination->server;
        $containerId = $this->resource->name.'-'.$this->resource->service->uuid;

        $this->connectedNetworks = $this->getConnectedNetworks($server, $containerId);
        $this->availableNetworks = $this->getAvailableNetworks($server);
    }

    private function getConnectedNetworks($server, string $containerId): array
    {
        $cmd = "docker inspect {$containerId} --format '{{json .NetworkSettings.Networks}}'";
        $result = instant_remote_process([$cmd], $server);
        if (empty($result)) {
            return [];
        }

        $networks = json_decode($result, true);
        if (! $networks) {
            return [];
        }

        $connected = [];
        foreach ($networks as $name => $network) {
            $connected[] = [
                'name' => $name,
                'ipv4' => data_get($network, 'IPAddress', ''),
                'ipv6' => data_get($network, 'GlobalIPv6Address', ''),
                'gateway' => data_get($network, 'Gateway', ''),
                'mac' => data_get($network, 'MacAddress', ''),
            ];
        }

        return $connected;
    }

    private function getAvailableNetworks($server): Collection
    {
        $cmd = "docker network ls --format '{{.Name}}' 2>/dev/null | grep -v '^coolify\|^bridge\|^host\|^none$' | sort -u";
        $result = instant_remote_process([$cmd], $server);
        if (empty($result)) {
            return collect();
        }

        $networks = array_filter(explode("\n", trim($result)));

        return collect($networks)->filter(fn ($n) => ! empty(trim($n)))->values();
    }

    public function connectNetwork()
    {
        try {
            $this->authorize('update', $this->resource);

            if (! $this->selectedNetwork) {
                $this->dispatch('error', 'Please select a network.');

                return;
            }

            $server = $this->resource->service->destination->server;
            $containerId = $this->resource->name.'-'.$this->resource->service->uuid;

            $isRunning = $this->isContainerRunning($server, $containerId);
            $cmd = "docker network connect {$this->selectedNetwork} {$containerId}";
            instant_remote_process([$cmd], $server);

            $this->loadNetworks();
            $this->selectedNetwork = null;
            $this->dispatch('success', "Connected to network: {$this->selectedNetwork}");
        } catch (\Throwable $e) {
            return handleError($e, $this);
        }
    }

    public function disconnectNetwork(string $networkName)
    {
        try {
            $this->authorize('update', $this->resource);

            $server = $this->resource->service->destination->server;
            $containerId = $this->resource->name.'-'.$this->resource->service->uuid;

            $cmd = "docker network disconnect {$networkName} {$containerId}";
            instant_remote_process([$cmd], $server);

            $this->loadNetworks();
            $this->dispatch('success', "Disconnected from network: {$networkName}");
        } catch (\Throwable $e) {
            return handleError($e, $this);
        }
    }

    private function isContainerRunning($server, string $containerId): bool
    {
        $cmd = "docker inspect {$containerId} --format '{{.State.Running}}' 2>/dev/null";
        $result = instant_remote_process([$cmd], $server);

        return trim($result) === 'true';
    }

    public function getListeners()
    {
        $teamId = Auth::user()->currentTeam()->id;

        return [
            "echo-private:team.{$teamId},ServiceStatusChanged" => '$refresh',
        ];
    }

    public function render()
    {
        return view('livewire.project.service.network', [
            'parameters' => $this->parameters,
        ]);
    }
}
