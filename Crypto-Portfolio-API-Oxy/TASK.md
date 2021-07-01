# Crypto Portfolio API
### Create HTTP API in which you can manage your assets and see their values

Create two entities in this API: **User** and **Asset**:
* **User** can CRUD his **Assets**.
    * Straightforward CRUD is enough.
* **Asset**
    * Has label e.g 'binance'.
    * Currencies available: `BTC, ETH, IOTA`.
    * Value cannot be negative.
    * Can have same currency multiple times. e.g (1BTC 'usb stick', 1BTC 'binance').
* Get value of his **Assets** in **USD** (both total and separately).
    * Get exchange rate from external API.
* For simplicity's sake there is no need to create CRUD for **User**, just have a fixture ready.

## Requirements
* Use Version Control System (preferably `git`).
    * If you put the repository online (GitHub, GitLab, etc.), keep it private.
* Use popular PHP framework (SlimPHP, Symfony, Lumen, Laravel, Zend or any other).
* Implement external API calling by yourself (do not use any crypto API wrappers).

## Tips
* Imagine it as **production ready** code and many users could be using it at the same time.
* Design code as beautifully as you can.
* Tests are welcome.
* VCS skills are also tested.
* Crypto APIs: https://github.com/toddmotto/public-apis#cryptocurrency.

## FAQ

#### Can't understand purpose of this API
Imagine this as "Blockfolio" simplified Api. Basically you can add your assets quite anonymously without giving addresses of your assets. and you can easily check their value every time you want.

#### Do Users have to have authentication
Yes they do. Just have them in database preloaded.
