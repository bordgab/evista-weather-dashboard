<?php

namespace App\Command;

use App\Websocket\WebsockerServer;
use App\Websocket\WeatherApp;
use React\EventLoop\Loop;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'weather:server:run')]
class WeatherServerCommand extends Command
{
    public function __construct(
        private readonly WeatherApp $weatherApp,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Run weather websocket server')
            ->addOption(
                'refresh',
                'r',
                InputOption::VALUE_OPTIONAL,
                'Weather data refresh period in seconds (default: 60)',
                60
            )
            ->addOption(
                'port',
                'p',
                InputOption::VALUE_OPTIONAL,
                'The port to server sockets on (default: 4000)',
                4000
            )
            ->addOption(
                'address',
                'a',
                InputOption::VALUE_OPTIONAL,
                'The address to receive sockets on (0.0.0.0 means receive connections from any',
                '0.0.0.0'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $loop = Loop::get();

            $this->weatherApp->setLoop($loop);

            $server = WebsockerServer::create(
                $this->weatherApp,
                $loop,
                $input->getOption('port'),
                $input->getOption('address')
            );

            $output->writeLn(sprintf(
                '🚀 <info>Weather websocket server ws://%s:%d is running... press ctrl-c to stop.</info>',
                $input->getOption('address'),
                $input->getOption('port')
            ));

            $this->weatherApp->startPublishWeatherData($input->getOption('refresh'));

            $server->run();

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln(sprintf('<error>Failed to run the server: %s</error>', $e->getMessage()));

            return Command::FAILURE;
        }
    }
}