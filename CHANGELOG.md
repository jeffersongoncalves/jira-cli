# Changelog

All notable changes to `jira-cli` will be documented in this file.

## v1.0.0 - 2026-02-23

### Initial Release

Full-featured Jira Cloud CLI built with Laravel Zero.

#### Features

- **Authentication**: Basic (email + API token) and Bearer (PAT) support
- **Issues** (13 commands): list, view, create, edit, move, assign, delete, comment, worklog, link, unlink, clone, watch
- **Epics** (4 commands): list, create, add/remove issues
- **Sprints** (3 commands): list, add issues, close
- **Boards**: list boards with project filtering
- **Projects**: list accessible projects
- **Releases**: list project versions
- **Utilities**: current user info, open in browser, server info

#### Technical

- PHP 8.2+ with Laravel Zero 12
- REST API v3 + Agile API v1
- 58 Pest tests with 168 assertions
- PHPStan level 6 static analysis
- Laravel Pint code formatting
- GitHub Actions CI/CD (tests, build, PHAR publish)

## Unreleased
