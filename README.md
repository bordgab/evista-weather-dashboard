# Evista weather dashboard app

## Server
- PHP 8.4
- Symfony 6
- Weather api: [WeatherAPI.com](https://www.weatherapi.com/)
- Tool to test websocket communication: [WebSocket King client](https://websocketking.com/)

- To change location of weather data send `{"location":"<new location>"}` message to socket server. Default location is "Budapest".

### Docker containers
- evista-php
  - Staring websocket server (the `-r` option is the realtime data refresh interval in seconds):
  ```bash
  bin/console weather:server:run -r 60
  ```
  

- evista-nginx
  - Not using in current project status.
## Frontend (dashboard)
- Not implemented yet