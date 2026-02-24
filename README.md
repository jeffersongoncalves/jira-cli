# Jira CLI

A modern Jira Cloud CLI built with [Laravel Zero](https://laravel-zero.com/).

<p align="center">
  <a href="https://github.com/jeffersongoncalves/jira-cli/actions"><img src="https://github.com/jeffersongoncalves/jira-cli/actions/workflows/run-tests.yml/badge.svg" alt="Tests" /></a>
  <a href="https://packagist.org/packages/jeffersongoncalves/jira-cli"><img src="https://img.shields.io/packagist/dt/jeffersongoncalves/jira-cli" alt="Total Downloads" /></a>
  <a href="https://github.com/jeffersongoncalves/jira-cli/blob/main/LICENSE"><img src="https://img.shields.io/github/license/jeffersongoncalves/jira-cli" alt="License" /></a>
  <img src="https://img.shields.io/badge/php-%3E%3D8.2-8892BF" alt="PHP 8.2+" />
</p>

## Features

- **Issues** - Create, list, view, edit, move, assign, delete, comment, worklog, link, clone, and watch issues
- **Epics** - List, create, and manage epic associations
- **Sprints** - List, add issues, and close sprints
- **Boards** - List boards with project filtering
- **Projects** - List accessible projects
- **Releases** - List project versions and releases
- **Authentication** - Secure credential storage with API tokens (Basic and Bearer)
- **Browse** - Open issues and projects in the browser from the terminal

## Requirements

- PHP 8.2+

## Installation

```bash
composer global require jeffersongoncalves/jira-cli
```

Or clone and build locally:

```bash
git clone https://github.com/jeffersongoncalves/jira-cli.git
cd jira-cli
composer install
php jira app:build jira
```

## Getting Started

### 1. Create a Jira API Token

1. Go to [https://id.atlassian.com/manage-profile/security/api-tokens](https://id.atlassian.com/manage-profile/security/api-tokens)
2. Click **Create API token**
3. Give it a descriptive label (e.g. `jira-cli`)
4. Click **Create** and **copy it immediately** - it will only be shown once

The API token inherits all permissions from your Atlassian account. For OAuth 2.0 / Forge apps, the following granular scopes are required:

> **Note:** API tokens (Basic auth) inherit all permissions from the account - no scope configuration needed. Scopes below are only for OAuth 2.0 and Forge integrations.

**Jira Platform Scopes:**

| Scope | Permission | Required for |
|-------|-----------|-------------|
| `read:issue:jira` | Read | List, view, and clone issues |
| `write:issue:jira` | Write | Create, edit, assign, and clone issues |
| `delete:issue:jira` | Delete | Delete issues |
| `read:issue.transition:jira` | Read | List available status transitions |
| `read:comment:jira` | Read | View issue comments |
| `write:comment:jira` | Write | Add comments to issues |
| `read:issue-worklog:jira` | Read | List worklogs |
| `write:issue-worklog:jira` | Write | Log time on issues |
| `read:issue-link:jira` | Read | View linked issues |
| `write:issue-link:jira` | Write | Link issues together |
| `delete:issue-link:jira` | Delete | Remove issue links |
| `read:issue-link-type:jira` | Read | List link types |
| `write:issue.watcher:jira` | Write | Watch issues |
| `read:project:jira` | Read | List projects |
| `read:project-version:jira` | Read | List releases/versions |
| `read:user:jira` | Read | User info and assignment |

**Jira Software (Agile) Scopes:**

| Scope | Permission | Required for |
|-------|-----------|-------------|
| `read:board-scope:jira-software` | Read | List boards |
| `read:sprint:jira-software` | Read | List sprints |
| `write:sprint:jira-software` | Write | Add issues to sprint, close sprint |
| `read:epic:jira-software` | Read | List epics |
| `write:epic:jira-software` | Write | Add/remove issues from epics |

### 2. Save your credentials

```bash
jira auth:save
```

You will be prompted for your Jira server URL, Atlassian account email, and API token.

### 3. Verify authentication

```bash
jira auth:show
```

### 4. Start using commands

```bash
jira issue:list --project=PROJ
jira issue:view PROJ-123
jira me
```

## Commands

### Authentication

| Command | Description |
|---------|-------------|
| `auth:save` | Save Jira credentials (server, email, API token) |
| `auth:show` | Display saved credentials |

### Issues

| Command | Description |
|---------|-------------|
| `issue:list` | List/search issues with JQL filters (`--project`, `--type`, `--status`, `--assignee`) |
| `issue:view <key>` | View issue details (`--comments` to include comments) |
| `issue:create` | Create a new issue (interactive or via options) |
| `issue:edit <key>` | Edit issue fields (`--summary`, `--priority`, `--assignee`) |
| `issue:move <key>` | Transition issue status (`--status` or interactive) |
| `issue:assign <key> [user]` | Assign issue (`me` for yourself, empty to unassign) |
| `issue:delete <key>` | Delete an issue (`--force` to skip confirmation) |
| `issue:comment <key>` | Add a comment (`--body` or interactive) |
| `issue:worklog <key>` | Log time (`--time=2h`) or list worklogs (`--list`) |
| `issue:link <inward> <outward>` | Link two issues (`--type` or interactive) |
| `issue:unlink <linkId>` | Remove an issue link |
| `issue:clone <key>` | Duplicate an issue |
| `issue:watch <key>` | Watch an issue |

### Epics

| Command | Description |
|---------|-------------|
| `epic:list` | List epics (`--project=KEY`) |
| `epic:create` | Create a new epic (interactive or via options) |
| `epic:add <epic> <issues...>` | Add issues to an epic |
| `epic:remove <issues...>` | Remove issues from their epic |

### Sprints

| Command | Description |
|---------|-------------|
| `sprint:list` | List sprints (`--board=ID`, `--state=active\|closed\|future`) |
| `sprint:add <sprint> <issues...>` | Add issues to a sprint |
| `sprint:close <sprint>` | Close/complete a sprint (`--force` to skip confirmation) |

### Boards, Projects & Releases

| Command | Description |
|---------|-------------|
| `board:list` | List boards (`--project` to filter) |
| `project:list` | List accessible projects |
| `release:list` | List project versions (`--project=KEY`) |

### Utilities

| Command | Description |
|---------|-------------|
| `me` | Show current authenticated user |
| `open [key]` | Open issue or project in the browser |
| `serverinfo` | Show Jira server information |

## Development

```bash
# Install dependencies
composer install

# Run tests
composer test

# Run tests only
composer test:unit

# Code formatting
./vendor/bin/pint

# Static analysis
composer test:types
```

## License

Jira CLI is open-source software licensed under the [MIT license](LICENSE).
