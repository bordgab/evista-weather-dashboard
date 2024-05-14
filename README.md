# Evista weather dashboard app

## Server
- PHP 8.4
- Symfony 6
- Weather api: [WeatherAPI.com](https://www.weatherapi.com/)
- Tool to test websocket communication: [WebSocket King client](https://websocketking.com/)

- To change location of weather data send `{"location":"<new location>"}` message to socket server. Default location is "Budapest".

### Installing and running the application
To start (and build, if necessary) docker containers, run the following command: 
```bash
docker compose up -d
```

Install dependencies:
```bash
docker exec evista-php composer install
```

Set `SYMFONY_DECRYPTION_SECRET` environment variable in `.env` file:
```bash
SYMFONY_DECRYPTION_SECRET=<secret key>
```

Decrypt WEATHER_API_KEY to environment variable:
```bash
docker exec evista-php bin/console secrets:decrypt-to-local --force
```

Staring the application (websocket server) (the `-r` option is the realtime weather data refresh interval in seconds):
  ```bash
  docker exec evista-php bin/console weather:server:run -r 60
  ```

## Frontend (dashboard)
- Not implemented yet