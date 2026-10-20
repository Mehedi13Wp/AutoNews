# AutoNews

A responsive Bangla + English news website and editorial dashboard built with Laravel 12, Livewire, Tailwind CSS and Vite. Bangla is the default; the top-right language control switches the site and dashboard to English.

## What works

- Responsive homepage, categories, search, article pages, social sharing, sitemap and article SEO metadata.
- Admin-only login with CSRF protection, rate limiting and hashed passwords; no public account registration.
- Manual bilingual stories, image upload or URL, image credit, draft/published/scheduled status, featured stories and editable SEO.
- Categories, HTTPS RSS/Atom sources, comma-separated topic/person keywords, per-source intervals and limits, draft or automatic publication, and import history.
- Queue jobs fetch feed text, request original bilingual articles from an AI provider, preserve source attribution and avoid repeated source URLs or exact normalized headlines.
- Optional remote images are included only when the administrator confirms reuse permission.

The initial articles are clearly marked **demonstration content**, not real reporting. Illustrations are original local SVG assets. You can edit or delete demo stories in the dashboard. No admin password is embedded in the repository or seeders.

## Local setup — Windows, macOS or Linux

Install **PHP 8.4+**, Composer 2, and Node.js 22+ with npm. On Windows, Laravel Herd is a convenient PHP/Composer installation. Required PHP extensions include ctype, curl, dom, fileinfo, filter, mbstring, openssl, PDO, pdo_sqlite, session, SimpleXML, tokenizer, xml and zip. The default database is SQLite, so MySQL installation is optional.

Open a terminal inside the project folder:

```sh
composer run setup
php artisan news:admin freelancermehedi400@gmail.com
```

The admin command prompts for a hidden password. Use your chosen local-test password; it is hashed in the database. This command can also reset the password later. It does not expose a public registration route.

`composer run setup` installs locked dependencies, creates `.env` and an application key if missing, prepares SQLite, runs migrations, seeds demo stories without duplicating existing demos, links uploads and builds frontend assets. It does not overwrite an existing `.env` or regenerate an existing key.

Start the website:

```sh
php artisan serve --host=0.0.0.0 --port=8000
```

On your computer, visit `http://127.0.0.1:8000`. The dashboard is at `/admin`. **A server started in Codex is not automatically running on your own computer.** Download or clone this project and run the commands locally first.

### Test on your phone

1. Connect the phone and computer to the same trusted Wi-Fi.
2. Run `npm run build`; use compiled assets instead of a running Vite dev server for this test.
3. Run the Laravel command above on your computer.
4. Find your computer's Wi-Fi IPv4 address (`ipconfig` on Windows, or your OS network settings).
5. If it is `192.168.1.5`, open `http://192.168.1.5:8000` on the phone; add `/admin` for the dashboard.
6. Allow port 8000 on the computer's private-network firewall if necessary. The computer must stay on. Some guest Wi-Fi networks isolate devices.

For frontend development on the computer, `composer run dev` starts Laravel, Vite, the queue worker and scheduler together. Stop it before doing the compiled-assets phone test above.

## Automatic news

In `/admin`, open **Sources & automation**. Add the publisher's public **HTTPS RSS or Atom feed URL**, not its homepage, choose a category, and optionally enter keywords such as `শেখ হাসিনা, Sheikh Hasina, Shakib Al Hasan`. An empty keyword list accepts every feed item. The latest dated items are considered first; items older than seven days are skipped when the feed supplies a parseable date.

Add your own API key securely to your local `.env` (never commit it):

```dotenv
NEWS_AI_API_KEY=
NEWS_AI_MODEL=gpt-4o-mini
```

The integration uses OpenAI's chat completions API. API usage is billed by the provider. The key stays on the server. No key is required for manual posting, managing sources, or the automated test suite.

Run these in separate terminals while the app is running:

```sh
php artisan queue:work --timeout=1100 --tries=1
php artisan schedule:work
```

The scheduler checks due sources every five minutes and publishes scheduled manual stories every minute. Each source has its own enabled switch, interval, import limit, image-permission switch and automatic-publish switch. With automatic publishing off, generated posts remain drafts for review. **Start with drafts and review factual accuracy before enabling automatic publication.**

Use **Import now** to enqueue a source manually, including a paused source. Status and errors appear in the import history; refresh that page after the worker finishes. To trigger all due enabled sources manually:

```sh
php artisan news:collect
```

### Import limits

- This version supports RSS/Atom feeds. It does not scrape arbitrary website homepages or bypass publisher access restrictions. Feed items need sufficient article text; summary-only feeds may be skipped. Use licensed full-content feeds/APIs for reliable complete reporting.
- Similar events with different headlines/URLs can still produce duplicates. Current duplicate detection matches canonicalized source URLs and normalized exact headlines, not semantic similarity.
- An AI rewrite does not guarantee factual accuracy, legal reuse rights, SEO ranking or editorial originality. Image permission must be confirmed separately. Attribution is preserved.
- A missing API key, quota/network failure or invalid feed is reported; the app does not silently substitute demo text for a generated article.
- Live imports require access to the chosen publisher's hostname and `api.openai.com`. In cloud environment settings, add only the hosts you actually use and supply keys securely.

## Tests and build

```sh
php artisan test
npm run build
```

The feature suite covers authentication/authorization, bilingual public visibility, upload and URL images, post lifecycle, scheduled publishing, category validation, RSS/Atom parsing, private-network URL rejection, mocked AI generation, duplicates, queues and source intervals. Tests use an isolated in-memory SQLite database and fake API responses; they never consume your AI quota.

## Optional MySQL

Create a database, change `DB_CONNECTION=mysql` and the usual `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` settings in `.env`, enable `pdo_mysql`, then run migrations, seeders and the admin command. Existing SQLite data is not automatically migrated. The current instance and automated tests are validated on SQLite.

## Before going public

Set a strong admin password, `APP_ENV=production`, `APP_DEBUG=false`, HTTPS `APP_URL`, persistent database/uploads, a queue worker and the scheduler. Configure the web server document root to `public/`. Hosting and deployment are separate from local testing.

## Git updates on Windows

The main branch includes compiled frontend assets so local users can update without running Node/npm. Developers changing CSS/JS must run `npm run build` and commit the source plus the updated `public/build` assets together.

If you already use a Git clone, run `update.bat` from the project directory. It checks for local edits, pulls with `--ff-only`, installs locked PHP dependencies, makes a consistent SQLite backup in ignored `.autonews-backups/`, runs migrations and clears caches. It preserves `.env`, posts, passwords and uploaded images. Non-SQLite databases need their own backup/migration workflow; the automatic updater stops rather than migrating them without a backup.

To connect an existing ZIP installation, run `bin/connect-github.php` from inside that project folder. The connector clones the repository, backs up the source files it will replace outside the project directory, copies the new source and attaches the clone's Git metadata. It never copies repository credentials or runtime databases, and refuses existing Git metadata and symlink conflicts. For a first connection, download that script from this repository's `main` branch and run it using your installed PHP. Afterward, run `update.bat` for updates.
