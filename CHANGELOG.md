# Changelog

All notable changes to `jira-cli` will be documented in this file.

## v1.0.6 - 2026-07-24

Release v1.0.6

## v1.0.5 - 2026-07-22

### What's Changed

* Bump actions/cache from 5 to 6 by @dependabot[bot] in https://github.com/jeffersongoncalves/jira-cli/pull/5

**Full Changelog**: https://github.com/jeffersongoncalves/jira-cli/compare/v1.0.4...v1.0.5

## v1.0.4 - 2026-06-23

Add the `self-update` command — update the jira CLI to the latest release directly from the terminal.

## v1.0.3 - 2026-06-06

Fix PHAR packaging: keep dev dependencies (laravel-zero/framework) in the binary. Prior releases failed at boot with "Class LaravelZero\Framework\Application not found".

## v1.0.2 - 2026-06-06

Adopt version.txt release flow (version.txt as version source, no tag-move; concurrency on builds).

## v1.0.1 - 2026-02-24

### What's Changed

- Updated README to match bb-cli format with composer global install instructions
- Added granular Atlassian API scopes documentation (Jira Platform + Jira Software)
- Generated build artifact

**Full Changelog**: https://github.com/jeffersongoncalves/jira-cli/compare/v1.0.0...v1.0.1

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
