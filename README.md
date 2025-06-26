## Environment Variables

> Note: On the virtual machine, the application code lives in the `httpdocs/` folder.

We need a separate `env/` directory at the same level as `httpdocs/`, containing a file named `.env` with the following content:

```dotenv
TWILIO_ACCOUNT_SID=<Twilio Account SID>
TWILIO_AUTH_TOKEN=<Twilio Auth Token>
```

```
/var/www/vhosts/myphotocode.com/
├─ env/.env         # Contains the Twilio credentials
└─ httpdocs/        # Public webroot served by the web server
```

> **Important:** Keeping the `.env` file in `env/` (outside `httpdocs/`) ensures it cannot be accessed via HTTP.


### File Permissions

Ensure the `.env` file is owned by the web‑server group and readable only by owner and group:

```bash
# Replace USERNAME with the deploy user (e.g. 'amazing-leavitt_9lp2jtvmq4m')
sudo chown USERNAME:www-data /var/www/vhosts/myphotocode.com/env/.env
sudo chmod 640 /var/www/vhosts/myphotocode.com/env/.env
```

* `www-data` is Apache’s default user/group on Debian/Ubuntu. Adjust if your distro differs.
* `chmod 640` grants read/write to owner, read to group, none to others, so Apache can read but no one else can access it.
