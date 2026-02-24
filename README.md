# Jira CLI

A feature-rich Jira Cloud CLI built with [Laravel Zero](https://laravel-zero.com/) and PHP.

[![tests](https://github.com/jeffersongoncalves/jira-cli/actions/workflows/run-tests.yml/badge.svg)](https://github.com/jeffersongoncalves/jira-cli/actions/workflows/run-tests.yml)
[![PHPStan](https://github.com/jeffersongoncalves/jira-cli/actions/workflows/phpstan.yml/badge.svg)](https://github.com/jeffersongoncalves/jira-cli/actions/workflows/phpstan.yml)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

## Requirements

- PHP 8.2+
- Composer
- A Jira Cloud instance with API access

## Installation

```bash
# Clone the repository
git clone git@github.com:jeffersongoncalves/jira-cli.git
cd jira-cli

# Install dependencies
composer install

# Configure credentials
php jira auth:save
```

### Download PHAR (alternative)

Download the latest `jira.phar` from the [Releases](https://github.com/jeffersongoncalves/jira-cli/releases) page:

```bash
chmod +x jira.phar
mv jira.phar /usr/local/bin/jira
```

## Authentication

### Creating an API Token

1. Go to [https://id.atlassian.com/manage-profile/security/api-tokens](https://id.atlassian.com/manage-profile/security/api-tokens)
2. Click **Create API token**
3. Give it a descriptive label (e.g. "Jira CLI")
4. Copy the generated token

### Required OAuth / API Scopes

For OAuth 2.0 (3LO) or Forge apps, the following granular scopes are required for full CLI functionality:

#### Jira Platform Scopes

| Scope | Required For |
|-------|-------------|
| `read:issue:jira` | `issue:list`, `issue:view`, `issue:clone` |
| `write:issue:jira` | `issue:create`, `issue:edit`, `issue:assign`, `issue:clone` |
| `delete:issue:jira` | `issue:delete` |
| `read:issue.transition:jira` | `issue:move` (list available transitions) |
| `read:comment:jira` | `issue:view --comments` |
| `write:comment:jira` | `issue:comment` |
| `read:issue-worklog:jira` | `issue:worklog --list` |
| `write:issue-worklog:jira` | `issue:worklog` |
| `read:issue-link:jira` | `issue:view` (linked issues) |
| `write:issue-link:jira` | `issue:link` |
| `delete:issue-link:jira` | `issue:unlink` |
| `read:issue-link-type:jira` | `issue:link` (list link types) |
| `write:issue.watcher:jira` | `issue:watch` |
| `read:project:jira` | `project:list`, `release:list` |
| `read:project-version:jira` | `release:list` |
| `read:user:jira` | `issue:assign me`, `me` |

#### Jira Software (Agile) Scopes

| Scope | Required For |
|-------|-------------|
| `read:board-scope:jira-software` | `board:list` |
| `read:sprint:jira-software` | `sprint:list` |
| `write:sprint:jira-software` | `sprint:add`, `sprint:close` |
| `read:epic:jira-software` | `epic:list`, `epic:add`, `epic:remove` |
| `write:epic:jira-software` | `epic:add`, `epic:remove` |

> **Note:** API tokens (Basic auth) inherit all permissions from the Atlassian account that created them - no scope configuration is needed. The scopes above are only relevant for OAuth 2.0 and Forge app integrations.

### Saving Credentials

```bash
# Interactive setup
php jira auth:save

# You'll be prompted for:
# - Jira server URL (e.g. https://your-domain.atlassian.net)
# - Authentication type (Basic or Bearer/PAT)
# - Email address
# - API Token
# - Default project key (optional)
# - Default board ID (optional)

# View saved credentials
php jira auth:show
```

Credentials are stored in `~/.jira-cli/config.json` with restricted file permissions (0600).

## Commands

### Authentication

| Command | Description |
|---------|-------------|
| `auth:save` | Save Jira credentials interactively |
| `auth:show` | Display saved credentials (token masked) |

### Issues

| Command | Description |
|---------|-------------|
| `issue:list` | List/search issues with JQL filters |
| `issue:view <key>` | View issue details |
| `issue:create` | Create a new issue |
| `issue:edit <key>` | Edit issue fields |
| `issue:move <key>` | Transition issue status |
| `issue:assign <key> [user]` | Assign issue (use `me` for yourself) |
| `issue:delete <key>` | Delete an issue |
| `issue:comment <key>` | Add a comment |
| `issue:worklog <key>` | Log time or list worklogs |
| `issue:link <inward> <outward>` | Link two issues |
| `issue:unlink <linkId>` | Remove an issue link |
| `issue:clone <key>` | Duplicate an issue |
| `issue:watch <key>` | Watch an issue |

### Epics

| Command | Description |
|---------|-------------|
| `epic:list` | List epics in a project |
| `epic:create` | Create a new epic |
| `epic:add <epic> <issues...>` | Add issues to an epic |
| `epic:remove <issues...>` | Remove issues from their epic |

### Sprints

| Command | Description |
|---------|-------------|
| `sprint:list` | List sprints for a board |
| `sprint:add <sprint> <issues...>` | Add issues to a sprint |
| `sprint:close <sprint>` | Close/complete a sprint |

### Other

| Command | Description |
|---------|-------------|
| `board:list` | List boards |
| `project:list` | List accessible projects |
| `release:list` | List project versions/releases |
| `me` | Show current authenticated user |
| `open [key]` | Open issue/project in the browser |
| `serverinfo` | Show Jira server information |

## Usage Examples

```bash
# List issues assigned to me
php jira issue:list --assignee=me --project=PROJ

# Search with custom JQL
php jira issue:list --jql="project = PROJ AND status = 'In Progress' ORDER BY priority DESC"

# View issue with comments
php jira issue:view PROJ-123 --comments

# Create a bug
php jira issue:create --project=PROJ --type=Bug --summary="Login fails" --priority=High

# Move issue to Done
php jira issue:move PROJ-123 --status=Done

# Assign to me
php jira issue:assign PROJ-123 me

# Log 2 hours of work
php jira issue:worklog PROJ-123 --time=2h --comment="Fixed auth module"

# List active sprints
php jira sprint:list --board=1 --state=active

# Open issue in browser
php jira open PROJ-123
```

## APIs Used

This CLI interacts with two Jira APIs:

- **REST API v3** (`/rest/api/3/`) - Issues, projects, users, comments, worklogs
- **Agile API v1** (`/rest/agile/1.0/`) - Boards, sprints, epics

## Development

```bash
# Run tests
composer test:unit

# Run code style check
composer test:lint

# Fix code style
./vendor/bin/pint

# Run static analysis
composer test:types

# Build PHAR
composer build
```

## License

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.
