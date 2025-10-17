CREATE DATABASE webtv_player;
CREATE USER 'webtv_user'@'localhost' IDENTIFIED BY 'webtv_password';
GRANT ALL PRIVILEGES ON webtv_player.* TO 'webtv_user'@'localhost';
FLUSH PRIVILEGES;