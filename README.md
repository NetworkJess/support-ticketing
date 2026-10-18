# Support Ticketing System (simple version)

PHP + MySQL helpdesk ticketing system for the Web & Cloud Applications assignment.

## Run

```bash
docker compose up --build
```

- App: <http://localhost:8000>
- phpMyAdmin: <http://localhost:8080> (root / root)

First time: open <http://localhost:8000/setup.php> to create the tables and
seed data (safe to re-run).

### Database login details

The database defaults to the values below. To change them, copy these into
a local `.env` file (already gitignored) and run
`docker compose up -d` again:

```env
MYSQL_DATABASE=Ticketing
MYSQL_USER=Jess
MYSQL_PASSWORD=admin
MYSQL_ROOT_PASSWORD=root
```

## Demo accounts

All passwords are `password123`.

| Role  | Username | Full name        | Support level |
|-------|----------|------------------|---------------|
| Agent | alice    | Alice Manager    | 1 (senior)    |
| Agent | bob      | Bob Technician   | 2 (junior)    |
| User  | carol    | Carol User       | —             |
| User  | dave     | Dave User        | —             |

## How it fits the assignment

- **User area**: register, login/logout, open a ticket (status *Open*),
  My Tickets (statuses open/resolved/closed), close a resolved ticket.
- **Support area**: agent login/logout, search open tickets (by subject),
  resolve a ticket (adds resolution notes), elevate a ticket to a
  higher support level (status stays *Open*), and browse full ticket
  history (open / resolved / closed).
- **Status badges** — every ticket list and detail page shows the status
  colour-coded (amber open, green resolved, grey closed) so it is obvious
  at a glance.
- **Resolved-by attribution** — when an agent resolves a ticket, their
  name is recorded and shown on the ticket detail page (both user and
  agent views).
- **Client-side (JavaScript)**: a shared `js/validate.js` runs on every
  form — it checks that all `required` fields are filled in before the
  page submits, shows a friendly message listing what's missing, and puts
  the cursor on the first empty field. The server still re-checks
  everything; this is just a nicer experience.
- **Server-side (PHP)**: all pages process and validate their own data
  again before touching the database.
- **Visual theme**: dark terminal-style look (monospace, cyan accents,
  magenta buttons) — the single site stylesheet is `html/style.css`.
- Plaintext passwords are stored for simplicity — acceptable for this
  project; note it as an assumption in the report.