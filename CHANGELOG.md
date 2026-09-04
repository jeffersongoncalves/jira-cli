# Changelog

All notable changes to this project will be documented in this file.

## [1.0.8] - 2026-09-04

### Bug Fixes

- **deps:** Update guzzlehttp/guzzle to patch security advisories

### CI/CD

- Pin actions to commit SHA, add dependabot cooldown/composer, trim dist archive
- **release:** Generate CHANGELOG.md and release notes with git-cliff

### Features

- **auth:** Add non-interactive options to auth:save

### Other

- Bump shivammathur/setup-php

Bumps [shivammathur/setup-php](https://github.com/shivammathur/setup-php) from b604ade2a87db23f8871b7182e69ec5e75effb45 to f3e473d116dcccaddc5834248c87452386958240.
- [Release notes](https://github.com/shivammathur/setup-php/releases)
- [Commits](https://github.com/shivammathur/setup-php/compare/b604ade2a87db23f8871b7182e69ec5e75effb45...f3e473d116dcccaddc5834248c87452386958240)

---
updated-dependencies:
- dependency-name: shivammathur/setup-php
  dependency-version: f3e473d116dcccaddc5834248c87452386958240
  dependency-type: direct:production
...

Signed-off-by: dependabot[bot] <support@github.com>

## [1.0.7] - 2026-07-24

### Bug Fixes

- Mock IssueService in CommentCommand validation tests

### Miscellaneous Tasks

- Bump guzzlehttp/guzzle and guzzlehttp/psr7 for security advisories

## [1.0.6] - 2026-07-24

### CI/CD

- Replace split build/changelog/publish-phar workflows with a single release job

## [1.0.5] - 2026-07-22

### Features

- Rich comments with markdown and idempotent upsert by marker

## [1.0.4] - 2026-06-23

### Features

- Adicionar comando self-update

### Refactor

- Consume shared laravel-zero-* packages

## [1.0.3] - 2026-06-06

### Bug Fixes

- **build:** Keep dev dependencies in the PHAR

### Miscellaneous Tasks

- Bump version to v1.0.3

## [1.0.2] - 2026-06-06

### CI/CD

- **build:** Serialize builds with a concurrency group to avoid ref-lock race
- **release:** Use version.txt as the single source of truth for the version

### Miscellaneous Tasks

- Refresh portfolio banner
- Bump version to v1.0.2

### Other

- Add project banner and update README

Co-Authored-By: Claude Opus 4.6 <noreply@anthropic.com>
- Add build badge and changelog section to README

Co-Authored-By: Claude Opus 4.6 <noreply@anthropic.com>
- Standardize .gitignore: add .claude/settings.local.json, .phpunit.cache, .env
- Chain builds after Update Changelog + fix release-tagged rebuild

On the release path, three workflows fan out in parallel: publish-phar,
Update Changelog, and builds. Update Changelog force-pushes CHANGELOG
and version.txt, which raced with builds and caused non-fast-forward
rejections. Worse, the tag created by the release stayed on the commit
that existed before the PHAR was rebuilt, so `composer require` would
pull a PHAR with the previous version baked in.

This rewires build.yml to:

- Run via workflow_run after Update Changelog completes successfully,
  eliminating the race. Regular push on main still triggers.
- Pin ref and commit branch to main on workflow_run invocations
  (github.event.workflow_run.head_branch resolves to the tag name for
  release events and would land the commit on a detached HEAD / fail
  to push).
- Resolve the build version from workflow_run.head_branch when running
  under workflow_run. `git describe --tags --abbrev=0` is unreliable
  once the pre-release tag and current release tag share a commit.
- After the rebuild commit lands, move the release tag to that commit
  so Packagist (and direct git installs) serve the PHAR whose embedded
  version matches the tag.

Validated end-to-end in the git-worktree-cli sibling repo.

Co-Authored-By: Claude Opus 4.6 (1M context) <noreply@anthropic.com>
- Delete .github/workflows/dependabot-auto-merge.yml

## [1.0.1] - 2026-02-24

### Other

- Update README to match bb-cli format with composer global install and granular scopes

Co-Authored-By: Claude Opus 4.6 <noreply@anthropic.com>

## [1.0.0] - 2026-02-24

### Other

- Initial implementation of Jira CLI with Laravel Zero

Full-featured Jira Cloud CLI with 28 commands covering issues, epics, sprints,
boards, projects, releases, authentication, and browser integration.

- Auth: save/show credentials with Basic and Bearer support
- Issues: list, view, create, edit, move, assign, delete, comment, worklog, link, unlink, clone, watch
- Epics: list, create, add/remove issues
- Sprints: list, add issues, close
- Boards, Projects, Releases: list commands
- Utilities: me, open (browser), serverinfo
- 58 Pest tests with 168 assertions
- PHPStan level 6, Laravel Pint formatting
- GitHub Actions: tests, build, PHPStan, Pint, publish PHAR, changelog

Co-Authored-By: Claude Opus 4.6 <noreply@anthropic.com>


