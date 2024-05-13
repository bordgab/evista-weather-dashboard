<?php

declare(strict_types=1);

namespace App\Websocket;

use App\WeatherApi\WeatherApiClient;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use React\EventLoop\LoopInterface;

class WeatherApp implements MessageComponentInterface
{
    private const DEFAULT_LOCATION = 'Budapest';

    private ?\SplObjectStorage $clients = null;

    private ?LoopInterface $loop = null;

    private string $location;

    public function __construct(private readonly WeatherApiClient $weatherApiClient)
    {
        if (null === $this->clients) {
            $this->clients = new \SplObjectStorage();
        }

        $this->location = self::DEFAULT_LOCATION;
    }

    public function setLoop(LoopInterface $loop): void
    {
        $this->loop = $loop;
    }

    public function startPublishWeatherData(int $refreshInterval): void
    {
        $this->loop->addPeriodicTimer($refreshInterval, fn() => $this->refreshRealtimeData());
    }

    public function onOpen(ConnectionInterface $conn): void
    {
        $this->clients->attach($conn);

        $data = [
            'type' => 'connection-callback',
            'resourceId' => $conn->resourceId ?? null,

        ];

        $conn->send(json_encode($data));
    }

    public function onMessage(ConnectionInterface $from, $msg): void
    {
        try {
            $message = json_decode($msg, true, JSON_THROW_ON_ERROR);
            if (isset($message['location'])) {
                $this->location = (string)$message['location'];
                $this->refreshRealtimeData();
            }
        } catch (\JsonException $e) {
            ;
        }
    }

    public function onClose(ConnectionInterface $conn): void
    {
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e): void
    {
        $conn->close();
    }

    private function refreshRealtimeData(): void
    {
        if (empty($this->clients)) {
            return;
        }

        try {
            $data = $this->weatherApiClient->getCurrent($this->location);
            $this->sendToAllClients(['realtime' => $data]);
        } catch (\Throwable $e) {
            $this->sendToAllClients(['error' => [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]]);
        }
    }

    private function sendToAllClients(array $message): void
    {
        foreach ($this->clients as $client) {
            $client->send(json_encode($message));
        }
    }
}
