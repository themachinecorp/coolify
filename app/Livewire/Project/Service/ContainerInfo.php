<?php

namespace App\Livewire\Project\Service;

use App\Models\ServiceApplication;
use App\Models\ServiceDatabase;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ContainerInfo extends Component
{
    public ServiceApplication|ServiceDatabase|null $resource = null;

    public array $containerInfo = [];

    public string $resourceType = '';

    public array $parameters = [];

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
            $this->loadContainerInfo();
        }
    }

    private function loadContainerInfo(): void
    {
        $server = $this->resource->service->destination->server;
        $containerId = $this->resource->name.'-'.$this->resource->service->uuid;

        $info = $this->getContainerInfo($server, $containerId);
        if ($info) {
            $this->containerInfo = $info;
        }
    }

    private function getContainerInfo($server, string $containerId): array
    {
        $commands = [
            "docker inspect {$containerId} --format '{{json .}}'",
        ];

        $result = instant_remote_process($commands, $server);
        if (empty($result)) {
            return [];
        }

        $data = json_decode($result, true);
        if (! $data) {
            return [];
        }

        $info = data_get($data, '0', []);

        return [
            'id' => data_get($info, 'Id', 'N/A'),
            'name' => data_get($info, 'Name', 'N/A'),
            'image' => data_get($info, 'Config.Image', 'N/A'),
            'image_hash' => data_get($info, 'Image', 'N/A'),
            'mac_address' => data_get($info, 'NetworkSettings.Networks', 'N/A'),
            'created' => data_get($info, 'Created', 'N/A'),
            'state' => data_get($info, 'State.Status', 'N/A'),
            'state_running' => data_get($info, 'State.Running', false),
            'started_at' => data_get($info, 'State.StartedAt', 'N/A'),
            'finished_at' => data_get($info, 'State.FinishedAt', 'N/A'),
            'ip4_address' => $this->extractIpv4($info),
            'ip6_address' => $this->extractIpv6($info),
            'networks' => $this->extractNetworks($info),
        ];
    }

    private function extractIpv4(array $info): ?string
    {
        $networks = data_get($info, 'NetworkSettings.Networks', []);
        foreach ($networks as $network) {
            $ip = data_get($network, 'IPAddress', '');
            if ($ip) {
                return $ip;
            }
        }

        return null;
    }

    private function extractIpv6(array $info): ?string
    {
        $networks = data_get($info, 'NetworkSettings.Networks', []);
        foreach ($networks as $network) {
            $ip6 = data_get($network, 'GlobalIPv6Address', '');
            if ($ip6) {
                return $ip6;
            }
        }

        return null;
    }

    private function extractNetworks(array $info): array
    {
        $networks = data_get($info, 'NetworkSettings.Networks', []);
        $result = [];
        foreach ($networks as $name => $network) {
            $result[] = [
                'name' => $name,
                'ipv4' => data_get($network, 'IPAddress', ''),
                'ipv6' => data_get($network, 'GlobalIPv6Address', ''),
                'mac' => data_get($network, 'MacAddress', ''),
                'gateway' => data_get($network, 'Gateway', ''),
            ];
        }

        return $result;
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
        return view('livewire.project.service.container-info', [
            'parameters' => $this->parameters,
        ]);
    }
}
