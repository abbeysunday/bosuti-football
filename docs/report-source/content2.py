# -*- coding: utf-8 -*-
"""Chapters Three to Five, references and appendices."""
import os

SHOTS = os.path.dirname(os.path.abspath(__file__))
D = lambda *p: os.path.join(SHOTS, *p)


def build(g):
    chapter_three(g)
    chapter_four(g)
    chapter_five(g)
    references(g)
    appendices(g)


# =========================================================================== CHAPTER THREE
def chapter_three(g):
    P, bullets, numbered, h2, h3, chapter, table, figure = g["P"], g["bullets"], g["numbered"], g["h2"], g["h3"], g["chapter"], g["table"], g["figure"]
    inch = g["inch"]

    chapter("THREE", "SYSTEM ANALYSIS AND DESIGN")
    h2("3.1 Introduction")
    P("This chapter presents the methodology used, an analysis of the existing system, the requirements of the proposed system and its design: the system architecture, use cases, data flow, database design, the key algorithms and the input, output and security design.")

    h2("3.2 Research and Development Methodology")
    P("Requirements were gathered through observation of how internal competition information is currently shared, informal discussion with students involved in the clubs, and a review of the information presented by established football competition websites. The system was built using an <b>iterative and incremental</b> development methodology, in which the product was delivered in increments that were each designed, implemented, tested and reviewed before the next began:")
    table("3.1: Development increments", ["Increment", "Deliverables"], [
        ["1. User interface", "Responsive public website design migrated into Laravel Blade layouts and components; UI/UX and accessibility refinement."],
        ["2. Data model", "Relational schema (migrations), Eloquent models, relationships, scopes and seeders for seasons, competitions and teams."],
        ["3. Services", "League table computation, team and player statistics."],
        ["4. Administration", "Role-protected admin panel with CRUD for all entities, result entry, match events and line-ups."],
        ["5. Dynamic public site", "Public pages connected to the database; trials and contact forms; empty states; pagination."],
        ["6. Supporting features", "Rich-text news editor with HTML sanitisation, image optimisation, CSV import, sample-season generator."],
        ["7. Sports portal", "Rebrand to BOUESTI Sports; Sports menu with Football and Basketball; basketball page; homepage sports and facilities sections using real photographs of the university's pitch and courts, served in responsive sizes."],
        ["Continuous", "Automated tests written alongside each increment and re-run after every change."],
    ], [1.6 * inch, 4.4 * inch])

    h2("3.3 Analysis of the Existing System")
    P("In the existing arrangement, fixtures are agreed among organisers and announced on notice boards or in messaging groups; results are reported verbally or by message after each match; any league table is computed by hand; and records of scorers and cards, if kept at all, are held privately by individual organisers. Students who wish to join a team approach team officials informally.")
    h3("3.3.1 Weaknesses of the existing system")
    bullets([
        "No central, public and permanent record of fixtures, results or standings.",
        "Manual table computation is slow and error-prone, and corrections are hard to publicise.",
        "Individual statistics (goals, assists, cards, appearances) are not produced.",
        "Historical data is lost between sessions.",
        "No formal channel for trial applications or enquiries.",
        "Student personal data may be circulated without access control.",
    ])

    h2("3.4 The Proposed System")
    P("The proposed system, branded <b>BOUESTI Sports</b>, is a web-based portal with two interfaces sharing one database: a <b>public website</b> for students and supporters, and an <b>administration panel</b> for authorised officials. Officials record each fact once — a player, a fixture, a result, a goal — and the system derives everything else: standings, team and player statistics, the homepage's next match and recent results, and the match centre. The public website is organised by sport: a Sports menu leads to Football — the fully managed competition with teams, squads, fixtures, results and standings — and to Basketball, which presents the programme, the university's courts and how students can get involved. The advantages over the existing system are accuracy, availability, preservation of history, recognition of players, visibility of every sport and facility, and privacy through role-based access control.")
    h3("3.4.1 Justification of the chosen technologies")
    table("3.2: Technologies selected and justification", ["Layer", "Technology", "Justification"], [
        ["Server language", "PHP 8.2", "Mature, widely hosted (including low-cost shared hosting), fast, and taught in Nigerian computing curricula."],
        ["Framework", "Laravel 12", "MVC structure, Eloquent ORM, migrations, validation, authentication, CSRF protection and testing tools out of the box."],
        ["Database", "MariaDB 10.4 / MySQL 8 (SQLite for tests)", "Reliable open-source RDBMS with foreign-key support; bundled with XAMPP for local development."],
        ["Public UI", "Blade, custom CSS, vanilla JavaScript", "Preserves the bespoke BOUESTI design; lightweight for mobile data connections."],
        ["Admin UI", "Tailwind CSS 3, Alpine.js 3, Vite 7", "Utility-first styling and small reactive components; production asset bundling."],
        ["Authentication", "Laravel Breeze", "Secure login, registration, password reset and email verification scaffolding."],
        ["Rich text", "Trix editor + Symfony HtmlSanitizer", "Simple editor for officials; server-side allow-list sanitisation prevents XSS."],
        ["Images", "PHP GD extension", "Resizing and WebP conversion without additional dependencies."],
        ["Testing", "Pest 3 (on PHPUnit)", "Readable automated feature tests against an in-memory database."],
    ], [1.15 * inch, 1.7 * inch, 3.15 * inch])

    h2("3.5 Requirements Specification")
    h3("3.5.1 Functional requirements")
    table("3.3: Functional requirements", ["ID", "Requirement"], [
        ["FR1", "The system shall display upcoming fixtures and results, filterable by competition, team and month."],
        ["FR2", "The system shall compute and display the league table from completed fixtures (3 points for a win, 1 for a draw; ties broken by goal difference then goals scored)."],
        ["FR3", "The system shall display teams, squads grouped by position, and player profiles with appearances, goals, assists, cards and (for goalkeepers) clean sheets."],
        ["FR4", "The system shall provide a match centre with score, status, minute-by-minute events, line-ups, match summary and related photos."],
        ["FR5", "The system shall publish news articles, a photo gallery with categories and a video list."],
        ["FR6", "The system shall accept trial applications and contact messages with validation and spam protection."],
        ["FR7", "Only authenticated administrators shall access the administration panel."],
        ["FR8", "Administrators shall create, update and delete seasons, competitions, teams, players, fixtures, news, gallery items, videos and staff."],
        ["FR9", "Administrators shall enter results and record match events and line-ups; only players of the teams in the fixture may be selected."],
        ["FR10", "Administrators shall review trial applications and contact messages and set their status."],
        ["FR11", "The system shall import teams, players, staff, fixtures and events from CSV files, rejecting the whole import if any row is invalid."],
        ["FR12", "The system shall resize and convert uploaded images and remove their metadata."],
        ["FR13", "The system shall present the university's sports through a Sports menu, with a Football section (teams, squads, fixtures, results and standings) and a Basketball section (programme, courts and how to get involved), and shall showcase the university's sports facilities."],
    ], [0.6 * inch, 5.4 * inch])
    h3("3.5.2 Non-functional requirements")
    table("3.4: Non-functional requirements", ["ID", "Category", "Requirement"], [
        ["NFR1", "Usability", "Pages shall be usable on screens from 320 px to 1440 px wide without horizontal scrolling; controls shall be at least 44 px high on touch screens."],
        ["NFR2", "Accessibility", "The interface shall follow WCAG 2.1 principles: keyboard access, visible focus, text alternatives, semantic landmarks and adequate contrast."],
        ["NFR3", "Security", "The system shall enforce role-based access, CSRF protection, validation of all input, escaping of output and sanitisation of rich text."],
        ["NFR4", "Privacy", "Matriculation numbers, state of origin and applicants' contact details shall never be shown on public pages."],
        ["NFR5", "Integrity", "Deleting teams, players, news or staff shall not destroy historical match records (soft deletes and restrictive foreign keys)."],
        ["NFR6", "Performance", "Statistics shall be computed with aggregate queries; images shall be optimised and served in sizes suited to the screen; pages shall load their critical styles without blocking on third-party resources."],
        ["NFR7", "Maintainability", "Code shall follow MVC with services for business logic, form requests for validation and reusable view components."],
        ["NFR8", "Reliability", "Core workflows shall be covered by automated tests that pass before deployment."],
    ], [0.6 * inch, 1.1 * inch, 4.3 * inch])

    h2("3.6 System Architecture")
    P("The system follows a three-tier web architecture implemented with Laravel's MVC structure (Figure 3.1). The browser sends HTTP requests; routes and middleware apply authentication, administrator authorisation, CSRF verification and rate limiting; form requests validate input; controllers coordinate the request, delegating business logic to service classes; Eloquent models read and write the relational database; and Blade views render the response. Uploaded files are stored on Laravel's public storage disk.")
    figure(g["architecture_diagram"](), "3.1: System architecture")

    h2("3.7 Use Case Model")
    P("Three actors interact with the system (Figure 3.2). A <b>Visitor</b> is anyone browsing the public site. A <b>Student</b> is a visitor who submits a trial application or a contact message. An <b>Administrator</b> is an authenticated official with the admin role who manages all data. Table 3.5 describes the principal use case.")
    figure(g["use_case_diagram"](), "3.2: Use case diagram")
    table("3.5: Use case description — Record a match result", ["Item", "Description"], [
        ["Use case", "Record a match result"],
        ["Actor", "Administrator"],
        ["Pre-condition", "The administrator is logged in; the fixture exists."],
        ["Main flow", "1. Open Fixtures and select the match. 2. Choose status “Full time” and enter the home and away scores. 3. Optionally add the referee, attendance and a short summary. 4. Save. 5. Add goal, assist, card and substitution events; for each, choose the team — the player list shows only that team's players. 6. Tick the line-up for each team."],
        ["Alternative flow", "Scores missing for a completed match: the system shows a validation message and nothing is saved. A player not in the selected team: the event is rejected."],
        ["Post-condition", "The league table, team statistics, player statistics, homepage results and match centre reflect the result immediately."],
    ], [1.4 * inch, 4.6 * inch])

    h2("3.8 Data Flow")
    P("Figure 3.3 shows the context-level data flow diagram. Visitors and students send requests, trial applications and contact messages and receive published pages; administrators supply competition data and content and receive the dashboard, statistics and inbox; the system optionally notifies the club by email when a contact message arrives.")
    figure(g["context_dfd"](), "3.3: Context diagram (level-0 data flow diagram)")

    h2("3.9 Database Design")
    P("The database was designed in third normal form. Each real-world entity has its own table with a surrogate primary key; relationships are enforced by foreign keys. Standings and statistics are <b>not stored</b>; they are derived from fixtures and match events, which removes update anomalies. Deletion rules were chosen deliberately: seasons, competitions and teams referenced by fixtures are protected (<i>restrict</i>); match events and line-ups are removed with their fixture (<i>cascade</i>); optional links such as a gallery photo's fixture are cleared (<i>set null</i>); and teams, players, news and staff are soft-deleted so that historical results keep their names. Figure 3.4 shows the entity-relationship diagram.")
    figure(g["erd_diagram"](), "3.4: Entity-relationship diagram")

    h3("3.9.1 Data dictionary")
    P("Tables 3.6 to 3.13 describe the main tables. Every table also has <i>created_at</i> and <i>updated_at</i> timestamps; soft-deletable tables have <i>deleted_at</i>.")
    W = [1.55 * inch, 1.35 * inch, 3.1 * inch]
    H = ["Field", "Type", "Description / constraint"]
    table("3.6: seasons", H, [
        ["id", "BIGINT, PK", "Surrogate key"], ["name", "VARCHAR(255)", "e.g. 2026/2027"], ["slug", "VARCHAR, unique", "URL identifier"],
        ["start_date, end_date", "DATE, null", "Session dates"], ["is_current", "BOOLEAN", "Only one season may be current (enforced in the model)"], ["is_active", "BOOLEAN", "Shown in selectors"],
    ], W)
    table("3.7: competitions", H, [
        ["id", "BIGINT, PK", ""], ["season_id", "FK to seasons (restrict)", "Owning season"], ["name, slug", "VARCHAR", "Name; unique slug"],
        ["short_name", "VARCHAR(30), null", "e.g. BFL"], ["type", "VARCHAR(20)", "league | cup | friendly | tournament"],
        ["description, logo", "TEXT / VARCHAR, null", ""], ["start_date, end_date", "DATE, null", ""], ["is_active", "BOOLEAN", ""],
    ], W)
    table("3.8: teams", H, [
        ["id", "BIGINT, PK", ""], ["name, slug", "VARCHAR; slug unique", "e.g. Amapro FC / amapro-fc"], ["short_name", "VARCHAR(12), null", "Badge text"],
        ["logo", "VARCHAR, null", "Path on public disk"], ["primary_color, secondary_color", "CHAR(7), null", "Hex colours"],
        ["description", "TEXT, null", ""], ["founded_year", "SMALLINT, null", ""], ["captain_name, coach_name", "VARCHAR, null", ""],
        ["is_active", "BOOLEAN", ""], ["deleted_at", "TIMESTAMP, null", "Soft delete"],
    ], W)
    table("3.9: players", H, [
        ["id", "BIGINT, PK", ""], ["team_id", "FK to teams (restrict)", "Player's club"], ["first_name, last_name, slug", "VARCHAR; slug unique", ""],
        ["photo", "VARCHAR, null", ""], ["jersey_number", "TINYINT, null", "1–99; unique within team (validated)"],
        ["position", "VARCHAR(20)", "goalkeeper | defender | midfielder | forward"], ["department, level", "VARCHAR, null", "Public"],
        ["matric_number, state_of_origin", "VARCHAR, null", "<b>Private</b> — admin only; hidden from serialisation"],
        ["dominant_foot, height, bio", "VARCHAR / TEXT, null", ""], ["is_captain, is_featured, is_active", "BOOLEAN", ""], ["deleted_at", "TIMESTAMP, null", "Soft delete"],
    ], W)
    table("3.10: fixtures", H, [
        ["id", "BIGINT, PK", ""], ["competition_id, season_id", "FK (restrict)", "Season always follows the competition"],
        ["home_team_id, away_team_id", "FK to teams (restrict)", "Must differ"], ["match_date, kickoff_time", "DATE, TIME null", ""],
        ["venue, referee", "VARCHAR, null", ""], ["status", "VARCHAR(20)", "scheduled | live | completed | postponed | cancelled"],
        ["home_score, away_score", "TINYINT, null", "Required for live/completed; cleared otherwise"],
        ["matchday, attendance", "INT, null", ""], ["featured", "BOOLEAN", "Highlight on homepage"], ["report", "TEXT, null", "Short summary"],
    ], W)
    table("3.11: match_events", H, [
        ["id", "BIGINT, PK", ""], ["fixture_id", "FK to fixtures (cascade)", ""], ["team_id", "FK to teams (restrict)", "Must be a team in the fixture"],
        ["player_id", "FK to players, null", "Scorer / booked player / player off"], ["related_player_id", "FK to players, null", "Assist provider / player on"],
        ["type", "VARCHAR(20)", "goal | penalty_scored | own_goal | assist | yellow_card | red_card | substitution | penalty_missed"],
        ["minute, additional_minute", "TINYINT", "e.g. 45 + 2"], ["description", "VARCHAR, null", "Note"],
    ], W)
    table("3.12: fixture_players (line-ups)", H, [
        ["id", "BIGINT, PK", ""], ["fixture_id", "FK (cascade)", "Unique with player_id"], ["player_id, team_id", "FK", ""],
        ["is_starting, is_captain", "BOOLEAN", "Starting XI / captain"], ["position, shirt_number, minutes_played", "VARCHAR / TINYINT, null", ""],
    ], W)
    table("3.13: Content and communication tables (summary)", ["Table", "Key fields"], [
        ["news_posts", "title, slug, excerpt, content (sanitised HTML), featured_image, category, author_id (FK to users), fixture_id (FK to fixtures), published_at, is_published, is_featured, deleted_at"],
        ["gallery_items", "title, image, category, fixture_id, team_id, description, is_featured, sort_order"],
        ["videos", "title, slug, thumbnail, video_url, platform, category, description, published_at, is_featured"],
        ["staff", "name, slug, photo, role, type (management | coaching), team_id, bio, sort_order, is_active, deleted_at"],
        ["trial_applications", "full_name, email, phone, matric_number, department, level, preferred_position, dominant_foot, height, previous_team, playing_experience, status, admin_notes"],
        ["contact_messages", "name, email, phone, subject, message, status (new | read | replied | archived), admin_notes"],
        ["users", "name, email, password (hashed), role (admin | user) — Laravel Breeze authentication"],
    ], [1.5 * inch, 4.5 * inch])

    h2("3.10 Algorithm Design")
    h3("3.10.1 League table computation")
    P("The league table is computed on demand by the <i>LeagueTableService</i> (Figure 3.5). Every team drawn in the competition receives a row, so that the table is complete even before the first result. Completed fixtures are processed in date order; for each, both teams' played, goals for and goals against are updated, the result adds a win, draw or loss and 3, 1 or 0 points, and the result is appended to the team's form. Rows are then sorted by points, goal difference, goals for and name, and positions are assigned. Because the table is a function of the results, entering or correcting any result is reflected immediately and consistently.")
    figure(g["league_flowchart"](), "3.5: Flowchart of the league table algorithm")
    h3("3.10.2 Player statistics")
    P("The <i>PlayerStatisticsService</i> derives each player's statistics from live and completed matches only:")
    bullets([
        "<b>Appearances</b> — distinct fixtures in which the player is in a line-up, is involved in an event, or comes on as a substitute.",
        "<b>Goals</b> — goal and penalty-scored events for the player. <b>Assists</b> — assist events plus goals on which the player is recorded as the assisting player.",
        "<b>Yellow and red cards</b> — card events for the player.",
        "<b>Clean sheets</b> (goalkeepers) — completed fixtures the goalkeeper started in which the opponent scored zero.",
    ])
    P("Team statistics and portal-wide totals are computed with single aggregate SQL queries (COUNT and conditional SUM) rather than by loading records into memory.")

    h2("3.11 Input and Output Design")
    P("<b>Inputs</b> are captured through validated forms: administrative forms for every entity (with dropdowns restricted to valid options, date and time pickers, colour pickers, file inputs limited to JPG, PNG and WebP images), the result and event forms on the match page, the public trial application and contact forms (with a hidden honeypot field against spam bots), and CSV files for bulk import. Validation errors are displayed beside the relevant field with an icon and message, not colour alone.")
    P("<b>Outputs</b> are the public pages — homepage (with the sports and facilities sections), basketball page, fixtures and results, match centre, league table with form guide and top scorers, teams, squad, player profiles, news, gallery, videos and staff pages — and the administrative dashboard with counts, upcoming fixtures, recent results and inbox alerts. Optional email notifications are sent for new contact messages.")

    h2("3.12 Security Design")
    table("3.14: Security measures", ["Threat", "Countermeasure implemented"], [
        ["Unauthorised access to administration", "Authentication (Breeze) plus an <i>admin</i> middleware that checks the user's role on every admin route; form requests re-check authorisation; the role cannot be mass-assigned."],
        ["SQL injection", "All database access through Eloquent / query builder with bound parameters."],
        ["Cross-site scripting (XSS)", "Blade escapes all output by default; rich text is cleaned by an allow-list HTML sanitiser on save and on display; javascript: links, scripts, event handlers and inline styles are removed."],
        ["Cross-site request forgery", "CSRF token on every state-changing form, verified by Laravel middleware."],
        ["Tampered form data", "Server-side validation of every relationship: a match event's team must be in the fixture and its players must belong to that team; IDs from hidden inputs are never trusted."],
        ["Malicious file upload", "Only images (JPG, PNG, WebP) within size limits are accepted; files are re-encoded to WebP under random names, which also strips embedded metadata such as GPS location."],
        ["Spam and abuse of public forms", "Honeypot field and rate limiting (5 submissions per minute)."],
        ["Password compromise", "Passwords stored with bcrypt hashing; no default admin password is created outside the local environment."],
        ["Privacy of student data", "Matriculation numbers, state of origin and applicants' contact details are shown only in the admin panel and hidden from model serialisation."],
    ], [1.8 * inch, 4.2 * inch])


# =========================================================================== CHAPTER FOUR
def chapter_four(g):
    P, bullets, numbered, h2, h3, chapter, table, screenshot = g["P"], g["bullets"], g["numbered"], g["h2"], g["h3"], g["chapter"], g["table"], g["screenshot"]
    inch = g["inch"]

    chapter("FOUR", "SYSTEM IMPLEMENTATION AND TESTING")
    h2("4.1 Introduction")
    P("This chapter describes the implementation environment, the structure of the implemented system and its modules, the testing carried out and the results obtained. Screenshots in this chapter were captured from the running system populated with the generated sample season described in Section 4.4.")

    h2("4.2 Implementation Environment")
    table("4.1: Development and runtime environment", ["Component", "Specification"], [
        ["Operating system", "Microsoft Windows 11"],
        ["Local server stack", "XAMPP (Apache, MariaDB 10.4.32) and PHP's built-in server"],
        ["Language / framework", "PHP 8.2.12; Laravel Framework 12.69"],
        ["Database", "MariaDB 10.4.32 (MySQL-compatible); SQLite in-memory for automated tests"],
        ["Front-end tooling", "Node.js 22, Vite 7, Tailwind CSS 3.4, Alpine.js 3, Trix 2"],
        ["Libraries", "Laravel Breeze 2, Symfony HtmlSanitizer 7.4, PHP GD and EXIF extensions"],
        ["Testing", "Pest 3 on PHPUnit; headless Microsoft Edge (Chrome DevTools Protocol) for responsive checks"],
        ["Editor", "Visual Studio Code"],
        ["Minimum client", "Any modern browser (Chrome, Edge, Firefox, Safari) on phone, tablet or desktop"],
    ], [1.8 * inch, 4.2 * inch])

    h2("4.3 System Structure and Modules")
    P("The implemented system comprises 14 Eloquent models, 38 controller classes (public and admin), 17 database migrations, 116 Blade view files, 6 service classes and 121 named routes, of which 71 belong to the administration panel. Application PHP code amounts to about 5,400 lines and view templates to about 5,400 lines.")
    table("4.2: Main modules", ["Module", "Key components", "Function"], [
        ["Public website", "HomeController, FrontendController (editorial pages including Basketball), TeamController, PlayerController, FixtureController, LeagueTableController, NewsController, GalleryController, VideoController, StaffController", "Presents the sports, facilities, competition information and content."],
        ["Standings and statistics", "LeagueTableService, TeamStatisticsService, PlayerStatisticsService", "Derives tables and statistics from results and events."],
        ["Administration", "Admin\\* controllers, Form Requests, admin Blade components", "CRUD for all entities; result, event and line-up entry; inboxes."],
        ["Authentication and access control", "Laravel Breeze, EnsureUserIsAdmin middleware, users.role", "Login and restriction of the admin panel."],
        ["Forms and communication", "TrialApplicationController, ContactController, ContactMessageReceived mailable", "Trial applications and contact messages."],
        ["Content editing", "Trix editor, RichText sanitiser", "Safe rich-text news articles."],
        ["Media", "ImageOptimizer, ManagesUploads", "Resizing, orientation, WebP conversion and cleanup of uploads."],
        ["Data loading", "FootballImporter, football:import command, SampleDataSeeder, SampleGraphics", "CSV import of real data; generation of a sample season and graphics."],
    ], [1.45 * inch, 2.75 * inch, 1.8 * inch], font_size=9)

    h2("4.4 Test Data")
    P("Verified squad lists were not available during development (Section 1.6). To exercise every feature realistically, a deterministic <i>SampleDataSeeder</i> generates a complete season for the seven real teams: 126 fictional players (18 per team) with realistic Nigerian names, departments and levels; 22 fictional management and coaching staff; a full double round-robin league of 42 fixtures, of which matches already due are played with consistent results, goals, assists, cards, substitutions and line-ups; match reports; and gallery items. Illustrations (team crests, player shirt cards and staff silhouettes) are drawn by the system rather than using photographs of people. The seeder is disabled by default outside development, and real data is loaded with the <i>football:import</i> command (Appendix B).")

    h2("4.5 Implementation Screenshots")
    h3("4.5.1 Public website")
    P("Figures 4.1 to 4.12 show the public website. The homepage introduces BOUESTI Sports over a photograph of the university's football pitch and grandstand (Figure 4.1), leads into the individual sports (Figure 4.2), and presents featured players, latest news, the next football fixture with a live kick-off countdown, recent results, the university's sports grounds (Figure 4.3) and statistics computed from the database. The Basketball page (Figure 4.4) describes the programme and shows the university's courts.")
    screenshot(D("doc", "_-1280.png"), "4.1: Homepage — BOUESTI Sports")
    screenshot(D("doc", "_home_section0-1280.png"), "4.2: Homepage — Our Sports (Football and Basketball)")
    screenshot(D("doc", "_home_section1-1280.png"), "4.3: Homepage — the university's sports grounds")
    screenshot(D("doc", "_basketball-1280.png"), "4.4: Basketball page")
    screenshot(D("doc", "_league_table-1280.png"), "4.5: League table with form guide, computed from completed fixtures")
    screenshot(D("doc", "_matches_7-1280.png"), "4.6: Match centre with score and minute-by-minute events")
    screenshot(D("doc", "_players_daniel_arogundade-1280.png"), "4.7: Player profile (public details only; matriculation number is never shown)")
    screenshot(D("doc", "_teams-1280.png"), "4.8: Teams directory with generated crests")
    screenshot(D("doc", "_squad-1280.png"), "4.9: Squad page with team and position filters")
    screenshot(D("doc", "_fixtures-1280.png"), "4.10: Fixtures with competition, team and month filters")
    screenshot(D("doc", "_news_amapro_fc_cruise_past_young_boys_fc_in_4_1_win-1280.png"), "4.11: Match report article")
    screenshot(D("doc", "_join_the_team-1280.png"), "4.12: Online trial application form")

    h3("4.5.2 Mobile views")
    P("The same pages adapt to phone screens: navigation moves into an accessible slide-in menu, grids collapse to a single column and the league table keeps the position and club columns fixed while statistics scroll (Figure 4.13).")
    g["story"].append(g["CondPageBreak"](5 * inch))
    mobile = mobile_strip(g)
    g["figure"](mobile, "4.13: Mobile views — homepage, league table, basketball page and navigation menu")

    h3("4.5.3 Administration panel")
    screenshot(D("doca", "_admin_dashboard-1280.png"), "4.14: Administration dashboard")
    screenshot(D("doca", "_admin_fixtures_7-1280.png"), "4.15: Match management — result entry and match events")
    screenshot(D("doca", "_admin_players-1280.png"), "4.16: Player management with search and filters (admin-only matriculation numbers)")
    screenshot(D("doca", "_admin_news_create-1280.png"), "4.17: News editor with rich-text toolbar")
    screenshot(D("doca", "_admin_trial_applications-1280.png"), "4.18: Trial applications inbox")

    h2("4.6 System Testing")
    h3("4.6.1 Test strategy")
    P("Testing combined automated feature tests, automated responsive-layout audits and manual exploratory testing. Automated tests run with Pest against a fresh in-memory SQLite database for every test, so each test starts from a known state and does not affect development data. Feature tests drive the application through HTTP requests exactly as a browser would — submitting forms, following redirects and inspecting rendered pages — and therefore test routes, middleware, validation, controllers, services, models and views together.")
    h3("4.6.2 Test cases and results")
    rows = [
        ["TC01", "Seven internal teams seeded with correct slugs", "amapro-fc … amcoms created; one current season", "Pass"],
        ["TC02", "Guest opens admin panel; ordinary user opens or posts to admin", "Guest redirected to login; user receives 403; no data created", "Pass"],
        ["TC03", "End-to-end workflow: add players to Amapro FC and Elite FC, create fixture, enter 2–1 result, record goal with assist, publish match report", "Fixture listed publicly; table shows Amapro FC 3 pts first; scorer has 1 goal and 1 appearance; assister 1 assist; result on homepage; report published", "Pass"],
        ["TC04", "Create fixture with the same home and away team", "Rejected with message; nothing saved", "Pass"],
        ["TC05", "Complete a match without the away score; postpone a match with scores", "Validation error; scores cleared for postponed match", "Pass"],
        ["TC06", "Duplicate shirt number in same team / different team", "Rejected in same team; allowed across teams", "Pass"],
        ["TC07", "Record event with a player from the other team, or a team not in the fixture (tampered form)", "Both rejected; no events stored", "Pass"],
        ["TC08", "Mark a second season as current", "Only the new season is current", "Pass"],
        ["TC09", "Delete a team that has played", "Team soft-deleted; historical match still shows its name", "Pass"],
        ["TC10", "Delete a competition that has fixtures", "Refused with explanatory message", "Pass"],
        ["TC11", "Line-up including a player from another team", "Rejected; valid line-up saved with starter and captain", "Pass"],
        ["TC12", "Upload team logo; replace it; upload a PHP file as logo", "Stored on public disk; old file deleted; PHP file rejected", "Pass"],
        ["TC13", "Submit trial application (valid, invalid, honeypot filled)", "Stored as pending; errors shown; spam rejected", "Pass"],
        ["TC14", "Visit all 26 public URLs (including the Basketball page and filtered views) with an empty database", "All return HTTP 200 with empty-state messages", "Pass"],
        ["TC15", "Open legacy static URLs", "Redirect to dynamic pages", "Pass"],
        ["TC16", "Open an unpublished article publicly", "HTTP 404", "Pass"],
        ["TC17", "Open every admin screen", "All render; opening an application marks it “reviewing”", "Pass"],
        ["TC18", "Contact form with and without notification address", "Message stored; email sent with reply-to only when configured", "Pass"],
        ["TC19", "Contact form validation, honeypot, confirmation; admin inbox workflow", "Errors shown; message marked read / replied; non-admins refused", "Pass"],
        ["TC20", "Upload a 4000×3000 photo; upload a 300×200 image", "Stored as 2000×1500 WebP; small image not enlarged", "Pass"],
        ["TC21", "Rich text containing script, onerror, javascript: link, iframe, style", "Formatting kept; all dangerous content removed", "Pass"],
        ["TC22", "Save article from editor; save empty editor; view legacy plain-text article", "Stored clean and rendered; empty rejected; legacy rendered safely", "Pass"],
        ["TC23", "CSV import: full import; re-import; invalid rows; dry run; missing photo", "Data and statistics created; no duplicates; whole import rolled back with line-numbered errors; dry run saves nothing; missing photo is a warning", "Pass"],
        ["TC24", "Sample season generation", "126 players, 22 staff, 42 fixtures; every score equals its goal events; 11 starters and 5 substitutes per team; balanced standings", "Pass"],
    ]
    table("4.3: Summary of test cases and results", ["ID", "Test case", "Expected result", "Result"], rows, [0.5 * inch, 2.35 * inch, 2.55 * inch, 0.6 * inch], font_size=8.8)
    P("In total, <b>96 automated tests with 921 assertions</b> were executed; all passed. These comprise the project-specific tests grouped in Table 4.3 (several table rows cover multiple tests) together with the authentication, registration, password and profile tests supplied with Laravel Breeze, which confirm that existing account functionality was preserved.")

    h3("4.6.3 Responsive and usability testing")
    P("An automated audit loaded every public page in headless Microsoft Edge with true mobile emulation at ten widths — 320, 360, 375, 390, 414, 430, 768, 1024, 1280 and 1440 pixels (240 page loads) — and checked for horizontal scrolling, elements overflowing the viewport, controls smaller than touch-target guidelines, text below 11 pixels and JavaScript errors. After fixes made during development, no page produced horizontal scrolling, overflow or console errors. Interactive behaviour — mobile menu, search dialog, filters, form validation, accordion, lightbox, sticky table columns, the admin drawer and confirmation dialogs — was also exercised automatically.")
    P("The audit was repeated after the BOUESTI Sports update over all 27 public pages, including the new Basketball page, at the same ten widths (270 page loads). No page produced horizontal scrolling or overflow; the only text reported below 11 pixels belonged to buttons that deliberately collapse to icons on phones while keeping their accessible labels.")
    h3("4.6.4 Defects found and fixed")
    P("Testing revealed and led to the correction of several defects, for example: a blocking font import that delayed page styling on slow connections; hero text touching the screen edge on mobile; a screen-reader-only label causing horizontal scrolling in admin tables; unreadable 10-pixel labels; and an incorrectly ordered score (1–4 instead of 4–1) in generated match-report headlines. This illustrates the value of combining automated and visual testing.")

    h2("4.7 Performance Considerations")
    bullets([
        "Statistics use single aggregate SQL queries; related records are eager-loaded to avoid repeated queries.",
        "A 4000×3000 phone photograph is stored as a 2000×1500 WebP image, typically a small fraction of the original size.",
        "The production admin stylesheet is about 13 KB compressed (gzip); the rich-text editor script (about 52 KB compressed) loads only on the news form.",
        "Images below the fold are lazy-loaded, and phones receive smaller image variants: each facility photograph is provided at about 1080 and 640 pixels wide (roughly 85–130 KB and 35–55 KB) and the browser selects the appropriate size.",
    ])

    h2("4.8 Deployment")
    P("The system can be deployed on any host supporting PHP 8.2 and MySQL/MariaDB, including shared hosting. Deployment steps — installing dependencies, configuring the environment, running migrations, linking storage and building assets — are given in Appendix B. Before going live, the administrator password must be set through the environment, sample data disabled, and verified squad data imported.")


def mobile_strip(g):
    from PIL import Image as PILImage
    from io import BytesIO
    paths = [D("docm", "_-390.png"), D("docm", "_league_table-390.png"), D("docm", "_basketball-390.png"), D("docm", "_menu-390.png")]
    imgs = [PILImage.open(p).convert("RGB") for p in paths]
    gap = 20
    w = sum(i.width for i in imgs) + gap * 2
    h = max(i.height for i in imgs)
    strip = PILImage.new("RGB", (w, h), "white")
    x = 0
    for im in imgs:
        strip.paste(im, (x, 0))
        x += im.width + gap
    buf = BytesIO()
    strip.save(buf, "JPEG", quality=82)
    buf.seek(0)
    width = 6.0 * g["inch"]
    return g["Image"](buf, width=width, height=width * h / w)


# =========================================================================== CHAPTER FIVE
def chapter_five(g):
    P, bullets, numbered, h2, chapter = g["P"], g["bullets"], g["numbered"], g["h2"], g["chapter"]

    chapter("FIVE", "SUMMARY, CONCLUSION AND RECOMMENDATIONS")
    h2("5.1 Summary")
    P("This project addressed the lack of a central, accurate and accessible source of information for student sport — in particular the internal football competitions — at Bamidele Olumilua University of Education, Science and Technology, Ikere-Ekiti. A web-based sports management and information portal, BOUESTI Sports, was designed and implemented using the Laravel framework, a relational database and a responsive user interface.")
    P("The system provides a public website that presents the university's sports and facilities — football and basketball, the football pitch and grandstand and the outdoor courts — together with football fixtures, results, automatically computed standings, a match centre, teams, squads, player profiles and statistics, news, a gallery and videos, together with online trial applications and contact messages. A secure administration panel enables officials to manage all data and content, record results, match events and line-ups, and review applications and messages. Supporting features include a sanitised rich-text editor, automatic image optimisation, a CSV import tool for real data and a generator for a realistic sample season. The system was verified with 96 automated tests and responsive audits across ten screen widths.")
    h2("5.2 Achievement of Objectives")
    numbered([
        "A normalised relational database of 17 migrations was designed and implemented for all competition and content entities (Objective 1).",
        "A responsive public website presenting the university's sports (football and basketball), its facilities and all competition information was developed (Objective 2).",
        "League standings and player and team statistics are computed automatically from results and events (Objective 3).",
        "A role-based administration panel with full management of all data was implemented (Objective 4).",
        "Online trial applications and contact messages with administrative inboxes were implemented (Objective 5).",
        "Security controls addressing the OWASP Top 10 risks relevant to the system, and privacy rules for student data, were implemented (Objective 6).",
        "The system was tested with automated feature tests and responsive audits, all of which passed (Objective 7).",
    ])
    h2("5.3 Conclusion")
    P("The study shows that a purpose-built, institution-owned sports portal can replace informal handling of internal competition information with a single reliable source of truth, and can bring every campus sport together under one brand. Recording each fact once and deriving standings and statistics from it removes computational errors and disputes, preserves history across seasons, gives players recognition, and makes information instantly available to every student on any device, while role-based access control protects student data. The use of an established MVC framework, a normalised database and automated testing produced a system that is maintainable and can be extended by future students and developers.")
    h2("5.4 Recommendations")
    bullets([
        "The university's sports unit should adopt the portal as the official record of internal competitions and the official website for campus sport, and assign trained officials to record results and events promptly after each match.",
        "Verified squad lists, staff details, team colours and logos should be imported before public launch, and players' consent should be obtained before publishing photographs.",
        "Basketball organisers should provide training times, team selection dates and contact details so that the basketball section can be kept current.",
        "The system should be hosted on the university's infrastructure or a reputable host with HTTPS, regular database backups and a strong administrator password.",
        "Officials should be trained using the user manual in Appendix A.",
        "The Department of Computer Science may use the project as a platform for future student projects.",
    ])
    h2("5.5 Suggestions for Further Work")
    bullets([
        "Full multi-sport management: adding a sport attribute to competitions, teams and fixtures so that basketball — and later sports such as volleyball, athletics and table tennis — have fixtures, results, standings and statistics managed in the same way as football.",
        "Sport-specific rules, for example basketball standings by wins and points difference without draws, and quarter-by-quarter scores.",
        "Real-time live match updates using WebSockets and push notifications to subscribed students.",
        "Automatic fixture generation (round-robin scheduling) and knockout-bracket visualisation for cup competitions.",
        "Multiple administrator roles (for example, team managers who can submit their own line-ups) with approval workflows.",
        "Mobile applications or a Progressive Web App with offline access.",
        "Advanced analytics and season-to-season historical comparisons.",
        "Integration with the university's student information system to verify players' registration.",
    ])


# =========================================================================== REFERENCES
def references(g):
    story = g["story"]
    story.append(g["PageBreak"]())
    story.append(g["toc_para"]("REFERENCES", "front", 0))
    refs = [
        "Beck, K. (2002). <i>Test-Driven Development: By Example</i>. Boston: Addison-Wesley.",
        "Codd, E. F. (1970). A relational model of data for large shared data banks. <i>Communications of the ACM</i>, 13(6), 377–387.",
        "Connolly, T., and Begg, C. (2015). <i>Database Systems: A Practical Approach to Design, Implementation, and Management</i> (6th ed.). Harlow: Pearson.",
        "Elmasri, R., and Navathe, S. B. (2016). <i>Fundamentals of Database Systems</i> (7th ed.). Hoboken, NJ: Pearson.",
        "Fielding, R. T. (2000). <i>Architectural Styles and the Design of Network-based Software Architectures</i> (Doctoral dissertation). University of California, Irvine.",
        "Fowler, M. (2002). <i>Patterns of Enterprise Application Architecture</i>. Boston: Addison-Wesley.",
        "Gamma, E., Helm, R., Johnson, R., and Vlissides, J. (1994). <i>Design Patterns: Elements of Reusable Object-Oriented Software</i>. Reading, MA: Addison-Wesley.",
        "Krasner, G. E., and Pope, S. T. (1988). A cookbook for using the model-view-controller user interface paradigm in Smalltalk-80. <i>Journal of Object-Oriented Programming</i>, 1(3), 26–49.",
        "Laravel. (2025). <i>Laravel 12.x Documentation</i>. Retrieved from https://laravel.com/docs/12.x",
        "Marcotte, E. (2010, May 25). Responsive web design. <i>A List Apart</i>. Retrieved from https://alistapart.com/article/responsive-web-design/",
        "Nielsen, J. (1993). <i>Usability Engineering</i>. Boston: Academic Press.",
        "OWASP Foundation. (2021). <i>OWASP Top 10:2021</i>. Retrieved from https://owasp.org/Top10/",
        "Pest. (2025). <i>Pest PHP Testing Framework Documentation</i>. Retrieved from https://pestphp.com/docs",
        "Pressman, R. S., and Maxim, B. R. (2020). <i>Software Engineering: A Practitioner's Approach</i> (9th ed.). New York: McGraw-Hill Education.",
        "Sommerville, I. (2016). <i>Software Engineering</i> (10th ed.). Boston: Pearson.",
        "Symfony. (2025). <i>The HtmlSanitizer Component</i>. Retrieved from https://symfony.com/doc/current/html_sanitizer.html",
        "Tailwind Labs. (2025). <i>Tailwind CSS Documentation</i>. Retrieved from https://tailwindcss.com/docs",
        "World Wide Web Consortium (W3C). (2018). <i>Web Content Accessibility Guidelines (WCAG) 2.1</i>. Retrieved from https://www.w3.org/TR/WCAG21/",
    ]
    for r in refs:
        story.append(g["Paragraph"](r, g["styles"]["ref"]))


# =========================================================================== APPENDICES
def appendices(g):
    P, bullets, numbered, table, code, story = g["P"], g["bullets"], g["numbered"], g["table"], g["code"], g["story"]
    inch = g["inch"]

    def appendix(title):
        story.append(g["PageBreak"]())
        story.append(g["toc_para"](title, "front", 0))

    appendix("APPENDIX A: USER MANUAL")
    P("<b>A.1 Visitors and students.</b> Open the website in any browser. Use the menu (the menu button on phones) to reach Sports (Football and Basketball), Club, Team, Fixtures, League Table, News, Media and Fans pages; use the search button to find any page. On the Fixtures page, switch between Upcoming and Results and filter by competition, team or month; select <i>Match Centre</i> for a match's events and line-ups. To apply for trials, open <i>Join the Club</i>, complete the form and submit; to contact the club, use the Contact page.")
    P("<b>A.2 Logging in as an administrator.</b> Go to <i>/login</i>, enter the administrator email and password, and you are taken to <i>/admin/dashboard</i>. The sidebar groups all functions under Football, Matches, Content and People; on phones, open it with the menu button.")
    P("<b>A.3 Setting up a season.</b>")
    numbered([
        "Seasons &gt; New season: enter e.g. 2026/2027 and tick <i>Current season</i>.",
        "Competitions &gt; New competition: choose the season and type (League for a table).",
        "Teams: edit each team to add the short name, colours, logo, coach and description.",
        "Players &gt; Add player: choose the team and position, enter the student's details and photo; use <i>Save and add another</i> for quick entry. Matriculation number and state of origin remain private.",
    ])
    P("<b>A.4 Scheduling and recording matches.</b>")
    numbered([
        "Fixtures &gt; New fixture: choose competition, home and away teams, date, kick-off time, venue and matchday.",
        "After the match, open the fixture. In <i>Result</i>, choose <i>Full time</i>, enter both scores and save — the league table updates immediately.",
        "In <i>Match events</i>, choose the event type and team, then the player (only that team's players are listed); add the assisting player for goals or the player coming on for substitutions; enter the minute and save.",
        "In <i>Line-ups</i>, tick each team's match-day squad, the starters (maximum 11) and the captain, then save.",
        "Select <i>Write match report</i> to publish a news article linked to the match, and <i>Add photos</i> to upload match photos.",
    ])
    P("<b>A.5 Content and inboxes.</b> News: write with the toolbar, choose a category, tick <i>Published</i> (or set a future date to schedule). Gallery: upload up to 20 photos at once. Videos: paste a YouTube link. Management and Staff: add people with type Management or Coaching. Trial Applications and Messages: open an item to read it (it is marked as read), change its status and add private notes.")
    P("<b>A.6 Deleting.</b> Every delete asks for confirmation. Teams, players, news and staff are archived rather than destroyed, so past results remain correct. Competitions and seasons that have fixtures cannot be deleted; mark them inactive instead.")

    appendix("APPENDIX B: INSTALLATION AND DEPLOYMENT GUIDE")
    P("<b>Requirements:</b> PHP 8.2+ with GD, EXIF and PDO MySQL extensions; Composer 2; Node.js 18+; MySQL 8 or MariaDB 10.4+.")
    story.append(g["Preformatted"]("""# 1. Install dependencies
composer install --no-dev --optimize-autoloader
npm install && npm run build

# 2. Configure the environment
cp .env.example .env
php artisan key:generate
#    edit .env: APP_URL, DB_DATABASE, DB_USERNAME, DB_PASSWORD,
#    ADMIN_EMAIL, ADMIN_PASSWORD, MAIL_* settings, CONTACT_NOTIFY_EMAIL,
#    APP_TIMEZONE=Africa/Lagos, SEED_SAMPLE_DATA=false (production)

# 3. Database, storage and caches
php artisan migrate --seed          # season, league, seven teams, administrator
php artisan storage:link
php artisan optimize

# 4. Load verified data (fill database/data/import/*.csv, photos in photos/)
php artisan football:import --dry-run
php artisan football:import

# Development only: complete sample season with fictional people
#    SEED_SAMPLE_DATA=true  then  php artisan migrate:fresh --seed

# Run the automated tests
php artisan test""", g["styles"]["code"]))
    story.append(g["Spacer"](1, 10))
    P("For shared hosting, point the domain's document root to the project's <i>public</i> folder. Enable HTTPS and schedule regular database backups.")

    appendix("APPENDIX C: SELECTED SOURCE CODE")
    code("app/Services/LeagueTableService.php", title="C.1 League table computation")
    code("app/Services/PlayerStatisticsService.php", 1, 80, title="C.2 Player statistics (excerpt)")
    code("app/Http/Middleware/EnsureUserIsAdmin.php", title="C.3 Administrator access middleware")
    code("app/Http/Requests/Admin/MatchEventRequest.php", title="C.4 Validation of match events against the fixture's teams")
    code("app/Support/RichText.php", 1, 60, title="C.5 Rich-text sanitisation (excerpt)")
    code("routes/web.php", title="C.6 Route definitions")
