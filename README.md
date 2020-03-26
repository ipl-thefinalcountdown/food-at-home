# Docker PHP Development Environment

This is a docker-compose environment to run PHP webapps with MySQL/MariaDB databases.

## Usage

### Run

```bash
make up
```

If you want to run the containers in background, use `start` target:
```bash
make start
```

### Stop

```bash
make down
```

or even:
```bash
make stop
```

## Configurations

The web service exports `8080` port and `10000` for database management service.

### Database
- **Root Password:** mardb
- **Default Database:** sql_db1
- **Default User:** db_user
- **User password:** db_user

Use `db.docker.local` host to connect to the database.


## How to contribute
Check out our [wiki](https://wiki.aurorafoss.org/).

## License
MIT License

---
Made with ❤ by a bunch of geeks

[![License](https://img.shields.io/badge/license-MIT-lightgrey.svg)](https://opensource.org/licenses/MIT) [![Discord Server](https://discordapp.com/api/guilds/350229534832066572/embed.png)](https://discord.gg/Tsb2gpk)
