# Keep-explorer version 2 with Codeigniter 3


## Features
1. Dashboard - common information and some useful charts by dates;
2. Deposit history - list of deposits with a useful filter by the "current state", "lot size", "deposit address","operator address", "сollateralization less than (%)", "keep contract address", "bitcoin address", "bitcoin transaction hash" fields;
3. Detail page of the selected deposit - common deposit information;
4. Subscribe for new events of the selected deposit;
5. Grants page - Summary chart with a list of all grants;
6. Operators page - The list of all operators with filters by "Operator address", "Deposits state", "Deposit contract", "Has collateralization less than (%)";
7. Operator detail page - The detail page has common information, a list of the securing deposits, and the ability to subscribe for events;
8. KEEP transfers page - A list of all KEEP token transfers with filter;
6. tBTC mints history;
7. Transfers history.

## Installation
- git clone or donwload the zip file;
- Extract zip file;
- Upload db.sql on phpmyadmin;
- Configure the confg/database.php file;
- Configure the base_url on confg/confg.php;
- Configure NodeJS params /cron/config/.
