# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Yii2-based Telegram bot application for cryptocurrency trading signal processing. Receives trading signals, processes them via OpenAI, executes trades on Bybit exchange, and communicates through Telegram.

## Development Commands

```bash
# Start Docker environment (port 8000)
docker-compose up -d

# Database migrations
php yii migrate

# Setup RBAC
php yii rbac

# Code generation
php yii gii

# Run tests (Codeception)
./vendor/bin/codecept run
./vendor/bin/codecept run unit
./vendor/bin/codecept run unit path/to/TestCest.php
```

## Key Console Commands

```bash
php yii market/bybit/<action>   # Bybit market operations
php yii order/<action>          # Order management
php yii signal/<action>         # Trading signal processing
php yii telegram/<action>       # Telegram bot operations
php yii gpt/<action>            # GPT/AI integration
```

## Architecture

**Entry points:**
- Web: `web/index.php` → `config/web.php`
- CLI: `yii` → `config/console.php`
- Telegram webhooks: `/callback?id=<bot_id>` → `controllers/CallbackController.php`

**Key directories:**
- `commands/` — Console controllers (CLI commands)
- `controllers/` — Web controllers
- `models/` — Data models; `models/base/` contains auto-generated base classes (don't edit)
- `components/` — Reusable app components (`Bot.php`, `Formatter.php`, `AppBootstrap.php`)
- `helpers/` — Utility classes (`AIHelper.php`, `MarketHelper.php`, `TelegramHelper.php`)
- `telegram/` — Telegram bot logic, signal parsing, task manager
- `market/` — Exchange integration (`BybitMarket.php`, `Market.php`, order DTOs)
- `clients/bybit/` — Low-level Bybit API client
- `neuro/` — AI/ML components
- `config/` — All configuration files

**Configuration files:**
- `config/components.php` — URL manager, formatter (timezone: Europe/Moscow), auth, i18n (language: ru)
- `config/params.php` + `config/params-local.php` (gitignored) — API keys for Telegram, Bybit, OpenAI
- `config/db.php` — Database connection

## Tech Stack

- **Framework**: Yii2 ~2.0.14, PHP 8.2
- **Database**: MySQL with RBAC via DbManager
- **AI**: OpenAI PHP client v0.8.1
- **Exchange**: Bybit API (`market/BybitMarket.php`)
- **Telegram**: `carono/telegram-bot-components`
- **Testing**: Codeception (unit/functional/acceptance)
- **HTTP**: Guzzle 7.8, Symfony HTTP Client 7.0

## Important Patterns

- Models in `models/base/` are auto-generated — extend them in `models/` instead of editing directly
- Telegram signal flow: webhook → `CallbackController` → `telegram/crypto_signal/` → `helpers/MarketHelper.php` → `market/BybitMarket.php`
- Local config overrides live in `config/*-local.php` (gitignored) — copy from `config/*-local.php.example` if present
- RBAC permissions configured via `php yii rbac` command using `components/RbacController.php`
